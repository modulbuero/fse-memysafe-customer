<?php
/**
 * Plugin Name: Erweiterte Registrierung mit Double-Opt-in
 * Description: Registrierungsformular mit zusätzlichen Feldern und Double-Opt-in Verfahren
 * Version: 1.0
 * Author: Ihr Name
 */

// Sicherheit: Plugin nur in WordPress-Umgebung laden
if (!defined('ABSPATH')) {
    exit;
}

class ErweiterteRegistrierung {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('erweiterte_registrierung', array($this, 'registrierung_shortcode'));
        add_action('wp_ajax_nopriv_erweiterte_registrierung', array($this, 'handle_registration'));
        add_action('wp_ajax_erweiterte_registrierung', array($this, 'handle_registration'));
        add_action('init', array($this, 'handle_email_verification'));
        register_activation_hook(__FILE__, array($this, 'create_tables'));
    }
    
    /**
     *  Benutzerrole und Rechte
     */
    public function init() {
        $wpRechte = [
            // Grundlegende Berechtigungen
            'read' => true,
            
            // Beiträge verwalten (nur eigene)
            'edit_posts' => true,
            'edit_published_posts' => true,
            'publish_posts' => true,
            'delete_posts' => true,
            'delete_published_posts' => true,
            
            // Medien hochladen
            'upload_files' => true,
            
            // Dashboard Zugriff
            'edit_dashboard' => true,
        ];

        // Neue Benutzerrollen erstellen
        if (!get_role('basic')) {
            add_role('basic', 'Basic', $wpRechte);
        }
        
        if (!get_role('premium')) {
            add_role('premium', 'Premium', $wpRechte);
        }
        
        if (!get_role('enterprise')) {
            add_role('enterprise', 'Enterprise', $wpRechte);
        }
    }
    
    /**
     *  Tabelle für "Wartende Benutzer"
     */
    public function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'pending_registrations';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(100) NOT NULL,
            username varchar(60) NOT NULL,
            password varchar(255) NOT NULL,
            branche varchar(100) NOT NULL,
            partner_email varchar(100) NOT NULL,
            auswahl_option varchar(50) NOT NULL,
            paketabo text NOT NULL,
            verification_token varchar(255) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     *  Ajax Registrieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_localize_script('jquery', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('erweiterte_registrierung_nonce')
        ));
    }
    
    /**
     *  Der Shortcode: [erweiterte_registrierung]
     */
    public function registrierung_shortcode() {
        ob_start();
        ?>
        <div id="erweiterte-registrierung-container">
            <form id="erweiterte-registrierung-form" method="post">
                <?php wp_nonce_field('erweiterte_registrierung_nonce', 'erweiterte_registrierung_nonce'); ?>
                
                <div class="form-group">
                    <label for="username">Benutzername *</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">E-Mail-Adresse *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Passwort *</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Passwort bestätigen *</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                
                <div class="form-group">
                    <label for="branche">Branche *</label>
                    <input type="text" id="branche" name="branche" required>
                </div>
                
                <div class="form-group">
                    <label for="partner_email">Partner E-Mail</label>
                    <input type="email" id="partner_email" name="partner_email">
                </div>
                
                <div class="form-group selectbox">
                    <label for="auswahl_option">Auswahl Option *</label>
                    <select id="auswahl_option" name="auswahl_option" required>
                        <option value="">Bitte wählen...</option>
                        <option value="option1">Option 1</option>
                        <option value="option2">Option 2</option>
                        <option value="option3">Option 3</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Paketabo *</label>
                    <div class="checkbox-group">
                        <label>
                            <input type="radio" name="paketabo" value="basic" required> Basis
                        </label>
                        <label>
                            <input type="radio" name="paketabo" value="premium" required> Premium
                        </label>
                        <label>
                            <input type="radio" name="paketabo" value="enterprise" required> Enterprise
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="datenschutz" required>
                        Ich stimme den Datenschutzbestimmungen zu *
                    </label>
                </div>
                
                <button type="submit">Registrieren</button>
            </form>
            
            <div id="registration-message" style="display: none;"></div>
        </div>
        
        <style>
        #erweiterte-registrierung-container {
            max-width: 600px;
            margin: 20px 0;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .checkbox-group label {
            font-weight: normal;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        button[type="submit"] {
            background-color: #0073aa;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        button[type="submit"]:hover {
            background-color: #005a87;
        }
        
        .success-message {
            color: green;
            padding: 10px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }
        
        .error-message {
            color: red;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('#erweiterte-registrierung-form').on('submit', function(e) {
                e.preventDefault();
                
                // Passwort-Validierung
                var password = $('#password').val();
                var passwordConfirm = $('#password_confirm').val();
                
                if (password !== passwordConfirm) {
                    $('#registration-message')
                        .removeClass('success-message')
                        .addClass('error-message')
                        .html('Die Passwörter stimmen nicht überein.')
                        .show();
                    return;
                }
                
                // Formular-Daten sammeln
                var formData = $(this).serialize();
                formData += '&action=erweiterte_registrierung';
                
                // AJAX-Request
                $.ajax({
                    url: ajax_object.ajax_url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#erweiterte-registrierung-form').hide();
                            $('#registration-message')
                                .removeClass('error-message')
                                .addClass('success-message')
                                .html(response.data.message)
                                .show();
                        } else {
                            $('#registration-message')
                                .removeClass('success-message')
                                .addClass('error-message')
                                .html(response.data.message)
                                .show();
                        }
                    },
                    error: function() {
                        $('#registration-message')
                            .removeClass('success-message')
                            .addClass('error-message')
                            .html('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.')
                            .show();
                    }
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     *  Validierung der eingegebenen Registrierungsdaten
     */
    public function handle_registration() {
        // Nonce-Überprüfung
        if (!wp_verify_nonce($_POST['erweiterte_registrierung_nonce'], 'erweiterte_registrierung_nonce')) {
            wp_die('Sicherheitsfehler');
        }
        
        // Eingaben validieren
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $branche = sanitize_text_field($_POST['branche']);
        $partner_email = sanitize_email($_POST['partner_email']);
        $auswahl_option = sanitize_text_field($_POST['auswahl_option']);
        $paketabo = sanitize_text_field($_POST['paketabo']);
        
        // Validierung
        if (empty($username) || empty($email) || empty($password) || empty($branche) || empty($auswahl_option) || empty($paketabo)) {
            wp_send_json_error(array('message' => 'Bitte füllen Sie alle Pflichtfelder aus.'));
        }
        
        if (username_exists($username)) {
            wp_send_json_error(array('message' => 'Dieser Benutzername ist bereits vergeben.'));
        }
        
        if (email_exists($email)) {
            wp_send_json_error(array('message' => 'Diese E-Mail-Adresse ist bereits registriert.'));
        }
        
        // Verification Token generieren
        $verification_token = wp_generate_password(32, false);
        
        // Daten in temporärer Tabelle speichern
        global $wpdb;
        $table_name = $wpdb->prefix . 'pending_registrations';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'email' => $email,
                'username' => $username,
                'password' => wp_hash_password($password),
                'branche' => $branche,
                'partner_email' => $partner_email,
                'auswahl_option' => $auswahl_option,
                'paketabo' => $paketabo,
                'verification_token' => $verification_token
            )
        );
        
        if ($result === false) {
            wp_send_json_error(array('message' => 'Fehler beim Speichern der Registrierungsdaten.'));
        }
        
        // Bestätigungs-E-Mail senden
        $this->send_verification_email($email, $username, $verification_token);
        
        wp_send_json_success(array(
            'message' => 'Vielen Dank für Ihre Registrierung! Bitte überprüfen Sie Ihr E-Mail-Postfach und klicken Sie auf den Bestätigungslink.'
        ));
    }
    
    /**
     *  DOI-Email Inhalt und versand
     */
    private function send_verification_email($email, $username, $token) {
        $verification_url = home_url('/?verify_email=' . $token);
        
        $subject = 'Bestätigen Sie Ihre Registrierung';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        $message = "
        <html>
        <body>
            <h2>Willkommen, {$username}!</h2>
            <p>Vielen Dank für Ihre Registrierung. Um Ihr Konto zu aktivieren, klicken Sie bitte auf den folgenden Link:</p>
            <p><a href='{$verification_url}'>Registrierung bestätigen</a></p>
            <p>Oder kopieren Sie diesen Link in Ihren Browser:</p>
            <p>{$verification_url}</p>
            <p>Dieser Link ist 24 Stunden gültig.</p>
            <br>
            <p>Falls Sie sich nicht registriert haben, ignorieren Sie diese E-Mail.</p>
        </body>
        </html>
        ";
        
        wp_mail($email, $subject, $message, $headers);
    }
    
    /**
     *  Benutzer final bestätigen
     */
    public function handle_email_verification() {
        if (isset($_GET['verify_email'])) {
            $token = sanitize_text_field($_GET['verify_email']);
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'pending_registrations';
            
            // Token in Datenbank suchen
            $pending_user = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE verification_token = %s AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)",
                $token
            ));
            
            if (!$pending_user) {
                wp_die('Ungültiger oder abgelaufener Bestätigungslink.');
            }
            
            // Benutzer erstellen
            $user_id = wp_create_user(
                $pending_user->username,
                wp_generate_password(), // Temporäres Passwort, wird gleich überschrieben
                $pending_user->email
            );
            
            if (is_wp_error($user_id)) {
                wp_die('Fehler beim Erstellen des Benutzerkontos: ' . $user_id->get_error_message());
            }
            
            // Gespeichertes Passwort setzen
            $wpdb->update(
                $wpdb->users,
                array('user_pass' => $pending_user->password),
                array('ID' => $user_id)
            );
            
            // Benutzerrolle basierend auf Paketabo setzen
            $user = new WP_User($user_id);
            $role = $this->get_role_from_paketabo($pending_user->paketabo);
            $user->set_role($role);
            
            // Meta-Daten speichern
            update_user_meta($user_id, 'branche', $pending_user->branche);
            update_user_meta($user_id, 'partner_email', $pending_user->partner_email);
            update_user_meta($user_id, 'auswahl_option', $pending_user->auswahl_option);
            update_user_meta($user_id, 'paketabo', $pending_user->paketabo);
            
            // Temporären Eintrag löschen
            $wpdb->delete($table_name, array('id' => $pending_user->id));
            
            // Erfolg anzeigen
            wp_die('
                <h1>Registrierung erfolgreich!</h1>
                <p>Ihr Konto wurde erfolgreich aktiviert. Sie können sich jetzt <a href="' . wp_login_url() . '">anmelden</a>.</p>
            ');
        }
    }
    
    /**
     *  Hilfsfunktion: Rolle = Paketabo
     */
    private function get_role_from_paketabo($paketabo) {
        switch ($paketabo) {
            case 'basic':
                return 'basic';
            case 'premium':
                return 'premium';
            case 'enterprise':
                return 'enterprise';
            default:
                return 'basic';
        }
    }
}

