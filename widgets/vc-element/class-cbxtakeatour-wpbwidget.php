<?php
// Prevent direct file access
if ( ! defined('ABSPATH')) {
    exit;
}


class CBXTakeaTour_WPBWidget extends WPBakeryShortCode
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('init', [$this, 'bakery_shortcode_mapping'], 12);
    }// /end of constructor


    /**
     * Element Mapping
     */
    public function bakery_shortcode_mapping()
    {
        $args = [
            'post_type'      => 'cbxtour',
            'orderby'        => 'ID',
            'order'          => 'DESC',
            //'post_status'    => 'publish',
            'posts_per_page' => -1,
        ];

        $tour_posts = get_posts($args);
        $tours      = [];
        /*if ( ! is_admin() ) {
            global $post;
        }*/

        foreach ($tour_posts as $post) :
            //CBXTakeaTourHelper::setup_admin_postdata( $post );
            //setup_postdata($post);
            $post_id    = intval($post->ID);
            $post_title = get_the_title($post_id);

            $tours[esc_attr($post_title)] = $post_id;


        endforeach;
        //CBXTakeaTourHelper::wp_reset_admin_postdata();
        //wp_reset_postdata();

        // Map the block with vc_map()
        vc_map([
            "name"        => esc_html__("CBX Tour - User Walkthroughs", 'cbxtakeatour'),
            "description" => esc_html__("CBX Tour - User Walkthroughs & Guided Tours Widget", 'cbxtakeatour'),
            "base"        => "cbxtakeatour",
            "icon"        => CBXTAKEATOUR_ROOT_URL.'assets/images/icon.png',
            "category"    => esc_html__('CBX Widgets', 'cbxtakeatour'),
            "params"      => apply_filters('cbxtakeatour_wpbakery_params', [
                [
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Select Tour', 'cbxtakeatour'),
                    'param_name'  => 'id',
                    'admin_label' => true,
                    'value'       => $tours,
                ],
                [
                    "type"        => "textfield",
                    "holder"      => "div",
                    "class"       => "",
                    'admin_label' => true,
                    "heading"     => esc_html__("Button Text (Leave empty to use tour post meta)", 'cbxtakeatour'),
                    "param_name"  => "button_text",
                    "std"         => ''
                ],
                [
                    "type"        => "dropdown",
                    'admin_label' => true,
                    "heading"     => esc_html__("Display Tour Button", 'cbxtakeatour'),
                    "param_name"  => "display",
                    'value'       => [
                        esc_html__('Use meta setting', 'cbxtakeatour') => '',
                        esc_html__('Show', 'cbxtakeatour')             => 1,
                        esc_html__('Hide', 'cbxtakeatour')             => 0,
                    ],
                    'std'         => '',
                ],
                [
                    "type"        => "dropdown",
                    'admin_label' => true,
                    "heading"     => esc_html__("Tour Auto-start", 'cbxtakeatour'),
                    "param_name"  => "auto_start",
                    'value'       => [
                        esc_html__('Use meta setting', 'cbxtakeatour') => '',
                        esc_html__('Yes', 'cbxtakeatour')              => 1,
                        esc_html__('No', 'cbxtakeatour')               => 0,
                    ],
                    'std'         => '',
                ],
                [
                    "type"        => "dropdown",
                    'admin_label' => true,
                    "heading"     => esc_html__("Block Button (Full Width)", 'cbxtakeatour'),
                    "param_name"  => "block",
                    'value'       => [
                        esc_html__('Use meta setting', 'cbxtakeatour') => '',
                        esc_html__('Yes', 'cbxtakeatour')              => 1,
                        esc_html__('No', 'cbxtakeatour')               => 0,
                    ],
                    'std'         => '',
                ],
                [
                    "type"        => "dropdown",
                    'admin_label' => true,
                    "heading"     => esc_html__("Button Align", 'cbxtakeatour'),
                    "param_name"  => "align",
                    'value'       => [
                        esc_html__('Use meta setting', 'cbxtakeatour') => '',
                        esc_html__('Center', 'cbxtakeatour')           => 'center',
                        esc_html__('Left', 'cbxtakeatour')             => 'left',
                        esc_html__('Right', 'cbxtakeatour')            => 'right',
                        esc_html__('None', 'cbxtakeatour')             => 'none',
                    ],
                    'std'         => '',
                ],
            ])
        ]);
    }//end bakery_shortcode_mapping
}// end class CBXTakeaTour_WPBWidget