<?php 
/**
 * Title: The Dashboard Tile Notifications
 */
?>

<div class="dashboard-item" id="memy-dashboard-my-notifications" data-user-id="<?php echo esc_html( $user_ID ); ?>">

    <div class="item-headline spalte" data-goto="manage-notifications">
        <i class="mmsi-icon nachricht"></i>
        <h2>Nachrichten</h2>
        <i class="mmsi-icon weiter"></i>
    </div>

    <div class="item-content">        
        <?php    
            $user_id           = getAdminUserID();
            $notification_list = MemyProtocolManager::get_protocols_for_user(3);
            
            if (empty($notification_list) || !is_array($notification_list)) {
                echo '<div class="no-notifications-message"><p>Keine Nachrichten vorhanden</p></div>';
            } else {
                foreach ($notification_list as $notification_data) {
                    $news_activity = $notification_data['aktivitaet'] ?? '';
                    $news_date     = $notification_data['datum'] ?? '';
                    $news_status   = $notification_data['status'] ?? '';
                    $news_datum    = !empty($news_date) ? date_i18n('d.m.Y H:i', strtotime($news_date)) : '';
                    $news_id    = $notification_data['id'] ?? '';

                    if (!empty($news_activity)) {
                        ?>
                        <div data-news="<?php echo esc_attr($news_id); ?>" class="dash-item spalte">
                            <i class="mmsi-icon nachricht"></i>
                            <p><?php echo htmlspecialchars($news_activity); ?></p>
                        </div>
                        <?php
                    }
                }
            }
        ?>

        <!--    
        <div class='spalte' style='margin-top: 20px;'>
            <button id='btn-remove-notification' class='memy-button'>Nachrichten Löschen</button>
        </div>
        -->
    </div>
</div>
