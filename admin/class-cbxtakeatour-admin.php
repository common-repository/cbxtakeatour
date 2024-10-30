<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    CBXTakeaTour
 * @subpackage CBXTakeaTour/admin
 * @author     Codeboxr Team <sabuj@codeboxr.com>
 */
class CBXTakeaTour_Admin
{

    private $cbxtakeatour;

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
     * @param  string  $plugin_name  The name of this plugin.
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
    }//end of construct

    /**
     * Init plugin setting (using wordpress setting api)
     */
    public function setting_init()
    {
        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());

        //initialize settings
        $this->settings_api->admin_init();
    }//end setting_init

    /**
     * Set settings fields
     *
     * @return type array
     */
    public function get_settings_sections(){
        return CBXTakeaTourHelper::cbxtakeatour_setting_sections();
    }//end get_settings_sections

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields(){
        return CBXTakeaTourHelper::cbxtakeatour_setting_fields();
    }//end get_settings_fields

    /**
     * Custom admin menu pages
     */
    public function admin_pages()
    {
        $page = isset($_GET['page']) ? esc_attr(wp_unslash($_GET['page'])) : '';

        $main_page_hook = add_menu_page(esc_html__('CBX Tours', 'cbxtakeatour'),
            esc_html__('Tours', 'cbxtakeatour'),
            'manage_options',
            'cbxtakeatour-listing',
            [$this, 'menu_tours'], CBXTAKEATOUR_ROOT_URL.'assets/images/icon_w_24.png');

        //add settings for this plugin
        $setting_page_hook = add_submenu_page('cbxtakeatour-listing',
            esc_html__('Global Setting', 'cbxtakeatour'),
            esc_html__('Global Setting', 'cbxtakeatour'),
            'manage_options',
            'cbxtakeatour-settings',
            [$this, 'menu_settings']);

        $support_page_hook = add_submenu_page('cbxtakeatour-listing',
            esc_html__('Helps & Updates', 'cbxtakeatour'),
            esc_html__('Helps & Updates', 'cbxtakeatour'),
            'manage_options', 'cbxtakeatour-support',
            [$this, 'menu_support']);


        //add screen save option for bookmark listing
        if (isset($_GET['page']) && $_GET['page'] == 'cbxtakeatour-listing') {
            add_action("load-$main_page_hook", [$this, 'menu_listing_screen']);
        }


        do_action('cbxtakeatour_admin_pages');
    }//end admin_pages

    /**
     * Add screen option for tour listing
     */
    public function menu_listing_screen()
    {
        $option = 'per_page';
        $args   = [
            'label'   => esc_html__('Number of items per page', 'cbxtakeatour'),
            'default' => 50,
            'option'  => 'cbxtakeatour_listing_per_page',
        ];
        add_screen_option($option, $args);
    }//end method menu_listing_screen

    /**
     * Set options for cbxtakeatour listing result (called from hook 'set-screen-option')
     *
     * @param $new_status
     * @param $option
     * @param $value
     *
     * @return mixed
     */
    public function cbxtakeatour_listing_per_page($new_status, $option, $value)
    {
        if ('cbxtakeatour_listing_per_page' == $option) {
            return $value;
        }

        return $new_status;
    }//end cbxtakeatour_listing_per_page

    /**
     * log listing screen option columns
     *
     * @param $columns
     *
     * @return mixed
     */
    public function tour_listing_screen_cols($columns)
    {
        $columns = [
            'post_title'    => esc_html__('Title', 'cbxtakeatour'),
            'post_author'   => esc_html__('User', 'cbxtakeatour'),
            'post_status'   => esc_html__('Status', 'cbxtakeatour'),
            'post_date'     => esc_html__('Created', 'cbxtakeatour'),
            'post_modified' => esc_html__('Updated', 'cbxtakeatour'),
            'shortcode'     => esc_html__('Shortcode', 'cbxtakeatour'),
        ];

        return apply_filters('cbxtakeatour_list_admin_screen_columns', $columns);

    }//end tour_listing_screen_cols

    /**
     * Tour listing menu
     */
    public function menu_tours()
    {
        $view = isset($_REQUEST['view']) ? $_REQUEST['view'] : '';

        $template_loaded = false;

        if ($view == 'add') {
            $post_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;

            if ($post_id > 0 && current_user_can('edit_post', $post_id)) {
                $tour = get_post($post_id);
                if ($tour !== null) {
                    $template_loaded = true;
                    echo cbxtakeatour_get_template_html('admin/tours_add.php', [
                        'admin_ref' => $this,
                        'settings'  => $this->settings_api,
                        'id'        => $post_id,
                        'ID'        => $post_id,//compatibility fix
                        'tour'      => $tour,
                    ]);
                }
            }

        } else {
            if ( ! class_exists('CBXTakeaTour_Listing')) {
                require_once plugin_dir_path(dirname(__FILE__)).'includes/class-cbxtakeatour-listing.php';
            }

            $template_loaded = true;
            echo cbxtakeatour_get_template_html('admin/tours_list.php', [
                'admin_ref' => $this,
                'settings'  => $this->settings_api
            ]);
        }

        if ( ! $template_loaded) {
            echo cbxtakeatour_get_template_html('admin/tours_error.php', [
                'admin_ref'  => $this,
                'settings'   => $this->settings_api,
                'error_text' => esc_html__('Invalid tour or you don\'t have permission to edit this tour.', 'cbxtakeatour')
            ]);
        }

    }//end method menu_tours

    /**
     * Display settings page
     *
     * @global type $wpdb
     */
    public function menu_settings()
    {
        echo cbxtakeatour_get_template_html('admin/settings.php', [
            'admin_ref' => $this,
            'settings'  => $this->settings_api
        ]);
    }//end menu_settings

    /**
     * Render the help & support page for this plugin.
     *
     * @since    1.0.8
     */
    public function menu_support()
    {
        echo cbxtakeatour_get_template_html('admin/support.php');
    }//end method menu_support


    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook){
        global $post_type, $post;

        $version = $this->version;
        $page    = isset($_GET['page']) ? esc_attr(wp_unslash($_GET['page'])) : '';


        $css_url_part     = CBXTAKEATOUR_ROOT_URL.'assets/css/';
        $js_url_part      = CBXTAKEATOUR_ROOT_URL.'assets/js/';
        $vendors_url_part = CBXTAKEATOUR_ROOT_URL.'assets/vendors/';

        $css_path_part     = CBXTAKEATOUR_ROOT_PATH.'assets/css/';
        $js_path_part      = CBXTAKEATOUR_ROOT_PATH.'assets/js/';
        $vendors_path_part = CBXTAKEATOUR_ROOT_PATH.'assets/vendors/';

        //tour add/edit screen +  listing
        if ($page == 'cbxtakeatour-listing') {
	        wp_register_style('awesome-notifications', $vendors_url_part.'awesome-notifications/style.css', [], $version);
	        wp_register_style('pickr', $vendors_url_part.'pickr/themes/classic.min.css', [], $version);
	        wp_register_style('cbxtakeatour-admin', $css_url_part.'cbxtakeatour-admin.css', [], $version);

            wp_register_style('cbxtakeatour-manage', $css_url_part.'cbxtakeatour-manage.css',
                ['pickr', 'editor-buttons', 'awesome-notifications', 'cbxtakeatour-admin'], $version);

            wp_enqueue_style('editor-buttons');
            wp_enqueue_style('awesome-notifications');
            wp_enqueue_style('pickr');


            wp_enqueue_style('cbxtakeatour-admin');//common admin styles
            wp_enqueue_style('cbxtakeatour-manage');
        }


        if ($page == 'cbxtakeatour-settings') {
	        wp_register_style('awesome-notifications', $vendors_url_part.'awesome-notifications/style.css', [], $version);
	        wp_register_style('pickr', $vendors_url_part.'pickr/themes/classic.min.css', [], $version);
            wp_register_style('select2', $vendors_url_part.'select2/css/select2.min.css', [], $version);

	        wp_register_style('cbxtakeatour-admin', $css_url_part.'cbxtakeatour-admin.css', [], $version);
            wp_register_style('cbxtakeatour-setting', $css_url_part.'cbxtakeatour-setting.css',
                ['pickr', 'select2', 'awesome-notifications', 'cbxtakeatour-admin'], $version);

            wp_enqueue_style('pickr');
            wp_enqueue_style('select2');
            wp_enqueue_style('awesome-notifications');

            wp_enqueue_style('cbxtakeatour-admin');//common admin styles
            wp_enqueue_style('cbxtakeatour-setting');
        }

        if ($page == 'cbxtakeatour-support') {
	        wp_register_style('cbxtakeatour-admin', $css_url_part.'cbxtakeatour-admin.css', [], $version);
            wp_enqueue_style('cbxtakeatour-admin');//common admin styles
        }

        //dashboard common menu custom image hack
        $dashboard_menu_style_fix = '
            #adminmenu .toplevel_page_cbxtakeatour-listing .wp-menu-image {
                height: 34px;
            }
            #adminmenu .toplevel_page_cbxtakeatour-listing .wp-menu-image img {
                padding:5px 0 0 0 !important;
                vertical-align: middle;
            }
        ';

        wp_register_style('cbxtakeatour-dashboard-menu-style-fix', false, ['common']);
        wp_enqueue_style('cbxtakeatour-dashboard-menu-style-fix');
        wp_add_inline_style('cbxtakeatour-dashboard-menu-style-fix', $dashboard_menu_style_fix);
    }//end method enqueue_styles


    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook)
    {
        global $post_type, $post;

        $version = $this->version;
        $page    = isset($_GET['page']) ? esc_attr(wp_unslash($_GET['page'])) : '';
        $view    = isset($_GET['view']) ? esc_attr(wp_unslash($_GET['view'])) : '';

        $css_url_part     = CBXTAKEATOUR_ROOT_URL.'assets/css/';
        $js_url_part      = CBXTAKEATOUR_ROOT_URL.'assets/js/';
        $vendors_url_part = CBXTAKEATOUR_ROOT_URL.'assets/vendors/';

        $css_path_part     = CBXTAKEATOUR_ROOT_PATH.'assets/css/';
        $js_path_part      = CBXTAKEATOUR_ROOT_PATH.'assets/js/';
        $vendors_path_part = CBXTAKEATOUR_ROOT_PATH.'assets/vendors/';


        $translation_placeholder =
            [
                'ajaxurl'                  => admin_url('admin-ajax.php'),
                'ajax_fail'                => esc_html__('Request failed, please reload the page.', 'cbxtakeatour'),
                'nonce'                    => wp_create_nonce("settingsnonce"),
                'editnonce'                => wp_create_nonce("cbxtakeatournonce"),
                'is_user_logged_in'        => is_user_logged_in() ? 1 : 0,
                'please_select'            => esc_html__('Please Select', 'cbxtakeatour'),
                'upload_title'             => esc_html__('Window Title', 'cbxtakeatour'),
                'search_placeholder'       => esc_html__('Search here', 'cbxtakeatour'),
                'teeny_setting'            => [
                    'teeny'         => true,
                    'media_buttons' => true,
                    'editor_class'  => '',
                    'textarea_rows' => 5,
                    'quicktags'     => false,
                    'menubar'       => false,
                ],
                'copycmds'                 => [
                    'copy'       => esc_html__('Copy', 'cbxtakeatour'),
                    'copied'     => esc_html__('Copied', 'cbxtakeatour'),
                    'copy_tip'   => esc_html__('Click to copy', 'cbxtakeatour'),
                    'copied_tip' => esc_html__('Copied to clipboard', 'cbxtakeatour'),
                ],
                'confirm_msg'              => esc_html__('Are you sure to remove this step?', 'cbxtakeatour'),
                'confirm_msg_all'          => esc_html__('Are you sure to remove all steps?', 'cbxtakeatour'),
                'confirm_yes'              => esc_html__('Yes', 'cbxtakeatour'),
                'confirm_no'               => esc_html__('No', 'cbxtakeatour'),
                'are_you_sure_global'      => esc_html__('Are you sure?', 'cbxtakeatour'),
                'are_you_sure_delete_desc' => esc_html__('Once you delete, it\'s gone forever. You can not revert it back.', 'cbxtakeatour'),
                'pickr_i18n'               => [
                    // Strings visible in the UI
                    'ui:dialog'       => esc_html__('color picker dialog', 'cbxtakeatour'),
                    'btn:toggle'      => esc_html__('toggle color picker dialog', 'cbxtakeatour'),
                    'btn:swatch'      => esc_html__('color swatch', 'cbxtakeatour'),
                    'btn:last-color'  => esc_html__('use previous color', 'cbxtakeatour'),
                    'btn:save'        => esc_html__('Save', 'cbxtakeatour'),
                    'btn:cancel'      => esc_html__('Cancel', 'cbxtakeatour'),
                    'btn:clear'       => esc_html__('Clear', 'cbxtakeatour'),

                    // Strings used for aria-labels
                    'aria:btn:save'   => esc_html__('save and close', 'cbxtakeatour'),
                    'aria:btn:cancel' => esc_html__('cancel and close', 'cbxtakeatour'),
                    'aria:btn:clear'  => esc_html__('clear and close', 'cbxtakeatour'),
                    'aria:input'      => esc_html__('color input field', 'cbxtakeatour'),
                    'aria:palette'    => esc_html__('color selection area', 'cbxtakeatour'),
                    'aria:hue'        => esc_html__('hue selection slider', 'cbxtakeatour'),
                    'aria:opacity'    => esc_html__('selection slider', 'cbxtakeatour'),
                ],
                'awn_options'              => [
                    'tip'           => esc_html__('Tip', 'cbxtakeatour'),
                    'info'          => esc_html__('Info', 'cbxtakeatour'),
                    'success'       => esc_html__('Success', 'cbxtakeatour'),
                    'warning'       => esc_html__('Attention', 'cbxtakeatour'),
                    'alert'         => esc_html__('Error', 'cbxtakeatour'),
                    'async'         => esc_html__('Loading', 'cbxtakeatour'),
                    'confirm'       => esc_html__('Confirmation', 'cbxtakeatour'),
                    'confirmOk'     => esc_html__('OK', 'cbxtakeatour'),
                    'confirmCancel' => esc_html__('Cancel', 'cbxtakeatour')
                ],
                'validation'               => [
                    'required'    => esc_html__('This field is required.', 'cbxtakeatour'),
                    'remote'      => esc_html__('Please fix this field.', 'cbxtakeatour'),
                    'email'       => esc_html__('Please enter a valid email address.', 'cbxtakeatour'),
                    'url'         => esc_html__('Please enter a valid URL.', 'cbxtakeatour'),
                    'date'        => esc_html__('Please enter a valid date.', 'cbxtakeatour'),
                    'dateISO'     => esc_html__('Please enter a valid date ( ISO ).', 'cbxtakeatour'),
                    'number'      => esc_html__('Please enter a valid number.', 'cbxtakeatour'),
                    'digits'      => esc_html__('Please enter only digits.', 'cbxtakeatour'),
                    'equalTo'     => esc_html__('Please enter the same value again.', 'cbxtakeatour'),
                    'maxlength'   => esc_html__('Please enter no more than {0} characters.', 'cbxtakeatour'),
                    'minlength'   => esc_html__('Please enter at least {0} characters.', 'cbxtakeatour'),
                    'rangelength' => esc_html__('Please enter a value between {0} and {1} characters long.', 'cbxtakeatour'),
                    'range'       => esc_html__('Please enter a value between {0} and {1}.', 'cbxtakeatour'),
                    'max'         => esc_html__('Please enter a value less than or equal to {0}.', 'cbxtakeatour'),
                    'min'         => esc_html__('Please enter a value greater than or equal to {0}.', 'cbxtakeatour'),
                    'recaptcha'   => esc_html__('Please check the captcha.', 'cbxtakeatour'),
                ],
                'global_setting_link_html' => '<a href="'.admin_url('admin.php?page=cbxtakeatour-settings').'"  class="button outline primary pull-right">'.esc_html__('Global Settings', 'cbxtakeatour').'</a>',
                'lang'                     => get_user_locale(),
                'search_text' => esc_html__('Search', 'cbxtakeatour')
            ];


        //tour listing
        if ($page == 'cbxtakeatour-listing' && $view == '') {
	        wp_register_script('awesome-notifications', $vendors_url_part.'awesome-notifications/script.js', [], $version, true);
            wp_register_script('cbxtakeatour-listing', $js_url_part.'cbxtakeatour-listing.js', ['jquery', 'awesome-notifications'], $version, true);
            wp_localize_script('cbxtakeatour-listing', 'cbxtakeatour_listing', apply_filters('cbxtakeatour-listing-vars', $translation_placeholder));
            wp_enqueue_script('jquery');
            wp_enqueue_script('awesome-notifications');

            wp_enqueue_script('cbxtakeatour-listing');
        }

        //tour add/edit
        if ($page == 'cbxtakeatour-listing' && $view == 'add') {
	        wp_register_script('jquery-validate', $vendors_url_part.'jquery-validation/jquery.validate.min.js', ['jquery'], $version, true);
	        wp_register_script('awesome-notifications', $vendors_url_part.'awesome-notifications/script.js', [], $version, true);
	        wp_register_script('pickr', $vendors_url_part.'pickr/pickr.min.js', [], $version, true);

            wp_register_style('cbxtakeatour-public', $css_url_part.'cbxtakeatour-public.css', [], $version, 'all');
            wp_enqueue_style('cbxtakeatour-public');

            wp_register_script('minitoggle', $js_url_part.'minitoggle.js', ['jquery'], $version, true);

            wp_register_script('cbxtakeatour-edit', $js_url_part.'cbxtakeatour-edit.js',
                ['jquery', 'editor', 'awesome-notifications', 'pickr', 'minitoggle', 'jquery-validate'],
                $version, true);

            wp_localize_script('cbxtakeatour-edit', 'cbxtakeatour_edit', apply_filters('cbxtakeatour_edit_vars', $translation_placeholder));

            if ( ! class_exists('_WP_Editors', false)) {
                require(ABSPATH.WPINC.'/class-wp-editor.php');
            }
            \_WP_Editors::print_tinymce_scripts();

            wp_enqueue_media();
            wp_enqueue_script('jquery');
            wp_enqueue_script('editor');
            wp_enqueue_script('awesome-notifications');
            wp_enqueue_script('pickr');
            wp_enqueue_script('minitoggle');
            wp_enqueue_script('jquery-validate');

            wp_enqueue_script('cbxtakeatour-edit');

            CBXTakeaTourHelper::public_styles_scripts($version);
        }


        //setting page
        if ($page == 'cbxtakeatour-settings') {
            wp_enqueue_script('jquery');
            wp_enqueue_media();

	        wp_register_script('awesome-notifications', $vendors_url_part.'awesome-notifications/script.js', [], $version, true);
	        wp_register_script('pickr', $vendors_url_part.'pickr/pickr.min.js', [], $version, true);
            wp_register_script('select2', $vendors_url_part.'select2/js/select2.min.js', ['jquery'], $version, true);
            wp_register_script('cbxtakeatour-setting', CBXTAKEATOUR_ROOT_URL.'assets/js/cbxtakeatour-setting.js',
                [
                    'jquery',
                    'select2',
                    'pickr',
                    'awesome-notifications'
                ],
                $version, true);


            wp_localize_script('cbxtakeatour-setting', 'cbxtakeatour_setting', apply_filters('cbxtakeatour_setting_vars', $translation_placeholder));

            wp_enqueue_script('jquery');
            wp_enqueue_script('pickr');
            wp_enqueue_script('select2');
            wp_enqueue_script('awesome-notifications');

            wp_enqueue_script('cbxtakeatour-setting');
        }
    }//end enqueue_scripts


    // Register Custom Post Type
    public function create_tour()
    {

        $labels = [
            'name'               => _x('CBX Tours', 'Post Type General Name', 'cbxtakeatour'),
            'singular_name'      => _x('CBX Tour', 'Post Type Singular Name', 'cbxtakeatour'),
            'menu_name'          => esc_html__('CBX Tour', 'cbxtakeatour'),
            'parent_item_colon'  => esc_html__('Parent Tour:', 'cbxtakeatour'),
            'all_items'          => esc_html__('All Tours', 'cbxtakeatour'),
            'view_item'          => esc_html__('View Tour', 'cbxtakeatour'),
            'add_new_item'       => esc_html__('Add New Tour', 'cbxtakeatour'),
            'add_new'            => esc_html__('Add New', 'cbxtakeatour'),
            'edit_item'          => esc_html__('Edit Tour', 'cbxtakeatour'),
            'update_item'        => esc_html__('Update Tour', 'cbxtakeatour'),
            'search_items'       => esc_html__('Search Tour', 'cbxtakeatour'),
            'not_found'          => esc_html__('No tours available, please create one.', 'cbxtakeatour'),
            'not_found_in_trash' => esc_html__('Not found in Trash', 'cbxtakeatour'),
        ];
        $args   = [
            'label'               => esc_html__('cbxtour', 'cbxtakeatour'),
            'description'         => esc_html__('Tour steps', 'cbxtakeatour'),
            'labels'              => $labels,
            'supports'            => ['title', 'custom-fields'],
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => false,
            'show_in_menu'        => false,  //left dashboard menu
            'show_in_nav_menus'   => false,  //menu create interface
            'show_in_admin_bar'   => false,  //top admin bar menu
            'menu_position'       => 5,
            'menu_icon'           => CBXTAKEATOUR_ROOT_URL.'assets/images/icon_w_24.png',
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => 'post',
        ];
        register_post_type('cbxtour', $args);
    }// End create_tour


    /**
     * Renders metabox in right col to show  shortcode with copy to clipboard
     */
    function cbxtourmetabox_shortcode_display()
    {
        global $post;
        $post_id = $post->ID;

        echo '<span data-clipboard-text=\'[cbxtakeatour id="'.intval($post_id).'"]\' title="'.esc_html__("Click to clipboard",
                "cbxtakeatour").'" id="cbxtakeatourshortcode-'.intval($post_id).'" class="cbxtakeatourshortcode cbxtakeatourshortcode-edit cbxtakeatourshortcode-single cbxtakeatourshortcode-'.intval($post_id).'">[cbxtakeatour id="'.intval($post_id).'"]</span>';
        echo '<div class="cbxtourclear"></div>';
    }//End cbxtourmetabox_shortcode_display


    /**
     * (Not in use) Show preview Tour button & box
     */
    public function cbxtourmetabox_preview_display()
    {
        global $post;
        $post_id = intval($post->ID);
        $meta    = get_post_meta($post_id, '_cbxtourmeta', true);

        $layout           = isset($meta['layout']) ? esc_attr(wp_unslash($meta['layout'])) : 'basic';
        $tour_button_text = (isset($meta['tour_button_text']) && $meta['tour_button_text'] != '') ? esc_attr(wp_unslash($meta['tour_button_text'])) : esc_html__('Take a tour',
            'cbxtakeatour');


        $layouts = array_keys(CBXTakeaTourHelper::cbxtakeatour_layouts());

        if ( ! in_array($layout, $layouts)) {
            $layout = 'basic';
        }

        $layout_class = 'cbxtakeatour_popover_'.$layout;
        $button_class = 'cbxtakeatour-btn-'.$layout;

        $ready_layouts = CBXTakeaTourHelper::cbxtakeatour_layout_ready_styles();
        if (array_key_exists($layout, $ready_layouts)) {
            $custom_css = CBXTakeaTourHelper::add_custom_css($post_id, CBXTakeaTourHelper::cbxtakeatour_layout_ready_style($layout));

            wp_register_style('cbxtakeatour-admin-inline', false, ['cbxtakeatour-admin']);
            wp_enqueue_style('cbxtakeatour-admin-inline');
            wp_add_inline_style('cbxtakeatour-admin-inline', $custom_css);
        }

        do_action('cbxtakeatour_display_tour_admin_enqueue', $layout, $post_id, $meta);
        ?>
        <div class="cbxtakeatour-preview">
            <div class="cbxtakeatour_button">
                <h3><?php esc_html_e('Tour Button/Link Preview',
                        'cbxtakeatour') ?></h3>
                <a href="#"
                   class="cbxtakeatour-btn <?php echo esc_attr($button_class); ?> cbxtakeatour-btn-<?php echo intval($post_id); ?>"><?php echo esc_attr($tour_button_text) ?></a>
            </div>

            <div class="cbxtakeatour_box">
                <h3><?php esc_html_e('Tour Box Preview',
                        'cbxtakeatour') ?></h3>
                <div class="cbxtakeatour_popover <?php echo esc_attr($layout_class); ?> cbxtakeatour_popover_<?php echo intval($post_id); ?> tour-tour tour-tour-0 fade show bs-cbxtakeatour_popover-top"
                     role="tooltip" id="step-0" x-placement="top"
                     style="position: relative;">
                    <div class="cbxtatarrow" style="left: 133px;"></div>
                    <h3 style="margin-top: 0px" class="cbxtakeatour_popover-header ">Welcome to CBX Tour!</h3>
                    <div class="cbxtakeatour_popover-body">Introduce new users to your product by walking them through
                        it step by step
                    </div>
                    <div class="cbxtakeatour_popover-navigation">
                        <div class="btn-group">
                            <a href="#" class="btn btn-sm btn-secondary disabled" disabled=""
                               tabindex="-1">« Prev </a> <a href="#" class="btn btn-sm btn-secondary">Next »</a>
                        </div>
                        <a href="#" style="float: right;" class="btn btn-sm btn-secondary">End tour</a>
                    </div>
                </div>
            </div>
        </div>
    <?php }// End cbxtourmetabox_preview_display


    /**
     * If we need to do something in upgrader process is completed for poll plugin
     *
     * @param $upgrader_object
     * @param $options
     */
    public function plugin_upgrader_process_complete($upgrader_object, $options)
    {
        if (isset($options['plugins']) && $options['action'] == 'update' && $options['type'] == 'plugin') {
            if (isset($options['plugins']) && is_array($options['plugins']) && sizeof($options['plugins']) > 0) {
                foreach ($options['plugins'] as $each_plugin) {
                    if ($each_plugin == CBXTAKEATOUR_BASE_NAME) {
                        set_transient('cbxtakeatour_upgraded_notice', 1);
                        break;
                    }
                }
            }
        }

    }//end plugin_upgrader_process_complete

    /**
     * Show a notice to anyone who has just installed the plugin for the first time
     * This notice shouldn't display to anyone who has just updated this plugin
     */
    public function plugin_activate_upgrade_notices()
    {
        // Check the transient to see if we've just activated the plugin
        if (get_transient('cbxtakeatour_activated_notice')) {
            echo '<div class="notice notice-success is-dismissible" style="border-color: #6648fe !important;">';
            echo '<p>'.sprintf(__('Thanks for installing/deactivating <strong>CBX Tour - User Walkthroughs & Guided Tours</strong> V%s - Codeboxr Team',
                    'cbxtakeatour'),
                    CBXTAKEATOUR_PLUGIN_VERSION).'</p>';
            echo '<p>'.sprintf(__('Check <a style="color:#6648fe !important; font-weight: bold;" href="%s">Settings</a> | <a style="color:#6648fe !important; font-weight: bold;" href="%s" target="_blank">Documentation</a> | Create <a style="color:#6648fe !important; font-weight: bold;" href="%s">Tour</a>',
                    'cbxtakeatour'), admin_url('admin.php?page=cbxtakeatour-settings'),
                    'https://codeboxr.com/product/cbx-tour-user-walkthroughs-guided-tours-for-wordpress/',
                    admin_url('admin.php?page=cbxtakeatour-listing')).'</p>';
            echo '</div>';

            // Delete the transient. so we don't keep displaying the activation message
            delete_transient('cbxtakeatour_activated_notice');

            $this->pro_addon_compatibility_campaign();
        }

        // Check the transient to see if we've just activated the plugin
        if (get_transient('cbxtakeatour_upgraded_notice')) {
            echo '<div class="notice notice-success is-dismissible" style="border-color: #6648fe !important;">';
            echo '<p>'.sprintf(__('Thanks for upgrading <strong>CBX Tour - User Walkthroughs & Guided Tours</strong> V%s , enjoy the new features and bug fixes - Codeboxr Team',
                    'cbxtakeatour'),
                    CBXTAKEATOUR_PLUGIN_VERSION).'</p>';
            echo '<p>'.sprintf(__('Check <a style="color:#6648fe !important; font-weight: bold;" href="%s">Settings</a> | <a style="color:#6648fe !important; font-weight: bold;" href="%s" target="_blank">Documentation</a> | Create <a style="color:#6648fe !important; font-weight: bold;" href="%s" >Tour</a>', 'cbxtakeatour'), admin_url('admin.php?page=cbxtakeatour-settings'),
                    'https://codeboxr.com/product/cbx-tour-user-walkthroughs-guided-tours-for-wordpress/',
                    admin_url('admin.php?page=cbxtakeatour-listing')).'</p>';
            echo '</div>';

            // Delete the transient, so we don't keep displaying the activation message
            delete_transient('cbxtakeatour_upgraded_notice');

            $this->pro_addon_compatibility_campaign();
        }
    }//end plugin_activate_upgrade_notices

    /**
     * Check plugin compatibility and pro addon install campaign
     */
    public function pro_addon_compatibility_campaign()
    {
        if ( ! function_exists('is_plugin_active')) {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
        }

        //if the pro addon is active or installed
        if (in_array('cbxtakeatour/cbxtakeatour.php',
                apply_filters('active_plugins',
                    get_option('active_plugins'))) || defined('cbxtakeatour_PLUGIN_NAME')) {
            //plugin is activated

            $plugin_version = CBXTAKEATOUR_PLUGIN_VERSION;

            /*if(version_compare($plugin_version,'1.0.11', '<=') ){
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'CBX Petition Pro Addon Vx.x.x or any previous version is not compatible with CBX Petition Vx.x.x or later. Please update CBX Petition Pro Addon to version x.x.0 or later  - Codeboxr Team', 'cbxtakeatour' ) . '</p></div>';
            }*/
        } else {
            echo '<div class="notice notice-success is-dismissible" style="border-color: #6648fe !important;">';
            echo '<p>'.sprintf(__('CBX Tour - User Walkthroughs & Guided Tours Pro has extended features and more controls, <a style="color:#6648fe !important; font-weight: bold;" target="_blank" href="%s">try it</a>  - Codeboxr Team',
                    'cbxtakeatour'),
                    'https://codeboxr.com/product/cbx-tour-user-walkthroughs-guided-tours-for-wordpress//').'</p>';
            echo '</div>';
        }

    }//end pro_addon_compatibility_campaign

    /**
     * Disallow more than 1 published tour in core
     */
    public function disallow_create_tour($allow, $tour_count)
    {
        if ($tour_count > 0) {
            return false;
        }

        return $allow;
    }//end method disallow_create_tour


    /**
     * Init all gutenberg blocks
     */
    public function gutenberg_blocks()
    {
        if ( ! function_exists('register_block_type')) {
            // Gutenberg is not active.
            return;
        }

        $active_tours = [];

        $active_tours[] = [
            'label' => esc_html__('Select Tour', 'cbxtakeatour'),
            'value' => '0',
        ];

        $args = [
            'post_type'      => 'cbxtour',
            'orderby'        => 'ID',
            'order'          => 'DESC',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ];

        $tour_posts = get_posts($args);



        foreach ($tour_posts as $post) :
            $post_id    = intval($post->ID);
            $post_title = get_the_title($post_id);

            $active_tours[] = [
                'label' => esc_html($post_title),
                'value' => $post_id
            ];
        endforeach;


        $align_arr = [
            '-'      => esc_html__('Use meta setting', 'cbxtakeatour'),
            'center' => esc_html__('Center', 'cbxtakeatour'),
            'left'   => esc_html__('Left', 'cbxtakeatour'),
            'right'  => esc_html__('Right', 'cbxtakeatour'),
            'none'   => esc_html__('None', 'cbxtakeatour'),
        ];

        $align_options = [];
        foreach ($align_arr as $key => $value) {
            $align_options[] = [
                'label' => esc_attr($value),
                'value' => esc_attr($key),
            ];
        }

        $yes_no_arr = [
            '2' => esc_html__('Use meta setting', 'cbxtakeatour'),
            '1' => esc_html__('Yes', 'cbxtakeatour'),
            '0' => esc_html__('No', 'cbxtakeatour'),
        ];

        $yes_no_options = [];
        foreach ($yes_no_arr as $key => $value) {
            $yes_no_options[] = [
                'label' => esc_attr($value),
                'value' => esc_attr($key),
            ];
        }

        wp_register_style('cbxtakeatour-block', CBXTAKEATOUR_ROOT_URL.'assets/css/cbxtakeatour-block.css', [], filemtime(plugin_dir_path(__FILE__).'../assets/css/cbxtakeatour-block.css'));

        wp_register_script('cbxtakeatour-block',
            CBXTAKEATOUR_ROOT_URL.'assets/js/blocks/cbxtakeatour-block.js',
            [
                'wp-blocks',
                'wp-element',
                'wp-components',
                'wp-editor',
                'jquery',
            ],
            filemtime(plugin_dir_path(__FILE__).'../assets/js/blocks/cbxtakeatour-block.js'));


        $js_vars = apply_filters('cbxtakeatour_block_js_vars',
            [
                'block_title'      => esc_html__('CBX Tour - User Walkthroughs', 'cbxtakeatour'),
                'block_category'   => 'codeboxr',
                'block_icon'       => 'universal-access-alt',
                'general_settings' => [
                    'title'          => esc_html__('CBX Tour - User Walkthroughs Settings', 'cbxtakeatour'),
                    'id'             => esc_html__('Select Tour', 'cbxtakeatour'),
                    'id_options'     => $active_tours,
                    'button_text'    => esc_html__('Button Text (Leave empty to use tour post meta)', 'cbxtakeatour'),
                    'display'        => esc_html__('Display Tour Button', 'cbxtakeatour'),
                    'auto_start'     => esc_html__('Tour Auto-start', 'cbxtakeatour'),
                    'block'          => esc_html__('Block Button (Full Width)', 'cbxtakeatour'),
                    'align'          => esc_html__('Button Align', 'cbxtakeatour'),
                    'align_options'  => $align_options,
                    'yes_no_options' => $yes_no_options,
                ],
            ]);

        wp_localize_script('cbxtakeatour-block', 'cbxtakeatour_block', $js_vars);

        register_block_type('codeboxr/cbxtakeatour',
            [
                'editor_script'   => 'cbxtakeatour-block',
                //'editor_style'    => 'cbxtakeatour-block',
                'attributes'      => apply_filters('cbxtakeatour_block_attributes',
                    [
                        'id'          => [
                            'type'    => 'integer',
                            'default' => 0,
                        ],
                        'button_text' => [
                            'type'    => 'string',
                            'default' => '',
                        ],
                        'display'     => [
                            'type'    => 'string',
                            'default' => '2',
                        ],
                        'auto_start'  => [
                            'type'    => 'string',
                            'default' => '2',
                        ],
                        'block'       => [
                            'type'    => 'string',
                            'default' => '2',
                        ],
                        'align'       => [
                            'type'    => 'string',
                            'default' => '-',
                        ],
                    ]),
                'render_callback' => [$this, 'cbxtakeatour_block_render'],
            ]);

    }//end method gutenberg_blocks

    /**
     * Gutenberg server side render
     *
     * @param $attr
     *
     * @return string
     */
    public function cbxtakeatour_block_render($attr)
    {
        $params = [];

        $params['id'] = isset($attr['id']) ? intval($attr['id']) : 0;

        if (intval($params['id']) == 0) {
            return esc_html__('Please select  a tour', 'cbxtakeatour');
        }

        $params['button_text'] = isset($attr['button_text']) ? esc_attr(wp_unslash($attr['button_text'])) : '';

        $params['display']    = isset($attr['display']) ? esc_attr(wp_unslash($attr['display'])) : '2';
        $params['auto_start'] = isset($attr['auto_start']) ? esc_attr(wp_unslash($attr['auto_start'])) : '2';
        $params['block']      = isset($attr['block']) ? esc_attr(wp_unslash($attr['block'])) : '2';
        $params['align']      = isset($attr['align']) ? esc_attr(wp_unslash($attr['align'])) : '-';

        if ($params['display'] == '2') {
            $params['display'] = '';
        }
        if ($params['auto_start'] == '2') {
            $params['auto_start'] = '';
        }
        if ($params['block'] == '2') {
            $params['block'] = '';
        }

        if ($params['align'] == '-') {
            $params['align'] = '';
        }


        $params = apply_filters('cbxtakeatour_shortcode_builder_block_attr', $params, $attr);

        $params_html = '';

        foreach ($params as $key => $value) {
            $params_html .= ' '.$key.'="'.$value.'" ';
        }

        return '[cbxtakeatour '.$params_html.']'; //we need to use this form because the final output may not show anything due to button display none feature
    }//end method cbxtakeatour_block_render

    /**
     * Register New Gutenberg block Category if need
     *
     * @param $categories
     * @param $post
     *
     * @return mixed
     */
    public function gutenberg_block_categories($categories, $post)
    {
        $found = false;
        foreach ($categories as $category) {
            if ($category['slug'] == 'codeboxr') {
                $found = true;
                break;
            }
        }

        if ( ! $found) {
            return array_merge(
                $categories,
                [
                    [
                        'slug'  => 'codeboxr',
                        'title' => esc_html__('CBX Blocks', 'cbxtakeatour'),
                    ],
                ]
            );
        }

        return $categories;
    }//end method gutenberg_block_categories


    /**
     * Enqueue style for block editor
     */
    public function enqueue_block_editor_assets()
    {
        /*$css_url_part     = CBXTAKEATOUR_ROOT_URL.'assets/css/';
        $js_url_part      = CBXTAKEATOUR_ROOT_URL.'assets/js/';
        $vendors_url_part = CBXTAKEATOUR_ROOT_URL.'assets/vendors/';

        $css_path_part     = CBXTAKEATOUR_ROOT_PATH.'assets/css/';
        $js_path_part      = CBXTAKEATOUR_ROOT_PATH.'assets/js/';
        $vendors_path_part = CBXTAKEATOUR_ROOT_PATH.'assets/vendors/';

        $version = $this->version;

        wp_register_style('cbxtakeatour-public', CBXTAKEATOUR_ROOT_URL.'assets/css/cbxtakeatour-public.css', [], $version, 'all');
        wp_enqueue_style('cbxtakeatour-public');*/

    }//end method enqueue_block_editor_assets

    /**
     * Show action links on the plugin screen.
     *
     * @param  mixed  $links  Plugin Action links.
     *
     * @return  array
     */
    public function plugin_action_links($links)
    {
        $action_links = [
            'tour_settings' => '<a style="color:#6648fe !important; font-weight: bold;" href="'.admin_url('admin.php?page=cbxtakeatour-settings').'" aria-label="'.esc_attr__('Settings', 'cbxtakeatour').'">'.esc_html__('Settings', 'cbxtakeatour').'</a>',
            'explore_tours' => '<a style="color:#6648fe !important; font-weight: bold;" href="'.admin_url('admin.php?page=cbxtakeatour-listing').'" aria-label="'.esc_attr__('Explore Tours', 'cbxtakeatour').'">'.esc_html__('Explore Tours', 'cbxtakeatour').'</a>',
        ];

        return array_merge($action_links, $links);
    }//end plugin_action_links

    /**
     * Filters the array of row meta for each/specific plugin in the Plugins list table.
     * Appends additional links below each/specific plugin on the plugins page.
     *
     * @access  public
     *
     * @param  array  $links_array  An array of the plugin's metadata
     * @param  string  $plugin_file_name  Path to the plugin file
     * @param  array  $plugin_data  An array of plugin data
     * @param  string  $status  Status of the plugin
     *
     * @return  array       $links_array
     */
    public function plugin_row_meta($links_array, $plugin_file_name, $plugin_data, $status)
    {
        if (strpos($plugin_file_name, CBXTAKEATOUR_BASE_NAME) !== false) {
            if ( ! function_exists('is_plugin_active')) {
                include_once(ABSPATH.'wp-admin/includes/plugin.php');
            }

            $links_array[] = '<a target="_blank" style="color:#6648fe !important; font-weight: bold;" href="https://wordpress.org/support/plugin/cbxtakeatour/" aria-label="'.esc_attr__('Free Support', 'cbxtakeatour').'">'.esc_html__('Free Support', 'cbxtakeatour').'</a>';
            $links_array[] = '<a target="_blank" style="color:#6648fe !important; font-weight: bold;" href="https://wordpress.org/plugins/cbxtakeatour/#reviews" aria-label="'.esc_attr__('Reviews', 'cbxtakeatour').'">'.esc_html__('Reviews', 'cbxtakeatour').'</a>';
            $links_array[] = '<a target="_blank" style="color:#6648fe !important; font-weight: bold;" href="https://codeboxr.com/doc/cbxtour-doc/" aria-label="'.esc_attr__('Documentation', 'cbxtakeatour').'">'.esc_html__('Documentation', 'cbxtakeatour').'</a>';


            if (in_array('cbxtakeatour/cbxtakeatour.php', apply_filters('active_plugins', get_option('active_plugins'))) || defined('cbxtakeatour_PLUGIN_NAME')) {
                $links_array[] = '<a target="_blank" style="color:#6648fe !important; font-weight: bold;" href="https://codeboxr.com/contact-us/" aria-label="'.esc_attr__('Pro Support', 'cbxtakeatour').'">'.esc_html__('Pro Support', 'cbxtakeatour').'</a>';
            } else {
                $links_array[] = '<a target="_blank" style="color:#6648fe !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-tour-user-walkthroughs-guided-tours-for-wordpress" aria-label="'.esc_attr__('Try Pro Addon', 'cbxtakeatour').'">'.esc_html__('Try Pro Addon', 'cbxtakeatour').'</a>';
            }
        }

        return $links_array;
    }//end plugin_row_meta

    /**
     * Load setting html
     *
     * @return void
     */
    public function settings_reset_load()
    {
        //security check
        check_ajax_referer('settingsnonce', 'security');

        $msg            = [];
        $msg['html']    = '';
        $msg['message'] = esc_html__('Tour reset setting html loaded successfully', 'cbxtakeatour');
        $msg['success'] = 1;

        if ( ! current_user_can('manage_options')) {
            $msg['message'] = esc_html__('Sorry, you don\'t have enough permission', 'cbxtakeatour');
            $msg['success'] = 0;
            wp_send_json($msg);
        }

        $msg['html'] = CBXTakeaTourHelper::setting_reset_html_table();

        wp_send_json($msg);
    }//end method settings_reset_load

    /**
     * Reset plugin data
     */
    public function plugin_reset()
    {
        //security check
        check_ajax_referer('settingsnonce', 'security');

        $url = admin_url('admin.php?page=cbxtakeatour-settings');

        $msg            = [];
        $msg['message'] = esc_html__('Tour setting reset scheduled successfully', 'cbxtakeatour');
        $msg['success'] = 1;
        $msg['url']     = $url;

        if ( ! current_user_can('manage_options')) {
            $msg['message'] = esc_html__('Sorry, you don\'t have enough permission', 'cbxtakeatour');
            $msg['success'] = 0;
            wp_send_json($msg);
        }


        do_action('cbxtakeatour_plugin_reset_before');

        global $wpdb;

        $plugin_resets = $_POST;

        //delete options
        $reset_options = isset($plugin_resets['reset_options']) ? $plugin_resets['reset_options'] : [];
        $option_values = (is_array($reset_options) && sizeof($reset_options) > 0) ? array_values($reset_options) : array_values(CBXTakeaTourHelper::getAllOptionNamesValues());

	    do_action('cbxtakeatour_plugin_options_deleted_before');

        foreach ($option_values as $key => $option) {
	        do_action('cbxtakeatour_plugin_option_delete_before', $option);
	        delete_option( $option );
	        do_action('cbxtakeatour_plugin_option_delete_after', $option);
        }

	    do_action( 'cbxtakeatour_plugin_options_deleted_after' );
	    do_action( 'cbxtakeatour_plugin_options_deleted' );
	    //end delete options

        //delete tables
        $reset_tables = isset($plugin_resets['reset_tables']) ? $plugin_resets['reset_tables'] : [];
        $table_names  = (is_array($reset_tables) && sizeof($reset_tables) > 0) ? array_values($reset_tables) : array_values(CBXTakeaTourHelper::getAllDBTablesList());


	    do_action('cbxtakeatour_plugin_tables_deleted_before', $table_names);

        if (is_array($table_names) && sizeof($table_names) > 0) {
	        do_action('cbxtakeatour_plugin_tables_delete_before', $table_names);
            $sql          = "DROP TABLE IF EXISTS ".implode(', ', $table_names);
            $query_result = $wpdb->query($sql);
	        do_action('cbxtakeatour_plugin_tables_delete_after', $table_names);
        }

	    do_action('cbxtakeatour_plugin_tables_deleted_after', $table_names);
	    do_action('cbxtakeatour_plugin_tables_deleted');
        //end delete tables

        do_action('cbxtakeatour_plugin_reset_after');
        do_action('cbxtakeatour_plugin_reset');

        wp_send_json($msg);
    }//end method plugin_reset

    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param $transient
     *
     * @return object $ transient
     */
    public function pre_set_site_transient_update_plugins_pro_addon($transient)
    {
        // Extra check for 3rd plugins
        if (isset($transient->response['cbxtakeatour/cbxtakeatour.php'])) {
            return $transient;
        }

        if ( ! function_exists('get_plugins')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        $plugin_info = [];

        $all_plugins = get_plugins();
        if ( ! isset($all_plugins['cbxtakeatour/cbxtakeatour.php'])) {
            return $transient;
        } else {
            $plugin_info = $all_plugins['cbxtakeatour/cbxtakeatour.php'];
        }

        $remote_version = '1.1.2';

        if (version_compare($plugin_info['Version'], $remote_version, '<')) {
            $obj                                                  = new stdClass();
            $obj->slug                                            = 'cbxtakeatour';
            $obj->new_version                                     = $remote_version;
            $obj->plugin                                          = 'cbxtakeatour/cbxtakeatour.php';
            $obj->url                                             = '';
            $obj->package                                         = false;
            $obj->name                                            = 'CBX Tour - User Walkthroughs & Guided Tours Pro Addon';
            $transient->response['cbxtakeatour/cbxtakeatour.php'] = $obj;
        }

        return $transient;
    }//end pre_set_site_transient_update_plugins_pro_addons

    /**
     * Pro Addon update message
     */
    public function plugin_update_message_pro_addons()
    {
        echo ' '.sprintf(__('Check how to <a style="color:#9c27b0 !important; font-weight: bold;" href="%s"><strong>Update manually</strong></a> , download latest version from <a style="color:#9c27b0 !important; font-weight: bold;" href="%s"><strong>My Account</strong></a> section of Codeboxr.com', 'cbxtakeatour'), 'https://codeboxr.com/manual-update-pro-addon/', 'https://codeboxr.com/my-account/');
    }//end plugin_update_message_pro_addons

    /**
     * Create tour auto drafts
     */
    public function create_auto_drafts()
    {

	    //security check
	    check_ajax_referer('cbxtakeatournonce', 'security');

        $msg = [];

        $msg['message'] = esc_html__('Tour created successfully', 'cbxtakeatour');
        $msg['success'] = 1;
        $msg['post_id'] = 0;

        $my_post = [
            'post_title'     => esc_html__('Untitled tour', 'cbxtakeatour'),
            'post_content'   => '',
            'post_status'    => 'auto-draft',
            'post_type'      => 'cbxtour',
            'comment_status' => 'closed'
            //'post_author'   => 1,
        ];


        $post_id = wp_insert_post($my_post);
        if ( ! is_wp_error($post_id)) {
            //the post is valid
            $msg['post_id'] = $post_id;
            $msg['url']     = add_query_arg('id', $post_id, admin_url('admin.php?page=cbxtakeatour-listing&view=add'));
        } else {
            //there was an error in the post insertion,
            $msg['message'] = $post_id->get_error_message();
            $msg['success'] = 0;
        }

        wp_send_json($msg);
    }//end method create_auto_drafts

    /**
     * Delete tour auto drafts
     *
     */
    public function delete_auto_drafts()
    {
        //security check
        check_ajax_referer('cbxtakeatournonce', 'security');

        $url = admin_url('admin.php?page=cbxtakeatour-listing');

        $msg            = [];
        $msg['message'] = esc_html__('All Tour auto drafts deleted', 'cbxtakeatour');
        $msg['success'] = 1;
        $msg['url']     = $url;

        if ( ! current_user_can('manage_options')) {
            $msg['message'] = esc_html__('Sorry, you don\'t have enough permission', 'cbxtakeatour');
            $msg['success'] = 0;
            wp_send_json($msg);
        }

        try {
            global $wpdb;

            // Delete auto-drafts.
            $old_posts = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'cbxtour' AND post_status = 'auto-draft'");
            foreach ((array) $old_posts as $delete) {
                // Force delete.
                wp_delete_post($delete, true);
            }
        } catch (\Exception $ex) {
            $msg['message'] = $ex->getMessage();
            $msg['success'] = 0;
        }

        wp_send_json($msg);
    }//end delete_auto_drafts

    /**
     * Move the posts to trash
     */
    public function move_to_trash()
    {
        //security check
        check_ajax_referer('cbxtakeatournonce', 'security');


        $url = admin_url('admin.php?page=cbxtakeatour-listing');

        $msg            = [];
        $msg['message'] = esc_html__('Tour trashed successfully', 'cbxtakeatour');
        $msg['success'] = 1;
        $msg['url']     = $url;


        if ( ! current_user_can('manage_options')) {
            $msg['message'] = esc_html__('Sorry, you don\'t have enough permission', 'cbxtakeatour');
            $msg['success'] = 0;
            wp_send_json($msg);
        }

        $post_id = isset($_REQUEST['post_id']) ? absint($_REQUEST['post_id']) : 0;


        $trashed = false;

        if ($post_id > 0) {
            $trashed = wp_trash_post($post_id);
        } else {
            //
        }

        if ($trashed === null || $trashed === false) {
            $msg['message'] = esc_html__('Failed to trash', 'cbxtakeatour');
            $msg['success'] = 0;
        }

        wp_send_json($msg);
    }//end method move_to_trash


    /**
     * Save tour (using ajax)
     */
    public function save_tour_post()
    {
        //security check
        check_ajax_referer('cbxtakeatournonce', 'security');

        $msg = [];

        $msg['message']           = esc_html__('Tour saved/updated successfully', 'cbxtakeatour');
        $msg['success']           = 1;
        $msg['validation_errors'] = [];

        $validation_errors = [];

        $status_arr = array_keys(CBXTakeaTourHelper::allowed_status());

        $post_id     = isset($_REQUEST['post_id']) ? absint($_REQUEST['post_id']) : 0;
        $post_status = isset($_REQUEST['post_status']) ? sanitize_text_field($_REQUEST['post_status']) : 'draft';
        $post_title  = isset($_REQUEST['post_title']) ? sanitize_text_field($_REQUEST['post_title']) : esc_html__('Untitled tour', 'cbxtakeatour');

        //check if user can edit this tour post
        if ( ! current_user_can('edit_post', $post_id)) {
            $msg['message'] = esc_html__('Sorry, you don\'t have permission to edit this post.', 'cbxtakeatour');
            $msg['success'] = 0;
            wp_send_json($msg);
        }


        if ( ! in_array($post_status, $status_arr)) {
            $post_status = 'draft';
        }


        $valid = true;
        //validation
        if ($post_title == '') {
            $validation_errors['post_title'] = esc_html__('Sorry tour title can not be empty', 'cbxtakeatour');
            $valid                           = false;
        } elseif (strlen($post_title) < 3) {
            $validation_errors['post_title'] = esc_html__('Sorry tour title length is too short', 'cbxtakeatour');
            $valid                           = false;
        }

        if ( ! $valid) {
            $msg['message'] = esc_html__('Validation failed, pls check error notes', 'cbxtakeatour');

            $msg['success']           = 0;
            $msg['validation_errors'] = $validation_errors;

            wp_send_json($msg);
        }


        $my_post = [
            'post_title'  => $post_title,
            'post_status' => $post_status,
            'ID'          => $post_id
        ];

        $post_id = wp_update_post($my_post);

        if ( ! is_wp_error($post_id)) {
            $msg['post_id'] = $post_id;

            //now update post meta
            if ( ! empty($_POST['cbxtourmeta'])) {
                $postData      = wp_unslash($_POST['cbxtourmeta']);
                $saveable_data = [];

                $post_steps = isset($postData['steps']) ? $postData['steps'] : [];

                $i = 0;
                foreach ($post_steps as $key => $deals) {
                    if (is_numeric($key)) {
                        $saveable_data['steps'][$i]['element'] = sanitize_text_field($deals['element']);
                        $saveable_data['steps'][$i]['content'] = wp_kses($deals['content'], CBXTakeaTourHelper::allowedHtmlTags());
                        $saveable_data['steps'][$i]['title']   = sanitize_text_field($deals['title']);
                        $saveable_data['steps'][$i]['state']   = isset($deals['state']) ? intval($deals['state']) : 1;


                        $i++;
                    }
                }

                $saveable_data['redirect_url'] = (isset($postData['redirect_url']) && $postData['redirect_url'] != '') ? esc_url($postData['redirect_url']) : '';


                $saveable_data['display']           = isset($postData['display']) ? intval($postData['display']) : 1;
                $saveable_data['auto_start']        = isset($postData['auto_start']) ? intval($postData['auto_start']) : 0;
                $saveable_data['tour_button_block'] = isset($postData['tour_button_block']) ? intval($postData['tour_button_block']) : 0;
                $saveable_data['tour_button_align'] = isset($postData['tour_button_align']) ? sanitize_text_field($postData['tour_button_align']) : '';
                $saveable_data['tour_button_text']  = isset($postData['tour_button_text']) ? sanitize_text_field($postData['tour_button_text']) : esc_html__('Take a Tour',
                    'cbxtakeatour');
                $saveable_data['layout']            = sanitize_text_field($postData['layout']);


                //new fields
                $saveable_data['dialog_animate']        = isset($postData['dialog_animate']) ? intval($postData['dialog_animate']) : 1;
                $saveable_data['hide_prev']             = isset($postData['hide_prev']) ? intval($postData['hide_prev']) : 0;
                $saveable_data['hide_next']             = isset($postData['hide_next']) ? intval($postData['hide_next']) : 0;
                $saveable_data['backdrop_animate']      = isset($postData['backdrop_animate']) ? intval($postData['backdrop_animate']) : 1;
                $saveable_data['show_step_dots']        = isset($postData['show_step_dots']) ? intval($postData['show_step_dots']) : 1;
                $saveable_data['show_step_progress']    = isset($postData['show_step_progress']) ? intval($postData['show_step_progress']) : 1;
                $saveable_data['keyboard_controls']     = isset($postData['keyboard_controls']) ? intval($postData['keyboard_controls']) : 1;
                $saveable_data['exit_on_escape']        = isset($postData['exit_on_escape']) ? intval($postData['exit_on_escape']) : 1;
                $saveable_data['exit_on_click_outside'] = isset($postData['exit_on_click_outside']) ? intval($postData['exit_on_click_outside']) : 1;
                $saveable_data['close_button']          = isset($postData['close_button']) ? intval($postData['close_button']) : 1;
                $saveable_data['dev_debug']             = isset($postData['dev_debug']) ? intval($postData['dev_debug']) : 0;


                $saveable_data = apply_filters('cbxtakeatour_meta_saveable_data',
                    $saveable_data,
                    $post_id,
                    $postData);


                update_post_meta($post_id, '_cbxtourmeta', $saveable_data);

            }
        } else {
            $msg['message'] = esc_html__('Tour save/update failed', 'cbxtakeatour');
            $msg['success'] = 0;
        }


        wp_send_json($msg);
    }//end method save_tour_post
}//end class CBXTakeaTour_Admin