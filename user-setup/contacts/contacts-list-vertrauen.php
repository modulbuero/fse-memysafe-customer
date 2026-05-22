<div class='spalte inner-main-heading'>
    <i class='mmsi-icon kontakte'></i>
    <h3>Vertrauensperson</h3>
    <?php
    $infotxt_vp = "Person aus deinem persönlichen Umfeld, die dich im Ernstfall vor Ort unterstützen kann — zum Beispiel beim Zugang zu Wohnung, Büro, Unterlagen oder Geräten. Kann zusätzlich digital in MMSI eingebunden werden.<br>Mehr dazu im Glossar.";
    infoPopup($infotxt_vp, "VERTRAUENSPERSON");
    ?>
</div>

<div class='setup-contact-person-data full-height' id='setup-contact-person-4' data-target='vertrauenskontakt'>
    <?php 
    $i = 4; //Vertrauensperson
    //Variable
    $v_person_fname   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['first_name'] ?? '';
    $v_person_lname   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['last_name'] ?? '';
    $v_person_email   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['email'] ?? '';
    $v_person_tel     = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['tel'] ?? '';
    $v_person_firma   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['firma'] ?? '';
    ?>  
    <div class="inner-input-wrapper overflow-wrapper">      
    <div class="contact-data txt-distance-bottom">
        <div class="spalte">
            <?php 
            addInput('Vorname', $v_person_fname, 'contact-first_name-'.$i);
            addInput('Nachname', $v_person_lname, 'contact-last_name-'.$i);
            ?>
        </div>
        <?php 
        addInput('E-Mail-Adresse', $v_person_email, 'contact-email-'.$i, 'email');
        addInput('Telefonnummer', $v_person_tel, 'contact-tel-'.$i, 'number');
        addInput('Firma (Optional)', $v_person_firma, 'contact-firma-'.$i);
        addInput('', 'Vertrauensperson', 'contact-typ-'.$i, '','hidden'); 
        ?>   
    </div>

    <?php 
    if(get_current_user_id() == getAdminUserID() ): ?>
        <div class="spalte">
            <div><!--mmsi can --></div>
            <?php if(!email_exists($v_person_email)): ?>
                <button class="send-invitation" style="padding: 5px; font-size: 14px;">
                    <i class='mmsi-icon send'></i> Einladung senden
                </button>
            <?php endif; ?>
        </div>
    <?php 
    endif;
    echo '</div>';
    
    saveDeleteButton('contact');
    deletePopup('delete-contact', 'Vertrauensperson ' . $v_person_fname . ' ' . $v_person_lname . ' löschen'); ?>
</div>