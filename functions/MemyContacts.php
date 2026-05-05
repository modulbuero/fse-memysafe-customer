<?php 
/**
 * Notfallkontakt und Vertrauesnspersonen handler
 */
class MemyContacts {
    /**
     *  Constructor mit inits
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_handle_update_contacts', array($this, 'handle_update_contacts'));
        add_action('wp_ajax_handle_delete_contacts', array($this, 'handle_delete_contacts'));
        add_action('wp_ajax_handle_send_contact_invitation', array($this, 'handle_send_contact_invitation'));
        add_action('template_redirect', array($this, 'handle_accept_invitation'));
    }
    
    /**
     * Scripte initiieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('contacts-manager', get_stylesheet_directory_uri() . '/assets/js/contacts-manager.js', array('jquery','wp-util'), '1.0', true);
        wp_localize_script('contacts-manager', 'ajax_object_contacts', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('contacts_manager_nonce')
        ));
        wp_localize_script('memy-first-settings', 'ajax_object_contacts', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            '_nonce' => wp_create_nonce('contacts_manager_nonce')
        ));
    }

    /**
     * Einrichten des Kontakts
     */
    public function handle_update_contacts() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id    = get_current_user_id();
        $email      = sanitize_text_field($_POST['email']);
        $typ        = sanitize_text_field($_POST['typ']);
        $status     = sanitize_text_field($_POST['status']);
        $name       = sanitize_text_field($_POST['name']);
        $tel        = sanitize_text_field($_POST['tel']);
        $firma      = sanitize_text_field($_POST['firma']);
        $mmsi_safe  = sanitize_text_field($_POST['mmsi_safe']);
        #$is_main    = sanitize_text_field($_POST['is_main']);
        $mmsi_can   = sanitize_text_field($_POST['mmsi_can']);
        $contact_id = intval($_POST['contact_id']);
        
        $contact_data= [
            'email'         => $email,
            'typ'           => $typ,
            'name'          => $name,
            'tel'           => $tel,
            'firma'         => $firma,
            'mmsi_safe'     => $mmsi_safe,
            'status'        => $status,
            #'hauptkontakt'  => $is_main,
            'mmsi_can'      => $mmsi_can
        ];
        
