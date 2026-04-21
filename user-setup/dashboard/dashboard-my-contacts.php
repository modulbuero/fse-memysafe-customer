<?php 
/**
 * Title: The Dashboard Tile Contacts
 */
?>

<div class="dashboard-item" id="memy-dashboard-my-contacts" data-user-id="<?php echo esc_html( $user_ID ); ?>">

    <div class="item-headline spalte" data-goto="manage-contacts">
        <i class="mmsi-icon kontakte"></i>
        <h2>Kontakte</h2>
        <i class="mmsi-icon weiter"></i>
    </div>

    <div class="item-content">        
        <?php
        $user_id    = getAdminUserID();
        $kontakte   = [];
        $vertrauensperson = get_user_meta($user_id, 'contact-person-4', true);
        
        if(!empty(get_user_meta($user_id, 'contact-person-1', true))):
            // Bestimme die maximale Anzahl Notfallkontakte
            $max_notfall = !empty($vertrauensperson['email']) ? 2 : 3;
        
            // Notfallkontakte hinzufügen
            for ($i = 1; $i <= $max_notfall; $i++) {
                $contact = get_user_meta($user_id, 'contact-person-' . $i, true);
                if (!empty($contact['email'])) {
                    $contact['pers_number'] = 'contact-person-' . $i;
                    $kontakte[] = $contact;
                }
            }

            // Vertrauensperson hinzufügen, falls vorhanden
            if (!empty($vertrauensperson['email'])) {
                $vertrauensperson['pers_number'] = 'vertrauensperson';
                $kontakte[] = $vertrauensperson;
            }

            // Ausgabe
            foreach ($kontakte as $kontakt) {
                $status = contactIsActive($kontakt['email']);
                if (!empty($kontakt['name'])) {
                    $goto = $kontakt['pers_number'];
                    echo "<div class='spalte dash-item goto-btn' data-goto='".$goto."'>
                        <i class='mmsi-icon kontakt'></i>
                        <p>" . esc_html($kontakt['name']) . "</p>
                        <i style='white-space: nowrap;'>$status</i>
                    </div>";
                }else{
                    echo "<div class='spalte dash-item'>
                        <i class='mmsi-icon kontakt'></i>
                        <p>Kein Name hinterlegt (" . esc_html($kontakt['email']) . ")</p>
                        <i>$status</i>
                    </div>";
                }
                
            }
        else:
            echo "<p>Keine Notfallkontakte hinterlegt.</p>";
        endif;
        ?>
    </div>
</div>
