<?php 
/**
 * Title: The Dashboard Tile Safe
 */
?>

<div class="dashboard-item" id="memy-dashboard-my-safe" data-user-id="<?php echo esc_html( $user_ID ); ?>">

    <div class="item-headline spalte" data-goto="safe-list" data-step="1">
        <i class="mmsi-icon safe"></i>
        <h2>Mein Safe</h2>
        <i class="mmsi-icon weiter"></i>
    </div>

    <div class="item-content">   
        <div id="memy-file-list-short"></div>
    </div>
</div>
