<?php
/**
 * Title: First Settings
 * Description: Speichert das user_meta first_settings via AJAX.
 * Author: Modulbüro
 */

if (!defined('ABSPATH')) {
    exit;
}

class MemyFirstSettings {
    const AJAX_ACTION = 'save_first_settings_meta';
    const AJAX_ACTION_SAFE_INFO = 'save_safe_info_txt';

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_' . self::AJAX_ACTION, array($this, 'ajax_save_first_settings_meta'));
        add_action('wp_ajax_' . self::AJAX_ACTION_SAFE_INFO, array($this, 'ajax_save_safe_info_txt'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            'memy-first-settings',
            get_stylesheet_directory_uri() . '/assets/js/first-settings.js',
            array('jquery'),
            '1.0',
            true
        );

        wp_localize_script('memy-first-settings', 'memyFirstSettingsAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('save_first_settings'),
        ));
    }

    public function ajax_save_first_settings_meta() {
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Sie müssen angemeldet sein.'), 403);
        }

        check_ajax_referer('save_first_settings', 'nonce');

        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Ungültiger Benutzer.'), 400);
        }

        // Speichere first_settings Flag
        update_user_meta($user_id, 'first_settings', 'done');

        // Speichere User Meta aus step-03.php
        if (isset($_POST['user_meta']) && is_array($_POST['user_meta'])) {
            $user_meta = array_map('sanitize_text_field', $_POST['user_meta']);
            
            if (!empty($user_meta['strasze'])) {
                update_user_meta($user_id, 'strasze', $user_meta['strasze']);
            }
            if (!empty($user_meta['plz'])) {
                update_user_meta($user_id, 'plz', $user_meta['plz']);
            }
            if (!empty($user_meta['ort'])) {
                update_user_meta($user_id, 'ort', $user_meta['ort']);
            }
            if (!empty($user_meta['telefon'])) {
                update_user_meta($user_id, 'telefon', $user_meta['telefon']);
            }
        }

        // Speichere Kontakt-Meta aus step-04.php
        if (isset($_POST['contact_meta']) && is_array($_POST['contact_meta'])) {
            $contact_meta = array_map('sanitize_text_field', $_POST['contact_meta']);
            
            $contact_data = array(
                'name'      => $contact_meta['name'] ?? '',
                'email'     => sanitize_email($contact_meta['email'] ?? ''),
                'tel'       => $contact_meta['tel'] ?? '',
                'typ'       => 'Notfallkontakt',
                'firma'     => '',
                'status'    => 'Ausstehend',
                'mmsi_safe' => '',
                'mmsi_can'  => '',
                'hauptkontakt' => ''
            );
            
            update_user_meta($user_id, 'contact-person-1', $contact_data);
        }

        wp_send_json_success(array(
            'message'     => 'first_settings updated',
            'dataUser'    => json_encode($$_POST['user_meta']),
            'dataContact' => json_encode($contact_data)
        ));
    }

    public function ajax_save_safe_info_txt() {
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Sie müssen angemeldet sein.'), 403);
        }

        check_ajax_referer('save_first_settings', 'nonce');

        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json_error(array('message' => 'Ungültiger Benutzer.'), 400);
        }

        // Sammle Safe-Info Daten
        if (!isset($_POST['safe_info_data']) || !is_array($_POST['safe_info_data'])) {
            wp_send_json_error(array('message' => 'Keine Daten vorhanden.'), 400);
        }

        $safe_info_data = $_POST['safe_info_data'];

        // Erstelle TXT-Inhalt mit Labels und Werten
        $txt_content = "SICHERHEITSINFORMATIONEN für NOTFALLKONTAKT\n";
        $txt_content .= "Erstellungsdatum: " . date('d.m.Y', current_time('timestamp')) . "\n";
        $txt_content .= "==========================================\n\n";

        foreach ($safe_info_data as $item) {
            $label = sanitize_text_field($item['label']);
            $value = sanitize_textarea_field($item['value']);
            
            $txt_content .= $label . ":\n";
            $txt_content .= $value . "\n\n";
        }

        // Bestimme Upload-Verzeichnis
        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];
        $safe_data_dir = $base_dir . '/safe-data/user-' . $user_id;

        // Erstelle Verzeichnis, falls nicht vorhanden
        if (!file_exists($safe_data_dir)) {
            wp_mkdir_p($safe_data_dir);
        }

        // Speichere TXT-Datei
        $file_name = 'notfallkontakt-informationen.txt';
        $file_path = $safe_data_dir . '/' . $file_name;

        $saved = file_put_contents($file_path, $txt_content);

        if ($saved === false) {
            wp_send_json_error(array('message' => 'Datei konnte nicht gespeichert werden.'), 500);
        }

        wp_send_json_success(array(
            'message'     => 'Sicherheitsinformationen erfolgreich gespeichert.',
            'file_name'   => $file_name,
            'file_path'   => $file_path
        ));
    }
}

new MemyFirstSettings();
