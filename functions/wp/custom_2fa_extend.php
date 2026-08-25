<?php 

add_action('init', function () {
    add_shortcode('custom_2fa_settings', 'my_custom_2fa_settings');
});

function my_custom_2fa_settings() {

    if ( ! is_user_logged_in() ) {
        return '<p>Bitte einloggen.</p>';
    }

    $user_id = get_current_user_id();

    // Provider über Filter holen (offizieller Weg im Plugin)
    $providers = apply_filters( 'two_factor_providers', [] );

    if ( empty( $providers ) ) {
        return '<p>Keine 2FA-Provider verfügbar.</p>';
    }

    // aktivierte Provider
    $enabled = (array) get_user_meta( $user_id, '_two_factor_enabled_providers', true );

    ob_start();
    ?>

    <form method="post">
        <h3>Two-Factor Optionen</h3>

        <?php foreach ( $providers as $provider_class ) :

            if ( ! class_exists( $provider_class ) ) {
                continue;
            }

            $provider = call_user_func( [ $provider_class, 'get_instance' ] );

            // Label absichern (nicht alle haben get_label)
            $label = method_exists( $provider, 'get_label' )
                ? $provider->get_label()
                : $provider_class;

            $checked = in_array( $provider_class, $enabled, true );
            ?>

            <label>
                <input type="checkbox"
                       name="two_factor_providers[]"
                       value="<?php echo esc_attr( $provider_class ); ?>"
                       <?php checked( $checked ); ?> />
                <?php echo esc_html( $label ); ?>
            </label>
            <br>

        <?php endforeach; ?>

        <br>
        <button type="submit" name="save_2fa">Speichern</button>
    </form>

    <?php

    // speichern
    if ( isset($_POST['save_2fa']) ) {

        $selected = isset($_POST['two_factor_providers'])
            ? array_map('sanitize_text_field', $_POST['two_factor_providers'])
            : [];

        update_user_meta( $user_id, '_two_factor_enabled_providers', $selected );

        if ( ! empty( $selected ) ) {
            update_user_meta( $user_id, '_two_factor_primary_provider', $selected[0] );
        }

        echo '<p>Gespeichert.</p>';
    }

    return ob_get_clean();
}