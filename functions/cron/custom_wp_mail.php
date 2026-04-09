<?php
/**
 * SMTP Konfiguration für wp_mail()
 */

/**
 * Konfiguriert PHPMailer für den Versand über einen dedizierten SMTP-Server.
 */
function memy_custom_smtp_setup($phpmailer) {
    // Prüfen, ob die benötigten Konstanten in der wp-config.php definiert sind
    if ( ! defined( 'SMTP_HOST' ) || ! defined( 'SMTP_USER' ) || ! defined( 'SMTP_PASS' ) ) {
        return;
    }

    // PHPMailer Konfiguration
    $phpmailer->isSMTP();
    $phpmailer->SMTPAutoTLS = true; 
    $phpmailer->Host       = SMTP_HOST;
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = defined( 'SMTP_PORT' ) ? SMTP_PORT : 587;
    $phpmailer->Username   = SMTP_USER;
    $phpmailer->Password   = SMTP_PASS;
    $phpmailer->SMTPSecure = defined( 'SMTP_SECURE' ) ? SMTP_SECURE : 'tls';

    // Optional: Absenderadresse erzwingen (hilft gegen Spam-Einstufung)
    if ( defined( 'SMTP_FROM' ) ) {
        $phpmailer->From = SMTP_FROM;
    }
    if ( defined( 'SMTP_FROM_NAME' ) ) {
        $phpmailer->FromName = SMTP_FROM_NAME;
    }
}
add_action('phpmailer_init', 'memy_custom_smtp_setup');

/**
 * Stellt sicher, dass die Absender-E-Mail-Adresse mit dem SMTP-Account übereinstimmt.
 */
add_filter('wp_mail_from', function($email) {
    return defined( 'SMTP_FROM' ) ? SMTP_FROM : $email;
});

/**
 * Stellt sicher, dass der Absender-Name korrekt gesetzt ist.
 */
add_filter('wp_mail_from_name', function($name) {
    return defined( 'SMTP_FROM_NAME' ) ? SMTP_FROM_NAME : $name;
});

/**
 * Protokolliert Fehler beim E-Mail-Versand.
 */
add_action('wp_mail_failed', function($wp_error) {
    error_log('wp_mail failure: ' . $wp_error->get_error_message());
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('wp_mail error details: ' . print_r($wp_error->get_error_data(), true));
    }
});

// Beispielhafter Test-Aufruf (auskommentiert):
// wp_mail('test@empfaenger.de', 'SMTP Test', 'Dies ist eine Testnachricht über SMTP.');
?>
