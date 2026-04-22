<div class="dashboard-helper">
    <div class="spalte">
        <div class="spalte">
            <i class="mmsi-icon helper"></i>
            <h2>Helfer-Modus</h2>
        </div>
        <div>
            <?php 
            $helperText = "Lorem Ipsum";
            infoPopup($helperText, "Helper Modus"); 
            ?>
        </div>
    </div>
    <div class="spalte">
        <div class="protokoll full-width">
            <button data-goto="helper-protocol" class="dash-goto-btn">Protokoll erfassen</button>
            <p>Bitte hier das Helfer-Aktivitäts-Protokoll erfassen</p>
        </div>
        <div class="wieder-da full-width">
            <button>Ich bin wieder da</button>
            <p>Beende hier den Helfer-Modus</p>
        </div>
    </div>
</div>