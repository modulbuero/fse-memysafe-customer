<?php 
/**
 * Title: Benutzerprofil - Daten anzeigen und bearbeiten
 * Nutzt die MemyUserDataEditor Klasse über den Shortcode
 */
?>

<h3>Profil bearbeiten</h3>

<div class="overflow-wrapper full-height profile-bearbeiten-wrap">
    <?php
    $current_user = wp_get_current_user();
    $user_metas   = get_user_meta($current_user->ID);          
    $webseite     = (!empty($user_metas['webseite'][0])) ? $user_metas['webseite'][0]:"";
    $beruf        = (!empty($user_metas['berufsbezeichnung'][0])) ? $user_metas['berufsbezeichnung'][0] : "";
    $firma        = (!empty($user_metas['firmenname'][0])) ? $user_metas['firmenname'][0] : "";

    wp_nonce_field('user_data_nonce', '_wpnonce'); 
    ?>
    <h4>Proffesionelles Profil</h4>

    <div class="spalte">
        <div id="profile-persoenliche-daten" class="settings-labels half-width">

            <h5>Kurzbeschreibung</h5>
            <?php addTextarea('', $current_user->description, 'description', 'Beschreibung'); ?>
            
            <h5>Webseite</h5>
            <?php addInput('', $webseite, 'webseite', 'Webseite'); ?>

            <h5>Berufsbezeichnung</h5>
            <?php addInput('', $beruf, 'berufsbezeichnung', 'Berufsbezeichnung'); ?>

            <h5>Firmenname</h5>
            <?php addInput('', $firma, 'firmenname', 'Firmenname'); ?>

            <h5>Skills</h5>
            
        </div>

        <div id="profile-persoenliche-daten-imgs">

        </div>
    </div>

</div>

<div class="spalte">
    <button id="user-data-save">Daten speichern</button>
    <span id="loading" style="display: none;">Speichere...</span>
</div>