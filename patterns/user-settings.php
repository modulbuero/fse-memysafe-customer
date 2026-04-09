<?php 
/**
 * Title: The Settingslist 
 * Slug: fse-memysafe-customer/user-settings
 * Inserter: no
 * Benutzt in front-page.
 * Author: Modulbüro
 */

if ( is_user_logged_in() ) :

$setting_names = [
    'darstellung'       => 'Darstellung',
    'nachrichten'       => 'Nachrichten',
    'daten-speicher'    => 'Daten-Speicher',
    'sicherheit'        => 'Sicherheit',
    'system'            => 'System',
    'rechtliches'       => 'Rechtliches',
];
?>
<div class="container"  data-target="memy-settings" data-step="1" id="memy-settings" data-user-id="<?php echo esc_html( wp_get_current_user()->ID ); ?>">

    <div data-target="memy-settings" >
        <h3>Einstellungen</h3>
        <div class="settings-labels">
        <?php
        foreach ( $setting_names as $setting_slug => $setting_title ) : ?>
            <div class="spalte">
                <button class="goto-btn memy-button full-width" data-goto="<?php echo esc_attr( $setting_slug ); ?>" data-step="2">
                    <?php echo esc_html( $setting_title ); ?>
                </button>
            </div>
        <?php 
        endforeach;
        ?>
        </div>
    </div>
    
    <!-- Eingabefelder --> 
    <?php
    foreach ( $setting_names as $setting_slug => $setting_title ) : ?>        
        <div data-target="<?php echo esc_attr( $setting_slug ); ?>" data-step="2" class="full-height">
            <?php require_once get_stylesheet_directory() . '/user-setup/settings/' . $setting_slug . '.php'; ?>
        </div>    
    <?php endforeach;?>    
    
</div>

<?php endif; ?>