        // Update user meta
        update_user_meta($user_id, 'contact-person-'.$contact_id, $contact_data);
        wp_send_json_success(array(
            'message' => $contact_data["typ"]. ' ' . $contact_data["name"] . ' gespeichert.',
            'debug'   => print_r($contact_data, true)
        ));
    }

    /**
     * Löschen des Kontakts
     */    
    public function handle_delete_contacts() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id      = get_current_user_id();
        $contact_id   = intval($_POST['contact_id']);
        $contact_name = sanitize_text_field($_POST['contact_name']);
        
        // Kontaktdaten leeren
        $empty_data = [
            'email'         => '',
            'typ'           => '',
            'name'          => '',
            'tel'           => '',
            'firma'         => '',
            'mmsi_safe'     => '',
            'status'        => '',
            'hauptkontakt'  => '',
            'mmsi_can'      => ''
        ];
        
        // Update user meta with empty data
        update_user_meta($user_id, 'contact-person-'.$contact_id, $empty_data);
        wp_send_json_success(array(
            'message' => $contact_name . ' erfolgreich gelöscht.',
            'debug'   => [
                'contact_id' => $contact_id,
                'user_id' => $user_id,
                'contact_name' => $contact_name
            ]
        ));
    }

    /**
     * Generiert einen automatischen Benutzername anhand der Emailadresse zum Blog.
     * Sendet eine Einladungsemail mit Passwort.
     * 
     */
    public function handle_send_contact_invitation(){
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce', false)){
            wp_send_json_error('Invalid nonce');
            return;
        }

        $user_id = get_current_user_id();
        if(!$user_id){
            wp_send_json_error('Nicht angemeldet');
            return;
        }

        $contact_mail = sanitize_email($_POST['contact_mail'] ?? '');
        $contact_name = sanitize_text_field($_POST['contact_name'] ?? '');

        if(empty($contact_mail) || !is_email($contact_mail)){
            wp_send_json_error('Ungültige E-Mail-Adresse.');
            return;
        }

        if(email_exists($contact_mail)){
            wp_send_json_error('Diese E-Mail-Adresse ist bereits registriert.');
            return;
        }

        $username = $this->generate_username_from_email($contact_mail);
        $password = wp_generate_password(12, false);
        $token    = wp_generate_password(32, false);

        $invite_data = array(
            'email'        => $contact_mail,
            'username'     => $username,
            'password'     => wp_hash_password($password),
            'inviter_id'   => $user_id,
            'contact_name' => $contact_name,
            'created_at'   => current_time('mysql'),
        );

        update_option('memy_contact_invitation_' . $token, $invite_data);

        $sent = $this->send_contact_invitation_email($contact_mail, $contact_name, $username, $password, $token);
        if(!$sent){
            wp_send_json_error('Einladung konnte nicht versendet werden.');
            return;
        }

        wp_send_json_success(array('message' => 'Einladung erfolgreich gesendet.'));
    }

    private function generate_username_from_email($email){
        $local_part = strstr($email, '@', true);
        $username   = sanitize_user(strtolower($local_part), true);

        if(empty($username)){
            $username = 'kontakt';
        }

        $base = substr($username, 0, 60);
        $candidate = $base;
        $suffix = 1;

        while(username_exists($candidate)){
            $candidate = substr($base, 0, 55) . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function send_contact_invitation_email($email, $name, $username, $password, $token){
        $invitation_url = home_url('/?accept_invitation=' . rawurlencode($token));
        $inviter = wp_get_current_user();
        $inviter_name = $inviter->display_name ?: $inviter->user_login;

        $subject = 'Ihre Einladung zum Memy Safe';
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $message = "<html><body>";
        $message .= "<h2>Hallo " . esc_html($name ?: 'Kontakt') . ",</h2>";
        $message .= "<p>Sie wurden von " . esc_html($inviter_name) . " eingeladen, auf den Memy Safe zuzugreifen.</p>";
        $message .= "<p>Benutzername: <strong>" . esc_html($email) . "</strong><br>";
        $message .= "Temporäres Passwort: <strong>" . esc_html($password) . "</strong></p>";
        $message .= "<p>Bitte bestätigen Sie Ihre Einladung, indem Sie auf den folgenden Link klicken:</p>";
        $message .= "<p><a href=\"" . esc_url($invitation_url) . "\">Einladung bestätigen</a></p>";
        $message .= "<p>Oder kopieren Sie den Link in Ihren Browser:<br>" . esc_html($invitation_url) . "</p>";
        $message .= "<p>Nach der Bestätigung können Sie Ihr Passwort im Konto ändern.</p>";
        $message .= "<p>Falls Sie diese Einladung nicht erwartet haben, ignorieren Sie bitte diese E-Mail.</p>";
        $message .= "</body></html>";

        return wp_mail($email, $subject, $message, $headers);
    }

    public function handle_accept_invitation(){
        if(!isset($_GET['accept_invitation'])){
            return;
        }

        $token = sanitize_text_field($_GET['accept_invitation']);
        $option_key = 'memy_contact_invitation_' . $token;
        $invite_data = get_option($option_key);

        if(!$invite_data){
            wp_die('Ungültiger oder abgelaufener Einladungslink.');
        }

        // Prüfen, ob die Einladung abgelaufen ist (24 Stunden)
        $created_time = strtotime($invite_data['created_at']);
        if(time() - $created_time > 24 * 60 * 60){
            delete_option($option_key);
            wp_die('Der Einladungslink ist abgelaufen.');
        }

        // Prüfen, ob E-Mail bereits existiert
        if(email_exists($invite_data['email'])){
            delete_option($option_key);
            wp_die('Diese E-Mail-Adresse ist bereits registriert.');
        }

        // Benutzer erstellen
        $user_id = wp_create_user(
            $invite_data['username'],
            wp_generate_password(), // Temporäres Passwort, wird gleich überschrieben
            $invite_data['email']
        );

        if(is_wp_error($user_id)){
            wp_die('Fehler beim Erstellen des Benutzerkontos: ' . $user_id->get_error_message());
        }

        // Gespeichertes Passwort setzen
        global $wpdb;
        $wpdb->update(
            $wpdb->users,
            array('user_pass' => $invite_data['password']),
            array('ID' => $user_id)
        );

        // Rolle zuweisen (z. B. subscriber)
        $user = new WP_User($user_id);
        $user->set_role('subscriber');

        // Meta-Daten speichern
        update_user_meta($user_id, 'contact_inviter_id', $invite_data['inviter_id']);
        update_user_meta($user_id, 'contact_name', $invite_data['contact_name']);

        // Einladungsoption löschen
        delete_option($option_key);

        // Benutzer einloggen
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);

        // Weiterleitung zur Startseite oder Dashboard
        wp_redirect(home_url());
        exit;
    }
}

new MemyContacts();