<div class='spalte inner-main-heading'>
    <i class='mmsi-icon kontakte'></i>
    <h3>Kunden</h3>
    <?php
    $infotxt_vp = "ist jemand, der dir nahesteht und im Alltag unterstützend wirken kann – z.B. durch physischen Zugang zu Wohnung, Büro oder Doku-menten. Sie muss nicht digital eingebunden sein, kann aber bei Bedarf Zugriff auf bestimmte SafenInhalte erhalten.";
    infoPopup($infotxt_vp, "KUNDEN");
    ?>
</div>

<div id='kunden-section' class="overflow-wrapper full-height">
    <?php 
    /**
     * Übersichtsliste der Kunden
     * Zeigt Status und Buttons zur Bearbeitung
     */
    $user_id     = getAdminUserID();
    $kunden_list = get_user_meta($user_id, 'kunden_list', true);

    // Fallback wenn noch keine Kunden angelegt sind
    if (empty($kunden_list) || !is_array($kunden_list)) {
        ?>
        <div class="no-kunden-message cec">
            <p>Keine Kunden angelegt</p>
        </div>
        <?php
    }

    // Kunden in Liste anzeigen
    if (!empty($kunden_list) && is_array($kunden_list)) {
        echo "<div class='settings-labels'>";
        foreach ($kunden_list as $kunden_id => $kunden_data) {
            $kunden_email = $kunden_data['email'] ?? '';
            $kunden_name  = $kunden_data['name'] ?? '';
            
            if (!empty($kunden_email) || !empty($kunden_name)) {
                ?>
                <button data-kunden="<?php echo $kunden_id; ?>" class="kunden-person-mail memy-button goto-btn" data-goto='kundenkontakt' data-step="5">
                    <h5><?php echo htmlspecialchars($kunden_name ?? 'Kunde'); ?></h5>
                    <i class='mmsi-icon pfeil'></i>
                </button>
            <?php
            }
        }
        echo "</div>";
    }
    ?>
</div><!-- Ende Kunden-Liste -->

<div class="kunden-add-button-container goto-btn save-wrapper" data-goto="kundenkontakt" data-step="5">
    <button id="btn-add-kunden">
        <i class='mmsi-icon neu'></i> Kunde hinzufügen
    </button>
</div>
