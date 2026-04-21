
<div class='setup-kunden-data full-height' id='setup-kunden-new'>
    <div class='spalte inner-main-heading'>
        <i class='mmsi-icon kontakte'></i>
        <h3>Kunde</h3>
        <?php
        $infotxt_vp = "ist jemand, der dir nahesteht und im Alltag unterstützend wirken kann – z.B. durch physischen Zugang zu Wohnung, Büro oder Doku-menten. Sie muss nicht digital eingebunden sein, kann aber bei Bedarf Zugriff auf bestimmte SafenInhalte erhalten.";
        infoPopup($infotxt_vp, "KUNDE");
        ?>
    </div>
    <?php 
    // Daten laden wenn Bearbeitung
    $kunden_name     = '';
    $kunden_email    = '';
    $kunden_tel      = '';
    $kunden_firma    = '';
    ?>        
    <div class="overflow-wrapper full-height">
        <?php 
        addInput('Name', $kunden_name, 'kunden-name', 'Vorname Nachname');
        addInput('E-Mail-Adresse', $kunden_email, 'kunden-email');
        addInput('Telefonnummer', $kunden_tel, 'kunden-tel');
        addInput('Firma (Optional)', $kunden_firma, 'kunden-firma');            
        ?>
    </div>

    <?php 
    saveDeleteButton('kunden');
    deletePopup('delete-kunden', 'Kunde löschen'); 
    ?>
</div>