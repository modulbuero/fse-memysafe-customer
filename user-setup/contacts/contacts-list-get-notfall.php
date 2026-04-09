<div class='spalte inner-main-heading'>
    <i class="mmsi-icon kontakte"></i>
    <h3>Kontakte</h3>
</div>

<?php 
/**
*   Zeigt nur Kontakt Status
*   Leitet weiter zur Bearbeitung und Ansicht
**/
foreach (range(1, 3) as $i): 

    $person_email   = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['email'] ?? '';
    $person_status  = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['status'] ?? '';
    
    ?>
        
    <div data-goto="contact-person-<?php echo $i; ?>" class="contact-person-mail memy-button goto-btn" data-step="4">
        <i class="bi bi-people-fill"></i>
        <h6><?php echo $i; ?>. Notfallkontakt</h6>
        <i class="bi bi-caret-right-fill"></i>        
    </div>

    <p id="status-contact-person-<?php echo $i; ?>" class="status-contact-person memy-short-info">
        <?php 
        if(!empty($person_email)){
            if($person_status != 'aktiv') {
                echo 'Ausstehend';
            }else{
                echo "Aktiv";   
            }
        }else{
            echo 'Nicht benannt';
        }        
        ?>
    </p>

<?php endforeach; ?>