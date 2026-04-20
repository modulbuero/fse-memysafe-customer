<?php 
/**
 * Title: The Dashboard Tile Projects
 */
?>

<div class="dashboard-item" id="memy-dashboard-my-projects" data-user-id="<?php echo esc_html( $user_ID ); ?>">

    <div class="item-headline spalte goto-btn" data-goto="project-list" data-stept="2">
        <i class="mmsi-icon projekte"></i>
        <h2>Projekte</h2>
        <i class="mmsi-icon weiter"></i>
    </div>

    <div class="item-content">        
        <?php
        // Projekte Liste anzeigen   
        $user_id       = getAdminUserID();
        $projects_list = get_user_meta($user_id, 'projects_list', true);
        
        if (empty($projects_list) || !is_array($projects_list)) {
            echo '<div class="no-projects-message"><p>Keine Projekte angelegt</p></div>';
        } else {
            foreach ($projects_list as $project_id => $project_data) {
                $project_name = $project_data['projektname'] ?? '';
                
                if (!empty($project_name)) {
                    ?>
                    <div data-project="<?php echo esc_attr($project_id); ?>" class="project-person-mail dash-goto-btn dash-item spalte" data-goto='manage-projects'>
                        <i class='mmsi-icon projekt'></i>
                        <p><?php echo htmlspecialchars($project_name); ?></p>
                    </div>
                    <?php
                }
            }
        }
    ?>
        <!--    
        <div class='spalte' style='margin-top: 20px;'>
            <button id='btn-add-project' class='memy-button dash-goto-btn' data-goto='manage-projects'>+ Projekt hinzufügen</button>
        </div>
        -->
    </div>
</div>
