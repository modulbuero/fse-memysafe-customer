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
        $email      = sanitize_email($_POST['email'] ?? '');
        $typ        = sanitize_text_field($_POST['typ']);
        $status     = sanitize_text_field($_POST['status']);
        $fname      = sanitize_text_field($_POST['fname']);
        $lname      = sanitize_text_field($_POST['lname']);
        $tel        = sanitize_text_field($_POST['tel']);
        $firma      = sanitize_text_field($_POST['firma']);
        $mmsi_safe  = sanitize_text_field($_POST['mmsi_safe']);
        #$is_main    = sanitize_text_field($_POST['is_main']);
        #$mmsi_can   = sanitize_text_field($_POST['mmsi_can']);
        $contact_id = intval($_POST['contact_id']);
        $wp_user_id = intval($_POST['wp_id'] ?? 0);
        
        $contact_data= [
            'email'         => $email,
            'typ'           => $typ,
            'wp_user_id'    => $wp_user_id,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'tel'           => $tel,
            'firma'         => $firma,
            'mmsi_safe'     => $mmsi_safe,
            'status'        => $status,
            #'hauptkontakt'  => $is_main,
            #'mmsi_can'      => $mmsi_can
        ];

        $nachricht = $contact_data["typ"]. ' ' . $fname . ' ' . $lname. ' gespeichert.';

        // Falls die Kontaktperson bereits als WordPress-Benutzer existiert,
        if ($wp_user_id) {
            $wp_user_data = wp_update_user(array(
                'ID'         => $wp_user_id,
                'first_name' => $fname,
                'last_name'  => $lname,
                'user_email' => $email,
            ));

            if (is_wp_error($wp_user_data)) {
                wp_mail('webmaster@modulbuero.com', 'MMSI NK-Aktualisierung Failed', 'WP-User-ID: ' . $wp_user_id);
            }
        }

        // Eintrag in die MemyProtocolManager Tabelle für die Historie
        MemyProtocolManager::add_protocol_backoffice($user_id, $nachricht, 'edit');
        
        // Update user meta
        update_user_meta($user_id, 'contact-person-'.$contact_id, $contact_data);
        
        //Senden
        wp_send_json_success(array(
            'message' => $nachricht,
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
        $wpuser       = false;
        $user_id      = get_current_user_id();
        $contact_id   = intval($_POST['contact_id']);
        $wp_user_id   = ($_POST['user_id'])?intval($_POST['user_id']) : 0;
        $contact_name = sanitize_text_field($_POST['contact_fname'] . ' ' . $_POST['contact_lname']);
        
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
        
        $nachricht = $contact_name . ' gelöscht.';

        //NK ist WP-Benutzer und gehört nur zu diesem Blog
        $wp_user             = get_userdata($wp_user_id);
        $blogs               = function_exists('get_blogs_of_user') ? get_blogs_of_user($wp_user_id) : [];
        $is_wp_user          = $wp_user instanceof WP_User;
        $is_single_blog_user = $is_wp_user && is_array($blogs) && count($blogs) === 1;

error_log("zulöschende Infos: " . $is_wp_user . " | " . $is_single_blog_user);
        
        if ($is_wp_user && $is_single_blog_user) {
            wpmu_delete_user($wp_user_id);
            $wpuser = true;
        }
        
        if ($is_wp_user && $is_single_blog_user > 1) {
            remove_user_from_blog($wp_user_id);
            $wpuser = true;
        }

        MemyProtocolManager::add_protocol_backoffice($user_id, $nachricht, 'edit');

        // Update user meta with empty data
        update_user_meta($user_id, 'contact-person-'.$contact_id, $empty_data);
        wp_send_json_success(array(
            'message' => $nachricht,
            'debug'   => [
                'contact_id'    => $contact_id,
                'user_id'       => $user_id,
                'contact_name'  => $contact_name,
                'is_wpuser'     => $wpuser
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

        $contact_mail  = sanitize_email($_POST['contact_mail'] ?? '');
        $contact_fname = sanitize_text_field($_POST['contact_fname'] ?? '');
        $contact_lname = sanitize_text_field($_POST['contact_lname'] ?? '');
        $contact_name  = $contact_fname . '_' . $contact_lname;

        if(empty($contact_mail) || !is_email($contact_mail)){
            wp_send_json_error('Ungültige E-Mail-Adresse.');
            return;
        }

        if(email_exists($contact_mail)){
            wp_send_json_success('Diese E-Mail-Adresse ist bereits registriert. Aber kein Ding.');
            //Todo sende Email-Benachrichtigung an diese Adresse
            return;
        }

        
        $username = $this->generiere_eindeutigen_username($contact_name);
        $password = wp_generate_password(12, false);
        $token    = wp_generate_password(32, false);

        $invite_data = array(
            'email'        => $contact_mail,
            'username'     => $username,
            'password'     => wp_hash_password($password),
            'inviter_id'   => $user_id,
            'first_name'   => $contact_fname,
            'last_name'    => $contact_lname,
            'created_at'   => current_time('mysql'),
        );

        update_option('memy_contact_invitation_' . $token, $invite_data);

        $sent = $this->send_contact_invitation_email($contact_mail, $contact_fname, $password, $token);
        if(!$sent){
            wp_send_json_error('Einladung konnte nicht versendet werden.');
            return;
        }

        wp_send_json_success(array('message' => 'Einladung erfolgreich gesendet.'));
    }

    /**
     *  Einladungs-Mail Helfer (Notfallkontakt)
     */
    private function send_contact_invitation_email($email, $name, $password, $token){
        $invitation_url = home_url('/?accept_invitation=' . rawurlencode($token));
        $inviter        = wp_get_current_user();
        $first_name     = get_user_meta($inviter->ID, 'first_name', true);
        $last_name      = get_user_meta($inviter->ID, 'last_name', true);
        $inviter_name   = trim($first_name . ' ' . $last_name) ?: ($inviter->display_name ?: $inviter->user_login);
        $c_email        = '';

        for ($i = 0; $i < strlen($email); $i++) {
            $c_email .= '&#' . ord($email[$i]) . ';';
        }

        $subject = 'Ihre Einladung zu Me, My Safe and I';
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $message =  emailParts('head') . "<p>Hallo " . esc_html($name ?: 'Helfer') . ",</p>";
        $message .= "<p>" . esc_html($inviter_name) . "  hat dich zu Me, My Safe and I eingeladen.</p>";
        $message .= "<p>Bitte bestätige deine Einladung, damit du im Ernstfall als Notfallkontakt aktiviert werden kannst.</p>";

        $message .= emailParts('button', esc_url($invitation_url), 'Einladung bestätigen');
        
        $message .= "<p>Alternativ kannst du diesen Link verwenden:<br>" . esc_html($invitation_url) . "</p>";

        $message .= "<p>Benutzername: <strong>" . $c_email . "</strong><br>";
        $message .= "Passwort: <strong>" . esc_html($password) . "</strong></p>";        
        
        $message .= "<p>Das jetzt vergebene Passwort hat Gültigkeit bis zu einem Notfall. Sollte deine Unterstützung tatsächlich erforderlich werden, erhältst du von MMSI automatisch ein neues temporäres Passwort zur Aktivierung des Helfermodus.</p>";
        $message .= "<p>Da es sich um eine persönliche Einladung handelt, empfehlen wir dir, vor dem Ignorieren der Nachricht kurz mit $inviter_name Rücksprache zu halten.</p>";
        $message .= emailParts('footer');
        $message .= "</body></html>";

        MemyProtocolManager::add_protocol_backoffice($inviter->ID, 'Einladung an ' .$name . ' gesendet.');

        return wp_mail($email, $subject, $message, $headers);
    }

    public function handle_accept_invitation(){
        if(!isset($_GET['accept_invitation'])){
            return;
        }

        $token          = sanitize_text_field($_GET['accept_invitation']);
        $option_key     = 'memy_contact_invitation_' . $token;
        $invite_data    = get_option($option_key);
        $inviter        = get_userdata($invite_data['inviter_id']);
        $inviter_name   = $inviter->first_name . ' ' . $inviter->last_name;
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

        // Ergänze Vorname und Nachname, falls vorhanden oder aus dem Namen extrahierbar
        $first_name = !empty($invite_data['first_name']) ? $invite_data['first_name'] : '';
        $last_name  = !empty($invite_data['last_name']) ? $invite_data['last_name'] : '';

        // Fallback: Falls die Felder fehlen, versuchen wir sie aus dem contact_name zu splitten
        if (empty($first_name) && empty($last_name) && !empty($invite_data['contact_name'])) {
            $name_parts = explode(' ', trim($invite_data['contact_name']), 2);
            $first_name = $name_parts[0];
            $last_name  = isset($name_parts[1]) ? $name_parts[1] : '';
        }

        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);

        // Einladungsoption löschen
        delete_option($option_key);

        // Benutzer einloggen
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);

        $headers = array('Content-Type: text/html; charset=UTF-8');
        $trueSubject = "Willkommen bei Me, My Safe and I";
        $trueMessage = emailParts('head');
        $trueMessage .= "<p>Hallo " . esc_html($first_name) . ",</p>";
        $trueMessage .= "<p>MMSI hilft Menschen dabei, wichtige Informationen und Abläufe für den Ernstfall vorzubereiten.<br>
            Als Notfallkontakt kannst du informiert werden, wenn ".$inviter_name." über einen längeren Zeitraum nicht erreichbar ist.<br>
            Aktuell besteht kein Handlungsbedarf.<br>
            Wir empfehlen dir dennoch, dich mit ".$inviter_name." darüber auszutauschen:<br>
            welche Rolle du im Ernstfall übernehmen sollst, welche Informationen wichtig sind und wie du erreichbar bist.";
        $trueMessage .= "</p>";
        $trueMessage .= emailParts('footer');
        $trueMessage .= "</body></html>";

        wp_mail($invite_data['email'], $trueSubject, $trueMessage, $headers);

        MemyProtocolManager::add_protocol_backoffice($invite_data['inviter_id'], $first_name . ' hat die Einladung angenommen.');

        // Weiterleitung zur Startseite oder Dashboard
        wp_redirect(get_site_url(1) . '/login/?login=success');

        exit;
    }

    /**
     * Gibt einen eindeutigen Benutzernamen zurück, falls der gewünschte bereits vergeben ist.
     *
     * @param string $gewuenschter_username Der gewünschte Benutzername (ohne Zahl).
     * @return string Ein eindeutiger Benutzername.
     */
    public function generiere_eindeutigen_username($gewuenschter_username) {
        $original = sanitize_user($gewuenschter_username);
        $username = $original;
        $suffix = 1;

        // Prüfe, ob der Benutzer bereits existiert (netzwerkweit)
        while (username_exists($username)) {
            $username = $original . $suffix;
            $suffix++;
        }

        return $username;
    }
}

new MemyContacts();