<?php 
/**
 * Title: The Profile Settings
 * Slug: fse-memysafe-customer/user-profile
 * Inserter: no
 * Benutzt in front-page.
 * Author: Modulbüro
 */

if ( is_user_logged_in() ) :
$profile_names = [
    'profile'       => 'Persönliche Daten',
    'profi_profile' => 'Professionelles Profil',
];
?>
<div class="container"  data-target="memy-profile" data-step="1" id="memy-profile" data-user-id="<?php echo esc_html( wp_get_current_user()->ID ); ?>">
    
    <div data-target="memy-profile">
        <h3>Profil</h3>
        <div class="settings-labels">
        <?php
        foreach ( $profile_names as $profile_slug => $profile_title ) : ?>
            <div class="spalte">
                <button class="goto-btn memy-button full-width" data-goto="<?php echo esc_attr( $profile_slug ); ?>" data-step="2">
                    <?php echo esc_html( $profile_title ); ?>
                </button>
            </div>
        <?php 
        endforeach;
        ?>
        </div>
    </div>
    
    <!-- Eingabefelder -->    
    <?php
    foreach ( $profile_names as $profile_slug => $profile_title ) : ?>        
        <div data-target="<?php echo esc_attr( $profile_slug ); ?>" data-step="2">
            <?php require_once get_stylesheet_directory() . '/user-setup/profile/' . $profile_slug . '.php'; ?>
        </div>    
    <?php endforeach;?>    
    
</div>

<?php endif; ?>