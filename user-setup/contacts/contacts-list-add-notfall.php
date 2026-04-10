<div class='spalte inner-main-heading'>
    <i class="mmsi-icon kontakte"></i>
    <h3>Kontakte</h3>
</div>

<?php 
foreach (range(1, 3) as $i): 
    //Variable
    $person_email   = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['email'] ?? '';
    $person_typ     = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['typ'] ?? '';
    $person_name    = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['name'] ?? '';
    $person_tel     = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['tel'] ?? '';
    $person_firma   = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['firma'] ?? '';
    $person_status  = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['status'] ?? '';
    #$person_is_main = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['hauptkontakt'] ?? '';
    $mmsi_can       = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['mmsi_can'] ?? '';    

    echo "<div class='setup-contact-person-data full-height' data-target='contact-person-$i' id='setup-contact-person-$i' data-step='5'>
        <div class='spalte inner-main-heading'><h3>$i. Notfallkontakt</h3></div>
        ";
        if($i == 1){
            echo "Hauptkontakt";
        }
        ?>        
        <div>
            <?php 
            addInput('Name', $person_name, 'contact-name-'.$i);
            addInput('E-Mail-Adresse', $person_email, 'contact-email-'.$i, 'email');
            addInput('Telefonnummer', $person_tel, 'contact-tel-'.$i, 'number');
            addInput('Firma (Optional)', $person_firma, 'contact-firma-'.$i);            
            ?>
            
            <div class="selectbox">
                <label>
                    Status
                </label>
                <select class="contact-status" id="contact-person-status-<?php echo $i; ?>">
                    <option value="Aktiv" <?php echo ($person_status === 'Aktiv') ? 'selected' : ''; ?>>Aktiv</option>
                    <option value="Inaktiv" <?php echo ($person_status === 'Ausstehend') ? 'selected' : ''; ?>>Ausstehend</option>
                </select>
            </div>
        </div>
        
        <br>
        
        <?php addCheckbox('Darf MMSI den Safe öffnen?', $mmsi_can, 'contact-mmsi-can-'.$i); ?>

        <?php
        addInput('', 'Notfallkontakt', 'contact-typ-'.$i, '','hidden');     
        ?>
        
        <div class="spalte save-wrapper">
            <button id="save"><i class='mmsi-icon speichern'></i> Speichern</button>
            <button class="delete-btn-pop"><i class='mmsi-icon delete'></i> Löschen</button>
        </div>

        <?php deletePopup('delete-contact', 'Kontaktperson ' . $person_name . ' löschen'); ?>
    <?php 
    echo "</div>";
endforeach; ?>

<?php # require_once get_stylesheet_directory() . '/user-setup/contacts/contact-edit.php'; ?>
