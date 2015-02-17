<?php
// Exit if accessed directly
if ( !defined ( 'ABSPATH' ) )
    exit;

/**
 * Base class for regions in Timber templates
 *
 * Child-class it to set defaults for regions, and to add extra regions.
 *
 * API:
 * ::add_{$region}( $template, 'before'|'after'|'replace' );
 * ::set_{$region}( $template );
 * ::set_layout( $template );
 * ::set_wrapper( $template );
 *
 * // Add item to a region
 * TimberRegions::add_header( 'topbar', 'before' );
 * // Set the layout
 * TimberRegions::set_layout( 'full-width' );
 *
 */
class TimberRegions
{
    public static $wrapper = 'default';
    public static $layout  = 'default';

    protected static $_regions = array(
        'header' => array( 'default' ),
        'footer' => array( 'default' )
    );

    public function __call( $name, $arguments ) {
        $self = get_called_class();

        if ( isset( $self::$$name ) )
            return apply_filters( "timber/regions/$name", $self::$$name, $arguments );

        if ( isset( $self::$_regions[$name] ) )
            return apply_filters( "timber/regions/region/$name", $self::$_regions[$name], $arguments );

        return;
    }

    public static function __callStatic($name, $arguments ) {
        $self = get_called_class();

        if ( strpos( $name, 'unset_' ) === 0 ) {
            $var_name = substr( $name, strlen( 'unset_' ) );
            $name = 'set_' . $var_name;
            $arguments[0] = '';
        }

        if ( strpos( $name, 'set_' ) === 0 ) {
            $var_name = substr( $name, strlen( 'set_' ) );

            // Set string variable (wrapper, layout)
            if ( isset( $self::$$var_name ) ) {
                if ( isset( $arguments[0] ) && is_string( $arguments[0] ) )
                    $self::$$var_name = $arguments[0];

                return;
            }

            // Try setting a region array
            $name = 'add_' . $var_name;
            $arguments[1] = 'replace';
        }

        if ( strpos( $name, 'add_' ) === 0 ) {
            $region = str_replace( '_', '-', substr( $name, strlen( 'add_' ) ) );

            if ( isset( $self::$_regions[$region] ) && count( $arguments ) ) {

                $values = ( is_array( $arguments[0] ) ) ? $arguments[0] : array( $arguments[0] );

                $location = ( isset( $arguments[1] ) ) ? $arguments[1] : 'after';

                switch ( $location ) {
                    case 'replace' :
                        $self::$_regions[$region] = $values;
                        break;

                    case 'before' :
                        $self::$_regions[$region] = array_merge( $values, $self::$_regions[$region] );
                        break;

                    case 'after' :
                    default :
                        $self::$_regions[$region] = array_merge( $self::$_regions[$region], $values );
                        break;
                }
            }
            return;
        }
    }

}