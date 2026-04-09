<?php 
/**
 * Title: Get/Edit Userdata
 * Author: Modulbüro
 */
if (!defined('ABSPATH')) {
    exit;
}

class MemyUserDataEditor {
    
    /**
     *  Constructor mit inits
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('user_data_change', array($this, 'user_data_change'));
        add_action('wp_ajax_handle_update_user_data', array($this, 'handle_update_user_data'));
    }
    
    /**
     * Scripte initiieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('user-data-change', get_stylesheet_directory_uri() . '/assets/js/user-data-change.js', array('jquery','wp-util'), '1.0', true);
        wp_localize_script('user-data-change', 'ajax_object_userdata', array(
            'ajax_url'  => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce('user_data_nonce')
        ));
    }
    
    /**
     *   Ausgabe der Profildaten in einem Formular
     *   @deprecated
     */
    public function user_data_change() {

        if (!is_user_logged_in()) {
            return '<p>Sie müssen angemeldet sein, um Ihre Daten zu bearbeiten.</p>';
        }
        
        //Daten siehe profile.php
    }
    
    /**
     * AjX Handling zum Update der Daten
     */
    public function handle_update_user_data() {
        /**
         *  Safety first
         */
        if (!wp_verify_nonce($_POST['_wpnonce'], 'user_data_nonce')) {
            wp_send_json_error(array('message' => 'Sicherheitsüberprüfung fehlgeschlagen.'));
        }
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'Sie müssen angemeldet sein.'));
        }
        
        //Userdate
        $current_user = wp_get_current_user();
        $user_id      = $current_user->ID;
        
        // Postdaten sammeln
        $user_data = [
            'ID'         => $user_id,
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name'  => sanitize_text_field($_POST['last_name']),            
            'description'=> sanitize_text_field($_POST['description']),
        ];
        
        $user_meta = [
            'webseite'          => sanitize_text_field($_POST['webseite']),
            'berufsbezeichnung' => sanitize_text_field($_POST['berufsbezeichnung']),
            'firmenname'        => sanitize_text_field($_POST['firmenname']),
            'telefon'           => sanitize_text_field($_POST['telefon']),
            'strasze'           => sanitize_text_field($_POST['strasze']),
            'plz'               => sanitize_text_field($_POST['plz']),
            'ort'               => sanitize_text_field($_POST['ort']),
        ];

        /*
            // E-Mail-Validierung
            if (!is_email($user_data['user_email'])) {
                wp_send_json_error(array('message' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.'));
            }
            
            // Überprüfen, ob E-Mail bereits verwendet wird
            if ($user_data['user_email'] !== $current_user->user_email) {
                if (email_exists($user_data['user_email'])) {
                    wp_send_json_error(array('message' => 'Diese E-Mail-Adresse wird bereits verwendet.'));
                }
            }
        */

        // Passwort-Änderung verarbeiten
        if (!empty($_POST['new_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            
            // Aktuelles Passwort überprüfen
            if (!wp_check_password($current_password, $current_user->user_pass, $user_id)) {
                wp_send_json_error(array('message' => 'Das aktuelle Passwort ist falsch.'));
            }
            
            // Neues Passwort validieren
            if (strlen($new_password) < 8) {
                wp_send_json_error(array('message' => 'Das neue Passwort muss mindestens 8 Zeichen lang sein.'));
            }
            
            $user_data['user_pass'] = $new_password;
        }
        
        // Benutzerdaten aktualisieren
        $wpUserData = wp_update_user($user_data);
        $wpUserMeta = $this->wp_update_user_metas($user_id, $user_meta);

        if (is_wp_error($wpUserData) && !$wpUserMeta) {
            wp_send_json_error(array(
                'message' => 'Fehler beim Speichern: ' . $wpUserData->get_error_message() . '.Lade die Seite neu oder wende dich an den Support.',
            ));
        }
        
        wp_send_json_success(array(
            'message' => 'Daten wurden gespeichert',
            'logger'  => $user_meta
            )
        );
    }

    /**
     *  Hilfsfkt.: Metas speichern
     */
    public function wp_update_user_metas($user_id, $meta_array){
        if (!is_numeric($user_id) || empty($meta_array) || !is_array($meta_array)) {
            return false;
        }

        foreach ($meta_array as $key => $value) {
            update_user_meta($user_id, $key, $value);
        }

        return true; 
    }
}

new MemyUserDataEditor();