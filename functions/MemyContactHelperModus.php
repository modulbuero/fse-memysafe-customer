<?php 
/**
 * Notfallkontakt Helfermodus
 * Gibt die Möglichkeit zur Verarbeitung des Safes bei Notfall
 */
if ( ! class_exists( 'MemyContactHelperModus' ) ) {
    class MemyContactHelperModus {
        /**
         * Constructor mit inits
         */
        public function __construct() {
            add_action('init', array($this, 'register_rewrite_rules'), 10);
            add_filter('query_vars', array($this, 'register_query_vars'));
            add_filter('template_include', array($this, 'template_routing'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('wp_ajax_finish_helper_mode', array($this, 'finish_helper_mode'));
        }

        /**
         * Scripts für helpermodus laden
         */
        public function enqueue_scripts(){
            
                // jQuery laden
                wp_enqueue_script('jquery');

                // // Safe-Upload Script laden
                // wp_enqueue_script(
                //     'memy-safe-upload',
                //     get_stylesheet_directory_uri() . '/assets/js/safe-upload.js',
                //     array('jquery', 'wp-util'),
                //     wp_get_theme()->get('Version'),
                //     true
                // );

                // // AJAX-Daten für Safe-Upload bereitstellen
                // wp_localize_script(
                //     'memy-safe-upload',
                //     'memySafeUpload',
                //     array(
                //         'ajaxurl'    => admin_url('admin-ajax.php'),
                //         'uploadNonce' => wp_create_nonce('memy_upload_file'),
                //         'deleteNonce' => wp_create_nonce('memy_delete_file'),
                //         'filesNonce'  => wp_create_nonce('memy_get_files'),
                //         'downloadNonce' => wp_create_nonce('memy_download_file'),
                //     )
                // );

                // Helpermodus-spezifisches Script
                wp_enqueue_script(
                    'memy-helpermodus',
                    get_stylesheet_directory_uri() . '/assets/js/helpermodus.js',
                    array('jquery'),
                    wp_get_theme()->get('Version'),
                    true
                );

                // AJAX-Daten für Helpermodus bereitstellen
                wp_localize_script(
                    'memy-helpermodus',
                    'memy_ajax_object',
                    array(
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'nonce'    => wp_create_nonce('finish_helper_mode_nonce'),
                    )
                );

                // Zusätzlich ajax_object_deathman für den exam-clock Handler hinzufügen
                wp_localize_script(
                    'memy-helpermodus',
                    'ajax_object_deathman',
                    array(
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'nonce'    => wp_create_nonce('exam_clock_manager_nonce'),
                    )
                );
            
        }

        /**
         * Helfermodus beenden und Eskalations-Optionen zurücksetzen
         */
        public function finish_helper_mode() {
            // Nonce-Überprüfung für Sicherheit
            if (!wp_verify_nonce($_POST['nonce'], 'finish_helper_mode_nonce')) {
                wp_die('Sicherheitsfehler: Ungültiger Nonce');
            }

            // Optionen zurücksetzen
            delete_option('has_send_reminder_one');
            delete_option('has_send_reminder_two');
            delete_option('has_send_reminder_three');
            delete_option('has_send_notfall');

            // Erfolgsantwort zurückgeben
            wp_send_json_success('Helfer-Modus erfolgreich beendet und Eskalations-Optionen zurückgesetzt.');
        }

        /**
         * Rewrite Rule registrieren
         */
        public function register_rewrite_rules(){
            add_rewrite_rule(
                '^helpermodus/?$',
                'index.php?notfallaktiv=helpermodus',
                'top'
            );
        }

        /**
         * Query Variable registrieren
         */
        public function register_query_vars($vars) {
            $vars[] = 'notfallaktiv';
            return $vars;
        }

        /**
         * Template Routing mit Sicherheitsprüfung
         */
        public function template_routing($template) {
            $notfallaktiv = get_query_var('notfallaktiv');

            if($notfallaktiv){
                // Erlaubte Seiten (Whitelist)
                $allowed_pages = array('helpermodus');

                // Sicherheitsprüfung: nur erlaubte Seiten laden
                if(in_array($notfallaktiv, $allowed_pages)){
                    // Sichere Dateipfad-Konstruktion
                    $file = get_theme_file_path('templates-helper/' . sanitize_file_name($notfallaktiv) . '.php');

                    // Zusätzliche Sicherheitsprüfung: Datei muss im erwarteten Verzeichnis sein
                    $base_dir = realpath(get_theme_file_path('templates-helper/'));
                    $file_real = realpath($file);

                    if($file_real && $base_dir && strpos($file_real, $base_dir) === 0 && file_exists($file_real)){
                        return $file_real;
                    }
                }
            }

            return $template;
        }
    }

    // Instanziierung
    new MemyContactHelperModus();
}