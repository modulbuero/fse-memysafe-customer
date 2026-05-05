<div class="spalte inner-main-heading">
    <h3>Erzeuge eine Informationsdatei im Safe</h3>
</div>

<div class="overflow-wrapper full-height settings-labels" id="anweisung-vom-safe">
    <h4>
        Anweisung für den Notfallkontakt
    </h4>

    <?php 
    inputFieldsTxtFile();
    ?>

    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('save_first_settings')); ?>" id="fsettingn-wp">
</div>

<div class='spalte safe-info-save-wrapper' style='justify-content: flex-end;padding-top: 20px;'>
    <button id='safe-info-save' class='from-safe-upload'>Informationen speichern <i class='mmsi-icon speichern'></i></button>
</div>