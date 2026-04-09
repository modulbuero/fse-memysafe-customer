/**
 * AJAX Upload Manager - HTML Template & Integration Guide
 * 
 * Diese Datei zeigt, wie man die AJAX Upload Komponente integriert
 */

// ============================================================================
// 1. EINFACHE INTEGRATION (HTML/PHP)
// ============================================================================

?>

<!-- HTML Template für Upload Zone -->
<div class="memy-safe-upload-container">
    
    <!-- Upload Zone -->
    <div id="memy-upload-zone" class="memy-upload-zone">
        <p class="memy-upload-zone-text">Dateien hier ablegen oder klicken zum Hochladen</p>
        <p class="memy-upload-zone-hint">Unterstützte Formate: PDF, JPG, PNG, DOC, DOCX</p>
        <button type="button" class="memy-upload-trigger">Datei auswählen</button>
    </div>

    <!-- Versteckter File Input -->
    <input type="file" id="memy-file-input" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">

    <!-- Upload Progress -->
    <div id="memy-upload-progress"></div>

    <!-- Datei-Liste -->
    <div class="memy-file-list-wrapper">
        <h3>Meine Dateien</h3>
        <div id="memy-file-list"></div>
    </div>

</div>

<!-- CSS einbinden -->
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/scss/safe-upload.css">

<?php

// ============================================================================
// 2. KONFIGURIEREN VON MIME-TYPEN UND GRÖSSE
// ============================================================================

?>

<!-- Mit Konfiguration (optional) -->
<div class="memy-safe-upload-container">
    
    <!-- Erlaubte MIME-Typen -->
    <input type="hidden" id="memy-allowed-types" value="application/pdf,image/jpeg,image/png">
    
    <!-- Maximale Dateigröße in Bytes (z.B. 10MB) -->
    <input type="hidden" id="memy-max-size" value="<?php echo 10 * 1024 * 1024; ?>">
    
    <!-- Upload Zone -->
    <div id="memy-upload-zone" class="memy-upload-zone">
        <p class="memy-upload-zone-text">Dateien hier ablegen</p>
        <button type="button" class="memy-upload-trigger">Datei auswählen</button>
    </div>

    <input type="file" id="memy-file-input" multiple>
    <div id="memy-upload-progress"></div>
    <div id="memy-file-list"></div>

</div>

<?php

// ============================================================================
// 3. PHP FUNKTIONEN FÜR SICHERE INTEGRI INTEGRATION
// ============================================================================

/**
 * Sichere Upload-Komponente rendern
 * 
 * @param array $config
 * @return void
 */
function memy_render_safe_upload_component( $config = array() ) {
    
    // Standard-Konfiguration
    $defaults = array(
        'allowed_types' => 'application/pdf,image/jpeg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'max_size'      => 10 * 1024 * 1024, // 10MB
        'zone_text'     => 'Dateien hier ablegen oder klicken zum Hochladen',
        'zone_hint'     => 'Unterstützte Formate: PDF, JPG, PNG, DOC, DOCX',
        'button_text'   => 'Datei auswählen',
        'accept'        => '.pdf,.jpg,.jpeg,.png,.doc,.docx',
    );
    
    $config = wp_parse_args( $config, $defaults );
    
    // Nur für eingeloggte Benutzer
    if ( ! is_user_logged_in() ) {
        echo '<p>Bitte melden Sie sich an.</p>';
        return;
    }
    
    ?>
    <div class="memy-safe-upload-container">
        
        <!-- Konfiguration -->
        <input type="hidden" id="memy-allowed-types" value="<?php echo esc_attr( $config['allowed_types'] ); ?>">
        <input type="hidden" id="memy-max-size" value="<?php echo esc_attr( $config['max_size'] ); ?>">
        
        <!-- Upload Zone -->
        <div id="memy-upload-zone" class="memy-upload-zone">
            <p class="memy-upload-zone-text"><?php echo esc_html( $config['zone_text'] ); ?></p>
            <p class="memy-upload-zone-hint"><?php echo esc_html( $config['zone_hint'] ); ?></p>
            <button type="button" class="memy-upload-trigger"><?php echo esc_html( $config['button_text'] ); ?></button>
        </div>
        
        <!-- Versteckter File Input -->
        <input type="file" id="memy-file-input" multiple accept="<?php echo esc_attr( $config['accept'] ); ?>">
        
        <!-- Upload Progress -->
        <div id="memy-upload-progress"></div>
        
        <!-- Datei-Liste -->
        <div class="memy-file-list-wrapper">
            <h3>Meine Dateien</h3>
            <div id="memy-file-list"></div>
        </div>
        
    </div>
    <?php
}

/**
 * Beispiel: In einem Template aufrufen
 * 
 * memy_render_safe_upload_component( array(
 *     'allowed_types' => 'application/pdf,image/jpeg',
 *     'max_size'      => 5 * 1024 * 1024, // 5MB
 *     'zone_text'     => 'Bitte laden Sie nur PDF-Dateien hoch',
 * ) );
 */

