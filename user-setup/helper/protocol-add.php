<div class='setup-protocol-data full-height' id='setup-protocol-new'>
    <div class='spalte inner-main-heading'>
        <i class='mmsi-icon protokoll'></i>
        <h3><?php echo isset($_GET['edit_id']) ? 'HELFER-AKTIVITÄT BEARBEITEN' : 'HELFER-AKTIVITÄT'; ?></h3>
    </div>

    <div class="overflow-wrapper full-height">
        <?php
        $aktivitaet_value = '';
        $status_value = '';
        $edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
        if ($edit_id) {
            $user_id = get_current_user_id();
            $protocols = MemyProtocolManager::get_protocols_for_user($user_id);
            foreach ($protocols as $protocol) {
                if ($protocol['id'] == $edit_id) {
                    $aktivitaet_value = $protocol['aktivitaet'];
                    $status_value = $protocol['status'];
                    break;
                }
            }
        }
        addTextarea('Aktivität', $aktivitaet_value, 'protocol-aktivitaet', 'Beschreibe die Aktivität...');
        addTextarea('Status', $status_value, 'protocol-status', 'Beschreibe Status/Ergebnis...');
        ?>
    </div>

    <div class='spalte save-wrapper'>
        <button id='save-protocol' class='memy-button' data-edit-id='<?php echo $edit_id; ?>'>
            <i class="mmsi-icon speichern"></i>
            <?php echo $edit_id ? 'AKTUALISIEREN' : 'SPEICHERN'; ?>
        </button>
    </div>
</div>