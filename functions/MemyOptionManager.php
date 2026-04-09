<?php
/**
 * Title: Option Manager
 * Description: Speichert Theme/Plugin Optionen via AJAX (get_option / update_option).
 * Author: Modulbüro
 */

if (!defined('ABSPATH')) {
    exit;
}

class MemyOptionManager {
    const AJAX_ACTION = 'memy_save_option';

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_' . self::AJAX_ACTION, array($this, 'ajax_save_option'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            'memy-option-manager',
            get_stylesheet_directory_uri() . '/assets/js/memy-option-manager.js',
            array('jquery', 'wp-util'),
            '1.0',
            true
        );

        wp_localize_script('memy-option-manager', 'memyOption', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('memy-option-nonce'),
        ));
    }

    public function ajax_save_option() {
        if (!wp_verify_nonce($_REQUEST['nonce'] ?? '', 'memy-option-nonce')) {
            wp_send_json_error(array('message' => 'Sicherheitsüberprüfung fehlgeschlagen.'));
        }

        // Wenn nur Administratoren Einstellungen ändern dürfen, hier statt is_user_logged_in() current_user_can('manage_options') verwenden.
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Sie müssen angemeldet sein.'));
        }

        $key = sanitize_key($_REQUEST['key'] ?? '');
        if (empty($key)) {
            wp_send_json_error(array('message' => 'Ungültiger Schlüssel.'));
        }

        $value = isset($_REQUEST['value']) ? sanitize_text_field($_REQUEST['value']) : '';

        update_option($key, $value);

        //Message-Ausgabe der einzelnen Keys
        $message = sprintf('Option "%s" wurde erfolgreich gespeichert.', $key);
        
        if($key == 'examclock-reset'){
            if($value == 'login-reset'){
                $message = 'Reset der Uhr bei Login.';
            }else{
                $message = 'Reset der Uhr mit Button klick im Dashboard.';
            }
        }

        // Rückgabe der Antworten
        wp_send_json_success(array(
            'message' => $message,
            'key'     => $key,
            'value'   => $value,
        ));
    }

    public static function get($key, $default = '') {
        return get_option($key, $default);
    }
}

new MemyOptionManager();
