<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
    exit;

class MySite extends TimberRegionsSite
{

    public function __construct() {
        // Add editor style
        $this->editor_style = 'assets/css/editor-style.css';

        // Add custom theme supports
        $this->theme_supports['custom-background'] = array(
            'default-image' => '', // background image default
            'default-color' => '', // background color default (dont add the #)
            'wp-head-callback' => '_custom_background_cb',
            'admin-head-callback' => '',
            'admin-preview-callback' => ''
        );

        // Add nav menus
        $this->nav_menus['primary']      = __( 'Primary Menu', 'timber-foundation' );
        $this->nav_menus['footer']       = __( 'Footer Menu', 'timber-foundation' );

        // Add sidebars / widget areas
        $this->sidebars[] = array(
            'name'          => __( 'Sidebar', 'timber-foundation' ),
            'id'            => 'sidebar',
            'before_widget' => '<article id="%1$s" class="widget %2$s">',
            'after_widget'  => '</article>',
            'before_title'  => '<h4>',
            'after_title'   => '</h4>'
        );

        $this->sidebars[] = array(
            'id'            => 'footer',
            'name'          => __( 'Footer', 'timber-foundation' ),
            'before_widget' => '<div class="large-3 columns"><article id="%1$s" class="widget %2$s">',
            'after_widget'  => '</article></div>',
            'before_title'  => '<h4>',
            'after_title'   => '</h4>'
        );

        parent::__construct();
    }

    /**
     * !!! IMPORTANT !!! Make sure you define this
     */
    public function get_theme_regions() {
        return new TimberRegions_MyTheme;
    }

    public function register_post_types() {
        //this is where you can register custom post types
    }

    public function register_taxonomies() {
        //this is where you can register custom taxonomies
    }

    public function register_styles() {
        // and this is where you register styles
    }

    public function register_scripts() {
        // register scripts here
    }

    public function enqueue_styles() {
        // enqueue styles
    }

    public function enqueue_scripts() {
        // enqueue scripts here
    }

    public function add_to_context( $context ) {
        $context = parent::add_to_context( $context );
        return $context;
    }

    public function add_to_twig( $twig ) {
        // this is where you can add your own fuctions to twig
        return $twig;
    }

    public function add_body_classes( $body_classes ) {
        // this is where you can add extra body classes
        return $body_classes;
    }
}
