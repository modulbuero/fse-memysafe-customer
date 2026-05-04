<div class='spalte inner-main-heading'>
    <i class='mmsi-icon kontakte'></i>
    <h3>Vertrauensperson</h3>
    <?php
    $infotxt_vp = "ist jemand, der dir nahesteht und im Alltag unterstützend wirken kann – z.B. durch physischen Zugang zu Wohnung, Büro oder Doku-menten. Sie muss nicht digital eingebunden sein, kann aber bei Bedarf Zugriff auf bestimmte SafenInhalte erhalten.";
    infoPopup($infotxt_vp, "VERTRAUENSPERSON");
    ?>
</div>

<div class='setup-contact-person-data full-height inner-input-wrapper' id='setup-contact-person-4' data-target='vertrauenskontakt'>
    <?php 
    $i = 4; //Vertrauensperson
    //Variable
    $v_person_name    = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['name'] ?? '';
    $v_person_email   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['email'] ?? '';
    $v_person_tel     = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['tel'] ?? '';
    $v_person_firma   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['firma'] ?? '';
    ?>        
    <div>
        <?php 
        addInput('', 'Vertrauensperson', 'contact-typ-'.$i, '','hidden'); 
        addInput('Name', $v_person_name, 'contact-name-'.$i);
        addInput('E-Mail-Adresse', $v_person_email, 'contact-email-'.$i, 'email');
        addInput('Telefonnummer', $v_person_tel, 'contact-tel-'.$i, 'number');
        addInput('Firma (Optional)', $v_person_firma, 'contact-firma-'.$i);            
        ?>
    </div>

    <?php 
    saveDeleteButton('contact');
    deletePopup('delete-contact', 'Vertrauensperson ' . $v_person_name . ' löschen'); ?>
</div>