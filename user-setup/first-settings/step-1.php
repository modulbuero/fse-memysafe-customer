
    <div class="spalte inner-main-heading">
        <h3>Ersteinrichtung</h3>
    </div>
    <div class="overflow-wrapper full-height settings-labels">
         <h4>
            Deine Adresse
        </h4>
        <p>
            Infotext
        </p>
        <p>
            Lorem Ipsum -..-
        </p>
        <h5>Adresse</h5>
        <div class="settings-labels checkvalues-adresse">
            <?php 
            $current_user = wp_get_current_user();
            $user_metas   = get_user_meta($current_user->ID);       
            $strasze = (!empty($user_metas['strasze'][0])) ? $user_metas['strasze'][0] : "";
            $plz     = (!empty($user_metas['plz'][0])) ? $user_metas['plz'][0] : "";
            $ort     = (!empty($user_metas['ort'][0])) ? $user_metas['ort'][0] : "";
            ?>
            <?php 
            addInput('', $strasze, 'strasze', 'Straße');
            addInput('', $plz, 'plz', 'PLZ', 'number');
            addInput('', $ort, 'ort', 'Ort');
            ?>
        </div>
    </div>

    <?php firstStepNavi(true,true) ?>