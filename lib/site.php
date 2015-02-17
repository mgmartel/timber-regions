<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
    exit;

class TimberRegionsSite extends TimberSite
{

    protected $theme_supports = array(
        'post-formats',
        'post-thumbnails',
        'menus'
    );

    protected $editor_style = false;

    protected $nav_menus = array();
    protected $sidebars  = array();

    public function __construct() {
        $this->add_default_views_location();

        $this->_add_theme_support();
        $this->_add_editor_style();
        $this->_add_nav_menus();
        $this->_add_sidebars();

        add_filter( 'timber_context', array( $this, 'add_to_context' ) );
        add_filter( 'get_twig', array( $this, 'add_to_twig' ) );

        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );

        // Styles
        add_action( 'init', array( $this, 'register_styles' ) );

        if ( !is_admin() )
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

        // Scripts
        add_action( 'init', array( $this, 'register_scripts' ) );

        if ( !is_admin() )
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_filter( 'body_class', array( $this, 'add_body_classes' ) );

        parent::__construct();
    }

    protected function _add_theme_support() {
        foreach ( $this->theme_supports as $k => $args ) {
            if ( !is_numeric( $k ) ) { // feature => args
                $args = array( $k, $args );

            } elseif ( is_string( $args ) ) // feature
                $args = array( $args );

            call_user_func_array( 'add_theme_support', $args );
        }
    }

    protected function _add_editor_style() {
        if ( $this->editor_style )
            add_editor_style( $this->editor_style );
    }

    protected function _add_nav_menus() {
        if ( !empty( $this->nav_menus ) )
            register_nav_menus( $this->nav_menus );
    }

    protected function _add_sidebars() {
        foreach( $this->sidebars as $sidebar ) {
            register_sidebar( $sidebar );
        }
    }


    public function add_default_views_location() {
        $dirs = & Timber::$dirname;

        if ( is_string( $dirs ) )
            $dirs = array( $dirs );

        $dirs[] = 'views/default';

        if ( $this->_is_in_subdir() ) {
            add_filter( 'timber_locations', array( $this, 'add_subdir_location' ) );
        }
    }

        public function add_subdir_location( $locations ) {
            $root_dir = trailingslashit( dirname( dirname( __FILE__ ) ) );

            $locations[] = $root_dir . 'views/';
            $locations[] = $root_dir . 'views/default/';
            return $locations;
        }

        /**
         * @todo Make sure this check is reliable! (correct for slashes on Windows, for example)
         */
        private function _is_in_subdir() {
            return dirname( __FILE__ ) !== TEMPLATEPATH . '/lib';
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

    public function get_theme_regions() {
        return new TimberRegions_MyTheme;
    }

    public function add_to_context( $context ) {
        $context['regions'] = $this->get_theme_regions();

        $context['menu'] = new TimberMenu();
        $context['site'] = $this;

        $context['pagination']   = Timber::get_pagination();
        $context['comment_form'] = TimberHelper::get_comment_form();

        return $context;
    }

    public function add_to_twig( $twig ) {
        /* this is where you can add your own fuctions to twig */
        $twig->addExtension( new Twig_Extension_StringLoader() );
        return $twig;
    }

    public function add_body_classes( $body_classes ) {
        return $body_classes;
    }
}