/**
 *  Plugin initialisieren
 */ 
new ErweiterteRegistrierung();

/**
 *  Funktion zum Anzeigen der Benutzerdaten im Admin-Bereich
 */ 
add_action('show_user_profile', 'show_extra_profile_fields');
add_action('edit_user_profile', 'show_extra_profile_fields');

function show_extra_profile_fields($user) {
    ?>
    <h3>Zusätzliche Informationen</h3>
    <table class="form-table">
        <tr>
            <th><label for="branche">Branche</label></th>
            <td>
                <input type="text" name="branche" id="branche" value="<?php echo esc_attr(get_user_meta($user->ID, 'branche', true)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="partner_email">Partner E-Mail</label></th>
            <td>
                <input type="email" name="partner_email" id="partner_email" value="<?php echo esc_attr(get_user_meta($user->ID, 'partner_email', true)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="auswahl_option">Auswahl Option</label></th>
            <td>
                <input type="text" name="auswahl_option" id="auswahl_option" value="<?php echo esc_attr(get_user_meta($user->ID, 'auswahl_option', true)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="paketabo">Paketabo</label></th>
            <td>
                <input type="text" name="paketabo" id="paketabo" value="<?php echo esc_attr(get_user_meta($user->ID, 'paketabo', true)); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}

/**
 *  Speichern der zusätzlichen Felder
 */ 
add_action('personal_options_update', 'save_extra_profile_fields');
add_action('edit_user_profile_update', 'save_extra_profile_fields');

function save_extra_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    update_user_meta($user_id, 'branche', sanitize_text_field($_POST['branche']));
    update_user_meta($user_id, 'partner_email', sanitize_email($_POST['partner_email']));
    update_user_meta($user_id, 'auswahl_option', sanitize_text_field($_POST['auswahl_option']));
    update_user_meta($user_id, 'paketabo', sanitize_text_field($_POST['paketabo']));
}