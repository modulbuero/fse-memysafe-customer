
    <div class="spalte inner-main-heading">
        <h3>Ersteinrichtung</h3>
    </div>
    <div class="overflow-wrapper full-height settings-labels">
        <p>
            Infotext
        </p>
        <p>
            Lorem Ipsum -..-
        </p>
        <input type="hidden" name="first_settings" value="done">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('save_first_settings')); ?>" id="fsettingn-wp">
        
        <p>Bitte bestätige deine Erst-Einstellungen, damit wir dein Dashboard vollständig aktivieren können.</p>
        
    </div>

    <div class="spalte final-btns">
        <?php firstStepNavi(false, true);?>
        <button id="save-first-settings" type="submit" class="button">Einstellungen abschließen</button>
    </div>
    