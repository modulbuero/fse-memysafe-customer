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

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_' . self::AJAX_ACTION, array($this, 'ajax_save_first_settings_meta'));
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

        update_user_meta($user_id, 'first_settings', 'done');

        wp_send_json_success(array('message' => 'first_settings updated'));
    }
}

new MemyFirstSettings();
