
    <div class="spalte inner-main-heading">
        <h3>Ersteinrichtung abschließen</h3>
    </div>
    <div class="overflow-wrapper full-height settings-labels">
        <input type="hidden" name="first_settings" value="done">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('save_first_settings')); ?>" id="fsettingn-wp">
        
        <p>
            Dein MMSI ist jetzt eingerichtet.
        </p>
        <p>
            Du kannst deine Angaben jederzeit über das Dashboard anpassen und ergänzen.
        </p>
        
    </div>

    <div class="spalte final-btns">
        <?php firstStepNavi('9',false, true);?>
        <button id="save-first-settings" type="submit" class="button">Zum Dashboard und abschließen <i class="mmsi-icon dashboard"></i></button>
    </div>
    