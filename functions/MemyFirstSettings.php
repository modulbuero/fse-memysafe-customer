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

    private static $base_path = null;

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
        $contact_data=[];
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
                'first_name' => $contact_meta['f_name'] ?? '',
                'last_name'  => $contact_meta['l_name'] ?? '',
                'firma'    => $contact_meta['name'] ?? '',
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

        //Sende Notfallkontakt eine Einladung
        $notfallkontakt = $contact_data['email'];
        if ($notfallkontakt) {
            $memycontact = new MemyContacts();
            $memycontact->handle_send_contact_invitation();
        }

        wp_send_json_success(array(
            'message'     => 'Ersteinrichtung abgeschlossen.',
            'dataUser'    => json_encode($_POST['user_meta']),
            'dataContact' => json_encode($_POST['contact_meta'])
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
            wp_send_json_error(array('message' => 'Keine Daten hinterlegt.'), 400);
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

        // Erstelle Ordnerstruktur
        self::create_user_folder($user_id);

        // Bestimme files-Verzeichnis
        $files_path = self::get_user_files_path($user_id);
        if (!$files_path) {
            wp_send_json_error(array('message' => 'Benutzer-Verzeichnis konnte nicht erstellt werden.'), 500);
        }

        // Speichere TXT-Datei im files Ordner
        $date_time = date('ymdHis', current_time('timestamp'));
        $file_name = 'Notfallkontakt_Informationen_'.$date_time.'.txt';
        $file_path = $files_path . '/' . $file_name;

        $saved = file_put_contents($file_path, $txt_content);

        if ($saved === false) {
            wp_send_json_error(array('message' => 'Datei konnte nicht gespeichert werden.'), 500);
        }

        // Metadaten in User-Meta speichern, damit die Datei im Safe angezeigt wird
        $file_meta = array(
            'original_name' => $file_name, // Hier ist der Originalname der generierte Dateiname
            'stored_name'   => $file_name, // Der gespeicherte Name ist ebenfalls der generierte Dateiname
            'upload_date'   => current_time( 'mysql' ),
            'file_size'     => filesize( $file_path ),
            'mime_type'     => 'text/plain', // Festgelegter MIME-Typ für TXT
        );

        // Stellen Sie sicher, dass die Memy_Safe_Upload Klasse geladen ist
        if (class_exists('Memy_Safe_Upload')) {
            Memy_Safe_Upload::save_file_meta($user_id, $file_meta);
        }

        wp_send_json_success(array(
            'message'     => 'Sicherheitsinformationen erfolgreich gespeichert und im Safe abgelegt.',
            'file_name'   => $file_name,
            'file_path'   => $file_path
        ));
    }

    /**
     * Basis-Pfad für sichere Daten abrufen
     * 
     * @return string
     */
    private static function get_base_path() {
        if (self::$base_path === null) {
            $upload_dir = wp_upload_dir();
            self::$base_path = $upload_dir['basedir'] . '/safe-data';
        }
        return self::$base_path;
    }

    /**
     * Benutzerspezifischen Ordner-Pfad abrufen
     * 
     * @param int $user_id
     * @return string
     */
    private static function get_user_path($user_id = null) {
        if (empty($user_id)) {
            $user = wp_get_current_user();
            $user_id = $user->ID;
        }

        if (!$user_id) {
            return false;
        }

        return self::get_base_path() . '/user-' . intval($user_id);
    }

    /**
     * Dateien-Ordner-Pfad abrufen
     * 
     * @param int $user_id
     * @return string
     */
    private static function get_user_files_path($user_id = null) {
        $user_path = self::get_user_path($user_id);
        if (!$user_path) {
            return false;
        }
        return $user_path . '/files';
    }

    /**
     * Initialisierung: Ordnerstruktur erstellen (einmalig)
     * 
     * @return bool
     */
    private static function initialize_folder_structure() {
        $base_path = self::get_base_path();

        // 1. Basis-Ordner erstellen
        if (!file_exists($base_path)) {
            wp_mkdir_p($base_path);
        }

        // 2. .htaccess für Basis-Ordner (blockiert direkten Zugriff)
        $htaccess_base = $base_path . '/.htaccess';
        if (!file_exists($htaccess_base)) {
            $htaccess_content = self::get_htaccess_content();
            file_put_contents($htaccess_base, $htaccess_content);
        }

        // 3. index.php für Basis-Ordner (blockiert Directory Listing)
        $index_base = $base_path . '/index.php';
        if (!file_exists($index_base)) {
            file_put_contents($index_base, '<?php // Datei-Schutz');
        }

        return true;
    }

    /**
     * Benutzerspezifische Ordnerstruktur erstellen
     * 
     * @param int $user_id
     * @return bool
     */
    private static function create_user_folder($user_id = null) {
        if (empty($user_id)) {
            $user = wp_get_current_user();
            $user_id = $user->ID;
        }

        if (!$user_id) {
            return false;
        }

        // Basis-Struktur sicherstellen
        self::initialize_folder_structure();

        $user_path  = self::get_user_path($user_id);
        $files_path = self::get_user_files_path($user_id);

        // 1. Benutzer-Ordner erstellen
        if (!file_exists($user_path)) {
            wp_mkdir_p($user_path);
        }

        // 2. Dateien-Ordner erstellen
        if (!file_exists($files_path)) {
            wp_mkdir_p($files_path);
        }

        // 3. .htaccess für Benutzer-Ordner (blockiert direkten Zugriff)
        $htaccess_user = $user_path . '/.htaccess';
        if (!file_exists($htaccess_user)) {
            $htaccess_content = self::get_htaccess_content();
            file_put_contents($htaccess_user, $htaccess_content);
        }

        // 4. index.php für Benutzer-Ordner (blockiert Directory Listing)
        $index_user = $user_path . '/index.php';
        if (!file_exists($index_user)) {
            file_put_contents($index_user, '<?php // Datei-Schutz');
        }

        // 5. index.php für Dateien-Ordner
        $index_files = $files_path . '/index.php';
        if (!file_exists($index_files)) {
            file_put_contents($index_files, '<?php // Datei-Schutz');
        }

        return true;
    }

    /**
     * .htaccess Inhalt für Schutz
     * 
     * @return string
     */
    private static function get_htaccess_content() {
        return <<<'HTACCESS'
# Blockiere direkten Zugriff auf Dateien
<FilesMatch ".*">
    Deny from all
</FilesMatch>

# Erlaube nur PHP-Zugriff durch WordPress
<FilesMatch "\.(jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|txt)$">
    Allow from all
</FilesMatch>

# Verhindere Directory Listing
Options -Indexes

# Deaktiviere Script-Ausführung
<IfModule mod_php.c>
    php_flag engine off
</IfModule>

# Deaktiviere CGI
<IfModule mod_cgi.c>
    SetEnv PATH_INFO /dev/null
</IfModule>
HTACCESS;
    }
}

new MemyFirstSettings();
