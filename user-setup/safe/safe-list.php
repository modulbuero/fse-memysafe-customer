<div class="spalte inner-main-heading">
    <h3>
        <i class="mmsi-icon safe"></i>
        Mein Safe
    </h3>
</div>

<div data-user-id="<?php echo esc_html( $user_ID ); ?>" class="overflow-wrapper full-height">
   
    <!--JSGenerated-->
    <div id="memy-file-list"></div>
    <?php deletePopup('delete-safe-file', 'Datei löschen'); ?>
</div>
<?php if(get_current_user_id() == getAdminUserID() ): ?>
<div class='spalte' class="save-wrapper">
    <button class='memy-button goto-btn' data-goto="manage-safe"  data-step="2">
        <i class='mmsi-icon neu'></i>
        Datei hochladen
    </button>
    <button class="goto-btn" data-goto="manage-safe-file" data-step="2">
        <i class="mmsi-icon datei"></i> 
        Online anlegen
    </button>
</div>
<?php endif; ?>