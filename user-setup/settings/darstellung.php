<?php 
/**
 * Title: Einstellungen - Daten anzeigen und bearbeiten
 * Nutzt die MemyUserDataEditor zum speichern
 */
$opt_clockstyles = [
    'analog' => 'Analog',
    'digital' => 'Digital',
];

$current_user = wp_get_current_user();
$opt_clockstyle = get_user_meta($current_user->ID, 'mmsi_clockstyle', true) ?: 'analog';
$opt_darkmode   = get_user_meta($current_user->ID, 'mmsi_darkmode', true);
$opt_fontsize   = '';
?>

<div class="spalte inner-main-heading">
    <h3><i class='mmsi-icon setting'></i> Darstellung</h3>
</div>

<div class="overflow-wrapper full-height profile-bearbeiten-wrap settings-labels">
    <?php 
    wp_nonce_field('user_data_nonce', '_wpnonce');

    addRadioGroup('Uhrdarstellung', $opt_clockstyles, $opt_clockstyle, 'clockstyle');

    addCheckbox('DARK MODE',$opt_darkmode,'opt_darkmode'); 
    ?>
</div>

<button id="einstellung-darstellung-save" class="save-wrapper short-button"><i class="mmsi-icon speichern"></i>Änderungen speichern</button>