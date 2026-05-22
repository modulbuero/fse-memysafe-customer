<div class='spalte inner-main-heading'>
    <i class="mmsi-icon kontakte"></i>
    <h3>Kontakte</h3>
</div>

<?php 
$order = [];

// Zuerst prüfen und sortieren
foreach (range(1, 3) as $i) {

    $person = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true);

    // Bedingung für "nach oben"
    if (!empty($person['email'])) {
        array_unshift($order, $i); // vorne einfügen
    } else {
        $order[] = $i; // normal hinten
    }
}

/**
*   Zeigt nur Kontakt Status
*   Leitet weiter zur Bearbeitung und Ansicht
**/
foreach ($order as $i): 

    $person_email   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['email'] ?? '';
    $person_status  = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['status'] ?? '';
    $person_fname   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['first_name'] ?? '';
    $person_lname   = get_user_meta(getAdminUserID(), 'contact-person-'.$i, true)['last_name'] ?? '';
    $trenner        = ($person_email) ? ' | ' : '';
    ?>
        
    <div data-goto="contact-person-<?php echo $i; ?>" class="contact-person-mail memy-button goto-btn" data-step="4">
        <i class="mmsi-icon kontakt"></i>
        <h6>Notfallkontakt  <?php echo $trenner ." ". $person_fname ." ". $person_lname; ?></h6>
        <i class="mmsi-icon pfeil"></i>        
    </div>

    <p id="status-contact-person-<?php echo $i; ?>" class="status-contact-person memy-short-info">
        <?php 
        if(!empty($person_email)){
            echo contactIsActive($person_email);
        }else{
            echo 'Nicht benannt';
        }        
        ?>
    </p>

<?php endforeach; ?>