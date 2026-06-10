<?php 
/**
 * Title: Nachrichten-Übersicht
 */
?>

<div class="spalte inner-main-heading">
    <h3>
        <i class="mmsi-icon nachricht"></i>
        Nachrichten
    </h3>
</div>

<div class="overflow-wrapper full-height notifications-wrap settings-labels">

    <?php    
    $user_id           = getAdminUserID();
    $notification_list = MemyProtocolManager::get_protocols_for_user();
    $projects_list = get_user_meta($user_id, 'projects_list', true);
    
    if (empty($notification_list) || !is_array($notification_list)) {
        echo '<div class="no-notifications-message"><p>Keine Nachrichten vorhanden</p></div>';
    } else {
        foreach ($notification_list as $notification_data) {
            $news_activity = $notification_data['aktivitaet'] ?? '';
            $news_date     = $notification_data['datum'] ?? '';
            $news_status   = $notification_data['status'] ?? '';
            $news_datum    = !empty($news_date) ? date_i18n('d.m.Y H:i', strtotime($news_date)) : '';
            $news_id       = $notification_data['id'] ?? '';
            $news_icon     = 'protokoll';
            $news_date     = !empty($news_date) ? date_i18n('d.m.Y H:i', strtotime($news_date)) : '';
            
            
            if (!empty($news_activity)) {
                if($news_status == 'info'){
                    $news_icon = 'nachricht';
                }else if($news_status == 'system'){
                    $news_icon = 'system';
                }else if($news_status == 'edit'){
                    $news_icon = 'speichern';
                }
                
                ?>
                <div data-news="<?php echo esc_attr($news_id); ?>" class="dash-item spalte">
                    <i class="mmsi-icon <?php echo $news_icon?>"></i>
                    <span class="notfication-date"><?php echo $news_date ?></span>
                    <p><?php echo htmlspecialchars($news_activity); ?></p>
                </div>
                <?php
            }
        }
    }
    ?>
</div>

<div class="shadow-wrapper"> </div>
