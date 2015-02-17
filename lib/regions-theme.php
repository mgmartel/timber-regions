<?php
// Exit if accessed directly
if ( !defined ( 'ABSPATH' ) )
    exit;

class TimberRegions_MyTheme extends TimberRegions
{
    public static $wrapper = 'default'; /**< The name of the default wrapper **/
    public static $layout  = 'sidebar-right'; /**< The name of the default layout **/

    /**
     * Array of available regions mapped to their default templates
     *
     * @var array
     */
    protected static $_regions = array(
        'header' => array( 'default' ),
        'footer' => array( 'default' )
    );
}