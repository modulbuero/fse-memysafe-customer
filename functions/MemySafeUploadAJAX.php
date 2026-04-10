<?php
/**
 * Safe Upload AJAX Handler
 * 
 * Verwaltet alle AJAX-Anfragen für sichere Dateiuploads
 */

if ( ! class_exists( 'Memy_Safe_Upload_AJAX' ) ) {

    class Memy_Safe_Upload_AJAX {

        /**
         * Konstruktor: Hook registrieren
         */
        public static function init() {
            // AJAX Actions
            add_action( 'wp_ajax_memy_upload_file', array( __CLASS__, 'handle_upload' ) );
            add_action( 'wp_ajax_memy_get_files', array( __CLASS__, 'handle_get_files' ) );
            add_action( 'wp_ajax_memy_delete_file', array( __CLASS__, 'handle_delete_file' ) );
            add_action( 'wp_ajax_memy_download_file', array( __CLASS__, 'handle_download_file' ) );

            // Script & Daten laden
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ), 99 );
        }

        /**
         * AJAX Scripts und Daten laden
         */
        public static function enqueue_scripts() {
            // Nur für eingeloggte Benutzer
            if ( ! is_user_logged_in() ) {
                return;
            }

            wp_enqueue_script(
                'memy-safe-upload',
                get_stylesheet_directory_uri() . '/assets/js/safe-upload.js',
                array( 'jquery' ),
                wp_get_theme()->get( 'Version' ),
                true
            );

            // AJAX URL und Nonce an JavaScript übergeben
            wp_localize_script(
                'memy-safe-upload',
                'memySafeUpload',
                array(
                    'ajaxurl'    => admin_url( 'admin-ajax.php' ),
                    'uploadNonce' => wp_create_nonce( 'memy_upload_file' ),
                    'deleteNonce' => wp_create_nonce( 'memy_delete_file' ),
                    'filesNonce'  => wp_create_nonce( 'memy_get_files' ),
                    'downloadNonce' => wp_create_nonce( 'memy_download_file' ),
                )
            );
        }

        /**
         * AJAX Handler: Datei hochladen
         */
        public static function handle_upload() {
            // Sicherheit prüfen
            check_ajax_referer( 'memy_upload_file', 'nonce' );

            // Benutzer authentifizieren
            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array(
                    'message' => 'Benutzer nicht authentifiziert.',
                ) );
            }

            // $_FILES prüfen
            if ( ! isset( $_FILES['file'] ) ) {
                wp_send_json_error( array(
                    'message' => 'Keine Datei hochgeladen.',
                ) );
            }

            $user_id = get_current_user_id();

            // Erlaubte MIME-Typen abrufen
            $allowed_types = ! empty( $_POST['allowed_types'] )
                ? array_filter( array_map( 'sanitize_text_field', explode( ',', $_POST['allowed_types'] ) ) )
                : array();

            // Maximale Größe abrufen
            $max_size = isset( $_POST['max_size'] )
                ? intval( $_POST['max_size'] )
                : 0;

            // Upload durchführen
            $result = Memy_Safe_Upload::upload_file(
                $_FILES['file'],
                $user_id,
                $allowed_types,
                $max_size
            );

            if ( is_wp_error( $result ) ) {
                wp_send_json_error( array(
                    'message' => $result->get_error_message(),
                ) );
            }

            // Erfolg
            wp_send_json_success( array(
                'message'       => 'Datei erfolgreich hochgeladen.',
                'file_id'       => $result['file_id'],
                'file_name'     => $result['file_name'],
                'original_name' => $result['original_name'],
            ) );
        }

        /**
         * AJAX Handler: Dateien abrufen
         */
        public static function handle_get_files() {
            // Sicherheit prüfen
            check_ajax_referer( 'memy_get_files', 'nonce' );

            // Benutzer authentifizieren
            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array(
                    'message' => 'Benutzer nicht authentifiziert.',
                ) );
            }

            $user_id = get_current_user_id();
            $files = Memy_Safe_Upload::get_user_files( $user_id );

            if ( ! is_array( $files ) ) {
                $files = array();
            }

            wp_send_json_success( array(
                'files' => $files,
                'count' => count( $files ),
            ) );
        }

        /**
         * AJAX Handler: Datei löschen
         */
        public static function handle_delete_file() {
            // Sicherheit prüfen
            check_ajax_referer( 'memy_delete_file', 'nonce' );

            // Benutzer authentifizieren
            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array(
                    'message' => 'Benutzer nicht authentifiziert.',
                ) );
            }

            $file_name = isset( $_POST['file_name'] )
                ? sanitize_text_field( $_POST['file_name'] )
                : '';

            if ( empty( $file_name ) ) {
                wp_send_json_error( array(
                    'message' => 'Dateiname erforderlich.',
                ) );
            }

            $user_id = get_current_user_id();
            $result = Memy_Safe_Upload::delete_file( $file_name, $user_id );

            if ( is_wp_error( $result ) ) {
                wp_send_json_error( array(
                    'message' => $result->get_error_message(),
                ) );
            }

            wp_send_json_success( array(
                'message' => 'Datei erfolgreich gelöscht.',
            ) );
        }

        /**
         * AJAX Handler: Datei herunterladen
         */
        public static function handle_download_file() {
            // Sicherheit prüfen
            check_ajax_referer( 'memy_download_file', 'nonce' );

            // Benutzer authentifizieren
            if ( ! is_user_logged_in() ) {
                wp_send_json_error( array(
                    'message' => 'Benutzer nicht authentifiziert.',
                ) );
            }

            $file_name = isset( $_POST['file_name'] )
                ? sanitize_text_field( $_POST['file_name'] )
                : '';

            $mode = isset( $_POST['mode'] )
                ? sanitize_text_field( $_POST['mode'] )
                : 'download';

            if ( empty( $file_name ) ) {
                wp_send_json_error( array(
                    'message' => 'Dateiname erforderlich.',
                ) );
            }

            $user_id = get_current_user_id();
            
            // Download durchführen
            Memy_Safe_Upload::download_file( $file_name, $user_id, $mode );
        }
    }

    // Initialisierung
    Memy_Safe_Upload_AJAX::init();
}
