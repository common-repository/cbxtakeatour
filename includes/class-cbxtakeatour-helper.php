<?php
// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

/**
 * Helper class with all static methods
 *
 * Class CBXTakeaTourHelper
 */
class CBXTakeaTourHelper
{
    /**
     * Custom helper function for take a tour button display and integration
     *
     * @param  array  $meta
     *
     * @return string
     */
    public static function display_tour($meta = [])
    {
        $id = isset($meta['id']) ? intval($meta['id']) : 0;

        //write_log($meta);

        $steps     = isset($meta['steps']) ? $meta['steps'] : [];
        $steps_new = [];

        //write_log($steps);

        if (is_array($steps) && sizeof($steps) > 0) {
            foreach ($steps as $index => $step) {
                if ( ! isset($step['element'])) {
                    continue;
                } //if not set skip
                if (empty($step['element'])) {
                    continue;
                } //if empty skip

                $step['target'] = $step['element'];
                $step['state']  = $state = isset($step['state']) ? intval($step['state']) : 1;

                //needed for js library
                $step['group'] = 'cbxtakeatour_group_'.$id;

                if ($state) {
                    $steps_new[] = $step;
                }
            }
        }


        unset($steps);
        $meta['steps'] = $steps_new;


        $display = true;

        if ( ! apply_filters('cbxtakeatour_display_tour', $display, $meta, $id)) {
            return '';
        }


        if ($id > 0 && is_array($meta) && sizeof($meta) == 0) {
            $meta = get_post_meta($id, '_cbxtourmeta', true);
        }

        if ( ! is_array($meta) || (is_array($meta) && sizeof($meta) == 0)) {
            return '';
        }

        $layout  = isset($meta['layout']) ? esc_attr($meta['layout']) : 'basic';
        $layouts = array_keys(CBXTakeaTourHelper::cbxtakeatour_layouts());

        //if pro layout set and pro addon is disabled, lets fallback to 'basic'
        if ( ! in_array($layout, $layouts)) {
            $layout = 'basic';
        }

        //button text
        $tour_button_text = (isset($meta['tour_button_text']) && ($meta['tour_button_text'] != '')) ? esc_attr(wp_unslash($meta['tour_button_text'])) : esc_html__('Take a Tour', 'cbxtakeatour');

        //button align
        $tour_button_align = (isset($meta['tour_button_align']) && ($meta['tour_button_align'] != '')) ? esc_attr($meta['tour_button_align']) : 'center';

        //write_log($tour_button_align);

        //for fail-safe
        if ( ! in_array($tour_button_align, ['left', 'center', 'right', 'none'])) {
            $tour_button_align = 'center';
        }

        //button block
        $tour_button_block = isset($meta['tour_button_block']) ? intval($meta['tour_button_block']) : 0;

        $button_block_class = (intval($tour_button_block)) ? 'cbxtakeatour-btn-block' : '';

        $button_class = 'cbxtakeatour-btn-'.$layout;

        $display    = isset($meta['display']) ? $meta['display'] : 1;
        $auto_start = isset($meta['auto_start']) ? $meta['auto_start'] : 0;

        $ready_layouts = CBXTakeaTourHelper::cbxtakeatour_layout_ready_styles();
        if (array_key_exists($layout, $ready_layouts)) {
            $custom_css = CBXTakeaTourHelper::add_custom_css($id, CBXTakeaTourHelper::cbxtakeatour_layout_ready_style($layout));

            //add the same style as in frontend
            wp_register_style('cbxtakeatour-public-inline', false, ['cbxtakeatour-public']);
            wp_enqueue_style('cbxtakeatour-public-inline');
            wp_add_inline_style('cbxtakeatour-public-inline', $custom_css);
        }

        do_action('cbxtakeatour_display_tour_public_enqueue', $layout, $id, $meta);

        wp_add_inline_script('cbxtakeatour-public',
            ' cbxtakeatour.steps['.$id.']='.json_encode($meta).'; ',
            'before');

        $display_style = (0 == $display) ? ' display: none; ' : '';

        return '<span class="cbxtakeatour-btn-parent cbxtakeatour-btn-parent-'.esc_attr($tour_button_align).'"><a title="'.esc_attr(get_the_title($id)).'" style="'.$display_style.'" id="cbxtakeatour-'.$id.'" class="cbxtakeatour cbxtakeatour-btn '.esc_attr($button_class).' cbxtakeatour-btn-'.$id.' cbxtakeatour-btn-'.esc_attr($tour_button_align).' '.esc_attr($button_block_class).'"  data-auto-start="'.$auto_start.'" data-tour-id="'.$id.'" href="#">'.esc_attr($tour_button_text).'</a></span>';
    }//end display_tour

