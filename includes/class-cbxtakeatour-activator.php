<?php
/**
 * Fired during plugin activation
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/includes
 * @author     Codeboxr Team <sabuj@codeboxr.com>
 */
class CBXTakeaTour_Activator
{
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        set_transient('cbxtakeatour_activated_notice', 1);
    }//end activate
}//end class CBXTakeaTour_Activator
