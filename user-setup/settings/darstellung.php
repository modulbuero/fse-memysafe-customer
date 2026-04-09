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
$opt_clockstyle = '';
$opt_darkmode   = '';
$opt_fontsize   = '';
?>

<h3>Einstellungen</h3>

<div class="overflow-wrapper full-height profile-bearbeiten-wrap">
    <?php
    wp_nonce_field('user_option_nonce', '_wpnonce'); 
    ?>
    
    <div class="settings-labels">
        <h4>Uhrdarstellung</h4>    
        <?php addCheckboxGroup('', $opt_clockstyles, 'Numerisch', 'clockstyle'); ?>

        <?php addCheckbox('DARK MODE',$opt_darkmode,'opt_darkmode'); ?>
    </div>
</div>