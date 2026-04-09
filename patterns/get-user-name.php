<?php 
/**
 * Title: User Name
 * Slug: fse-memysafe-customer/get-user-name
 * Inserter: no
 * Benutzt in user-menu.
 * Author: Modulbüro
 */
?>
<p>
    <strong>
        Hallo <?php echo wp_get_current_user()->first_name; ?>!
    </strong>
</p>