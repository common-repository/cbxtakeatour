<?php

namespace CBXTakeaTourElemWidget\Widgets;


if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * CBX Take a Tour Elementor Widget
 */
class CBXTakeaTour_ElemWidget extends \Elementor\Widget_Base
{

    /**
     * Retrieve google maps widget name.
     *
     * @return string Widget name.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_name()
    {
        return 'cbxtakeatour';
    }

    /**
     * Retrieve google maps widget title.
     *
     * @return string Widget title.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_title()
    {
        return esc_html__('CBX Tour - User Walkthroughs', 'cbxtakeatour');
    }

    /**
     * Get widget categories.
     *
     * Retrieve the widget categories.
     *
     * @return array Widget categories.
     * @since  1.0.10
     * @access public
     *
     */
    public function get_categories()
    {
        return ['codeboxr'];
    }

    /**
     * Retrieve google maps widget icon.
     *
     * @return string Widget icon.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_icon()
    {
        return 'cbxtakeatour-icon';
    }

    /**
     * Register google maps widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function register_controls()
    {
        $this->start_controls_section(
            'section_cbxtakeatour_core',
            [
                'label' => esc_html__('CBX Tour - User Walkthroughs Settings', 'cbxtakeatour'),
            ]
        );

        do_action('cbxtakeatour_elementor_form_controls_start', $this);

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

        $tours[0] = esc_html__('Select Tour', 'cbxtakeatour');

        foreach ($tour_posts as $post) :
            //\CBXTakeaTourHelper::setup_admin_postdata( $post );
            //setup_postdata($post);
            $post_id    = intval($post->ID);
            $post_title = get_the_title($post_id);

            $tours[$post_id] = esc_attr($post_title);


        endforeach;
        //\CBXTakeaTourHelper::wp_reset_admin_postdata();
        //wp_reset_postdata();


        $this->add_control(
            'cbxtakeatour_tour_id',
            [
                'label'       => esc_html__('Select Tour', 'cbxtakeatour'),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'placeholder' => esc_html__('Select Tour', 'cbxtakeatour'),
                'options'     => $tours,
                'default'     => 0,
                'label_block' => true,
            ]
        );


        $this->add_control(
            'cbxtakeatour_button_text',
            [
                'label'   => esc_html__('Button Text (Leave empty to use tour post meta)', 'cbxtakeatour'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'cbxtakeatour_display_tour_button',
            [
                'label'   => esc_html__('Display Tour Button', 'cbxtakeatour'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    ''  => esc_html__('Use meta setting', 'cbxtakeatour'),
                    '1' => esc_html__('Yes', 'cbxtakeatour'),
                    '0' => esc_html__('No', 'cbxtakeatour'),
                ],
                'default' => '',
            ]
        );

        $this->add_control(
            'cbxtakeatour_auto_start',
            [
                'label'   => esc_html__('Tour Auto-start', 'cbxtakeatour'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    ''  => esc_html__('Use meta setting', 'cbxtakeatour'),
                    '1' => esc_html__('Yes', 'cbxtakeatour'),
                    '0' => esc_html__('No', 'cbxtakeatour'),
                ],
                'default' => '',
            ]
        );

        $this->add_control(
            'cbxtakeatour_block_button',
            [
                'label'   => esc_html__('Block Button (Full Width)', 'cbxtakeatour'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    ''  => esc_html__('Use meta setting', 'cbxtakeatour'),
                    '1' => esc_html__('Yes', 'cbxtakeatour'),
                    '0' => esc_html__('No', 'cbxtakeatour'),
                ],
                'default' => '',
            ]
        );

        $this->add_control(
            'cbxtakeatour_button_align',
            [
                'label'   => esc_html__('Button Align', 'cbxtakeatour'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    ''       => esc_html__('Use meta setting', 'cbxtakeatour'),
                    'center' => esc_html__('Center', 'cbxtakeatour'),
                    'left'   => esc_html__('Left', 'cbxtakeatour'),
                    'right'  => esc_html__('Right', 'cbxtakeatour'),
                    'none'   => esc_html__('None', 'cbxtakeatour'),
                ],
                'default' => 'center',
            ]
        );

        do_action('cbxtakeatour_elementor_form_controls_end', $this);

        $this->end_controls_section();
    }//end method _register_controls


    /**
     * Convert yes/no switch to integer value
     *
     * @param  string  $value
     *
     * @return int
     */
    private function yes_no_to_10($value = 'yes')
    {
        if ($value === 'yes') {
            return 1;
        }

        return 0;
    }//end yes_no_to_10

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render()
    {
        $atts = [];

        $instance = $this->get_settings();

        //render map from custom attributes
        $atts['id'] = isset($instance['cbxtakeatour_tour_id']) ? intval($instance['cbxtakeatour_tour_id']) : 0;

        if (intval($atts['id']) == 0) {
            echo esc_html__('Please select a tour', 'cbxtakeatour');
        } else {
            $atts['button_text'] = isset($instance['cbxtakeatour_button_text']) ? esc_attr(wp_unslash($instance['cbxtakeatour_button_text'])) : '';
            $atts['display']     = isset($instance['cbxtakeatour_display_tour_button']) ? esc_attr(wp_unslash($instance['cbxtakeatour_display_tour_button'])) : '';
            $atts['auto_start']  = isset($instance['cbxtakeatour_auto_start']) ? esc_attr(wp_unslash($instance['cbxtakeatour_auto_start'])) : '';
            $atts['block']       = isset($instance['cbxtakeatour_block_button']) ? esc_attr(wp_unslash($instance['cbxtakeatour_block_button'])) : '';
            $atts['align']       = isset($instance['cbxtakeatour_button_align']) ? esc_attr(wp_unslash($instance['cbxtakeatour_button_align'])) : '';

            $atts = apply_filters('cbxtakeatour_shortcode_builder_elementor_attr', $atts, $instance);

            $attr_html = '';

            foreach ($atts as $key => $value) {
                $attr_html .= ' '.$key.'="'.esc_attr($value).'" ';
            }

            echo do_shortcode('[cbxtakeatour '.$attr_html.']');
        }
    }//end method render
}//end method CBXTakeaTour_ElemWidget
