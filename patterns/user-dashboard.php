<?php 
/**
 * Title: The Dashboard
 * Slug: fse-memysafe-customer/user-dashboard
 * Inserter: no
 * Benutzt in front-page.
 * Author: Modulbüro
 * 
 * Dashboard Grund Struktur; erster sichtbarer Bereich der Kacheln und deren Unterseiten (Container).
 */
$current_user   = wp_get_current_user();
$user_ID        = $current_user->ID;
$isAdmin        = (get_current_user_id() == getAdminUserID() ) ? 'is-admin' : 'helper-mode'; 
global $wpdb;

if ( is_user_logged_in() ) : 
?>
<div class="wp-block-group <?php echo $isAdmin; ?>" id="memy-dashboard" data-user-id="<?php echo esc_html( wp_get_current_user()->ID ); ?>">

    <!--  ------- --->
    <!--  Kacheln --->
    <div class="tile-wrapper">

        <!-- Wiederda Modus -->
        <?php 
        $exam_clock_urlaubsmodus = MemyOptionManager::get('exam_clock_urlaubsmodus'); 
        if($exam_clock_urlaubsmodus == '1'):
            ?>
            <div id="chooser-urlaubsmodus"  >
                <div class="spalte tile">
                    <div class="spalte full-width">
                        <i class="mmsi-icon schloss-offen big"></i>
                        <p><strong>SCHÖN, DASS DU WIEDER DA BIST!</strong><br>
                        Dein Safe wurde in Deiner Abwesendheit geteilt.</p>
                    </div>
                    <button id="wieder-da-modus" class="full-width" style="justify-content: center;">
                        ICH BIN WIEDER DA
                    </button>
                </div>
            </div>
        <?php
        endif;
        ?>

        <!-- Zeitschaltuhr -->
        <div id="chooser-exam-clock" class="tile">  
            <?php if((get_current_user_id() == getAdminUserID()) && empty(get_option('has_send_notfall')) ): ?>      
                <?php require_once get_stylesheet_directory() . '/user-setup/dashboard/dashboard-my-exam-clock.php'; ?>
            <?php endif; ?>

            <?php if(!empty(get_option('has_send_notfall')) ) : ?>
                <?php require_once get_stylesheet_directory() . '/user-setup/dashboard/dashboard-helper.php'; ?>
            <?php endif; ?>
        </div>

        <div class="spalte">
            <!-- Kontakte -->
            <div id="chooser-contacts" class="tile">
                <?php require_once get_stylesheet_directory() . '/user-setup/dashboard/dashboard-my-contacts.php'; ?>
            </div>
            <!-- Safe -->
            <div id="chooser-safe" class="tile">        
                <?php require_once get_stylesheet_directory() . '/user-setup/dashboard/dashboard-my-safe.php'; ?>
            </div>
        </div>
        
        <div class="spalte">
            <!-- Projekte -->
            <div id="chooser-projects" class="tile">
                <?php require_once get_stylesheet_directory() . '/user-setup/dashboard/dashboard-my-projects.php'; ?>
            </div>
            <!-- Nachrichten -->
            <div id="chooser-notifications" class="tile disabled">        
                <?php require_once get_stylesheet_directory() . '/user-setup/dashboard/dashboard-my-notifications.php'; ?>
            </div>
        </div>
    </div>
    
    <!--  --------- -->
    <!--  Container -->
    <div class="container-wrapper">
        <!-- Zeitschaltuhr -->
        <div class="container" data-target="manage-exam-clock" data-step="1">
            <div data-target="manage-exam-clock">
                <?php require_once get_stylesheet_directory() . '/user-setup/exam-clock/exam-clock-settings.php'; ?>
            </div>
        </div>
        
        <!-- Kontakte -->
        <div class="container" data-target="manage-contacts" data-step="1">
            <!-- Auswahl der Typen -->
            <div data-target="manage-contacts">
                <?php require_once get_stylesheet_directory() . '/user-setup/contacts/contacts.php'; ?>
            </div>
            
            <!-- Notfallkontakte eintragen -->
            <div data-step="3" data-target="notfallkontakte">
                <?php require_once get_stylesheet_directory() . '/user-setup/contacts/contacts-list-get-notfall.php'; ?>
            </div>
            <?php require_once get_stylesheet_directory() . '/user-setup/contacts/contacts-list-add-notfall.php'; ?>
            
            <!-- Vertrauensperson eintragen -->
            <div data-step="3" data-target="vertrauensperson">
                <?php require_once get_stylesheet_directory() . '/user-setup/contacts/contacts-list-vertrauen.php'; ?>
            </div>

            <!-- Vertretungskontakt eintragen -->
            <div data-step="3" data-target="vertretungskontakte">
                <?php require_once get_stylesheet_directory() . '/user-setup/contacts/contacts-list-vertreter.php'; ?>
            </div>
            <div data-step="4" data-target='vertretungskontakt'>
                <?php require_once get_stylesheet_directory() . '/user-setup/contacts/contacts-list-add-vertreter.php'; ?>
            </div>
            
            <!-- Kundenkontakt eintragen -->
            <div data-step="3"  data-target="kundenkontakte">
                <?php require_once get_stylesheet_directory() . '/user-setup/contacts/contacts-list-kunden.php'; ?>
            </div>
            <div data-step="4" data-target='kundenkontakt'>
                <?php require_once get_stylesheet_directory() . '/user-setup/contacts/contacts-list-add-kunden.php'; ?>
            </div>
        </div>

        <!-- Safe -->
        <div class="container" data-target="manage-safe" data-step="2">
            <div data-target="safe-list" data-step="2">
                <?php require_once get_stylesheet_directory() . '/user-setup/safe/safe-list.php'; ?>
            </div>
            <div data-target="manage-safe" data-step="2">
                <?php require_once get_stylesheet_directory() . '/user-setup/safe/safe.php'; ?>
            </div>
            <div data-target="manage-safe-file" data-step="2">
                <?php require_once get_stylesheet_directory() . '/user-setup/safe/safe-file.php'; ?>
            </div>
        </div>

        <!-- Projekte -->
        <div class="container" data-target="manage-projects" data-step="2">
            <div data-target="project-list" data-step="2">
                <?php require_once get_stylesheet_directory() . '/user-setup/projects/projects-list.php'; ?>
            </div>
            <div data-target='manage-projects'>
                <?php require_once get_stylesheet_directory() . '/user-setup/projects/projects.php'; ?>
            </div>
        </div>
        
        <!-- 
            Mitteilungen
        -->

        <!-- Einstellungen -->
        <!-- wp:pattern {"slug":"fse-memysafe-customer/user-settings"} /-->

        <!-- Profil -->
        <!-- wp:pattern {"slug":"fse-memysafe-customer/user-profile"} /-->

        <!-- Helper-Protokoll -->
        <div class="container" data-target="helper-protocol" data-step="1">
            <div data-target="helper-protocol">
                <?php require_once get_stylesheet_directory() . '/user-setup/helper/protocol.php'; ?>
            </div>
            <div data-step="3" data-target='manage-protocol'>
                <?php require_once get_stylesheet_directory() . '/user-setup/helper/protocol-add.php'; ?>
            </div>
        </div>
    </div>
    
    <!--  -------------- -->
    <!--  First-Settings -->
    <?php 
    #if( get_user_meta($user_ID, 'first_settings', true) && current_user_can('administrator')) : 
    if( ! get_user_meta($user_ID, 'first_settings', true) && current_user_can('administrator')) : 
    ?>
        <div id="first-settings"><div calss="container-wrapper">
                <div class="container willkommen">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-00.php'; ?>
                </div>    
                <div class="container wichtig">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-01.php'; ?>
                </div>                              
                <div class="container einrichten">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-02.php'; ?>
                </div>
                <div class="container kontakt">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-03.php'; ?>
                </div>
                <div class="container ">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-04.php'; ?>
                </div>
                <div class="container safe-info">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-05.php'; ?>
                </div>
                <div class="container safe-file-1">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-060.php'; ?>
                </div>
                <div class="container safe-file-2">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-061.php'; ?>
                </div>
                <div class="container zweifaktor">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-07.php'; ?>
                </div>
                <div class="container timer">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-08.php'; ?>
                </div>
                <div class="container final">
                    <?php require_once get_stylesheet_directory() . '/user-setup/first-settings/step-09.php'; ?>
                </div>
        </div></div>
    <?php 
    endif;
    ?>
</div>

<?php endif; ?>