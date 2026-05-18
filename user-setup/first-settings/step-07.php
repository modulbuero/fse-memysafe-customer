<?php 

// $provider   = Two_Factor_Totp::get_instance();
// $user_id    = get_current_user_id();
// $secret     = $provider->get_key( $user_id );
// $issuer     = get_bloginfo('name');
// $user       = wp_get_current_user()->user_login;

// $otpauth    = sprintf(
//     'otpauth://totp/%s:%s?secret=%s&issuer=%s',
//     rawurlencode($issuer),
//     rawurlencode($user),
//     $secret,
//     rawurlencode($issuer)
// );
// echo "<pre>";
// var_dump($provider);
// var_dump($secret);
// var_dump($otpauth);
// echo "</pre>";
?>

<div class="spalte inner-main-heading">
    <h3>Ersteinrichtung</h3>
</div>
<div class="overflow-wrapper full-height settings-labels">
    <h4>
        Zwei-Faktor-Authentifizierung
    </h4>
    <p>
       Mit der Zwei-Faktor-Authentifizierung schützt du den Zugriff auf deinen Safe zusätzlich.
    </p>
    
    <p>
        Die Zwei-Faktor-Authentifizierung folgt mit dem offiziellen Start von MMSI.
    </p>

    <?php 
    echo "";#do_shortcode('[custom_2fa_settings]');
    ?>
</div>

<?php firstStepNavi('7') ?>