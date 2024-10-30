<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/public
 * @author     Codeboxr Team <sabuj@codeboxr.com>
 */
class CBXTakeaTour_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

	/**
	 * for setting
	 * @since    1.0.0
	 * @access   private
	 * @var      string $settings_api The current version of this plugin.
	 * */
	private $settings_api;

    /**
     * Initialize the class and set its properties.
     *
     * @param  string  $plugin_name  The name of the plugin.
     * @param  string  $version  The version of this plugin.
     *
     * @since    1.0.0
     *
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        if (defined('WP_DEBUG') && WP_DEBUG) {
            $this->version = current_time('timestamp'); //for development time only
        }

        $this->settings_api = new CBXTakeaTour_Settings();
    }//end method construct

    /**
     * [not in used anymore ] Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        $css_url_part         = CBXTAKEATOUR_ROOT_URL.'assets/css/';
        $css_url_part_vendors = CBXTAKEATOUR_ROOT_URL.'assets/vendors/';
        $js_url_part          = CBXTAKEATOUR_ROOT_URL.'assets/js/';
        $js_url_part_vendors  = CBXTAKEATOUR_ROOT_URL.'assets/vendors/';


        wp_register_style('cbxtakeatour-public', $css_url_part.'cbxtakeatour-public.css', [], $this->version, 'all');
        wp_enqueue_style('cbxtakeatour-public');
    }//end enqueue_styles

    /**
     * [not in used anymore ] Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        $css_url_part         = CBXTAKEATOUR_ROOT_URL.'assets/css/';
        $css_url_part_vendors = CBXTAKEATOUR_ROOT_URL.'assets/vendors/';
        $js_url_part          = CBXTAKEATOUR_ROOT_URL.'assets/js/';
        $js_url_part_vendors  = CBXTAKEATOUR_ROOT_URL.'assets/vendors/';


        wp_register_script('tourguidejs', $js_url_part_vendors.'tourguidejs/tour.js', ['jquery'], $this->version, true);
        wp_register_script('cbxtakeatour-events', $js_url_part.'cbxtakeatour-events.js', [], $this->version, true);
        wp_register_script("cbxtakeatour-public",
            $js_url_part.'cbxtakeatour-public.js',
            ['cbxtakeatour-events', 'jquery', 'tourguidejs'],
            $this->version,
            true);


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
    }//end enqueue_scripts

    /**
     * Register and enqueue all public styles and scripts
     *
     * @return void
     */
    public function public_styles_scripts()
    {
        CBXTakeaTourHelper::public_styles_scripts($this->version);
    }//end method public_styles_scripts

    /**
     * This method add shortcode and decides the name od shortcode
     */
    public function init_shortcode()
    {
        add_shortcode('cbxtakeatour', [$this, 'cbxtakeatour_shortcode']);
    }//end method init_shortcode

    /**
     * This method holds the all data like how shortcode would be
     *
     * @param $atts
     *
     * @return string
     */
    public function cbxtakeatour_shortcode($atts)
    {
        //normalize attribute keys, lowercase
        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        $atts = shortcode_atts([
            'id'          => 0,
            'button_text' => '',
            'align'       => '', //'left', 'center', 'right', 'none', put empty to ignore shortcode param
            'block'       => '', //1 = block/full width ,  0 = not applicable , put empty to ignore shortcode param
            'display'     => '', //1 = display , 0 = hide , put empty to ignore shortcode param
            'auto_start'  => ''  // 1  = auto start, 0 = starts on click, put empty to ignore shortcode param
        ], $atts, 'cbxtakeatour');


        $id = isset($atts['id']) ? absint($atts['id']) : 0;
        if ($id == 0) {
            return '';
        }

        $id = intval($id);

        $tour = get_post($id);
        if (is_null($tour)) {
            return '';
        }

	    $atts['button_text'] = trim(esc_attr($atts['button_text']));
	    $atts['align'] = $align = trim(esc_attr($atts['align']));
		if($align != '' && !in_array($align, ['left', 'center', 'right', 'none'])){
			$atts['align'] = $align = '';
		}

	    $atts['block'] = $block = trim(esc_attr($atts['block']));
		if($block != ''){
			$atts['block'] = $block = absint($block);
		}

	    $atts['display'] = $display = trim(esc_attr($atts['display']));
		if($display != ''){
			$atts['display'] = $display = absint($display);
		}

	    $atts['auto_start'] = $auto_start = trim(esc_attr($atts['auto_start']));
	    if($auto_start != ''){
		    $atts['auto_start'] = $auto_start = absint($auto_start);
	    }

        $meta = get_post_meta($id, '_cbxtourmeta', true);


        if ( ! empty($atts['button_text'])) {
            $meta['tour_button_text'] = esc_attr(wp_unslash($atts['button_text']));
        }

        if ( ! empty($atts['align'])) {
            $meta['tour_button_align'] = esc_attr(wp_unslash($atts['align']));
        }

        if ($atts['block'] != '') {
            $meta['tour_button_block'] = absint($atts['block']);
        }

        if ($atts['display'] != '') {
            $meta['display'] = absint($atts['display']);
        }

        if ($atts['auto_start'] != '') {
            $meta['auto_start'] = absint($atts['auto_start']);
        }

        $meta['id'] = $id;

        return cbxtakeatour_display(apply_filters('cbxtakeatour_tour_data', $meta, $atts, $id));
    }// end of cbxtakeatour_shortcode

    /**
     * Classic widget
     */
    public function register_widget()
    {
        if ( ! class_exists('CBXTakeaTourHelper')) {
            require_once plugin_dir_path(dirname(__FILE__)).'includes/class-cbxtakeatour-helper.php';
        }

        if ( ! class_exists('CBXTakeaTour_Widget')) {
            require_once plugin_dir_path(dirname(__FILE__)).'widgets/classic-widgets/class-cbxtakeatour-widget.php';
        }

        register_widget("CBXTakeaTour_Widget");
    }//end register_widget

    /**
     * Init elementor widget
     *
     * @throws Exception
     */
    public function init_elementor_widgets()
    {
        //include the file
        require_once plugin_dir_path(dirname(__FILE__)).'widgets/elementor-widgets/class-cbxtakeatour-elemwidget.php';

        //register the widget
        \Elementor\Plugin::instance()->widgets_manager->register(new CBXTakeaTourElemWidget\Widgets\CBXTakeaTour_ElemWidget());
    }//end widgets_registered

    /**
     * Add new category to elementor
     *
     * @param $elements_manager
     */
    public function add_elementor_widget_categories($elements_manager)
    {
        $elements_manager->add_category(
            'codeboxr',
            [
                'title' => esc_html__('Codeboxr Widgets', 'cbxtakeatour'),
                'icon'  => 'fa fa-plug',
            ]
        );
    }//end add_elementor_widget_categories

    /**
     * Load Elementor Custom Icon
     */
    public function elementor_icon_loader()
    {
        wp_register_style('cbxtakeatour_elementor_icon',
            CBXTAKEATOUR_ROOT_URL.'widgets/elementor-widgets/elementor-icon/icon.css', false, $this->version);
        wp_enqueue_style('cbxtakeatour_elementor_icon');

        /*wp_register_style( 'bootstrap-tour-standalone',
            CBXTAKEATOUR_ROOT_URL.'assets/css/bootstrap-tour-standalone.css',
            [],
            $this->version,
            'all' );
        wp_register_style( 'bootstrap-tour-standalone-red',
            CBXTAKEATOUR_ROOT_URL.'assets/css/bootstrap-tour-standalone_red.css',
            [],
            $this->version,
            'all' );

        wp_register_style( 'cbxtakeatour-public',
            CBXTAKEATOUR_ROOT_URL.'assets/css/cbxtakeatour-public.css',
            array( 'bootstrap-tour-standalone' ),
            $this->version,
            'all' );

        wp_enqueue_style( 'bootstrap-tour-standalone' );
        wp_enqueue_style( 'cbxtakeatour-public' );*/

    }//end elementor_icon_loader

    /**
     * Before VC Init
     */
    public function vc_before_init_actions()
    {
        if ( ! class_exists('CBXTakeaTour_WPBWidget')) {
            require_once CBXTAKEATOUR_ROOT_PATH.'widgets/vc-element/class-cbxtakeatour-wpbwidget.php';
        }

        new CBXTakeaTour_WPBWidget();
    }// end method vc_before_init_actions
}//end class CBXTakeaTour_Public