    /**
     * Placement helper array
     *
     * @return mixed|void
     */
    public static function cbxtakeatour_placement()
    {
        $placement_array = [
            'top'    => esc_html__('Top', 'cbxtakeatour'),
            'bottom' => esc_html__('Bottom', 'cbxtakeatour'),
            'left'   => esc_html__('Left', 'cbxtakeatour'),
            'right'  => esc_html__('Right', 'cbxtakeatour'),
        ];

        return apply_filters('cbxtakeatour_placement', $placement_array);
    }//end cbxtakeatour_placement


    /**
     * Boolean yes no array helper
     *
     * @return mixed|void
     */
    public static function cbxtakeatour_bool_arr()
    {
        $bool_options_arr = [
            1 => esc_html__('Yes', 'cbxtakeatour'),
            0 => esc_html__('No', 'cbxtakeatour'),
        ];

        return apply_filters('cbxtakeatour_bool_arr', $bool_options_arr);
    }//end cbxtakeatour_bool_arr

    /*/**
     * @return mixed|void
     */
    public static function cbxtakeatour_display()
    {
        $display_options_arr = [
            '1' => esc_html__('Show', 'cbxtakeatour'),
            '0' => esc_html__('Hide', 'cbxtakeatour'),
        ];

        return apply_filters('cbxtakeatour_display', $display_options_arr);
    }//end cbxtakeatour_display*/

    /**
     * List style layout types
     *
     * @return mixed|void
     */
    public static function cbxtakeatour_layouts()
    {
        $layout_options = [
            'basic' => esc_html__('Default', 'cbxtakeatour'),
            'red'   => esc_html__('Red', 'cbxtakeatour'),
            'blue'  => esc_html__('Blue', 'cbxtakeatour'),
            'green' => esc_html__('Green', 'cbxtakeatour')
        ];

        return apply_filters('cbxtakeatour_layouts', $layout_options);
    }//end cbxtakeatour_layouts

    /**
     * Ready layout styles
     *
     * @return mixed|void
     */
    public static function cbxtakeatour_layout_ready_styles()
    {
        $layout_ready_styles = [
            'red'   => [
                //Tour Trigger Button
                'tour_button_color'               => '#f44336',
                'tour_button_text_color'          => '#ffffff',
                'tour_button_border_color'        => '#f44336',
                'tour_button_hover_color'         => '#ef5350',
                'tour_button_hover_border_color'  => '#ef5350',
                'tour_button_active_color'        => '#ef5350',
                'tour_button_active_border_color' => '#e53935',
                'tour_button_focus_color'         => '#f44336',

                //Tour Dialog box
                //'heading_bg_color'            => '#f44336',
                //'heading_border_bottom_color' => '#d32f2f',

                'body_bg_color'     => '#ffebee',
                'body_text_color'   => '#212529',
                'body_border_color' => '#f443364d',

                'heading_text_color' => '#e53935',

                'arrow_color'                      => '#ffebee',
                //'arrow_border_color' => '#f443364d',

                //'button_disabled_hover_border_color' => '#d32f2f4d',

                // Button Color
                'button_color'                     => '#ffffff',
                'button_text_color'                => '#e53935',
                'button_border_color'              => '#d32f2f4d',

                // Button Active Color
                'button_active_color'              => '#fbccc9f2',
                'button_active_text_color'         => '#ef5350',
                'button_active_border_color'       => '#ef5350',

                // Button Focus Color
                'button_focus_color'               => '#ffffff',
                'button_focus_text_color'          => '#e53935',
                'button_focus_border_color'        => '#e53935',

                // Button Hover Color
                'button_hover_color'               => '#ef5350',
                'button_hover_text_color'          => '#ffffff',
                'button_hover_border_color'        => '#e539354d',

                // Stapes Bullet Color
                'dialog_progress_dot_color'        => '#c2c7ce',
                'dialog_progress_dot_active_color' => '#e53935',
                'dialog_progress_counter_color'    => '#252525',
            ],
            'green' => [
                //Tour Trigger Button
                'tour_button_color'               => '#4caf50',
                'tour_button_text_color'          => '#ffffff',
                'tour_button_border_color'        => '#4caf50',
                'tour_button_hover_color'         => '#66bb6a',
                'tour_button_hover_border_color'  => '#66bb6a',
                'tour_button_active_color'        => '#66bb6a',
                'tour_button_active_border_color' => '#43a047',
                'tour_button_focus_color'         => '#4caf504d',

                //Tour Dialog box
                //'heading_bg_color'            => '#4caf50',
                //'heading_border_bottom_color' => '#388e3c',

                'body_bg_color'     => '#e8f5e9',
                'body_text_color'   => '#212529',
                'body_border_color' => '#4caf504d',

                'heading_text_color' => '#43a047',

                'arrow_color'                      => '#e8f5e9',
                //'arrow_border_color' => '#4caf504d',

                //'button_disabled_hover_border_color' => '#388e3c4d',

                // Button Color
                'button_color'                     => '#ffffff',
                'button_text_color'                => '#66bb6a',
                'button_border_color'              => '#388e3c4d',

                // Button Active Color
                'button_active_color'              => '#b3e1b6',
                'button_active_text_color'         => '#43a047',
                'button_active_border_color'       => '#43a047',

                // Button Focus Color
                'button_focus_color'               => '#fff',
                'button_focus_text_color'          => '#66bb6a',
                'button_focus_border_color'        => '#66bb6a',

                // Button Hover Color
                'button_hover_color'               => '#66bb6a',
                'button_hover_text_color'          => '#ffffff',
                'button_hover_border_color'        => '#43a0474d',

                // Stapes Bullet Color
                'dialog_progress_dot_color'        => '#c2c7ce',//todo: need to set color
                'dialog_progress_dot_active_color' => '#43a047',//todo: need to set color
                'dialog_progress_counter_color'    => '#252525',//todo: need to set color
            ],
            'blue'  => [
                //Tour Trigger Button
                'tour_button_color'               => '#2196f3',
                'tour_button_text_color'          => '#ffffff',
                'tour_button_border_color'        => '#2196f3',
                'tour_button_hover_color'         => '#42a5f5',
                'tour_button_hover_border_color'  => '#42a5f5',
                'tour_button_active_color'        => '#42a5f5',
                'tour_button_active_border_color' => '#1e88e5',
                'tour_button_focus_color'         => '#2196f34d',

                //Tour Dialog Box
                //'heading_bg_color'            => '#2196f3',
                //'heading_border_bottom_color' => '#1976d2',

                'body_bg_color'     => '#e3f2fd',
                'body_text_color'   => '#212529',
                'body_border_color' => '#2196f34d',

                'heading_text_color' => '#1e88e5',

                'arrow_color'                      => '#e3f2fd',
                //'arrow_border_color' => '#2196f34d',

                //'button_disabled_hover_border_color' => '#1976d24d',

                // Button Color
                'button_color'                     => '#ffffff',
                'button_text_color'                => '#1e88e5',
                'button_border_color'              => '#1976d24d',

                // Button Active Color
                'button_active_color'              => '#c4def3',
                'button_active_text_color'         => '#1e88e5',
                'button_active_border_color'       => '#1e88e5',

                // Button Focus Color
                'button_focus_color'               => '#ffffff',
                'button_focus_text_color'          => '#1e88e5',
                'button_focus_border_color'        => '#1e88e5',

                // Button Hover Color
                'button_hover_color'               => '#42a5f5',
                'button_hover_text_color'          => '#ffffff',//todo: need to set color
                'button_hover_border_color'        => '#1e88e54d',

                // Stapes Bullet Color
                'dialog_progress_dot_color'        => '#c2c7ce',//todo: need to set color
                'dialog_progress_dot_active_color' => '#1e88e5',//todo: need to set color
                'dialog_progress_counter_color'    => '#252525',//todo: need to set color

            ]
        ];

        return apply_filters('cbxtakeatour_layout_ready_styles', $layout_ready_styles);
    }//end cbxtakeatour_layout_ready_styles

