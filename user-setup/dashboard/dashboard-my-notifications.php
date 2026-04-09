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
        // Nachrichten Liste anzeigen
        echo "<div id='notification-section' class='notification-list'>";
            
            $user_id = get_current_user_id();
            $notification_list = get_user_meta($user_id, 'notification_list', true);
            
            if (empty($notification_list) || !is_array($notification_list)) {
                echo '<div class="no-notifications-message"><p>Keine Nachrichten vorhanden</p></div>';
            } else {
                foreach ($notification_list as $notification_id => $notification_data) {
                    $project_name = $notification_data['benachrichtigung'] ?? '';
                    
                    if (!empty($project_name)) {
                        ?>
                        <div data-project="<?php echo esc_attr($notification_id); ?>" class="project-person-mail memy-button">
                            <i class="bi bi-folder-fill"></i>
                            <h5><?php echo htmlspecialchars($project_name); ?></h5>
                            <i class="bi bi-caret-right-fill"></i>        
                        </div>
                        <?php
                    }
                }
            }

        echo "</div>";
        ?>

        <!--    
        <div class='spalte' style='margin-top: 20px;'>
            <button id='btn-remove-notification' class='memy-button'>Nachrichten Löschen</button>
        </div>
        -->
    </div>
</div>
