<div class="spalte inner-main-heading">
    <h3>
        <i class="mmsi-icon projekte"></i>
        Projekte
    </h3>
</div>

<div class="overflow-wrapper settings-labels add-trenner" id="projects-list-container">

    <?php 
    $user_id       = getAdminUserID();
    $projects_list = get_user_meta($user_id, 'projects_list', true);
    /*
        Alle Kunden Projekte ausgeben
    */
    foreach ($projects_list as $project_id => $project_data) {
        $project_name = $project_data['projektname'] ?? '';
        
        if (!empty($project_name)) {
            ?>
            <div data-project="<?php echo esc_attr($project_id); ?>" class="project-person-mail goto-btn spalte" data-goto='manage-projects' data-step="2" >
                <i class="mmsi-icon projekt"></i>
                <p><?php echo htmlspecialchars($project_name); ?></p>
                <i class="mmsi-icon weiter"></i>
            </div>
            <?php
        }
    }
    ?>
</div>

<div class='spalte save-wrapper'>
    <button id='btn-add-project' class='memy-button goto-btn' data-goto='manage-projects' data-step="2">
        <i class="mmsi-icon neu"></i>
        Neues Projekt hinzufügen
    </button>
</div>