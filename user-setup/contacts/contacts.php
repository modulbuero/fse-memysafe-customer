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
        <div class="spalte">
            <button class="goto-btn memy-button full-width" data-goto="notfallkontakte" data-step="2">
                Notfallkontakte
                <i class='mmsi-icon pfeil'></i>
            </button>
            <?php 
            $infotxt_nk = "Eine Person, die im falle längerer Inaktivität digital benachrichtigt wird. Sie kann überall auf der Welt leben und erhält zugriff auf zuvor definierte informationen, sobald das System die automatische Prüfung auslöst.";
            infoPopup($infotxt_nk, "NOTFALLKONTAKT");
            ?>
        </div>
        
        <div class="spalte">
            <button class="goto-btn memy-button full-width" data-goto="vertrauensperson" data-step="2">
                Vertrauensperson 
                <i class='mmsi-icon pfeil'></i>
            </button>
            <?php 
            $infotxt_vp = "ist jemand, der dir nahesteht und im Alltag unterstützend wirken kann – z.B. durch physischen Zugang zu Wohnung, Büro oder Doku-menten. Sie muss nicht digital eingebunden sein, kann aber bei Bedarf Zugriff auf bestimmte SafenInhalte erhalten.";
            infoPopup($infotxt_vp, "VERTRAUENSPERSON");
            ?>
        </div>
        
        <div class="spalte">
            <button class="goto-btn memy-button full-width" data-goto="vertretungskontakte" data-step="2">
                Vertreter 
                <i class='mmsi-icon pfeil'></i>
            </button>
            <?php 
            $infotxt_vt = "ist eine Person, die berufliche Aufgaben oder Projekte übernimmt, wenn du vorübergehend nicht verfügbar bist. Sie erhält nur Zugriff auf die für sie relevanten Informationen oder Projekte.";
            infoPopup($infotxt_vt, "VERTRETER");
            ?>
        </div>
        
        <div class="spalte">
            <button class="goto-btn memy-button full-width" data-goto="kundenkontakte" data-step="2">
                Kunden 
                <i class='mmsi-icon pfeil'></i>
            </button>
            <?php 
            $infotxt_kd = "ist jemand, der dir nahesteht und im Alltag unterstützend wirken kann – z.B. durch physischen Zugang zu Wohnung, Büro oder Doku-menten. Sie muss nicht digital eingebunden sein, kann aber bei Bedarf Zugriff auf bestimmte SafenInhalte erhalten.";
            infoPopup($infotxt_kd, "KUNDEN");
            ?>
        </div>
    </div>
<?php
}
?>