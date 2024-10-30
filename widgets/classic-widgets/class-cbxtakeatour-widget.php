<?php
// Prevent direct file access
if ( ! defined('ABSPATH')) {
    exit;
}

class CBXTakeaTour_Widget extends WP_Widget
{

    /**
     * Unique identifier for your widget.
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * widget file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $widget_slug = 'cbxtakeatour-widget'; //main parent plugin's language file
    /*--------------------------------------------------*/
    /* Constructor
    /*--------------------------------------------------*/

    /**
     * Specifies the classname and description, instantiates the widget,
     * loads localization files, and includes necessary stylesheets and JavaScript.
     */
    public function __construct()
    {
        parent::__construct(
            $this->get_widget_slug(),
            esc_html__('CBX Tour - User Walkthroughs', 'cbxtakeatour'),
            [
                'classname'   => 'widget-cbxtakeatour',
                'description' => esc_html__('CBX Tour - User Walkthroughs & Guided Tours Widget', 'cbxtakeatour')
            ]
        );

    }//end constructor

    /**
     * Return the widget slug.
     *
     * @return string
     */
    public function get_widget_slug()
    {
        return $this->widget_slug;
    }

    /**
     * Outputs the content of the widget.
     *
     * @param  array args  The array of form elements
     * @param  array instance The current instance of the widget
     */
    public function widget($args, $instance)
    {

        extract($args, EXTR_SKIP);

        $widget_string = $before_widget;

        $title = apply_filters('widget_title',
            empty($instance['title']) ? esc_html__('CBX Tour - User Walkthroughs',
                'cbxtakeatour') : $instance['title'], $instance, $this->id_base);
        // Defining the Widget Title
        if ($title) {
            $widget_string .= $args['before_title'].$title.$args['after_title'];
        } else {
            $widget_string .= $args['before_title'].$args['after_title'];
        }

        ob_start();

        $instance = apply_filters('cbxtakeatour_widget', $instance);

        $atts = [];

        $tour_id     = $atts['id'] = isset($instance['tour_id']) ? intval($instance['tour_id']) : 0;
        $button_text = $atts['button_text'] = isset($instance['button_text']) ? $instance['button_text'] : '';
        $display     = $atts['display'] = isset($instance['display']) ? $instance['display'] : '';
        $auto_start  = $atts['auto_start'] = isset($instance['auto_start']) ? $instance['auto_start'] : '';
        $block       = $atts['block'] = isset($instance['block']) ? $instance['block'] : '';
        $align       = $atts['align'] = isset($instance['align']) ? esc_attr($instance['align']) : '';

        extract($instance, EXTR_SKIP);

        $atts = apply_filters('cbxtakeatour_shortcode_builder_widget_attr', $atts, $instance);

        $attr_html = '';

        foreach ($atts as $key => $value) {
            $attr_html .= ' '.$key.'="'.esc_attr($value).'" ';
        }

        echo do_shortcode('[cbxtakeatour '.$attr_html.']');


        $content = ob_get_contents();
        ob_end_clean();

        $widget_string .= $content;

        //write_log($content);


        $widget_string .= $after_widget;

        echo $widget_string;

    }//end of method widget


    /**
     * Processes the widget's options to be saved.
     *
     * @param  array  $new_instance
     * @param  array  $old_instance
     *
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['title']       = isset($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['tour_id']     = isset($new_instance['tour_id']) ? intval($new_instance['tour_id']) : '';
        $instance['button_text'] = isset($new_instance['button_text']) ? sanitize_text_field($new_instance['button_text']) : '';
        $instance['display']     = isset($new_instance['display']) ? sanitize_text_field($new_instance['display']) : '';
        $instance['auto_start']  = isset($new_instance['auto_start']) ? sanitize_text_field($new_instance['auto_start']) : '';
        $instance['block']       = isset($new_instance['block']) ? sanitize_text_field($new_instance['block']) : '';
        $instance['align']       = isset($new_instance['align']) ? sanitize_text_field($new_instance['align']) : '';


        return apply_filters('cbxtakeatour_widget_update', $instance, $new_instance);
    }//end of method widget

    /**
     * Generates the administration form for the widget.
     *
     * @param  array instance The array of keys and values for the widget.
     */
    public function form($instance)
    {
        $defaults = [
            'title'       => esc_html__('CBX Tour - User Walkthroughs', 'cbxtakeatour'),
            'tour_id'     => 0,
            'button_text' => '',
            'display'     => '',
            'block'       => '',
            'auto_start'  => '',
            'align'       => '',
        ];

        $defaults = apply_filters('cbxtakeatour_widget_form_fields', $defaults);


        $instance = wp_parse_args(
            (array) $instance,
            $defaults
        );

        $instance = apply_filters('cbxtakeatour_widget_form', $instance);


        extract($instance, EXTR_SKIP);

        // Display the admin form
        include(plugin_dir_path(__FILE__).'views/admin.php');
    }//end of method form

}//end class CBXTakeaTour_Widget