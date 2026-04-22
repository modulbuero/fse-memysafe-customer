<div class='setup-project-data full-height' id='setup-project-new'>
    <div class="spalte inner-main-heading">
        <h3>
            <i class="mmsi-icon projekte"></i>
            Projekte
        </h3>
    </div>

    <?php 
    /** 
     *  Daten für die Select-Felder holen
     * */ 
    $user_id            = get_current_user_id();
    //Get Vertreter-Liste für Select-Feld
    $vertreter_list     = get_user_meta($user_id, 'vertreter_list', true);
    $vertreter_option   = [];
    $vertreter_option[] = 'Vertreter wählen';
    if(!empty($vertreter_list) && is_array($vertreter_list)){
        foreach ($vertreter_list as $id => $daten) {
            if (strpos($id, 'vertreter_') === 0 && isset($daten['name'])) {
                $id_var   = $id;
                $name_var = $daten['name'];
                $vertreter_option[$id_var] = $name_var;
            }
        }
    }else{
        $vertreter_option   = [];
        $vertreter_option[] = 'Keine Vertreter hinterlegt';
    }
        
    //Get Notfallkontakt-Liste für Select-Feld
    $notfallkontakt_option   = [];
    $notfallkontakt_option[] = 'Notfallkontakt wählen';
    foreach (range(1, 3) as $i): 
        $person_email   = get_user_meta(get_current_user_id(), 'contact-person-'.$i, true)['email'] ?? '';
        if(!empty($person_email)){
            $notfallkontakt_option['contact-person-'.$i] = $person_email;
        }
    endforeach;
    if (count($notfallkontakt_option) === 1){
        $notfallkontakt_option   = [];
        $notfallkontakt_option[] = 'Kein Notfallkontakt hinterlegt';
    }

    ?> 
    <div class="overflow-wrapper">
        <div class="spalte project-data-show-hide"><p>Hinterlegte Informationen</p> <i class="mmsi-icon bearbeiten"></i></div>

        <div class="project-data-container">
            <?php addInput('Projektinformationen', '', 'project-name', 'Name, Projektnummer, Aktenzeichen,…'); ?>

            <div class="project-data-kunde">
                <?php 
                addInput('Kunde, Patient, Klient, Mandant', '', 'project-mandant', 'Name');
                addInput('', '', 'project-mandant-ansprechpartner', 'Ansprechpartner');
                echo '<div class="spalte input-wrapper">';
                    addInput('', '', 'project-mandant-telefon', 'Telefon', 'number');
                    addInput('', '', 'project-mandant-mobile', 'Mobile', 'number');
                echo '</div>';
                addInput('', '', 'project-mandant-email', 'E-Mail', 'email');
                ?>
            </div>
            
            <div class="spalte input-wrapper">
                <?php
                addCheckboxGroup('Projektstatus', [
                    'Geplant'       => 'Geplant',
                    'Laufend'       => 'Laufend',
                    'Abgeschlossen' => 'Abgeschlossen',
                ], 'Geplant', 'project-status');

                addTextarea('', '', 'project-anmerkung', 'Anmerkung_', 5);
                ?>
            </div>

            <div>
                <?php 
                echo '<div class="spalte input-wrapper">';
                    addInput('Andere beteiligte und/oder Dienstleister', '', 'project-dienstleister-name', 'Name_');
                    addInput(' ', '', 'project-dienstleister-funktion', 'Funktion_');
                echo '</div>';

                addInput('Datenzugriff Speicherort', '', 'project-dateizugriff', 'Speicherort_');
                ?>
            </div>
        </div>

        <div class="spalte project-data-show-hide"><p>Kontakte</p> <i class="mmsi-icon bearbeiten"></i></div>

        <div class="project-data-container">
            <div id="project-kontakt-container">
                <?php addSelect('Kontakt zuweisen (Optional)', $notfallkontakt_option , '', 'project-notfallkontakt'); ?>
            </div>

            <div id="project-vertreter-container">
                <?php addSelect('Vertretung', $vertreter_option , '', 'project-vertreter'); ?>
            </div>
            
            <?php addTextarea('Anmerkungen', '', 'project-anmerkungen', 'Anmerkungen_', 8); ?>
        </div>

    </div>

    <?php saveDeleteButton('project') ?>
    
    <?php deletePopup('delete-project', 'Projekt löschen'); ?>
</div>