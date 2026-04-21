<div class='spalte inner-main-heading'>
    <i class="mmsi-icon kontakte"></i>
    <h3>Kontakte</h3>
</div>

<?php 
foreach (range(1, 3) as $i): 
    //Variable
    $person_email   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['email'] ?? '';
    $person_typ     = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['typ'] ?? '';
    $person_name    = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['name'] ?? '';
    $person_tel     = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['tel'] ?? '';
    $person_firma   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['firma'] ?? '';
    $person_status  = contactIsActive($person_email);
    
    #$person_is_main = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['hauptkontakt'] ?? '';
    $mmsi_can       = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['mmsi_can'] ?? '';    

    echo "<div class='setup-contact-person-data full-height' data-target='contact-person-$i' id='setup-contact-person-$i' data-step='5'>
        <div class='spalte inner-main-heading'><h3>$i. Notfallkontakt</h3></div>
        ";
        if($i == 1){
            echo "Hauptkontakt";
        }
        ?>        
        <div class="contact-data">
            <?php 
            addInput('Name', $person_name, 'contact-name-'.$i);
            addInput('E-Mail-Adresse', $person_email, 'contact-email-'.$i, 'email');
            addInput('Telefonnummer', $person_tel, 'contact-tel-'.$i, 'number');
            addInput('Firma (Optional)', $person_firma, 'contact-firma-'.$i);            
            addSelect('Status', ['Aktiv' => 'Aktiv', 'Ausstehend' => 'Ausstehend'], $person_status, 'contact-person-status-'.$i, false);
            ?>
        </div>
        
        <br>
        
        <?php 
        addCheckbox('Darf MMSI den Safe öffnen?', $mmsi_can, 'contact-mmsi-can-'.$i); 
        addInput('', 'Notfallkontakt', 'contact-typ-'.$i, '','hidden');     
        ?>

        <button class="send-invitation"><i class='mmsi-icon speichern'></i> Einladung senden</button>
        
        <?php 
        saveDeleteButton('contact');
        deletePopup('delete-contact', 'Kontaktperson ' . $person_name . ' löschen'); 
        ?>
    <?php 
    echo "</div>";
endforeach; ?>

<?php # require_once get_stylesheet_directory() . '/user-setup/contacts/contact-edit.php'; ?>
