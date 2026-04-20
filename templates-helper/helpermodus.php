<?php include get_theme_file_path('templates-helper/head.php'); ?>

<h1>Notfallmodus Aktiviert</h1>

<?php
$args = array(
    'role'    => 'administrator',
    'orderby' => 'registered',
    'order'   => 'ASC',
    'number'  => 1
);

$users = get_users($args);

$adminuserID = '';
if (!empty($users)) {
    $adminuserID = $users[0]->ID;
}

?>

<h2>Safe daten</h2>
<div data-user-id="<?php echo esc_html( $adminuserID ); ?>" class="overflow-wrapper full-height">
    
    <!--JSGenerated-->
    <div id="memy-file-list"></div>
</div>

<?php wp_footer(); ?>
</body>
</html>