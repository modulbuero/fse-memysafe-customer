<?php 

class MemyContactsKunden {
    /**
     *  Constructor mit inits
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_handle_save_kunden', array($this, 'handle_save_kunden'));
        add_action('wp_ajax_handle_delete_kunden', array($this, 'handle_delete_kunden'));
        add_action('wp_ajax_load_kunden_data', array($this, 'load_kunden_data'));
        add_action('wp_ajax_get_kunden_list', array($this, 'get_kunden_list'));
    }
    
    /**
     * Scripte initiieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('contacts-manager-kunden', get_stylesheet_directory_uri() . '/assets/js/contacts-manager-kunden.js', array('jquery','wp-util'), '1.0', true);
        wp_localize_script('contacts-manager-kunden', 'ajax_object_contacts', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('contacts_manager_nonce')
        ));
    }


    /**
     * Speichern eines neuen oder bestehenden Kunden-Kontakts
     */
    public function handle_save_kunden() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id    = get_current_user_id();
        $kunden_id = isset($_POST['kunden_id']) ? sanitize_text_field($_POST['kunden_id']) : null;
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
        
        // Kunden-Liste abrufen
        $kunden_list = get_user_meta($user_id, 'kunden_list', true);
        if (!is_array($kunden_list)) {
            $kunden_list = [];
        }
        
        // Neue ID generieren wenn neuer Kunde
        if (!$kunden_id || $kunden_id === 'new') {
            $kunden_id = 'kunden_' . time() . '_' . uniqid();
        }
        
        // Daten speichern
        $kunden_list[$kunden_id] = [
            'name'   => $name,
            'email'  => $email,
            'tel'    => $tel,
            'firma'  => $firma,
            'status' => $status
        ];
        
        update_user_meta($user_id, 'kunden_list', $kunden_list);
        
        wp_send_json_success([
            'message' => "Kunde $name erfolgreich gespeichert",
            'debug' => $kunden_id
        ]);
    }

    /**
     * Löschen eines Kunden-Kontakts
     */
    public function handle_delete_kunden() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id    = get_current_user_id();
        $kunden_id = sanitize_text_field($_POST['kunden_id']);
        
        // Kunden-Liste abrufen
        $kunden_list = get_user_meta($user_id, 'kunden_list', true);
        
        if (is_array($kunden_list) && isset($kunden_list[$kunden_id])) {
            unset($kunden_list[$kunden_id]);
            update_user_meta($user_id, 'kunden_list', $kunden_list);
            wp_send_json_success('Kunde erfolgreich gelöscht');
        } else {
            wp_send_json_error('Kunde nicht gefunden');
        }
    }

    /**
     * Laden der Kunden-Daten via AJAX
     */
    public function load_kunden_data() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $kunden_id = isset($_POST['kunden_id']) ? sanitize_text_field($_POST['kunden_id']) : null;
        $user_id = get_current_user_id();
        
        // Für neuen Kunde leere Daten
        if (!$kunden_id || $kunden_id === 'new') {
            wp_send_json_success([
                'kunden_id' => 'new',
                'name' => '',
                'email' => '',
                'tel' => '',
                'firma' => '',
                'status' => 'Aktiv'
            ]);
            return;
        }
        
        // Daten laden wenn Bearbeitung
        $kunden_list = get_user_meta($user_id, 'kunden_list', true);
        if (is_array($kunden_list) && isset($kunden_list[$kunden_id])) {
            wp_send_json_success([
                'kunden_id' => $kunden_id,
                'name' => $kunden_list[$kunden_id]['name'] ?? '',
                'email' => $kunden_list[$kunden_id]['email'] ?? '',
                'tel' => $kunden_list[$kunden_id]['tel'] ?? '',
                'firma' => $kunden_list[$kunden_id]['firma'] ?? '',
                'status' => $kunden_list[$kunden_id]['status'] ?? 'Aktiv'
            ]);
        } else {
            wp_send_json_error('Kunde nicht gefunden');
        }
    }

    /**
     * Reload Kunden-Liste HTML via AJAX
     */
    public function get_kunden_list() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        ob_start();
        
        $user_id        = get_current_user_id();
        $kunden_list = get_user_meta($user_id, 'kunden_list', true);
        
        // Fallback wenn noch keine Kunden angelegt sind
        if (empty($kunden_list) || !is_array($kunden_list)) {
            echo '<div class="no-kunden-message"><p>Keine Kunden angelegt</p></div>';
        } else {
            // Kunden in Liste anzeigen
            foreach ($kunden_list as $kunden_id => $kunden_data) {
                $kunden_email = $kunden_data['email'] ?? '';
                $kunden_name = $kunden_data['name'] ?? '';
                
                if (!empty($kunden_email) || !empty($kunden_name)) {
                    ?>
                    <div data-kunden="<?php echo esc_attr($kunden_id); ?>" class="kunden-person-mail memy-button">
                        <i class="bi bi-person-fill"></i>
                        <h5><?php echo htmlspecialchars($kunden_name ?? 'Kunde'); ?></h5>
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

new MemyContactsKunden();
