<?php
// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}


/**
 * Tour quick display
 *
 * @param  array  $tour_data
 *
 * @return string
 */
function cbxtakeatour_display($tour_data = [])
{
    return CBXTakeaTourHelper::display_tour($tour_data);
}//end cbxtakeatour_display

/**
 * Allow to create new tour
 *
 * @return mixed|void
 */
function cbxtakeatour_allow_create_tour()
{
    return CBXTakeaTourHelper::allow_create_tour();
}