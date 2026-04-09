<?php
/**
 * Safe Upload Manager
 * 
 * Diese Klasse verwaltet benutzer-spezifische, sichere Dateigespräche.
 * Ordner und Inhalte sind vor direktem Zugriff von außen geschützt.
 * 
 * Ordnerstruktur:
 * wp-content/uploads/safe-data/
 * ├── .htaccess (blockiert direkten Zugriff)
 * ├── index.php (leere Datei - blockiert Directory Listing)
 * └── user-{user_id}/
 *     ├── .htaccess (blockiert direkten Zugriff)
 *     ├── index.php (leere Datei)
 *     └── files/
 *         └── [Benutzerdateien]
 */

if ( ! class_exists( 'Memy_Safe_Upload' ) ) {

    class Memy_Safe_Upload {

        /**
         * Basis-Pfad für sichere Uploads
         * 
         * @var string
         */
        private static $base_path = null;

        /**
         * Basis-URL (wird nur für authentifizierte Benutzer bereitgestellt)
         * 
         * @var string
         */
        private static $base_url = null;

        /**
         * Basis-Pfad abrufen
         * 
         * @return string
         */
        public static function get_base_path() {
            if ( self::$base_path === null ) {
                $upload_dir = wp_upload_dir();
                self::$base_path = $upload_dir['basedir'] . '/safe-data';
            }
            return self::$base_path;
        }

        /**
         * Basis-URL abrufen
         * 
         * @return string
         */
        public static function get_base_url() {
            if ( self::$base_url === null ) {
                $upload_dir = wp_upload_dir();
                self::$base_url = $upload_dir['baseurl'] . '/safe-data';
            }
            return self::$base_url;
        }

        /**
         * Benutzerspezifischen Ordner-Pfad abrufen
         * 
         * @param int $user_id
         * @return string
         */
        public static function get_user_path( $user_id = null ) {
            if ( empty( $user_id ) ) {
                $user = wp_get_current_user();
                $user_id = $user->ID;
            }

            if ( ! $user_id ) {
                return false;
            }

            return self::get_base_path() . '/user-' . intval( $user_id );
        }

        /**
         * Dateien-Ordner-Pfad abrufen
         * 
         * @param int $user_id
         * @return string
         */
        public static function get_user_files_path( $user_id = null ) {
            $user_path = self::get_user_path( $user_id );
            if ( ! $user_path ) {
                return false;
            }
            return $user_path . '/files';
        }

        /**
         * Initialisierung: Ordnerstruktur erstellen (einmalig)
         * 
         * @return bool
         */
        public static function initialize_folder_structure() {
            $base_path = self::get_base_path();

            // 1. Basis-Ordner erstellen
            if ( ! file_exists( $base_path ) ) {
                wp_mkdir_p( $base_path );
            }

            // 2. .htaccess für Basis-Ordner (blockiert direkten Zugriff)
            $htaccess_base = $base_path . '/.htaccess';
            if ( ! file_exists( $htaccess_base ) ) {
                $htaccess_content = self::get_htaccess_content();
                file_put_contents( $htaccess_base, $htaccess_content );
            }

            // 3. index.php für Basis-Ordner (blockiert Directory Listing)
            $index_base = $base_path . '/index.php';
            if ( ! file_exists( $index_base ) ) {
                file_put_contents( $index_base, '<?php // Datei-Schutz' );
            }

            return true;
        }

        /**
         * Benutzerspezifische Ordnerstruktur erstellen
         * 
         * @param int $user_id
         * @return bool
         */
        public static function create_user_folder( $user_id = null ) {
            if ( empty( $user_id ) ) {
                $user = wp_get_current_user();
                $user_id = $user->ID;
            }

            if ( ! $user_id ) {
                return false;
            }

            // Basis-Struktur sicherstellen
            self::initialize_folder_structure();

            $user_path = self::get_user_path( $user_id );
            $files_path = self::get_user_files_path( $user_id );

            // 1. Benutzer-Ordner erstellen
            if ( ! file_exists( $user_path ) ) {
                wp_mkdir_p( $user_path );
            }

            // 2. Dateien-Ordner erstellen
            if ( ! file_exists( $files_path ) ) {
                wp_mkdir_p( $files_path );
            }

            // 3. .htaccess für Benutzer-Ordner (blockiert direkten Zugriff)
            $htaccess_user = $user_path . '/.htaccess';
            if ( ! file_exists( $htaccess_user ) ) {
                $htaccess_content = self::get_htaccess_content();
                file_put_contents( $htaccess_user, $htaccess_content );
            }

            // 4. index.php für Benutzer-Ordner (blockiert Directory Listing)
            $index_user = $user_path . '/index.php';
            if ( ! file_exists( $index_user ) ) {
                file_put_contents( $index_user, '<?php // Datei-Schutz' );
            }

            // 5. index.php für Dateien-Ordner
            $index_files = $files_path . '/index.php';
            if ( ! file_exists( $index_files ) ) {
                file_put_contents( $index_files, '<?php // Datei-Schutz' );
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

        /**
         * Datei hochladen (mit Validierung)
         * 
         * @param array $file $_FILES['fieldname']
         * @param int $user_id
         * @param array $allowed_types Array von erlaubten MIME-Typen
         * @param int $max_size Maximale Dateigröße in Bytes
         * @return array|WP_Error Array mit 'file_path', 'file_url', 'file_id' oder WP_Error
         */
        public static function upload_file( $file, $user_id = null, $allowed_types = array(), $max_size = 0 ) {
            // Benutzer authentifizieren
            if ( empty( $user_id ) ) {
                $user = wp_get_current_user();
                $user_id = $user->ID;
            }

            if ( ! $user_id ) {
                return new WP_Error( 'not_authenticated', 'Benutzer ist nicht angemeldet.' );
            }

            // Datei-Validierung
            $validation = self::validate_upload( $file, $allowed_types, $max_size );
            if ( is_wp_error( $validation ) ) {
                return $validation;
            }

            // Ordnerstruktur erstellen
            if ( ! self::create_user_folder( $user_id ) ) {
                return new WP_Error( 'folder_creation_failed', 'Ordnerstruktur konnte nicht erstellt werden.' );
            }

            $files_path = self::get_user_files_path( $user_id );
            $original_name = sanitize_file_name( $file['name'] );
            $unique_name = self::generate_unique_filename( $original_name, $files_path );
            $file_path = $files_path . '/' . $unique_name;

            // Datei verschieben
            if ( ! move_uploaded_file( $file['tmp_name'], $file_path ) ) {
                return new WP_Error( 'move_failed', 'Datei konnte nicht verschoben werden.' );
            }

            // Dateiberechtigungen setzen
            chmod( $file_path, 0600 );

            // Metadaten in User-Meta speichern
            $file_meta = array(
                'original_name' => $original_name,
                'stored_name'   => $unique_name,
                'upload_date'   => current_time( 'mysql' ),
                'file_size'     => filesize( $file_path ),
                'mime_type'     => $file['type'],
            );

            $file_id = self::save_file_meta( $user_id, $file_meta );

            return array(
                'file_path' => $file_path,
                'file_id'   => $file_id,
                'file_name' => $unique_name,
                'original_name' => $original_name,
            );
        }

        /**
         * Datei validieren
         * 
         * @param array $file
         * @param array $allowed_types
         * @param int $max_size
         * @return bool|WP_Error
         */
        private static function validate_upload( $file, $allowed_types = array(), $max_size = 0 ) {
            // Datei vorhanden?
            if ( ! isset( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
                return new WP_Error( 'no_file', 'Keine Datei hochgeladen.' );
            }

            // Dateigröße prüfen
            $file_size = filesize( $file['tmp_name'] );
            if ( $max_size > 0 && $file_size > $max_size ) {
                return new WP_Error( 'file_too_large', sprintf( 'Datei ist zu groß. Maximum: %s MB', $max_size / 1024 / 1024 ) );
            }

            // MIME-Typ prüfen
            if ( ! empty( $allowed_types ) ) {
                $file_type = wp_check_filetype( $file['name'], self::get_allowed_mimes() );
                if ( ! in_array( $file_type['type'], $allowed_types ) ) {
                    return new WP_Error( 'invalid_filetype', 'Dateityp nicht erlaubt.' );
                }
            }

            return true;
        }

        /**
         * Erlaubte MIME-Typen
         * 
         * @return array
         */
        private static function get_allowed_mimes() {
            return array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'gif'          => 'image/gif',
                'png'          => 'image/png',
                'pdf'          => 'application/pdf',
                'doc|docx'     => 'application/msword',
                'xls|xlsx'     => 'application/vnd.ms-excel',
                'txt'          => 'text/plain',
            );
        }

        /**
         * Eindeutigen Dateinamen generieren
         * 
         * @param string $original_name
         * @param string $path
         * @return string
         */
        private static function generate_unique_filename( $original_name, $path ) {
            $file_name = $original_name;
            $counter = 1;

            while ( file_exists( $path . '/' . $file_name ) ) {
                $file_info = pathinfo( $original_name );
                $file_name = $file_info['filename'] . '_' . $counter . '.' . $file_info['extension'];
                $counter++;
            }

            return $file_name;
        }

        /**
         * Datei-Metadaten speichern
         * 
         * @param int $user_id
         * @param array $meta
         * @return int Meta-ID
         */
        private static function save_file_meta( $user_id, $meta ) {
            return add_user_meta( $user_id, '_safe_upload_file', $meta );
        }

        /**
         * Alle Dateien eines Benutzers abrufen
         * 
         * @param int $user_id
         * @return array
         */
        public static function get_user_files( $user_id = null ) {
            if ( empty( $user_id ) ) {
                $user = wp_get_current_user();
                $user_id = $user->ID;
            }

            if ( ! $user_id ) {
                return array();
            }

            $files = get_user_meta( $user_id, '_safe_upload_file', false );
            return is_array( $files ) ? $files : array();
        }

        /**
         * Datei abrufen (nur für authentifizierte Benutzer)
         * 
         * @param int $file_id
         * @param int $user_id
         * @return array|WP_Error
         */
        public static function get_file( $file_id, $user_id = null ) {
            if ( empty( $user_id ) ) {
                $user = wp_get_current_user();
                $user_id = $user->ID;
            }

            if ( ! $user_id ) {
                return new WP_Error( 'not_authenticated', 'Benutzer ist nicht angemeldet.' );
            }

            $file_meta = get_user_meta( $user_id, '_safe_upload_file', false );
            
            if ( empty( $file_meta[ $file_id ] ) ) {
                return new WP_Error( 'file_not_found', 'Datei nicht gefunden.' );
            }

            return $file_meta[ $file_id ];
        }

        /**
         * Datei zum Download bereitstellen (mit Zugriffsprüfung)
         * 
         * @param string $file_name
         * @param int $user_id
         * @return bool|WP_Error
         */
        public static function download_file( $file_name, $user_id = null, $mode = 'download' ) {
            if ( empty( $user_id ) ) {
                $user = wp_get_current_user();
                $user_id = $user->ID;
            }

            if ( ! $user_id ) {
                return new WP_Error( 'not_authenticated', 'Benutzer ist nicht angemeldet.' );
            }

            $file_path = self::get_user_files_path( $user_id ) . '/' . sanitize_file_name( $file_name );

            // Sicherheitsprüfung: Datei muss im Benutzerordner sein
            if ( strpos( realpath( $file_path ), realpath( self::get_user_files_path( $user_id ) ) ) !== 0 ) {
                return new WP_Error( 'invalid_file', 'Zugriff verweigert.' );
            }

            if ( ! file_exists( $file_path ) ) {
                return new WP_Error( 'file_not_found', 'Datei nicht gefunden.' );
            }

            // MIME-Typ bestimmen
            $mime_type = mime_content_type( $file_path );
            if ( ! $mime_type ) {
                $mime_type = 'application/octet-stream';
            }

            // Header setzen
            header( 'Content-Type: ' . $mime_type );
            if ( $mode === 'open' ) {
                header( 'Content-Disposition: inline; filename="' . basename( $file_path ) . '"' );
            } else {
                header( 'Content-Disposition: attachment; filename="' . basename( $file_path ) . '"' );
            }
            header( 'Content-Length: ' . filesize( $file_path ) );

            readfile( $file_path );
            exit;
        }

        /**
         * Datei löschen
         * 
         * @param string $file_name
         * @param int $user_id
         * @return bool|WP_Error
         */
        public static function delete_file( $file_name, $user_id = null ) {
            if ( empty( $user_id ) ) {
                $user = wp_get_current_user();
                $user_id = $user->ID;
            }

            if ( ! $user_id ) {
                return new WP_Error( 'not_authenticated', 'Benutzer ist nicht angemeldet.' );
            }

            $file_path = self::get_user_files_path( $user_id ) . '/' . sanitize_file_name( $file_name );

            // Sicherheitsprüfung
            if ( strpos( realpath( $file_path ), realpath( self::get_user_files_path( $user_id ) ) ) !== 0 ) {
                return new WP_Error( 'invalid_file', 'Zugriff verweigert.' );
            }

            if ( ! file_exists( $file_path ) ) {
                return new WP_Error( 'file_not_found', 'Datei nicht gefunden.' );
            }

            return unlink( $file_path );
        }
    }
}

// Hook: Initialisiere Ordnerstruktur beim Benutzer-Login
add_action( 'wp_login', function( $user_login, $user ) {
    Memy_Safe_Upload::create_user_folder( $user->ID );
}, 10, 2 );
