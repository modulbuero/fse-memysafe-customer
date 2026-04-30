<div class="spalte inner-main-heading">
    <h3>Ersteinrichtung</h3>
</div>
<div class="overflow-wrapper full-height">
     <h4>
        Anweisung für den Notfallkontakt
    </h4>
    
    <p>Zwei Angaben sind entscheidend. Sie ermöglichen anderen, im Bedarfsfall zu handeln.</p>
    <div id="checkvalues-safeinfo" class="txt-distance-bottom">
        <?php 
        $quest_1 = '* Wo befinden sich deine wichtigsten Unterlagen und Informationen?';
        addInput($quest_1, '', 'upload-txt-1');
        $quest_2 = '* Wie kann auf deine digitalen Daten zugegriffen werden? (Keine Passwörter angeben.)';
        addInput($quest_2, '', 'upload-txt-2');
        ?>
    </div>
    <br>
    <p>Zusätzliche Hilfsinformationen</p>
    <div id="checkvalues-safeinfo-soft" class="txt-distance-bottom">
        <?php 
        $quest_3 = 'Wie kann deine Buchhaltung eingesehen werden?';
        addInput($quest_3, '', 'upload-txt-3');
        $quest_4 = 'Wer kann beim Zugang zu wichtigen Informationen unterstützen?';
        addInput($quest_4, '', 'upload-txt-4');
        $quest_5 = 'Gibt es etwas, das zuerst geklärt oder gesichert werden sollte?';
        addInput($quest_5, '', 'upload-txt-5');
        $quest_6 = 'Welche laufenden Verpflichtungen dürfen nicht übersehen werden?';
        addInput($quest_6, '', 'upload-txt-6');
        $quest_7 = 'Gibt es wichtige Hinweise, die unbedingt beachtet werden müssen?';
        addInput($quest_7, '', 'upload-txt-7');
        ?>
    </div>
    <div class="spalte safe-info-save-wrapper" style="justify-content: flex-end;padding-top: 20px;">
        <button id="safe-info-save">Informationen speichern <i class="mmsi-icon speichern"></i></button>
    </div>
    
</div>
<?php 
    firstStepNavi('6');
?>