    /**
     * Get a ready layout style
     *
     * @param  string  $layout
     *
     * @return array|mixed|string
     */
    public static function cbxtakeatour_layout_ready_style($layout = '')
    {
        if ($layout == '') {
            return '';
        }

        $layout = esc_attr($layout);

        $layout_ready_styles = CBXTakeaTourHelper::cbxtakeatour_layout_ready_styles();

        $layout_ready_style = isset($layout_ready_styles[$layout]) ? $layout_ready_styles[$layout] : [];

        return apply_filters('cbxtakeatour_layout_ready_style', $layout_ready_style);
    }//end cbxtakeatour_layout_ready_style

    /**
     * HTML elements, attributes, and attribute values will occur in your output
     * @return array
     */
    public static function allowedHtmlTags()
    {
        $allowed_html_tags = [
            'a'      => [
                'href'  => [],
                'title' => [],
                //'class' => [],
                //'data'  => [],
                //'rel'   => [],
            ],
            'img'    => [
                'src' => [],
                'alt' => [],
                //'class' => [],
                //'data'  => [],
                //'rel'   => [],
            ],
            'br'     => [],
            'em'     => [],
            'ul'     => [//'class' => [],
            ],
            'ol'     => [//'class' => [],
            ],
            'li'     => [//'class' => [],
            ],
            'strong' => [],
            'p'      => [
                //'class' => [],
                //'data'  => [],
                //'style' => [],
            ],
            'span'   => [
                //					'class' => [],
                //'style' => [],
            ],
        ];

        return apply_filters('cbxtakeatour_allowed_html_tags', $allowed_html_tags);
    }//end allowedHtmlTags

    /**
     * Get user display name
     *
     * @param  null  $user_id
     *
     * @return string
     */
    public static function userDisplayName($user_id = null)
    {
        $current_user      = $user_id ? new WP_User($user_id) : wp_get_current_user();
        $user_display_name = $current_user->display_name;
        if ($user_display_name != '') {
            return $user_display_name;
        }

        if ($current_user->first_name) {
            if ($current_user->last_name) {
                return $current_user->first_name.' '.$current_user->last_name;
            }

            return $current_user->first_name;
        }

        return esc_html__('Unnamed', 'cbxtakeatour');
    }//end method userDisplayName

