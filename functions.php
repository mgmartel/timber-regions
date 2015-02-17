<?php
/**
 * This file bootstraps Timber Regions
 *
 * When you are building a theme with this framework, there should be no need
 * to edit this file. Your own initialization code should go in lib/init.php.
 *
 * Any theme functions shoud be run from lib/site.php
 * Regions and region methods can be defined in lib/regions-theme.php
 */


if (!class_exists('Timber')){
    add_action( 'admin_notices', function() use ($text, $class){
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a></p></div>';
    });
    return;
}

Timber::$twig_template_hierarchy = true;

/**
 * Theme Includes
 */

// Require site class straight away (maybe from both subdir install and parent theme)
require_once 'lib/site.php';
require_once TEMPLATEPATH . '/lib/site.php';

// Maybe also load child site class
if ( TEMPLATEPATH !== STYLESHEETPATH && file_exists( STYLESHEETPATH . '/lib/site.php' ) )
    require STYLESHEETPATH . '/lib/site.php';

$theme_includes = array(
    'lib/regions.php',          // Main regions class
    'lib/regions-theme.php',    // Regions class for current theme
    'lib/init.php'              // Initialization code
);

foreach ( $theme_includes as $file ) {
    if ( !$filepath = locate_template( $file ) ) {
        // This must mean we're a subdir install
        require $file;

    } else {
        require $filepath;

    }

}

unset( $theme_includes, $file, $filepath );

do_action( 'timber_regions_init' );