// ============================================================================
// 4. AJAX ENDPOINTS ÜBERSICHT
// ============================================================================

/**
 * Verfügbare AJAX Endpoints:
 * 
 * memy_upload_file
 *   - POST
 *   - Parameter: file, allowed_types, max_size, nonce
 *   - Response: { success: true, data: { message, file_id, file_name, original_name } }
 * 
 * memy_get_files
 *   - POST
 *   - Parameter: nonce
 *   - Response: { success: true, data: { files: [], count: 0 } }
 * 
 * memy_delete_file
 *   - POST
 *   - Parameter: file_name, nonce
 *   - Response: { success: true, data: { message } }
 * 
 * memy_download_file
 *   - POST (mit direktem Download)
 *   - Parameter: file_name, nonce
 */

// ============================================================================
// 5. JAVASCRIPT API REFERENZ
// ============================================================================

/**
 * JavaScript API (verfügbar als SafeUpload Objekt)
 * 
 * SafeUpload.uploadFile(file)
 *   - Lädt eine Datei hoch
 *   - Beispiel: SafeUpload.uploadFile(document.getElementById('memy-file-input').files[0])
 * 
 * SafeUpload.loadFileList()
 *   - Lädt die aktuelle Datei-Liste
 * 
 * SafeUpload.deleteFile(fileName)
 *   - Löscht eine Datei
 *   - Beispiel: SafeUpload.deleteFile('dokument.pdf')
 * 
 * SafeUpload.downloadFile(fileName)
 *   - Lädt eine Datei herunter
 *   - Beispiel: SafeUpload.downloadFile('dokument.pdf')
 * 
 * SafeUpload.showNotification(message, type)
 *   - Zeigt Benachrichtigung an
 *   - type: 'success', 'error', 'info'
 */

// ============================================================================
// 6. HOOKS FÜR ERWEITERUNG
// ============================================================================

/**
 * Custom Hooks:
 * 
 * 'memy_safe_upload_before_upload'
 *   - Wird vor dem Upload ausgelöst
 *   - Parameter: $file_data, $user_id
 *   - Beispiel:
 *     add_action('memy_safe_upload_before_upload', function($file_data, $user_id) {
 *         // Eigene Validierung
 *     }, 10, 2);
 * 
 * 'memy_safe_upload_after_upload'
 *   - Wird nach erfolgreichem Upload ausgelöst
 *   - Parameter: $result, $user_id
 * 
 * 'memy_safe_upload_before_delete'
 *   - Wird vor dem Löschen ausgelöst
 *   - Parameter: $file_name, $user_id
 */

// ============================================================================
// 7. SICHERHEIT & BEST PRACTICES
// ============================================================================

/**
 * Sicherheits-Features:
 * 
 * ✓ Nonce-Validierung für alle AJAX-Anfragen
 * ✓ Benutzer-Authentifizierung erforderlich
 * ✓ MIME-Type Validierung
 * ✓ Dateigröße-Limit
 * ✓ Path Traversal Prevention
 * ✓ XSS-Schutz (escapeHtml)
 * ✓ CSRF-Schutz durch wp_verify_nonce()
 * 
 * Best Practices:
 * - Verwende immer MIME-Type Whitelist
 * - Setze realistische Größenlimits
 * - Validiere auf Server-Seite
 * - Logge Upload-Aktivitäten
 * - Backup kritischer Dateien
 */

// ============================================================================
// 8. ERROR HANDLING
// ============================================================================

/**
 * Mögliche Fehler-Responses:
 * 
 * { success: false, data: { message: "Benutzer nicht authentifiziert." } }
 * { success: false, data: { message: "Keine Datei hochgeladen." } }
 * { success: false, data: { message: "Datei ist zu groß." } }
 * { success: false, data: { message: "Dateityp nicht erlaubt." } }
 * { success: false, data: { message: "Datei konnte nicht verschoben werden." } }
 * 
 * JavaScript Error Handler:
 * 
 * $.ajax({
 *     url: memySafeUpload.ajaxurl,
 *     type: 'POST',
 *     data: { ... },
 *     error: function(xhr, status, error) {
 *         console.error('AJAX Error:', error);
 *         SafeUpload.showNotification('Fehler: ' + error, 'error');
 *     }
 * });
 */

// ============================================================================
// 9. BEISPIEL: CUSTOM IMPLEMENTATION
// ============================================================================

/**
 * Hook für Custom Validierung
 */
add_filter( 'memy_safe_upload_allowed_types', function( $types ) {
    // Nur PDF erlauben
    return array( 'application/pdf' );
} );

/**
 * Hook nach erfolgreichem Upload
 */
add_action( 'memy_safe_upload_after_upload', function( $result, $user_id ) {
    // Logging
    error_log( "File uploaded by user $user_id: " . $result['original_name'] );
    
    // E-Mail Benachrichtigung
    wp_mail(
        get_user_by( 'id', $user_id )->user_email,
        'Datei hochgeladen',
        'Ihre Datei "' . $result['original_name'] . '" wurde hochgeladen.'
    );
}, 10, 2 );

<?php