    /**
     * Get user display name alternative if display_name value is empty
     *
     * @param $current_user
     * @param $user_display_name
     *
     * @return string
     */
    public static function userDisplayNameAlt($current_user, $user_display_name)
    {
        if ($user_display_name != '') {
            return $user_display_name;
        }

        if ($current_user->first_name) {
            if ($current_user->last_name) {
                return $current_user->first_name.' '.$current_user->last_name;
            }

            return $current_user->first_name;
        }

        return esc_html__('Unnamed', 'cbxtakeatour');
    }//end method userDisplayNameAlt

    /**
     * Add custom style for any step
     *
     * @param  int  $id
     *
     * @return string
     */
    public static function add_custom_css($id = 0, $meta = [])
    {
        $id = intval($id);
        if ($id == 0) {
            return '';
        }

        if ( ! is_array($meta) || (is_array($meta) && sizeof($meta) == 0)) {
            return '';
        }

        //tour button
        $tour_button_text_size = (isset($meta['tour_button_text_size']) && ($meta['tour_button_text_size'] != '')) ? intval($meta['tour_button_text_size']) : 14;

        $tour_button_line_height = (isset($meta['tour_button_line_height']) && ($meta['tour_button_line_height'] != '')) ? floatval($meta['tour_button_line_height']) : 1.25;//new
        if ($tour_button_line_height == 0) {
            $tour_button_line_height = 1.25;
        }


        $padding_top    = (isset($meta['padding']['pt']) && ($meta['padding']['pt'] != '')) ? $meta['padding']['pt'] : 8;
        $padding_right  = (isset($meta['padding']['pr']) && ($meta['padding']['pr'] != '')) ? $meta['padding']['pr'] : 12;
        $padding_bottom = (isset($meta['padding']['pb']) && ($meta['padding']['pb'] != '')) ? $meta['padding']['pb'] : 8;
        $padding_left   = (isset($meta['padding']['pl']) && ($meta['padding']['pl'] != '')) ? $meta['padding']['pl'] : 12;

        $tour_button_color        = (isset($meta['tour_button_color']) && ($meta['tour_button_color'] != '')) ? $meta['tour_button_color'] : '#868e96';
        $tour_button_text_color   = (isset($meta['tour_button_text_color']) && ($meta['tour_button_text_color'] != '')) ? $meta['tour_button_text_color'] : '#ffffff';
        $tour_button_border_color = (isset($meta['tour_button_border_color']) && ($meta['tour_button_border_color'] != '')) ? $meta['tour_button_border_color'] : '#868e96';

        $tour_button_hover_color        = (isset($meta['tour_button_hover_color']) && ($meta['tour_button_hover_color'] != '')) ? $meta['tour_button_hover_color'] : '#727b84';
        $tour_button_hover_text_color   = (isset($meta['tour_button_hover_text_color']) && ($meta['tour_button_hover_text_color'] != '')) ? $meta['tour_button_hover_text_color'] : '#ffffff';
        $tour_button_hover_border_color = (isset($meta['tour_button_hover_border_color']) && ($meta['tour_button_hover_border_color'] != '')) ? $meta['tour_button_hover_border_color'] : '#6c757d';

        $tour_button_active_color        = (isset($meta['tour_button_active_color']) && ($meta['tour_button_active_color'] != '')) ? $meta['tour_button_active_color'] : '#727b84';
        $tour_button_active_border_color = (isset($meta['tour_button_active_border_color']) && ($meta['tour_button_active_border_color'] != '')) ? $meta['tour_button_active_border_color'] : '#6c757d';

        $tour_button_focus_color = (isset($meta['tour_button_focus_color']) && ($meta['tour_button_focus_color'] != '')) ? $meta['tour_button_focus_color'] : '#868e9680';
        //end tour button

        //Tour Dialog box
        $dialog_font_size   = (isset($meta['dialog_font_size']) && ($meta['dialog_font_size'] != '')) ? intval($meta['dialog_font_size']) : 14;
        $dialog_line_height = (isset($meta['dialog_line_height']) && ($meta['dialog_line_height'] != '')) ? floatval($meta['dialog_line_height']) : 1.2;
        if ($dialog_line_height == 0) {
            $dialog_line_height = 1.2;
        }

        $body_bg_color     = (isset($meta['body_bg_color']) && ($meta['body_bg_color'] != '')) ? $meta['body_bg_color'] : '#ffffff';
        $body_text_color   = (isset($meta['body_text_color']) && ($meta['body_text_color'] != '')) ? $meta['body_text_color'] : '#212529';
        $body_border_color = (isset($meta['body_border_color']) && ($meta['body_border_color'] != '')) ? $meta['body_border_color'] : '#00000040';

        //$heading_bg_color            = ( isset( $meta['heading_bg_color'] ) && ( $meta['heading_bg_color'] != '' ) ) ? $meta['heading_bg_color'] : '#f7f7f7';
        //$heading_border_bottom_color = ( isset( $meta['heading_border_bottom_color'] ) && ( $meta['heading_border_bottom_color'] != '' ) ) ? $meta['heading_border_bottom_color'] : '#ebebeb';

        $heading_text_color  = (isset($meta['heading_text_color']) && ($meta['heading_text_color'] != '')) ? $meta['heading_text_color'] : '#444444';
        $heading_font_size   = (isset($meta['heading_font_size']) && ($meta['heading_font_size'] != '')) ? intval($meta['heading_font_size']) : 15;
        $heading_line_height = (isset($meta['heading_line_height']) && ($meta['heading_line_height'] != '')) ? floatval($meta['heading_line_height']) : 1.2;


        //dialog
        //$button_disabled_hover_border_color = ( isset( $meta['button_disabled_hover_border_color'] ) && ( $meta['button_disabled_hover_border_color'] != '' ) ) ? $meta['button_disabled_hover_border_color'] : '#6868684d';


        $button_font_size   = (isset($meta['button_font_size']) && ($meta['button_font_size'] != '')) ? intval($meta['button_font_size']) : 13;
        $button_line_height = (isset($meta['button_line_height']) && ($meta['button_line_height'] != '')) ? floatval($meta['button_line_height']) : 1.2;

        $button_color        = (isset($meta['button_color']) && ($meta['button_color'] != '')) ? $meta['button_color'] : '#fff';
        $button_text_color   = (isset($meta['button_text_color']) && ($meta['button_text_color'] != '')) ? $meta['button_text_color'] : '#6f727e';
        $button_border_color = (isset($meta['button_border_color']) && ($meta['button_border_color'] != '')) ? $meta['button_border_color'] : '#cbcfd5';

        $button_hover_color        = (isset($meta['button_hover_color']) && ($meta['button_hover_color'] != '')) ? $meta['button_hover_color'] : '#fff';
        $button_hover_text_color   = (isset($meta['button_hover_text_color']) && ($meta['button_hover_text_color'] != '')) ? $meta['button_hover_text_color'] : '#464852';
        $button_hover_border_color = (isset($meta['button_hover_border_color']) && ($meta['button_hover_border_color'] != '')) ? $meta['button_hover_border_color'] : '#b5b7c2';


        $button_active_color        = (isset($meta['button_active_color']) && ($meta['button_active_color'] != '')) ? $meta['button_active_color'] : '#f1f2ff';
        $button_active_text_color   = (isset($meta['button_active_text_color']) && ($meta['button_active_text_color'] != '')) ? $meta['button_active_text_color'] : '#4655cb';
        $button_active_border_color = (isset($meta['button_active_border_color']) && ($meta['button_active_border_color'] != '')) ? $meta['button_active_border_color'] : '#5362d9';


        $button_focus_color        = isset($meta['button_focus_color']) ? $meta['button_focus_color'] : '#fff';
        $button_focus_text_color   = isset($meta['button_focus_text_color']) ? $meta['button_focus_text_color'] : '#6f727e';
        $button_focus_border_color = isset($meta['button_focus_border_color']) ? $meta['button_focus_border_color'] : '#cbcfd5';

        $arrow_color = (isset($meta['arrow_color']) && ($meta['arrow_color'] != '')) ? $meta['arrow_color'] : '#ffffff';
        //$arrow_border_color = ( isset( $meta['arrow_border_color'] ) && ( $meta['arrow_border_color'] != '' ) ) ? $meta['arrow_border_color'] : '#0000004d';


        $dialog_progress_dot_color        = (isset($meta['dialog_progress_dot_color']) && ($meta['dialog_progress_dot_color'] != '')) ? $meta['dialog_progress_dot_color'] : '#c2c7ce';
        $dialog_progress_dot_active_color = (isset($meta['dialog_progress_dot_active_color']) && ($meta['dialog_progress_dot_active_color'] != '')) ? $meta['dialog_progress_dot_active_color'] : '#5362d9';
        $dialog_progress_counter_color    = (isset($meta['dialog_progress_counter_color']) && ($meta['dialog_progress_counter_color'] != '')) ? $meta['dialog_progress_counter_color'] : '#212529';


        //button start
        $custom_css = '.cbxtakeatour-btn{
							padding: '.$padding_top.'px '.$padding_right.'px '.$padding_bottom.'px '.$padding_left.'px;
							font-size:'.$tour_button_text_size.'px;
							line-height:'.$tour_button_line_height.';
						}
						
