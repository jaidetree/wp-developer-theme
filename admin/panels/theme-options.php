<?php

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
    register_setting( 'pixel_fist_options', 'pixel_fist_theme_options', 'theme_options_validate' );
    add_settings_section( 'pixelfist_nav', 'Navigation Settings', 'nav_section_text', __FILE__ );
    add_settings_field( 'rssfeed', 'RSS Feed', 'xs_text_field', __FILE__, 'pixelfist_nav', array( 'rssfeed', 'text' ) );
    add_settings_field( 'twitter', 'Twitter Username', 'xs_text_field', __FILE__, 'pixelfist_nav', array( 'twitter', 'text' ) );
    add_settings_field( 'facebook', 'Facebook Page URL', 'xs_text_field', __FILE__, 'pixelfist_nav', array( 'facebook', 'text' ) );
    add_settings_field( 'pitch', 'Guest User Memebership Pitch', 'xs_text_field', __FILE__, 'pixelfist_nav', array( 'pitch', 'textarea' ) );
}

function xs_text_field( $args )
{
    $name = $args[0];
    $type = ( $args[1] ) ? $args[1] : 'text';
    $options = get_option( 'pixel_fist_theme_options', '' );

    if( $type == 'text' )
    {
        $input = HTML::tag('input');
        $input->type = $type;
        $input->value = $options[ $name ];
        $input->size = 40;
    }
    elseif( $type == 'textarea' )
    {
        //class="large-text" cols="50" rows="10" name="sample_theme_options[sometextarea]"
        $input = new HTML::tag('');
        $input->cols = 50;
        $input->rows = 10;
        $input->insert( $options[ $name ] );
    }

    $input->name = 'pixel_fist_theme_options[' . (string)$name . ']';
    $input->id = $name;

    echo $input;
}
    

function nav_section_text()
{
    echo '<p>Top right navigation icon links for our RSS feed, Twitter, and Facebook.</p>';
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
    add_theme_page( __( 'Theme Options', 'pixelfist' ), __( 'Theme Options', 'pixelfist' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}

/**
 * Create the options page
 */
function theme_options_do_page() {
    ?>
    <div class="wrap">
        <?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options', 'pixelfist' ) . "</h2>"; ?>
        Pixel Fist Theme Settings
        <?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
        <div class="updated fade"><p><strong><?php _e( 'Options saved', 'pixelfist' ); ?></strong></p></div>
        <?php endif; ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'pixel_fist_options' ); ?>
            <?php do_settings_sections( __FILE__ ) ?>
            <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'pixelfist' ); ?>" />
            </p>
        </form>
    </div>
    <?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function theme_options_validate( $input ) {
    global $select_options, $radio_options;

    // Say our text option must be safe text with no HTML tags
    $input['twitter'] = wp_filter_nohtml_kses( $input['twitter'] );
    $input['facebook'] = wp_filter_nohtml_kses( $input['facebook'] );

    return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
