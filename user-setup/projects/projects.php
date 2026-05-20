<div class='setup-project-data full-height' id='setup-project-new'>
    <div class="spalte inner-main-heading">
        <h3>
            <i class="mmsi-icon projekte"></i>
            Projekte
        </h3>
    </div>

    <div class="overflow-wrapper">
        <div class="spalte project-data-show-hide"><p style="display:flex;gap:10px"><i class="mmsi-icon informationen"></i> Hinterlegte Informationen</p> <i class="mmsi-icon bearbeiten"></i></div>

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
            
            <div class="spalte input-wrapper project-status-wrap">
                <?php
                addRadioGroup('Projektstatus', [
                    'Geplant'       => 'Geplant',
                    'Laufend'       => 'Laufend',
                ], 'Geplant', 'project-status');

                addTextarea('', '', 'project-anmerkung', 'Anmerkung_', 5);
                ?>
            </div>

            
            <?php 
            echo '<div class="spalte input-wrapper">';
                addInput('Andere Beteiligte und/oder Dienstleister', '', 'project-dienstleister-name', 'Name_');
                addInput('&nbsp;', '', 'project-dienstleister-funktion', 'Funktion_');
            echo '</div>';

            addInput('Datenzugriff, Speicherort', '', 'project-dateizugriff', 'Speicherort_');
            ?>
        
        </div>

        <div class="spalte project-data-show-hide"><p style="display:flex;gap:10px"><i class="mmsi-icon kontakte"></i> Kontakte</p> <i class="mmsi-icon bearbeiten"></i></div>

        <div class="project-data-container">
            <div id="project-kontakt-container">
                <?php addSelect('Kontakt zuweisen (Optional)', '' , '', 'project-notfallkontakt'); ?>
            </div>

            <div id="project-vertreter-container">
                <?php addSelect('Vertretung', '' , '', 'project-vertreter'); ?>
            </div>
            
            <?php addTextarea('Anmerkungen', '', 'project-anmerkungen', 'Anmerkungen_', 8); ?>
        </div>

    </div>

    <?php saveDeleteButton('project') ?>
    
    <?php deletePopup('delete-project', 'Projekt löschen'); ?>
</div>