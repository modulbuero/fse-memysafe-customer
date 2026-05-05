<?php
/**
 * WordPress Multisite Login Shortcode
 * Fügen Sie diesen Code in die functions.php Ihres Child-Themes ein
 * WICHTIG: Nach dem Einfügen Cache leeren und Seite neu laden!
 */

// Sicherstellen, dass der Code nur einmal ausgeführt wird
if (!function_exists('multisite_login_form')) {
    
    // Shortcode bei WordPress Init registrieren
    add_action('init', 'register_multisite_login_shortcode');
    
    function register_multisite_login_shortcode() {
        add_shortcode('multisite_login', 'multisite_login_form');
    }

    function multisite_login_form($atts) {
        // Debug: Prüfen ob Shortcode aufgerufen wird
        error_log('Multisite Login Shortcode wird ausgeführt');
        
        // Shortcode Attribute mit Defaults
        $atts = shortcode_atts(array(
            'redirect_delay' => 2,
            'show_register' => 'true',
            'form_title' => 'Anmelden'
        ), $atts);
        
        // Wenn bereits eingeloggt, zeige Weiterleitung
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $username = $current_user->user_login;
            $subfolder_url = home_url('/' . $username . '/');
            
            ob_start();
            ?>
            <div class="multisite-login-wrapper">
                <div class="login-success">
                    <h3>Willkommen zurück, <?php echo esc_html($username); ?>!</h3>
                    <p>Sie werden automatisch zu Ihrem Bereich weitergeleitet...</p>
                    <p><a href="<?php echo esc_url($subfolder_url); ?>">Klicken Sie hier, falls die Weiterleitung nicht funktioniert</a></p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = "<?php echo esc_url($subfolder_url); ?>";
                    }, <?php echo intval($atts['redirect_delay']) * 1000; ?>);
                </script>
            </div>
            <?php
            return ob_get_clean();
        }
        
        // Login-Formular für nicht eingeloggte Benutzer
        ob_start();
        ?>
        <div class="multisite-login-wrapper">
            <div class="multisite-login-form">
                <h3><?php echo esc_html($atts['form_title']); ?></h3>
                
                <div id="login-message"></div>
                
                <form id="multisite-login-form" method="post">
                    <input type="hidden" name="action" value="multisite_login_ajax">
                    <?php wp_nonce_field('multisite_login_nonce', 'multisite_login_nonce'); ?>
                    
                    <div class="form-group">
                        <label for="ms_username">Benutzername:</label>
                        <input type="text" id="ms_username" name="ms_username" required>
                        <small>Ihr Benutzername entspricht Ihrem Subfolder-Namen</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="ms_password">Passwort:</label>
                        <input type="password" id="ms_password" name="ms_password" required>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="ms_remember" value="1"> 
                            Angemeldet bleiben
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="login-button">Anmelden</button>
                    </div>
                </form>
                
                <?php if ($atts['show_register'] === 'true'): ?>
                <div class="login-links">
                    <p>
                        <a href="<?php echo wp_registration_url(); ?>">Neuen Account erstellen</a> | 
                        <a href="<?php echo wp_lostpassword_url(); ?>">Passwort vergessen?</a>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- CSS Styles -->
        <style>
        .multisite-login-wrapper {
            max-width: 450px;
            margin: 30px auto;
            padding: 30px;
            border: 1px solid #e1e1e1;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        .multisite-login-form h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-size: 24px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }
        
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e1e1;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            outline: none;
            border-color: #0073aa;
        }
        
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }
        
        .checkbox-group label {
            display: flex;
            align-items: center;
            font-weight: normal;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 8px;
        }
        
        .login-button {
            width: 100%;
            padding: 15px;
            background-color: #0073aa;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        
        .login-button:hover {
            background-color: #005a87;
        }
        
        .login-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        .login-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e1e1e1;
        }
        
        .login-links a {
            color: #0073aa;
            text-decoration: none;
            font-size: 14px;
        }
        
        .login-links a:hover {
            text-decoration: underline;
        }
        
        .login-success {
            text-align: center;
            padding: 20px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            color: #155724;
            margin-bottom: 15px;
        }
        
        .login-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .login-loading {
            background-color: #cce7ff;
            border: 1px solid #99d6ff;
            color: #0066cc;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
        </style>
        
        <!-- JavaScript -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('multisite-login-form');
            const messageDiv = document.getElementById('login-message');
            const submitButton = form.querySelector('.login-button');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Button deaktivieren und Loading-Status anzeigen
                    submitButton.disabled = true;
                    submitButton.textContent = 'Wird angemeldet...';
                    messageDiv.innerHTML = '<div class="login-loading">Anmeldung wird verarbeitet...</div>';
                    
                    // FormData erstellen
                    const formData = new FormData(form);
                    
                    // AJAX Request
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            messageDiv.innerHTML = '<div class="login-success">' + data.data.message + '</div>';
                            
                            // Weiterleitung nach Verzögerung
                            setTimeout(function() {
                                window.location.href = data.data.redirect_url;
                            }, <?php echo intval($atts['redirect_delay']) * 1000; ?>);
                        } else {
                            messageDiv.innerHTML = '<div class="login-error">' + data.data + '</div>';
                            submitButton.disabled = false;
                            submitButton.textContent = 'Anmelden';
                        }
                    })
                    .catch(error => {
                        console.error('Login Error:', error);
                        messageDiv.innerHTML = '<div class="login-error">Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.</div>';
                        submitButton.disabled = false;
                        submitButton.textContent = 'Anmelden';
                    });
                });
            }
        });
        </script>
        
        <?php
        return ob_get_clean();
    }

    // AJAX Handler für Login
    add_action('wp_ajax_nopriv_multisite_login_ajax', 'handle_multisite_login_ajax');
    add_action('wp_ajax_multisite_login_ajax', 'handle_multisite_login_ajax');

    function handle_multisite_login_ajax() {
        // Nonce prüfen
        if (!wp_verify_nonce($_POST['multisite_login_nonce'], 'multisite_login_nonce')) {
            wp_send_json_error('Sicherheitsprüfung fehlgeschlagen. Bitte laden Sie die Seite neu.');
        }
        
        $username = sanitize_user($_POST['ms_username']);
        $password = $_POST['ms_password'];
        $remember = isset($_POST['ms_remember']) ? true : false;
        
        // Grundlegende Validierung
        if (empty($username) || empty($password)) {
            wp_send_json_error('Bitte füllen Sie alle Felder aus.');
        }
        
        // Prüfen ob Subfolder existiert (optional - kann entfernt werden wenn nicht benötigt)
        $subfolder_path = ABSPATH . $username . '/';
        if (!is_dir($subfolder_path)) {
            error_log('Subfolder nicht gefunden: ' . $subfolder_path);
            // Kommentieren Sie die nächste Zeile aus, wenn Sie keine Subfolder-Prüfung möchten
            // wp_send_json_error('Benutzer-Verzeichnis nicht gefunden. Bitte kontaktieren Sie den Administrator.');
        }
        
        // Login-Versuch
        $credentials = array(
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember,
        );
        
        $user = wp_signon($credentials, false);
        
        if (is_wp_error($user)) {
            $error_message = $user->get_error_message();
            error_log('Login-Fehler: ' . $error_message);
            wp_send_json_error('Ungültige Anmeldedaten. Bitte prüfen Sie Benutzername und Passwort.');
        } else {
            // Erfolgreiche Anmeldung
            $redirect_url = home_url('/' . $username . '/');
            
            wp_send_json_success(array(
                'message' => 'Anmeldung erfolgreich! Sie werden zu Ihrem Bereich weitergeleitet...',
                'redirect_url' => $redirect_url,
                'username' => $username
            ));
        }
    }

    // Debug-Funktion: Zeigt registrierte Shortcodes an (nur für Admins)
    add_action('wp_footer', 'debug_multisite_shortcode');
    function debug_multisite_shortcode() {
        if (current_user_can('administrator') && isset($_GET['debug_shortcodes'])) {
            global $shortcode_tags;
            echo '<div style="position:fixed; bottom:0; left:0; background:white; padding:10px; border:1px solid black; z-index:9999;">';
            echo '<strong>Registrierte Shortcodes:</strong><br>';
            if (isset($shortcode_tags['multisite_login'])) {
                echo '✅ multisite_login ist registriert<br>';
            } else {
                echo '❌ multisite_login ist NICHT registriert<br>';
            }
            echo '</div>';
        }
    }
}
?>