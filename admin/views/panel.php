<div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php printf( __( '%s theme options', 'twentyeleven' ), get_current_theme() ); ?></h2>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
            settings_fields( $panel->panel_name );
            do_settings_sections( 'theme_options' );
            submit_button();
        ?>
    </form>
</div>
