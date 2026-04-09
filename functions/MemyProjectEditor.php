<?php 
/**
 * Title: CRUDE User Projects
 * Author: Modulbüro
 */
if (!defined('ABSPATH')) {
    exit;
}

class MemyProjectEditor {
    /**
     * Gibt den Tabellennamen für die aktuelle Site zurück
     */
    private function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'user_projects';
    }

    public function __construct() {
        add_action('after_switch_theme', array($this,'create_tables'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        //AjxAllowed
        add_action('wp_ajax_save_project', array($this, 'ajax_save_project'));
        add_action('wp_ajax_delete_project', array($this, 'ajax_delete_project'));
        add_action('wp_ajax_get_project', array($this, 'ajax_get_project'));
        add_action('wp_ajax_load_projects', array($this, 'ajax_load_projects'));
    }
    
    // Initialization Table
    public function create_tables() {
        global $wpdb;
        $table = $this->get_table_name();
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            projekt_id varchar(50) NOT NULL,
            projekt_titel varchar(255) NOT NULL,
            projekt_beschreibung text,
            projekt_contact_person bigint(20) DEFAULT 0,
            projekt_owner bigint(20) NOT NULL,
            projekt_status varchar(50) DEFAULT 'aktiv',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY projekt_id (projekt_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Scripte initiieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('user-projects', get_stylesheet_directory_uri() . '/assets/js/user-projects.js', array('jquery','wp-util'), '1.0', true);
        wp_localize_script('user-projects', 'ajax_object_project', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('user_projecs_nonce')
        ));
    }
    
    /**
     *  Projekt speichern
     */
    public function ajax_save_project() {

        //Security
        check_ajax_referer('user_projecs_nonce', 'nonce');        

        if (!is_user_logged_in()) {
            wp_die('Nicht autorisiert');
        }
        
        //GEt Post-Data
        global $wpdb;
        $current_user_id= get_current_user_id();
        $table = $this->get_table_name();
        $projekt_id     = sanitize_text_field($_POST['projekt_id']);
        $projekt_titel  = sanitize_text_field($_POST['projekt_titel']);
        $projekt_status = sanitize_text_field($_POST['projekt_status']);
        $edit_id        = intval($_POST['edit_id']);
        $projekt_beschreibung = sanitize_textarea_field($_POST['projekt_beschreibung']);

        if (empty($projekt_id) || empty($projekt_titel)) {
            wp_send_json_error('Projekt-ID und Titel sind erforderlich.');
        }
        
        $projekt_contact_person = isset($_POST['projekt_contact_person']) ? intval($_POST['projekt_contact_person']) : 0;

        $data = array(
            'projekt_id'    => $projekt_id,
            'projekt_titel' => $projekt_titel,
            'projekt_beschreibung' => $projekt_beschreibung,
            'projekt_contact_person' => $projekt_contact_person,
            'projekt_status'=> $projekt_status,
            'projekt_owner' => $current_user_id
        );

        //Check Data
        if ($edit_id > 0) {
            // Überprüfen, ob das Projekt dem aktuellen Benutzer gehört
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE id = %d AND projekt_owner = %d",
                $edit_id, $current_user_id
            ));
            
            if (!$existing) {
                wp_send_json_error('Projekt nicht gefunden oder keine Berechtigung.');
            }
            
            $result = $wpdb->update(
                $table,
                $data,
                array('id' => $edit_id),
                array('%s', '%s', '%s', '%d', '%s', '%d'),
                array('%d')
            );
        } else {
            // Überprüfen, ob Projekt-ID bereits existiert
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE projekt_id = %s",
                $projekt_id
            ));
            
            if ($exists > 0) {
                wp_send_json_error('Diese Projekt-ID existiert bereits.');
            }
            
            $result = $wpdb->insert($table, $data, array('%s', '%s', '%s', '%d', '%s', '%d'));
        }

        //Response
        if ($result !== false) {
            wp_send_json_success('Projekt erfolgreich gespeichert.');
        } else {
            wp_send_json_error('Fehler beim Speichern des Projekts. Versuche es erneut');
        }
    }
    
    /**
     *  Projekte bearbeiten
    */
    public function ajax_get_project() {
        check_ajax_referer('user_projecs_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_die('Nicht autorisiert');
        }
        
        global $wpdb;
        $current_user_id = get_current_user_id();
        $table = $this->get_table_name();
        $project_id      = intval($_POST['id']);
        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d AND projekt_owner = %d",
            $project_id, $current_user_id
        ));
        if ($project) {
            wp_send_json_success($project);
        } else {
            wp_send_json_error('Projekt nicht gefunden.');
        }
    }
    
    /**
     *  Projekt löschen
     */
    public function ajax_delete_project() {
        check_ajax_referer('user_projecs_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_die('Nicht autorisiert');
        }
        
        global $wpdb;
        $current_user_id = get_current_user_id();
        $table = $this->get_table_name();
        $project_id = intval($_POST['id']);
        $result = $wpdb->delete(
            $table,
            array(
                'id' => $project_id,
                'projekt_owner' => $current_user_id
            ),
            array('%d', '%d')
        );
        if ($result !== false) {
            wp_send_json_success('Projekt erfolgreich gelöscht.');
        } else {
            wp_send_json_error('Fehler beim Löschen des Projekts.');
        }
    }

    /**
     *  Projekt laden
     */
    public function ajax_load_projects() {
        check_ajax_referer('user_projecs_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_die('Nicht autorisiert');
        }
        
        $projects_html = $this->get_user_projects_html();
        wp_send_json_success($projects_html);
    }

    /**
     *  Projekt auflisten in HTML
     */
    private function get_user_projects_html() {
        global $wpdb;
        $current_user_id = get_current_user_id();
        $table = $this->get_table_name();
        $projects = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE projekt_owner = %d ORDER BY created_at DESC",
            $current_user_id
        ));
        
        if (empty($projects)) {
            return '<p>Noch keine Projekte vorhanden.</p>';
        }
        
        $output = '';
        foreach ($projects as $project) {
            $output .= sprintf(
                '<div class="project-item">
                    <div class="project-main-info">
                        <div>
                            <span class="project-id">ID: %s</span>
                            <span class="project-title">%s</span>
                            <span class="project-status status-%s">%s</span>
                        </div>
                        <div class="project-description">%s</div>
                        <div class="project-contact">Kontakt-ID: %s</div>
                    </div>
                    <div class="project-actions">
                        <button class="edit-btn" data-id="%d">Bearbeiten</button>
                        <button class="delete-btn" data-id="%d">Löschen</button>
                    </div>
                </div>',
                esc_html($project->projekt_id),
                esc_html($project->projekt_titel),
                esc_attr($project->projekt_status),
                esc_html(ucfirst($project->projekt_status)),
                esc_html($project->projekt_beschreibung),
                esc_html($project->projekt_contact_person),
                $project->id,
                $project->id
            );
        }
        
        return $output;
    }
    
}

new MemyProjectEditor();