<div id="" style="height:100%">
    <div class="spalte inner-main-heading">
        <h3>
            <i class="mmsi-icon protokoll"></i> 
            AKTIVITÄTEN-PROTOKOLL
        </h3>
    </div>

    <div class="overflow-wrapper settings-labels add-trenner" id="protocol-list-container">
        <?php 
        $user_id       = get_current_user_id();
        $protocol_list = MemyProtocolManager::get_protocols_for_user($user_id);
        /*
            Alle Protokolle ausgeben
        */
        if (!empty($protocol_list)) { ?>
            <table class="table-aktivitaeten">
                <tr>
                    <th>Nr.</th>
                    <th>Datum</th>
                    <th>Aktivität</th>
                    <th>Status</th>
                    <?php if(get_current_user_id() != getAdminUserID() ): ?>
                    <th class="edit-column">Bearbeiten</th>
                    <?php endif; ?>
                </tr>
                <?php
                foreach ($protocol_list as $protocol) {
                    $protocol_id         = $protocol['id'];
                    $protocol_datum      = date('d.m.Y H:i', strtotime($protocol['datum']));
                    $protocol_aktivitaet = $protocol['aktivitaet'];
                    $protocol_status     = $protocol['status'];
                    
                    echo "
                    <tr class='protocol-row clickable-row'>
                        <td>$protocol_id.</td>
                        <td>$protocol_datum</td>
                        <td>$protocol_aktivitaet</td>
                        <td>$protocol_status</td>
                        ";
                        if(get_current_user_id() != getAdminUserID() ): 
                    echo "<td class='edit-column'>
                            <button class='memy-button goto-btn edit-protocol' data-goto='manage-protocol' data-step='2' data-edit-id='$protocol_id'>
                                <i class='mmsi-icon bearbeiten'></i>
                            </button>
                        </td>";
                        endif;
                    
                    echo "
                    </tr>";
                }
                echo "</table>";
        }
        ?>
    </div>

    <?php if(get_current_user_id() != getAdminUserID() ): ?>
        <div class='spalte save-wrapper'>
            <button id='btn-add-protocol' class='memy-button goto-btn' data-goto='manage-protocol' data-step="2">
                <i class="mmsi-icon neu"></i>
                NEUE HELFER-AKTIVITÄT ANLEGEN
            </button>
        </div>
    <?php endif; ?>
</div>
