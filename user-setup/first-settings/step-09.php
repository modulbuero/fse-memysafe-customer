
    <div class="spalte inner-main-heading">
        <h3>Ersteinrichtung abschließen</h3>
    </div>
    <div class="overflow-wrapper full-height settings-labels">
        <input type="hidden" name="first_settings" value="done">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('save_first_settings')); ?>" id="fsettingn-wp">
        
        <p>
            Einrichtung bereit zur Aktivierung<br>
            Deine Grundeinrichtung ist abgeschlossen.<br>
            Erst mit dem Klick auf „Abschließen“ wird:<br>
        </p>
        <ul>
            <li>dein Timer aktiviert</li>
            <li>MMSI wirksam</li>
            <li>und deine Notfalllogik gestartet</li>
        </ul>
        
    </div>

    <div class="spalte final-btns">
        <?php firstStepNavi('9',false, true);?>
        <button id="save-first-settings" type="submit" class="button">Abschließen <i class="mmsi-icon pfeil"></i></button>
    </div>
    