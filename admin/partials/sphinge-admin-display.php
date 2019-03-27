<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://bwap.ch
 * @since      1.0.0
 *
 * @package    User_Progress
 * @subpackage User_Progress/admin/partials
 */
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?= $this->plugin_name ?></h1>
    <form method="post" action="options.php"> 
        <?php
        settings_fields( 'sphinge_fields' );
        do_settings_sections( 'sphinge_fields' );
        submit_button();
        ?>
    </form>
</div>
