<?php 
/**
 * Prüft ob ein Benutzer Adminrechte besitzt und die E-Mail webmaster@modulbuero.com hat
 *
 * @param int|null $user_id Optional. Benutzer-ID. Wenn nicht angegeben, wird der aktuelle Benutzer geprüft.
 * @return bool True wenn Benutzer Admin ist UND die korrekte E-Mail hat, sonst false
 */
function is_webmaster_admin($user_id = null) {
    // Wenn keine User-ID angegeben, aktuellen Benutzer verwenden
    if ($user_id === null) {
        $user_id = get_current_user_id();
    }
    
    // Prüfen ob Benutzer existiert
    if (!$user_id) {
        return false;
    }
    
    // Benutzer-Objekt abrufen
    $user = get_user_by('id', $user_id);
    
    // Prüfen ob Benutzer existiert
    if (!$user) {
        return false;
    }
    
    // Prüfen ob Benutzer Admin-Rechte hat
    $is_admin = user_can($user_id, 'administrator');
    
    // Prüfen ob E-Mail-Adresse korrekt ist
    $has_correct_email = ($user->user_email === 'webmaster@modulbuero.com');
    
    // Beide Bedingungen müssen erfüllt sein
    return $is_admin && $has_correct_email;
}

/**
 * Alternative Funktion die zusätzlich den aktuellen Benutzer direkt prüft
 *
 * @return bool True wenn aktueller Benutzer Admin ist UND die korrekte E-Mail hat
 */
function current_user_is_webmaster_admin() {
    return is_webmaster_admin();
}

/**
 * Beispiel-Hook: Funktion nur für Webmaster-Admin ausführen
 */
function webmaster_only_action() {
    if (is_webmaster_admin()) {
        // Hier Code einfügen, der nur für den Webmaster-Admin ausgeführt werden soll
        error_log('Webmaster-Admin Aktion ausgeführt');
    }
}

/**
 * Beispiel: Admin-Notice nur für Webmaster anzeigen
 */
function webmaster_admin_notice() {
    if (is_webmaster_admin()) {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p>Hallo Webmaster! Du hast spezielle Admin-Rechte.</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'webmaster_admin_notice');

/**
 * Sicherheitsfunktion: Bestimmte Aktionen nur für Webmaster-Admin erlauben
 *
 * @param string $capability Die zu prüfende Berechtigung
 * @return bool
 */
function webmaster_can($capability = 'administrator') {
    return is_webmaster_admin() && current_user_can($capability);
}
?>