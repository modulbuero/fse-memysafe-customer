<div class="spalte inner-main-heading">
    <h3>Ersteinrichtung</h3>
</div>
<div class="overflow-wrapper full-height">
     <h4>
        Anweisung für den Notfallkontakt
    </h4>
    <div class="memy-upload-wrapper">
        <!-- Dateien hochladen -->
        <div id="memy-upload-zone">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/icons/MMSI_Icon_UPLOAD.svg" alt="Upload Icon">
            <p>DATEI HIER ABLEGEN ODER AUSWÄHLEN</p>
            
            <button type="button" class="memy-upload-trigger"><i class='mmsi-icon upload'></i> Dateien auswählen</button>
            <p>Sie können Ihre Datei hierher ziehen oder über den Button auswählen.</p>
        </div>
        <input type="file" id="memy-file-input" multiple>
        <div id="memy-upload-progress"></div>
    </div> 
</div>
<?php 
    firstStepNavi('6');
?>