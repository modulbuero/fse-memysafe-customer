
<div class="spalte inner-main-heading">
    <h3>Ersteinrichtung</h3>
</div>
<div class="overflow-wrapper full-height settings-labels">
    <h4>
        Deine Profil
    </h4>
    <p>
        Lege die Grundlage für deine Erreichbarkeit fest.
    </p>
    <div id="checkvalues-kontakt">
        <?php 
        $current_user = wp_get_current_user();
        $user_metas   = get_user_meta($current_user->ID);       
        $strasze = (!empty($user_metas['strasze'][0])) ? $user_metas['strasze'][0] : "";
        $plz     = (!empty($user_metas['plz'][0])) ? $user_metas['plz'][0] : "";
        $ort     = (!empty($user_metas['ort'][0])) ? $user_metas['ort'][0] : "";
        $telefon = (!empty($user_metas['telefon'][0])) ? $user_metas['telefon'][0] : "";
        
        addInput('Straße', $strasze, 'strasze', 'Straße');
        addInput('PLZ', $plz, 'plz', 'PLZ', 'number');
        addInput('Ort', $ort, 'ort', 'Ort');
        addInput('Telefonnummer', $telefon, 'telefon', 'Telefonnummer', 'number');            
        ?>
    </div>
</div>

<?php firstStepNavi('3',true,true) ?>