						.cbxtakeatour-btn-'.$id.' {
						    color: '.$tour_button_text_color.' !important;
						    background-color: '.$tour_button_color.'!important;
						    border-color: '.$tour_button_border_color.'!important;
						    
						}
						
						.cbxtakeatour-btn-'.$id.':hover {
						    color: '.$tour_button_hover_text_color.';
						    background-color: '.$tour_button_hover_color.'!important;
						    border-color: '.$tour_button_hover_border_color.';
						}
						
						.cbxtakeatour-btn-'.$id.':focus, .cbxtakeatour-btn-'.$id.'.focus {
							background-color: '.$tour_button_active_color.';    
						    box-shadow: none !important;
						}
						
						.cbxtakeatour-btn-'.$id.':active, .cbxtakeatour-btn-'.$id.'.active
						 {
						    background-color: '.$tour_button_active_color.';
						    border-color: '.$tour_button_active_border_color.';
						}';
        //button end

        //pop hover start
        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog{
							color: '.$body_text_color.';
							background-color: '.$body_bg_color.';
				            border: 1px solid '.$body_border_color.';
				            font-size:'.$dialog_font_size.'px;
							line-height:'.$dialog_line_height.';
						}';

        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog .tg-dialog-header .tg-dialog-title{
							color: '.$heading_text_color.';			
				            font-size:'.$heading_font_size.'px;
							line-height:'.$heading_line_height.';
						}';

