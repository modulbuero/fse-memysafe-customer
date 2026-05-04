<div class='setup-vertreter-data full-height' id='setup-vertreter-new'>
    <div class='spalte inner-main-heading'>
        <i class='mmsi-icon kontakte'></i>
        <h3>Vertreter</h3>
        <?php
        $infotxt_vp = "ist jemand, der dir nahesteht und im Alltag unterstützend wirken kann – z.B. durch physischen Zugang zu Wohnung, Büro oder Doku-menten. Sie muss nicht digital eingebunden sein, kann aber bei Bedarf Zugriff auf bestimmte SafenInhalte erhalten.";
        infoPopup($infotxt_vp, "VERTRETER");
        ?>
    </div>

    <?php 
    // Daten laden wenn Bearbeitung
    $vertreter_name  = '';
    $vertreter_email = '';
    $vertreter_tel   = '';
    $vertreter_firma = '';
    ?>        
    <div class="overflow-wrapper full-height inner-input-wrapper">
        <div>
        <?php 
        addInput('Name', $vertreter_name, 'vertreter-name', 'Vorname Nachname');
        addInput('E-Mail-Adresse', $vertreter_email, 'vertreter-email');
        addInput('Telefonnummer', $vertreter_tel, 'vertreter-tel');
        addInput('Firma (Optional)', $vertreter_firma, 'vertreter-firma');            
        ?>
        </div>
    </div>

    
    
    <?php 
    saveDeleteButton('vertreter');
    deletePopup('delete-vertreter', 'Vertreter löschen'); 
    ?>
</div>