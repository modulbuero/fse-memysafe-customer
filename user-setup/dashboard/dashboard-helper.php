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
            <button data-goto="helper-protocol" class="dash-goto-btn">
                Protokoll
                <?php if(get_current_user_id() == getAdminUserID()) : ?>
                    anzeigen
                <?php else: ?>
                    erfassen
                <?php endif; ?>
            </button>
            <?php if(get_current_user_id() != getAdminUserID()) : ?>
                <p>Bitte hier das Helfer-Aktivitäts-Protokoll erfassen</p>
            <?php else: ?>
                <p>&nbsp;</p>
            <?php endif; ?>
        </div>
        <?php if(get_current_user_id() == getAdminUserID()) : ?>
        <div class="wieder-da full-width">
            <button id="finish-helper-mode">Ich bin wieder da</button>
            <p>Beende hier den Helfer-Modus</p>
        </div>
        <?php endif; ?>
    </div>
    
</div>