        //dialog button style
        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog .tg-dialog-footer button.tg-dialog-btn, .tg-dialog .tg-dialog-footer button:not(:hover):not(:active).tg-dialog-btn {
							font-size:'.$button_font_size.'px;
							line-height:'.$button_line_height.';
				            background-color: '.$button_color.';
							color: '.$button_text_color.';
				            border-color: '.$button_border_color.';            
						}';


        //dialog button active style
        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog .tg-dialog-footer button.tg-dialog-btn:active, .tg-dialog .tg-dialog-footer button:not(:hover):active.tg-dialog-btn {
							background-color: '.$button_active_color.';
				            color: '.$button_active_text_color.';
				            border-color: '.$button_active_border_color.';
						}';

        //dialog button focus style
        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog .tg-dialog-footer button.tg-dialog-btn:focus, .tg-dialog .tg-dialog-footer button:not(:hover):not(:active).tg-dialog-btn:focus {
							background-color: '.$button_focus_color.';
				            color: '.$button_focus_text_color.';
				            border-color: '.$button_focus_border_color.';
						}';

        //dialog button hover style
        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog .tg-dialog-footer button.tg-dialog-btn:hover, .tg-dialog .tg-dialog-footer button:not(:focus):not(:active).tg-dialog-btn:hover {
				            background-color: '.$button_hover_color.';
							color: '.$button_hover_text_color.';
				            border-color: '.$button_hover_border_color.';
				            outline: none !important;
				            text-decoration: none !important
						}';

        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog .tg-dialog-dots > span.tg-dot{
							background-color: '.$dialog_progress_dot_color.';
						}';

        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog .tg-dialog-dots > span.tg-dot.tg-dot-active{
							background-color: '.$dialog_progress_dot_active_color.';
						}';

        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog .tg-dialog-footer .tg-dialog-footer-sup .tg-dialog-dots+.tg-step-progress{
							color: '.$dialog_progress_counter_color.';
						}';

        //dialog arrow

