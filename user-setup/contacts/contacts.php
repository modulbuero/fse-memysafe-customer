<?php 
/**
 * Title: Manage Kontaktpersonen
 */

if('abo' == 'grosz'){
    //Auswahl Einzel und Projekte
}else{
?>
    <div class="spalte inner-main-heading">
        <h3>
            <i class="mmsi-icon kontakte"></i>
            Kontakte
        </h3>
    </div>
    <div class="settings-labels gotocontacts-wrapper">
        <div class="spalte is-line">
            <button class="goto-btn memy-button full-width" data-goto="notfallkontakte" data-step="2">
                Notfallkontakte
                <i class='mmsi-icon pfeil'></i>
            </button>
            <?php 
            $infotxt_nk = "Wird automatisch informiert, wenn du über längere Zeit nicht auf die Sicherheitsabfragen von MMSI reagierst. Erhält Zugriff auf die von dir freigegebenen Informationen und koordiniert die ersten Schritte im Ernstfall.<br>Mehr dazu im Glossar.";
            infoPopup($infotxt_nk, "NOTFALLKONTAKT");
            ?>
        </div>
        
        <div class="spalte is-line">
            <button class="goto-btn memy-button full-width" data-goto="vertrauensperson" data-step="2">
                Vertrauensperson 
                <i class='mmsi-icon pfeil'></i>
            </button>
            <?php 
            $infotxt_vp = "Person aus deinem persönlichen Umfeld, die dich im Ernstfall vor Ort unterstützen kann — zum Beispiel beim Zugang zu Wohnung, Büro, Unterlagen oder Geräten. Kann zusätzlich digital in MMSI eingebunden werden.<br>Mehr dazu im Glossar.";
            infoPopup($infotxt_vp, "VERTRAUENSPERSON");
            ?>
        </div>
        
        <div class="spalte is-line">
            <button class="goto-btn memy-button full-width" data-goto="vertretungskontakte" data-step="2">
                Vertreter 
                <i class='mmsi-icon pfeil'></i>
            </button>
            <?php 
            $infotxt_vt = "Person deines Vertrauens, die im Ernstfall bestimmte Aufgaben, Projekte oder Abläufe für dich übernehmen kann. Du entscheidest, welche Informationen sichtbar sind und auf welche Projekte Zugriff besteht.<br>Mehr dazu im Glossar.";
            infoPopup($infotxt_vt, "VERTRETER");
            ?>
        </div>
        
        <div class="spalte is-line">
            <button class="goto-btn memy-button full-width" data-goto="kundenkontakte" data-step="2">
                Kunden 
                <i class='mmsi-icon pfeil'></i>
            </button>
            
        </div>
    </div>
<?php
}
?>