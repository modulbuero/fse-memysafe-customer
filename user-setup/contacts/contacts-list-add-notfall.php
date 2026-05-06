<div class='spalte inner-main-heading'>
    <i class="mmsi-icon kontakte"></i>
    <h3>Kontakte</h3>
</div>

<?php 
foreach (range(1, 3) as $i): 
    //Variable
    $person_email   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['email'] ?? '';
    $person_typ     = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['typ'] ?? '';
    $person_f_name  = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['first_name'] ?? '';
    $person_l_name  = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['last_name'] ?? '';
    $person_tel     = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['tel'] ?? '';
    $person_firma   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['firma'] ?? '';
    $person_status  = contactIsActive($person_email);
    
    #$person_is_main = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['hauptkontakt'] ?? '';
    $mmsi_can       = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['mmsi_can'] ?? '';    

    echo "<div class='setup-contact-person-data full-height' data-target='contact-person-$i' id='setup-contact-person-$i' data-step='5'>
        <div class='spalte inner-main-heading'><h3><i class='mmsi-icon kontakte'></i> Notfallkontakte</h3></div>
        ";
        
        echo "<h4>".$i.". Notfallkontakt</h4>";
        ?>        
        <div class="inner-input-wrapper">
        <div class="contact-data">
            <div class="spalte">
                <?php     
                addInput('Vorname', $person_f_name, 'contact-first_name-'.$i);
                addInput('Nachname', $person_l_name, 'contact-last_name-'.$i);
            ?>
            </div>
            <?php 
            addInput('E-Mail-Adresse', $person_email, 'contact-email-'.$i, 'email');
            addInput('Telefonnummer', $person_tel, 'contact-tel-'.$i, 'number');
            addInput('Firma (Optional)', $person_firma, 'contact-firma-'.$i);            
            //addSelect('Status', ['Aktiv' => 'Aktiv', 'Ausstehend' => 'Ausstehend'], $person_status, 'contact-person-status-'.$i, false);
            ?>
        </div>
        <br>
        <?php 
        addInput('', 'Notfallkontakt', 'contact-typ-'.$i, '','hidden'); 
        
        if(get_current_user_id() == getAdminUserID() ): ?>
            <div class="spalte">
                <?php addCheckbox('Darf MMSI den Safe öffnen?', $mmsi_can, 'contact-mmsi-can-'.$i); ?>
                <?php if(!email_exists($person_email)): ?>
                    <button class="send-invitation" style="padding: 5px; font-size: 14px;">
                        <i class='mmsi-icon speichern'></i> Einladung senden
                    </button>
                <?php endif; ?>
            </div>
        <?php 
        endif;
        echo '</div>';
        
        saveDeleteButton('contact');
        deletePopup('delete-contact', 'Kontaktperson ' . $person_f_name . ' ' . $person_l_name . ' löschen'); 
        ?>
    <?php 
    echo "</div>";
endforeach; ?>

<?php # require_once get_stylesheet_directory() . '/user-setup/contacts/contact-edit.php'; ?>
