<?php 
/**
 * Title: User Name
 * Slug: fse-memysafe-customer/get-user-name
 * Inserter: no
 * Benutzt in user-menu.
 * Author: Modulbüro
 */
$user = wp_get_current_user();
$name = ( in_array( 'subscriber', (array) $user->roles ) ) ? 'Helfer': $user->first_name;

?>
<p>
    <strong>
        Hallo <?php echo $name ?>!
    </strong>
</p>