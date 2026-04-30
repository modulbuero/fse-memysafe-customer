
<div class="spalte inner-main-heading">
    <h3>Ersteinrichtung</h3>
</div>
<div class="overflow-wrapper full-height settings-labels">
    <h4>
        Der Notfallkontakt
    </h4>
    <p>
        Bestimme mindestens eine Person, die im Ernstfall informiert wird und handeln kann.
        <br>
        Die Kontakte werden per E-Mail eingeladen und können ihre Rolle bestätigen.
    </p>
    <div id="checkvalues-kontakt">
        <?php 
        $v_person_name  = get_user_meta(get_current_user_id(), 'contact-person-1', true)['name'] ?? '';
        $v_person_email = get_user_meta(get_current_user_id(), 'contact-person-1', true)['email'] ?? '';
        $v_person_tel   = get_user_meta(get_current_user_id(), 'contact-person-1', true)['tel'] ?? '';
        #$v_person_firma = get_user_meta(get_current_user_id(), 'contact-person-1', true)['firma'] ?? '';
            
        addInput('Name', $v_person_name, 'contact-name-1');
        addInput('E-Mail-Adresse', $v_person_email, 'contact-email-1', 'email');
        addInput('Telefonnummer', $v_person_tel, 'contact-tel-1', 'number');
        addInput('', 'Notfallkontakt', 'contact-typ-1', '','hidden'); 
        ?>  
    </div>          
</div>

<?php firstStepNavi('4') ?>