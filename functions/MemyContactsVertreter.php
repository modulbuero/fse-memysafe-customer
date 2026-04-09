<?php 

class MemyContactsVertreter {
    /**
     *  Constructor mit inits
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_handle_save_vertreter', array($this, 'handle_save_vertreter'));
        add_action('wp_ajax_handle_delete_vertreter', array($this, 'handle_delete_vertreter'));
        add_action('wp_ajax_load_vertreter_data', array($this, 'load_vertreter_data'));
        add_action('wp_ajax_get_vertreter_list', array($this, 'get_vertreter_list'));
    }
    
    /**
     * Scripte initiieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('contacts-manager-vertreter', get_stylesheet_directory_uri() . '/assets/js/contacts-manager-vertreter.js', array('jquery','wp-util'), '1.0', true);
        wp_localize_script('contacts-manager-vertreter', 'ajax_object_contacts', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('contacts_manager_nonce')
        ));
    }


    /**
     * Speichern eines neuen oder bestehenden Vertreter-Kontakts
     */
    public function handle_save_vertreter() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id    = get_current_user_id();
        $vertreter_id = isset($_POST['vertreter_id']) ? sanitize_text_field($_POST['vertreter_id']) : null;
        $name       = sanitize_text_field($_POST['name']);
        $email      = sanitize_email($_POST['email']);
        $tel        = sanitize_text_field($_POST['tel']);
        $firma      = sanitize_text_field($_POST['firma']);
        $status     = sanitize_text_field($_POST['status']);
        
        // Validierung
        if (empty($name) || empty($email)) {
            wp_send_json_error('Name und E-Mail sind erforderlich');
            return;
        }
        
        // Vertreter-Liste abrufen
        $vertreter_list = get_user_meta($user_id, 'vertreter_list', true);
        if (!is_array($vertreter_list)) {
            $vertreter_list = [];
        }
        
        // Neue ID generieren wenn neuer Vertreter
        if (!$vertreter_id || $vertreter_id === 'new') {
            $vertreter_id = 'vertreter_' . time() . '_' . uniqid();
        }
        
        // Daten speichern
        $vertreter_list[$vertreter_id] = [
            'name'   => $name,
            'email'  => $email,
            'tel'    => $tel,
            'firma'  => $firma,
            'status' => $status
        ];
        
        update_user_meta($user_id, 'vertreter_list', $vertreter_list);
        
        wp_send_json_success([
            'message' => "Vertreter $name erfolgreich gespeichert",
            'debug' => $vertreter_id
        ]);
    }

    /**
     * Löschen eines Vertreter-Kontakts
     */
    public function handle_delete_vertreter() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id    = get_current_user_id();
        $vertreter_id = sanitize_text_field($_POST['vertreter_id']);
        
        // Vertreter-Liste abrufen
        $vertreter_list = get_user_meta($user_id, 'vertreter_list', true);
        
        if (is_array($vertreter_list) && isset($vertreter_list[$vertreter_id])) {
            unset($vertreter_list[$vertreter_id]);
            update_user_meta($user_id, 'vertreter_list', $vertreter_list);
            wp_send_json_success('Vertreter erfolgreich gelöscht');
        } else {
            wp_send_json_error('Vertreter nicht gefunden');
        }
    }

    /**
     * Laden der Vertreter-Daten via AJAX
     */
    public function load_vertreter_data() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $vertreter_id = isset($_POST['vertreter_id']) ? sanitize_text_field($_POST['vertreter_id']) : null;
        $user_id = get_current_user_id();
        
        // Für neuen Vertreter leere Daten
        if (!$vertreter_id || $vertreter_id === 'new') {
            wp_send_json_success([
                'vertreter_id' => 'new',
                'name' => '',
                'email' => '',
                'tel' => '',
                'firma' => '',
                'status' => 'Aktiv'
            ]);
            return;
        }
        
        // Daten laden wenn Bearbeitung
        $vertreter_list = get_user_meta($user_id, 'vertreter_list', true);
        if (is_array($vertreter_list) && isset($vertreter_list[$vertreter_id])) {
            wp_send_json_success([
                'vertreter_id' => $vertreter_id,
                'name' => $vertreter_list[$vertreter_id]['name'] ?? '',
                'email' => $vertreter_list[$vertreter_id]['email'] ?? '',
                'tel' => $vertreter_list[$vertreter_id]['tel'] ?? '',
                'firma' => $vertreter_list[$vertreter_id]['firma'] ?? '',
                'status' => $vertreter_list[$vertreter_id]['status'] ?? 'Aktiv'
            ]);
        } else {
            wp_send_json_error('Vertreter nicht gefunden');
        }
    }

    /**
     * Reload Vertreter-Liste HTML via AJAX
     */
    public function get_vertreter_list() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        ob_start();
        
        $user_id        = get_current_user_id();
        $vertreter_list = get_user_meta($user_id, 'vertreter_list', true);
        
        // Fallback wenn noch keine Vertreter angelegt sind
        if (empty($vertreter_list) || !is_array($vertreter_list)) {
            echo '<div class="no-vertreter-message"><p>Keine Vertreter angelegt</p></div>';
        } else {
            // Vertreter in Liste anzeigen
            foreach ($vertreter_list as $vertreter_id => $vertreter_data) {
                $vertreter_email = $vertreter_data['email'] ?? '';
                $vertreter_name = $vertreter_data['name'] ?? '';
                
                if (!empty($vertreter_email) || !empty($vertreter_name)) {
                    ?>
                    <div data-vertreter="<?php echo esc_attr($vertreter_id); ?>" class="vertreter-person-mail memy-button">
                        <i class="bi bi-person-fill"></i>
                        <h5><?php echo htmlspecialchars($vertreter_name ?? 'Vertreter'); ?></h5>
                        <i class="bi bi-caret-right-fill"></i>        
                    </div>
                    <?php
                }
            }
        }
        
        $html = ob_get_clean();
        wp_send_json_success($html);
    }
}

new MemyContactsVertreter();