        $custom_css .= 'body[data-cbxtakeatour-dialog="'.$id.'"] .cbxtakeatour_dialog .tg-arrow{
							background: '.$arrow_color.';
						}';

        //dialog end

        return $custom_css;
    }//end add_custom_css

    /**
     * Char Length check  thinking utf8 in mind
     *
     * @param $text
     *
     * @return int
     */
    public static function utf8_compatible_length_check($text)
    {
        if (seems_utf8($text)) {
            $length = mb_strlen($text);
        } else {
            $length = strlen($text);
        }

        return $length;
    }

    /**
     * Add utm params to any url
     *
     * @param  string  $url
     *
     * @return string
     */
    public static function url_utmy($url = '')
    {
        if ($url == '') {
            return $url;
        }

        return add_query_arg([
            'utm_source'   => 'plgsidebarinfo',
            'utm_medium'   => 'plgsidebar',
            'utm_campaign' => 'wpfreemium',
        ], $url);

    }//end url_utmy

    /**
     * Get all  core tables list
     */
    public static function getAllDBTablesList()
    {
        $table_names = [];
        return apply_filters('cbxtakeatour_table_list', $table_names);
    }//end method getAllDBTablesList

    /**
     * List all global option name with prefix cbxtakeatour_
     */
    public static function getAllOptionNames()
    {
        global $wpdb;

        $prefix       = 'cbxtakeatour_';
        $option_names = $wpdb->get_results("SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'", ARRAY_A);

        return apply_filters('cbxtakeatour_option_names', $option_names);
    }//end method getAllOptionNames

    /**
     * Options name only
     *
     * @return array
     */
    public static function getAllOptionNamesValues()
    {
        $option_values = self::getAllOptionNames();
        $names_only    = [];

        foreach ($option_values as $key => $value) {
            $names_only[] = $value['option_name'];
        }

        return $names_only;
    }//end method getAllOptionNamesValues

    /**
     * Random color
     *
     * https://thisinterestsme.com/random-rgb-hex-color-php/
     *
     * @return string[]
     */
    public static function randomColor()
    {
        $result = ['rgb' => '', 'hex' => ''];
        foreach (['r', 'b', 'g'] as $col) {
            $rand = mt_rand(0, 255);
            //$result['rgb'][$col] = $rand;
            $dechex = dechex($rand);
            if (strlen($dechex) < 2) {
                $dechex = '0'.$dechex;
            }
            $result['hex'] .= $dechex;
        }

        return $result;
    }//end randomColor

    /**
     * Get tour listing url by status
     *
     * @param  string  $status
     *
     * @return string
     */
    public static function listing_url_by_status($status = 'all')
    {
        if ($status == '') {
            $status == 'all';
        }

        $listing_url = admin_url('admin.php?page=cbxtakeatour-listing');

        return add_query_arg('post_status', $status, $listing_url);
    }//end method listing_url_by_status

    /**
     * Allowed status array
     */
    public static function allowed_status()
    {
        return [
            'draft'   => esc_html__('Draft', 'cbxtakeatour'),
            'publish' => esc_html__('Publish', 'cbxtakeatour'),
            //'private'   => esc_html__('Private', 'cbxtakeatour'),
            'pending' => esc_html__('Pending', 'cbxtakeatour'),
        ];
    }//end method allowed_status

    /**
     * Allow to create new tour
     */
    public static function allow_create_tour()
    {
        $tour_count = wp_count_posts('cbxtour');
        $allow      = true;

        return apply_filters('cbxtour_allow_create_tour', $allow, $tour_count->publish);
    }//end method allow_create_tour

    /**
     * Register and enqueue all public styles and scripts
     *
     * @param $version
     *
     * @return void
     */
    public static function public_styles_scripts($version)
    {
        $css_url_part         = CBXTAKEATOUR_ROOT_URL.'assets/css/';
        $css_url_part_vendors = CBXTAKEATOUR_ROOT_URL.'assets/vendors/';
        $js_url_part          = CBXTAKEATOUR_ROOT_URL.'assets/js/';
        $js_url_part_vendors  = CBXTAKEATOUR_ROOT_URL.'assets/vendors/';


        //public styles
        wp_register_style('cbxtakeatour-public', $css_url_part.'cbxtakeatour-public.css', [], $version, 'all');
        wp_enqueue_style('cbxtakeatour-public');

        //public scripts
        wp_register_script('tourguidejs', $js_url_part_vendors.'tourguidejs/tour.js', ['jquery'], $version, true);
        wp_register_script('cbxtakeatour-events', $js_url_part.'cbxtakeatour-events.js', [], $version, true);
        wp_register_script("cbxtakeatour-public", $js_url_part.'cbxtakeatour-public.js', ['cbxtakeatour-events', 'jquery', 'tourguidejs'], $version, true);


        $cbxtakeatour_js_var = [
            'steps'              => [],
            'tour_label_pause'   => esc_html__('Pause', 'cbxtakeatour'),
            'tour_label_resume'  => esc_html__('Resume', 'cbxtakeatour'),
            'tour_label_next'    => esc_html__('Next', 'cbxtakeatour'),
            'tour_label_prev'    => esc_html__('Prev', 'cbxtakeatour'),
            'tour_label_endtour' => esc_html__('End tour', 'cbxtakeatour'),
        ];

        wp_localize_script('cbxtakeatour-public', 'cbxtakeatour', apply_filters('cbxtakeatour_js_var', $cbxtakeatour_js_var));

        wp_enqueue_script('tourguidejs');

        wp_enqueue_script('cbxtakeatour-events');

        do_action('cbxtakeatour_js_before_public');

        wp_enqueue_script('cbxtakeatour-public');
    }//end method public_styles_scripts


    /**
     * Returns setting sections
     *
     * @return void
     */
    public static function cbxtakeatour_setting_sections()
    {
        $sections = [
            [
                'id'    => 'cbxtakeatour_tools',
                'title' => esc_html__('Tools', 'cbxtakeatour')
            ]
        ];

        return apply_filters('cbxtakeatour_setting_sections', $sections);
    }//end method get_settings_sections

    /**
     * Plugin reset html table
     *
     * @return string
     * @since 1.1.0
     *
     */
    public static function setting_reset_html_table()
    {
        $option_values = CBXTakeaTourHelper::getAllOptionNames();
        $table_names   = CBXTakeaTourHelper::getAllDBTablesList();


        $table_html = '<div id="cbxtakeatour_resetinfo"';
        $table_html .= '<p style="margin-bottom: 15px;" id="cbxtakeatour_plg_gfig_info"><strong>'.esc_html__('Following option values created by this plugin(including addon) from WordPress core option table', 'cbxtakeatour').'</strong></p>';

		$table_html .= '<table class="widefat widethin cbxtakeatour_table_data">
	<thead>
	<tr>
		<th class="row-title">'.esc_attr__('Option Name', 'cbxtakeatour').'</th>
		<th>'.esc_attr__('Option ID', 'cbxtakeatour').'</th>		
	</tr>
	</thead>';

        $table_html .= '<tbody>';

        $i = 0;
        foreach ($option_values as $key => $value) {
            $alternate_class = ($i % 2 == 0) ? 'alternate' : '';
            $i++;
            $table_html .= '<tr class="'.esc_attr($alternate_class).'">
									<td class="row-title"><input checked class="magic-checkbox reset_options" type="checkbox" name="reset_options['.$value['option_name'].']" id="reset_options_'.esc_attr($value['option_name']).'" value="'.$value['option_name'].'" />
  <label for="reset_options_'.esc_attr($value['option_name']).'">'.esc_attr($value['option_name']).'</td>
									<td>'.esc_attr($value['option_id']).'</td>									
								</tr>';
        }

        $table_html .= '</tbody>';
        $table_html .= '<tfoot>
	<tr>
		<th class="row-title">'.esc_attr__('Option Name', 'cbxtakeatour').'</th>
		<th>'.esc_attr__('Option ID', 'cbxtakeatour').'</th>				
	</tr>
	</tfoot>
</table>';


        if (sizeof($table_names) > 0):
            $table_html .= '<p style="margin-bottom: 15px;" id="cbxscratingreview_info"><strong>'.esc_html__('Following database tables will be reset/deleted and then re-created.', 'cbxtakeatour').'</strong></p>';

            $table_html .= '<table class="widefat widethin cbxtakeatour_table_data">
        <thead>
        <tr>
            <th class="row-title">'.esc_attr__('Table Name', 'cbxtakeatour').'</th>
            <th>'.esc_attr__('Table Name in DB', 'cbxtakeatour').'</th>		
        </tr>
        </thead>';

            $table_html .= '<tbody>';


            $i = 0;
            foreach ($table_names as $key => $value) {
                $alternate_class = ($i % 2 == 0) ? 'alternate' : '';
                $i++;
                $table_html .= '<tr class="'.esc_attr($alternate_class).'">
                                        <td class="row-title"><input checked class="magic-checkbox reset_tables" type="checkbox" name="reset_tables['.esc_attr($key).']" id="reset_tables_'.esc_attr($key).'" value="'.$value.'" />
  <label for="reset_tables_'.esc_attr($key).'">'.esc_attr($key).'</label></td>
                                        <td>'.esc_attr($value).'</td>									
                                    </tr>';
            }

            $table_html .= '</tbody>';
            $table_html .= '<tfoot>
        <tr>
            <th class="row-title">'.esc_attr__('Table Name', 'cbxtakeatour').'</th>
            <th>'.esc_attr__('Table Name in DB', 'cbxtakeatour').'</th>		
        </tr>
        </tfoot>
    </table>';
            $table_html .= '</div>';
        endif;

        return $table_html;
    }//end method setting_reset_html_table

    /**
     * Return setting fields
     *
     * @return void
     */
    public static function cbxtakeatour_setting_fields()
    {
        //$table_html = self::setting_reset_html_table();

        $table_html = '<div id="cbxtakeatour_resetinfo_wrap">'.esc_html__('Loading ...', 'cbxtakeatour').'</div>';

        $settings_fields = [
            'cbxtakeatour_tools' => [
                'tools_heading'        => [
                    'name'    => 'tools_heading',
                    'label'   => esc_html__('Tools Settings', 'cbxtakeatour'),
                    'type'    => 'heading',
                    'default' => '',
                ],
                'delete_global_config' => [
                    'name'    => 'delete_global_config',
                    'label'   => esc_html__('On Uninstall delete plugin data', 'cbxtakeatour'),
                    'desc'    => '<p>'.__('Delete Global Config data and custom table created by this plugin on uninstall.', 'cbxtakeatour').'</p>'.'<p>'.__('<strong>Please note that this process can not be undone, and it is recommended to keep full database backup before doing this.</strong>', 'cbxtakeatour').'</p>',
                    'type'    => 'radio',
                    'options' => [
                        'yes' => esc_html__('Yes', 'cbxtakeatour'),
                        'no'  => esc_html__('No', 'cbxtakeatour'),
                    ],
                    'default' => 'no'
                ],
                'reset_data'           => [
                    'name'    => 'reset_data',
                    'label'   => esc_html__('Reset all data', 'cbxtakeatour'),
                    'desc'    => $table_html.'<p>'.esc_html__('Reset option values and all tables created by this plugin', 'cbxtakeatour').'<a data-busy="0" class="button secondary ml-20" id="reset_data_trigger"  href="#">'.esc_html__('Reset Data', 'cbxtakeatour').'</a></p>',
                    'type'    => 'html',
                    'default' => 'off'
                ]
            ]
        ];

        return apply_filters('cbxtakeatour_setting_fields', $settings_fields);
    }//end method cbxtakeatour_setting_fields
}//end class CBXTakeaTourHelper