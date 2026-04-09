<?php
/**
 * Plugin Name: Erweiterte Profil-Verwaltung
 * Description: Eigene Profilseite im Backend für erweiterte Benutzerdaten
 * Version: 1.0
 * Author: Ihr Name
 */

// Sicherheit: Plugin nur in WordPress-Umgebung laden
if (!defined('ABSPATH')) {
    exit;
}

class ErweiterteProfilVerwaltung {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_profile_menu'));
        add_action('wp_ajax_update_user_profile', array($this, 'handle_profile_update'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function add_profile_menu() {
        add_menu_page(
            'Mein Profil',
            'Mein Profil',
            'read',
            'mein-profil',
            array($this, 'display_profile_page'),
            'dashicons-admin-users',
            30
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_mein-profil') {
            return;
        }
        
        wp_enqueue_script('jquery');
        wp_localize_script('jquery', 'profile_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('update_profile_nonce')
        ));
    }
    
    public function display_profile_page() {
        $current_user = wp_get_current_user();
        $branche = get_user_meta($current_user->ID, 'branche', true);
        $partner_email = get_user_meta($current_user->ID, 'partner_email', true);
        $auswahl_option = get_user_meta($current_user->ID, 'auswahl_option', true);
        $paketabo = get_user_meta($current_user->ID, 'paketabo', true);
        
        // Rolle für Anzeige formatieren
        $user_roles = $current_user->roles;
        $display_role = '';
        if (in_array('einfach', $user_roles)) {
            $display_role = 'Einfach (Basis-Paket)';
        } elseif (in_array('premium', $user_roles)) {
            $display_role = 'Premium';
        } elseif (in_array('enterprise', $user_roles)) {
            $display_role = 'Enterprise';
        } else {
            $display_role = ucfirst($user_roles[0] ?? 'Unbekannt');
        }
        ?>
        <div class="wrap">
            <h1>Mein Profil</h1>
            
            <div id="profile-message" style="display: none;"></div>
            
            <form id="profile-form" method="post">
                <?php wp_nonce_field('update_profile_nonce', 'update_profile_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="username">Benutzername</label>
                        </th>
                        <td>
                            <input type="text" id="username" value="<?php echo esc_attr($current_user->user_login); ?>" class="regular-text" disabled>
                            <p class="description">Der Benutzername kann nicht geändert werden.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="email">E-Mail-Adresse *</label>
                        </th>
                        <td>
                            <input type="email" id="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="first_name">Vorname</label>
                        </th>
                        <td>
                            <input type="text" id="first_name" name="first_name" value="<?php echo esc_attr($current_user->first_name); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="last_name">Nachname</label>
                        </th>
                        <td>
                            <input type="text" id="last_name" name="last_name" value="<?php echo esc_attr($current_user->last_name); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="branche">Branche *</label>
                        </th>
                        <td>
                            <input type="text" id="branche" name="branche" value="<?php echo esc_attr($branche); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="partner_email">Partner E-Mail</label>
                        </th>
                        <td>
                            <input type="email" id="partner_email" name="partner_email" value="<?php echo esc_attr($partner_email); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="auswahl_option">Auswahl Option *</label>
                        </th>
                        <td>
                            <div class="selectbox">
                            <select id="auswahl_option" name="auswahl_option" class="regular-text" required>
                                <option value="">Bitte wählen...</option>
                                <option value="option1" <?php selected($auswahl_option, 'option1'); ?>>Option 1</option>
                                <option value="option2" <?php selected($auswahl_option, 'option2'); ?>>Option 2</option>
                                <option value="option3" <?php selected($auswahl_option, 'option3'); ?>>Option 3</option>
                            </select>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label>Aktuelles Paket</label>
                        </th>
                        <td>
                            <strong><?php echo esc_html($display_role); ?></strong>
                            <p class="description">Für eine Paket-Änderung wenden Sie sich bitte an den Administrator.</p>
                        </td>
                    </tr>
                </table>
                
                <h2>Passwort ändern</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="current_password">Aktuelles Passwort</label>
                        </th>
                        <td>
                            <input type="password" id="current_password" name="current_password" class="regular-text">
                            <p class="description">Nur ausfüllen, wenn Sie Ihr Passwort ändern möchten.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="new_password">Neues Passwort</label>
                        </th>
                        <td>
                            <input type="password" id="new_password" name="new_password" class="regular-text">
                            <div id="password-strength" class="password-strength"></div>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="confirm_password">Neues Passwort bestätigen</label>
                        </th>
                        <td>
                            <input type="password" id="confirm_password" name="confirm_password" class="regular-text">
                        </td>
                    </tr>
                </table>
                
                <?php submit_button('Profil aktualisieren'); ?>
            </form>
        </div>
        
        <style>
        .wrap {
            max-width: 800px;
        }
        
        .success-message {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 12px 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        .form-table th {
            width: 200px;
        }
        
        input[disabled] {
            background-color: #f1f1f1;
            color: #666;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        
        .password-weak {
            color: #dc3545;
        }
        
        .password-medium {
            color: #ffc107;
        }
        
        .password-strong {
            color: #28a745;
        }
        
        h2 {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Passwort-Stärke-Anzeige
            $('#new_password').on('input', function() {
                var password = $(this).val();
                var strength = checkPasswordStrength(password);
                var strengthDiv = $('#password-strength');
                
                strengthDiv.removeClass('password-weak password-medium password-strong');
                
                if (password.length === 0) {
                    strengthDiv.text('');
                } else if (strength < 3) {
                    strengthDiv.addClass('password-weak').text('Schwach');
                } else if (strength < 5) {
                    strengthDiv.addClass('password-medium').text('Mittel');
                } else {
                    strengthDiv.addClass('password-strong').text('Stark');
                }
            });
            
            function checkPasswordStrength(password) {
                var strength = 0;
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[^a-zA-Z0-9]/)) strength++;
                return strength;
            }
            
            $('#profile-form').on('submit', function(e) {
                e.preventDefault();
                
                // Passwort-Validierung falls Passwort geändert werden soll
                var currentPassword = $('#current_password').val();
                var newPassword = $('#new_password').val();
                var confirmPassword = $('#confirm_password').val();
                
                if (currentPassword || newPassword || confirmPassword) {
                    if (!currentPassword) {
                        showMessage('Bitte geben Sie Ihr aktuelles Passwort ein.', 'error');
                        return;
                    }
                    
                    if (!newPassword) {
                        showMessage('Bitte geben Sie ein neues Passwort ein.', 'error');
                        return;
                    }
                    
                    if (newPassword !== confirmPassword) {
                        showMessage('Die neuen Passwörter stimmen nicht überein.', 'error');
                        return;
                    }
                    
                    if (newPassword.length < 6) {
                        showMessage('Das neue Passwort muss mindestens 6 Zeichen lang sein.', 'error');
                        return;
                    }
                }
                
                // Submit-Button deaktivieren
                $('#submit').prop('disabled', true).val('Wird gespeichert...');
                
                // Formular-Daten sammeln
                var formData = $(this).serialize();
                formData += '&action=update_user_profile';
                
                // AJAX-Request
                $.ajax({
                    url: profile_ajax.ajax_url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.data.message, 'success');
                            // Passwort-Felder leeren
                            $('#current_password, #new_password, #confirm_password').val('');
                            $('#password-strength').text('');
                        } else {
                            showMessage(response.data.message, 'error');
                        }
                    },
                    error: function() {
                        showMessage('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.', 'error');
                    },
                    complete: function() {
                        // Submit-Button wieder aktivieren
                        $('#submit').prop('disabled', false).val('Profil aktualisieren');
                    }
                });
            });
            
            function showMessage(message, type) {
                $('#profile-message')
                    .removeClass('success-message error-message')
                    .addClass(type + '-message')
                    .html(message)
                    .show();
                
                // Nach oben scrollen
                $('html, body').animate({
                    scrollTop: $('#profile-message').offset().top - 50
                }, 500);
            }
        });
        </script>
        <?php
    }
    
    public function handle_profile_update() {
        // Nonce-Überprüfung
        if (!wp_verify_nonce($_POST['update_profile_nonce'], 'update_profile_nonce')) {
            wp_send_json_error(array('message' => 'Sicherheitsfehler'));
        }
        
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        
        // Eingaben validieren
        $email = sanitize_email($_POST['email']);
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $branche = sanitize_text_field($_POST['branche']);
        $partner_email = sanitize_email($_POST['partner_email']);
        $auswahl_option = sanitize_text_field($_POST['auswahl_option']);
        
        // Pflichtfelder prüfen
        if (empty($email) || empty($branche) || empty($auswahl_option)) {
            wp_send_json_error(array('message' => 'Bitte füllen Sie alle Pflichtfelder (*) aus.'));
        }
        
        // E-Mail-Adresse prüfen (falls geändert)
        if ($email !== $current_user->user_email && email_exists($email)) {
            wp_send_json_error(array('message' => 'Diese E-Mail-Adresse wird bereits verwendet.'));
        }
        
        // Passwort-Änderung prüfen
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        
        if (!empty($current_password) || !empty($new_password)) {
            // Aktuelles Passwort prüfen
            if (!wp_check_password($current_password, $current_user->user_pass, $user_id)) {
                wp_send_json_error(array('message' => 'Das aktuelle Passwort ist nicht korrekt.'));
            }
            
            if (strlen($new_password) < 6) {
                wp_send_json_error(array('message' => 'Das neue Passwort muss mindestens 6 Zeichen lang sein.'));
            }
            
            // Neues Passwort setzen
            wp_set_password($new_password, $user_id);
        }
        
        // Benutzerdaten aktualisieren
        $user_data = array(
            'ID' => $user_id,
            'user_email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name
        );
        
        $result = wp_update_user($user_data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => 'Fehler beim Aktualisieren der Benutzerdaten: ' . $result->get_error_message()));
        }
        
        // Meta-Daten aktualisieren
        update_user_meta($user_id, 'branche', $branche);
        update_user_meta($user_id, 'partner_email', $partner_email);
        update_user_meta($user_id, 'auswahl_option', $auswahl_option);
        
        // Erfolgs-Nachricht
        $message = 'Ihr Profil wurde erfolgreich aktualisiert.';
        if (!empty($new_password)) {
            $message .= ' Ihr Passwort wurde ebenfalls geändert.';
        }
        
        wp_send_json_success(array('message' => $message));
    }
}

// Plugin initialisieren
new ErweiterteProfilVerwaltung();
?>