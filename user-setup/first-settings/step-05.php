
    <div class="spalte inner-main-heading">
        <h3>Ersteinrichtung</h3>
    </div>
    <div class="overflow-wrapper full-height settings-labels">
         <h4>
            Dein Safe
        </h4>
        <p class="txt-distance-bottom">
            Lege fest, wie andere im Ernstfall handlungsfähig werden. Der Safe enthält keine Zugangsdaten. Er beschreibt, wo Informationen liegen, wie darauf zugegriffen wird und wer dabei unterstützen kann. 
            <br>
            Du kannst unsere Vorlagen nutzen, um deine Informationen strukturiert vorzubereiten – jetzt oder später.
        </p>
        
        <button class="half-width">
            <a href="your-link-here" class="button" target="_blank">Vorlage Herunterladen <i class="mmsi-icon download"></i></a> 
        </button>

        <p>Wie möchtest du fortfahren?</p>
        <?php 
        #addCheckbox('Ich nutze die Vorlagen und lade meine Informationen später hoch','','mmsi-uploadcheck');
        $auswahl = [
            'mmsi-file-later'  => 'Ich nutze die Vorlagen und lade meine Informationen später hoch',
            'mmsi-file-entry'   => 'Ich hinterlege die wichtigsten Informationen jetzt direkt',
            'mmsi-file-completed' => 'Ich habe die Vorlagen bereits ausgefüllt und lade sie jetzt hoch'
            ];
        addRadioGroup('', $auswahl, '', 'mmsi-uploadcheck');
        ?>

    </div>

    <?php firstStepNavi('5') ?>