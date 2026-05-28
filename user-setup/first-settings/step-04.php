
<div class="spalte inner-main-heading">
    <h3>Ersteinrichtung</h3>
</div>
<div class="overflow-wrapper full-height settings-labels">
    <h4>
        Notfallkontakt
    </h4>
    <p>
        Bestimme mindestens eine Person, die im Ernstfall informiert wird und handeln kann.
        <br>
        Für die Ersteinrichtung genügt ein Notfallkontakt. Weitere Kontakte kannst du später über dein Dashboard hinzufügen.
        Der Kontakt wird per E-Mail eingeladen und kann seine Rolle bestätigen.
    </p>
    <div id="checkvalues-kontakt">
        <div class="spalte">
        <?php     
            addInput('Vorname', '', 'contact_fname', 'Vorname');
            addInput('Nachname', '', 'contact_lname', 'Nachname');
        ?>
        </div>
        <?php 
        addInput('E-Mail-Adresse', '', 'contact_mail', 'E-Mail-Adresse');
        addInput('Telefonnummer', '', 'contact_tel', 'Telefonnummer');
        addInput('', 'Notfallkontakt', 'contact_typ', '','hidden'); 
        ?>  
    </div>          
</div>

<?php firstStepNavi('4') ?>