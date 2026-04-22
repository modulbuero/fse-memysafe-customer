<?php
/**
 * Aktivitäten-Protokoll Manager
 */
class MemyProtocolManager {
    /**
     * Constructor mit inits
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_handle_add_protocol', array($this, 'handle_add_protocol'));
        add_action('wp_ajax_handle_update_protocol', array($this, 'handle_update_protocol'));
        add_action('wp_ajax_handle_get_protocols', array($this, 'handle_get_protocols'));
        $this->create_table();
    }

    /**
     * Erstellt die Aktivitäten-Tabelle für den aktuellen Blog
     */
    public function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aktivitaeten';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            datum datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            aktivitaet text NOT NULL,
            status text NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Scripte initiieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('protocol-manager', get_stylesheet_directory_uri() . '/assets/js/protocol-manager.js', array('jquery','wp-util'), '1.0', true);
        wp_localize_script('protocol-manager', 'ajax_object_protocol', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('protocol_manager_nonce')
        ));
    }

    /**
     * Fügt eine neue Aktivität hinzu
     */
    public function handle_add_protocol() {
        if(!check_ajax_referer('protocol_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }

        $user_id = get_current_user_id();
        $aktivitaet = sanitize_text_field($_POST['aktivitaet']);
        $status = sanitize_text_field($_POST['status']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'aktivitaeten';

        $result = $wpdb->insert(
            $table_name,
            array(
                'aktivitaet' => $aktivitaet,
                'status' => $status,
                'user_id' => $user_id
            )
        );

        if ($result) {
            wp_send_json_success(array('message' => 'Aktivität erfolgreich hinzugefügt.'));
        } else {
            wp_send_json_error('Fehler beim Hinzufügen der Aktivität.');
        }
    }

    /**
     * Aktualisiert eine bestehende Aktivität
     */
    public function handle_update_protocol() {
        if(!check_ajax_referer('protocol_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }

        $user_id = get_current_user_id();
        $id = intval($_POST['id']);
        $aktivitaet = sanitize_text_field($_POST['aktivitaet']);
        $status = sanitize_text_field($_POST['status']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'aktivitaeten';

        $result = $wpdb->update(
            $table_name,
            array(
                'aktivitaet' => $aktivitaet,
                'status' => $status
            ),
            array(
                'id' => $id,
                'user_id' => $user_id
            )
        );

        if ($result !== false) {
            wp_send_json_success(array('message' => 'Aktivität erfolgreich aktualisiert.'));
        } else {
            wp_send_json_error('Fehler beim Aktualisieren der Aktivität.');
        }
    }

    /**
     * Ruft alle Aktivitäten ab
     */
    public function handle_get_protocols() {
        if(!check_ajax_referer('protocol_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }

        $user_id = get_current_user_id();

        global $wpdb;
        $table_name = $wpdb->prefix . 'aktivitaeten';

        $protocols = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, datum, aktivitaet, status FROM $table_name WHERE user_id = %d ORDER BY datum DESC",
                $user_id
            ),
            ARRAY_A
        );

        wp_send_json_success(array('protocols' => $protocols));
    }

    /**
     * Statische Methode, um Protokolle zu laden (für direkten Aufruf)
     */
    public static function get_protocols_for_user($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aktivitaeten';

        return $wpdb->get_results(
            "SELECT id, datum, aktivitaet, status 
            FROM $table_name 
            ORDER BY id ASC",
            ARRAY_A
        );
    }
}

new MemyProtocolManager();
