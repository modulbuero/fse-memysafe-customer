<div class='spalte inner-main-heading'>
    <i class='mmsi-icon kontakte'></i>
    <h3>Vertreter</h3>
    <?php
    $infotxt_vp = "ist jemand, der dir nahesteht und im Alltag unterstützend wirken kann – z.B. durch physischen Zugang zu Wohnung, Büro oder Doku-menten. Sie muss nicht digital eingebunden sein, kann aber bei Bedarf Zugriff auf bestimmte SafenInhalte erhalten.";
    infoPopup($infotxt_vp, "VERTRETER");
    ?>
</div>

<div id='vertreter-section' class='overflow-wrapper full-height'>
    <?php 
    /**
     * Übersichtsliste der Vertreter
     * Zeigt Status und Buttons zur Bearbeitung
     */
    $user_id        = get_current_user_id();
    $vertreter_list = get_user_meta($user_id, 'vertreter_list', true);

    // Fallback wenn noch keine Vertreter angelegt sind
    if (empty($vertreter_list) || !is_array($vertreter_list)) {
        ?>
        <div class="no-vertreter-message">
            <p>Keine Vertreter angelegt</p>
        </div>
        <?php
    }

    // Vertreter in Liste anzeigen
    if (!empty($vertreter_list) && is_array($vertreter_list)) {
        echo "<div class='settings-labels'>";
        foreach ($vertreter_list as $vertreter_id => $vertreter_data) {
            $vertreter_email = $vertreter_data['email'] ?? '';
            $vertreter_name  = $vertreter_data['name'] ?? '';
            
            if (!empty($vertreter_email) || !empty($vertreter_name)) {
                ?>
                <div data-vertreter="<?php echo $vertreter_id; ?>" class="vertreter-person-mail memy-button goto-btn" data-goto='vertretungskontakt' data-step="5">
                    <h5><?php echo htmlspecialchars($vertreter_name ?? 'Vertreter'); ?></h5>
                    <i class='mmsi-icon pfeil'></i>
                </div>
            <?php
            }
        }
        echo "</div>";
    }
    ?>
</div><!-- Ende Vertreter-Liste -->
<div class="vertreter-add-button-container goto-btn save-wrapper" data-goto="vertretungskontakt" data-step="5">
    <button id="btn-add-vertreter">
        <i class='mmsi-icon neu'></i> Vertreter hinzufügen
    </button>
</div>