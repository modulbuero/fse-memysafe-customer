<?php 

class MemyProjectsManager {
    /**
     *  Constructor mit inits
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_handle_save_project', array($this, 'handle_save_project'));
        add_action('wp_ajax_handle_delete_project', array($this, 'handle_delete_project'));
        add_action('wp_ajax_load_project_data', array($this, 'load_project_data'));
        add_action('wp_ajax_get_projects_list', array($this, 'get_projects_list'));
        add_action('wp_ajax_get_vertreter_select', array($this, 'get_vertreter_select'));
        add_action('wp_ajax_get_notfall_select', array($this, 'get_notfall_select'));
    }
    
    /**
     * Scripte initiieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('projects-manager', get_stylesheet_directory_uri() . '/assets/js/projects-manager.js', array('jquery','wp-util'), '1.0', true);
        wp_localize_script('projects-manager', 'ajax_object_projects', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('projects_manager_nonce')
        ));
    }

    /**
     * Speichern eines neuen oder bestehenden Projekts
     */
    public function handle_save_project() {
        if(!check_ajax_referer('projects_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id        = get_current_user_id();
        $project_id     = isset($_POST['project_id']) ? sanitize_text_field($_POST['project_id']) : null;
        $projektname    = sanitize_text_field($_POST['projektname']);
        
        // Status als Array verarbeiten
        $status = isset($_POST['status']) ? $_POST['status'] : [];
        if(is_array($status)){
            $status = array_map('sanitize_text_field', $status);
        } else {
            $status = sanitize_text_field($status);
        }
        
        $mandant        = sanitize_text_field($_POST['mandant']);
        $mandant_ansprechpartner = sanitize_text_field($_POST['mandant_ansprechpartner']);
        $mandant_telefon    = sanitize_text_field($_POST['mandant_telefon']);
        $mandant_mobile     = sanitize_text_field($_POST['mandant_mobile']);
        $mandant_email      = sanitize_email($_POST['mandant_email']);
        $dienstleister_name = sanitize_text_field($_POST['dienstleister_name']);
        $dienstleister_funktion = sanitize_text_field($_POST['dienstleister_funktion']);
        $dateizugriff   = sanitize_text_field($_POST['dateizugriff']);
        $anmerkung      = sanitize_textarea_field($_POST['anmerkung']);
        $anmerkungen    = sanitize_textarea_field($_POST['anmerkungen']);
        $vertreter      = sanitize_text_field($_POST['vertreter']);
        $kontakt        = sanitize_text_field($_POST['kontakt']);
        
        // Validierung
        if (empty($projektname)) {
            wp_send_json_error('Projektname ist erforderlich');
            return;
        }
        
        // Projekte-Liste abrufen
        $projects_list = get_user_meta($user_id, 'projects_list', true);
        if (!is_array($projects_list)) {
            $projects_list = [];
        }
        
        // Neue ID generieren wenn neues Projekt
        if (!$project_id || $project_id === 'new') {
            $project_id = 'project_' . time() . '_' . uniqid();
        }
        
        // Daten speichern
        $projects_list[$project_id] = [
            'projektname' => $projektname,
            'status'      => $status,
            'mandant'     => $mandant,
            'mandant_ansprechpartner' => $mandant_ansprechpartner,
            'mandant_telefon'   => $mandant_telefon,
            'mandant_mobile'    => $mandant_mobile,
            'mandant_email'     => $mandant_email,
            'dienstleister_name'=> $dienstleister_name,
            'dienstleister_funktion' => $dienstleister_funktion,
            'dateizugriff'  => $dateizugriff,
            'anmerkung'     => $anmerkung,
            'anmerkungen'   => $anmerkungen,
            'vertreter'     => $vertreter,
            'kontakt'       => $kontakt
        ];
        
        update_user_meta($user_id, 'projects_list', $projects_list);
        
        wp_send_json_success([
            'message' => 'Projekt erfolgreich gespeichert',
            'project_id' => $project_id
        ]);
    }

    /**
     * Löschen eines Projekts
     */
    public function handle_delete_project() {
        if(!check_ajax_referer('projects_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id    = get_current_user_id();
        $project_id = sanitize_text_field($_POST['project_id']);
        
        // Projekte-Liste abrufen
        $projects_list = get_user_meta($user_id, 'projects_list', true);
        
        if (is_array($projects_list) && isset($projects_list[$project_id])) {
            unset($projects_list[$project_id]);
            update_user_meta($user_id, 'projects_list', $projects_list);
            wp_send_json_success('Projekt erfolgreich gelöscht');
        } else {
            wp_send_json_error('Projekt nicht gefunden');
        }
    }

    /**
     * Laden der Projekt-Daten via AJAX
     */
    public function load_project_data() {
        if(!check_ajax_referer('projects_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $project_id = isset($_POST['project_id']) ? sanitize_text_field($_POST['project_id']) : null;
        $user_id = get_current_user_id();
        
        // Für neues Projekt leere Daten
        if (!$project_id || $project_id === 'new') {
            wp_send_json_success([
                'project_id' => 'new',
                'projektname' => '',
                'status' => ['Geplant'],
                'mandant' => '',
                'mandant_ansprechpartner' => '',
                'mandant_telefon' => '',
                'mandant_mobile' => '',
                'mandant_email' => '',
                'dienstleister_name' => '',
                'dienstleister_funktion' => '',
                'dateizugriff' => '',
                'anmerkung' => '',
                'anmerkungen' => '',
                'vertreter' => '',
                'kontakt' => ''
            ]);
            return;
        }
        
        // Daten laden wenn Bearbeitung
        $projects_list = get_user_meta($user_id, 'projects_list', true);
        if (is_array($projects_list) && isset($projects_list[$project_id])) {
            wp_send_json_success([
                'project_id' => $project_id,
                'projektname' => $projects_list[$project_id]['projektname'] ?? '',
                'status' => $projects_list[$project_id]['status'] ?? ['Geplant'],
                'mandant' => $projects_list[$project_id]['mandant'] ?? '',
                'mandant_ansprechpartner' => $projects_list[$project_id]['mandant_ansprechpartner'] ?? '',
                'mandant_telefon' => $projects_list[$project_id]['mandant_telefon'] ?? '',
                'mandant_mobile' => $projects_list[$project_id]['mandant_mobile'] ?? '',
                'mandant_email' => $projects_list[$project_id]['mandant_email'] ?? '',
                'dienstleister_name' => $projects_list[$project_id]['dienstleister_name'] ?? '',
                'dienstleister_funktion' => $projects_list[$project_id]['dienstleister_funktion'] ?? '',
                'dateizugriff' => $projects_list[$project_id]['dateizugriff'] ?? '',
                'anmerkung' => $projects_list[$project_id]['anmerkung'] ?? '',
                'anmerkungen' => $projects_list[$project_id]['anmerkungen'] ?? '',
                'vertreter' => $projects_list[$project_id]['vertreter'] ?? '',
                'kontakt' => $projects_list[$project_id]['kontakt'] ?? ''
            ]);
        } else {
            wp_send_json_error('Projekt nicht gefunden');
        }
    }

    /**
     * Reload Projekte-Liste HTML via AJAX
     */
    public function get_projects_list() {
        if(!check_ajax_referer('projects_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        ob_start();
        
        $user_id        = get_current_user_id();
        $projects_list = get_user_meta($user_id, 'projects_list', true);
        
        // Fallback wenn noch keine Projekte angelegt sind
        if (empty($projects_list) || !is_array($projects_list)) {
            echo '<div class="no-projects-message"><p>Keine Projekte angelegt</p></div>';
        } else {
            // Projekte in Liste anzeigen
            foreach ($projects_list as $project_id => $project_data) {
                $project_name = $project_data['projektname'] ?? '';
                
                if (!empty($project_name)) {
                    ?>
                    <div data-project="<?php echo esc_attr($project_id); ?>" class="project-person-mail goto-btn spalte" data-goto='manage-projects' data-step="2" >
                        <i class="mmsi-icon projekt"></i>
                        <p><?php echo htmlspecialchars($project_name); ?></p>
                        <i class="mmsi-icon weiter"></i>      
                    </div>
                    <?php
                }
            }
        }
        
        $html = ob_get_clean();
        wp_send_json_success($html);
    }

    /**
     * Vertreter-Select-Feld laden
     */
    public function get_vertreter_select() {
        if(!check_ajax_referer('projects_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id = get_current_user_id();
        $selected_vertreter = isset($_POST['selected']) ? sanitize_text_field($_POST['selected']) : '';
        $vertreter_list = get_user_meta($user_id, 'vertreter_list', true);
        
        $html = '<div class="selectbox"><label>Vertreter</label><select id="project-vertreter"><option value="">-- Vertreter wählen --</option>';
        
        if (is_array($vertreter_list) && !empty($vertreter_list)) {
            foreach ($vertreter_list as $vertreter_id => $vertreter_data) {
                $vertreter_name = $vertreter_data['name'] ?? '';
                $selected = ($vertreter_id === $selected_vertreter) ? 'selected' : '';
                $html .= sprintf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($vertreter_id),
                    $selected,
                    esc_html($vertreter_name)
                );
            }
        }
        
        $html .= '</select></div>';
        wp_send_json_success($html);
    }

    /**
     * Notfall-Kontakt-Select-Feld laden
     */
    public function get_notfall_select() {
        if(!check_ajax_referer('projects_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id = get_current_user_id();
        $selected_kontakt = isset($_POST['selected']) ? sanitize_text_field($_POST['selected']) : '';
        
        $html = '<div class="selectbox project-kontakt"><label>Notfallkontakt</label><select id="project-kontakt"><option value="">Notfallkontakt wählen</option>';
        
        // Notfall-Kontakte laden (aus den 3 statischen Kontakten)
        foreach (range(1, 3) as $i) {
            $contact_data = get_user_meta($user_id, 'contact-person-' . $i, true);
            $contact_name = $contact_data['name'] ?? '';
            
            if (!empty($contact_name)) {
                $contact_id = 'contact_' . $i;
                $selected = ($contact_id === $selected_kontakt) ? 'selected' : '';
                $html .= sprintf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($contact_id),
                    $selected,
                    esc_html($contact_name)
                );
            }
        }
        
        $html .= '</select></div>';
        wp_send_json_success($html);
    }
}

new MemyProjectsManager();
