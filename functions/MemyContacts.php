<?php 
/**
 * Notfallkontakt und Vertrauesnspersonen handler
 */
class MemyContacts {
    /**
     *  Constructor mit inits
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_handle_update_contacts', array($this, 'handle_update_contacts'));
        add_action('wp_ajax_handle_delete_contacts', array($this, 'handle_delete_contacts'));
    }
    
    /**
     * Scripte initiieren
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('contacts-manager', get_stylesheet_directory_uri() . '/assets/js/contacts-manager.js', array('jquery','wp-util'), '1.0', true);
        wp_localize_script('contacts-manager', 'ajax_object_contacts', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('contacts_manager_nonce')
        ));
    }

    /**
     * Einrichten des Kontakts
     */
    public function handle_update_contacts() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id    = get_current_user_id();
        $email      = sanitize_text_field($_POST['email']);
        $typ        = sanitize_text_field($_POST['typ']);
        $status     = sanitize_text_field($_POST['status']);
        $name       = sanitize_text_field($_POST['name']);
        $tel        = sanitize_text_field($_POST['tel']);
        $firma      = sanitize_text_field($_POST['firma']);
        $mmsi_safe  = sanitize_text_field($_POST['mmsi_safe']);
        #$is_main    = sanitize_text_field($_POST['is_main']);
        $mmsi_can   = sanitize_text_field($_POST['mmsi_can']);
        $contact_id = intval($_POST['contact_id']);
        
        $contact_data= [
            'email'         => $email,
            'typ'           => $typ,
            'name'          => $name,
            'tel'           => $tel,
            'firma'         => $firma,
            'mmsi_safe'     => $mmsi_safe,
            'status'        => $status,
            #'hauptkontakt'  => $is_main,
            'mmsi_can'      => $mmsi_can
        ];
        
        // Update user meta
        update_user_meta($user_id, 'contact-person-'.$contact_id, $contact_data);
        wp_send_json_success(array(
            'message' => $contact_data["typ"]. ' ' . $contact_data["name"] . ' gespeichert.',
            'debug'   => print_r($contact_data, true)
        ));
    }

    /**
     * Löschen des Kontakts
     */    
    public function handle_delete_contacts() {
        if(!check_ajax_referer('contacts_manager_nonce', '_wpnonce')){
            wp_send_json_error('Invalid nonce');
            return;
        }
        
        $user_id    = get_current_user_id();
        $contact_id = intval($_POST['contact_id']);
        
        // Kontaktdaten leeren
        $empty_data = [
            'email'         => '',
            'typ'           => '',
            'name'          => '',
            'tel'           => '',
            'firma'         => '',
            'mmsi_safe'     => '',
            'status'        => '',
            'hauptkontakt'  => '',
            'mmsi_can'      => ''
        ];
        
        // Update user meta with empty data
        update_user_meta($user_id, 'contact-person-'.$contact_id, $empty_data);
        wp_send_json_success('Kontakt erfolgreich gelöscht');
    }
}

new MemyContacts();