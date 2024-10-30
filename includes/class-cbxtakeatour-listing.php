<?php
// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

if ( ! class_exists('WP_List_Table')) {
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Tours listing class
 *
 * Class CBXTakeaTour_Listing
 */
class CBXTakeaTour_Listing extends WP_List_Table
{

    /**
     * The current list of all branches.
     */
    function __construct($args = [])
    {
        //$currentScreen = get_current_screen();
        //write_log($currentScreen);
        //Set parent defaults
        parent::__construct([
            'singular' => 'cbxtakeatour',     //singular name of the listed records
            'plural'   => 'cbxtakeatours',    //plural name of the listed records
            'ajax'     => false,              //does this table support ajax?
            'screen'   => isset($args['screen']) ? $args['screen'] : null,
        ]);
    }

    /**
     * Callback for column 'id'
     *
     * @param $item
     *
     * @return int
     */
    function column_id($item)
    {
        return intval($item['id']);
    }//end column-id

    /**
     * Callback for column 'post_status'
     *
     * @param $item
     *
     * @return int
     */
    function column_post_status($item)
    {
        return $item['post_status'];
    }//end column_post_status-id


    /**
     * Callback for column 'post_title'
     *
     * @param  array  $item
     *
     * @return string
     */
    function column_post_title($item)
    {
        $post_id     = intval($item['id']);
        $post_status = $item['post_status'];


        $allowed_status_edit = ['draft', 'auto-draft', 'private', 'future', 'publish'];

        if (current_user_can('edit_post', $post_id) && in_array($post_status, $allowed_status_edit)) {
            $url = add_query_arg('id', $post_id, admin_url('admin.php?page=cbxtakeatour-listing&view=add'));

            return '<a href="'.esc_url($url).'">'.$item['post_title'].'</a>';
        }

        return $item['post_title'];
    }//end column_post_title


    /**
     * Callback for column 'post_author'
     *
     * @param  array  $item
     *
     * @return string
     */
    function column_post_author($item)
    {
        $user_id = absint($item['post_author']);

        $user_html = '';

        if ($user_id > 0) {
            $display_name = CBXTakeaTourHelper::userDisplayName($user_id);
            if (current_user_can('edit_user', $user_id)) {
                $user_html .= '<a href="'.get_edit_user_link($user_id).'" target="_blank" title="'.esc_html__('Edit User', 'cbxtakeatour').'">'.$display_name.'</a>';
            } else {
                $user_html .= $display_name;
            }
        } else {
            $user_html = esc_html__('Guest User', 'cbxtakeatour');
        }

        return $user_html;
    }//end column_user_id


    /**
     * Callback for column 'Date Created'
     *
     * @param  array  $item
     *
     * @return string
     */
    function column_post_date($item)
    {
        return $item['post_date'];
    }//end column_post_date

    /**
     * Callback for column 'Date Modified'
     *
     * @param  array  $item
     *
     * @return string
     */
    function column_post_modified($item)
    {
        return $item['post_modified'];
    }//end column_post_modified

    /**
     * Callback for column 'Date Modified'
     *
     * @param  array  $item
     *
     * @return string
     */
    function column_shortcode($item)
    {
        $post_id = absint($item['id']);
        /*
                $shortcode = '<div class="cbxshortcode-wrap">';
                $shortcode .= '<span data-clipboard-text=\'[cbxtakeatour id="' . $post_id . '"]\' title="' . esc_html__( "Click to clipboard",	"cbxtakeatour" ) . '" id="cbxtakeatourshortcode-' . $post_id . '" class="cbxshortcode cbxshortcode-edit cbxshortcode-' . $post_id. '">[cbxtakeatour id="' . $post_id . '"]</span>';
                $shortcode .= '<span class="button outline primary icon cbxballon_ctp" aria-label="' . esc_html__( 'Click to copy', 'cbxtakeatour' ) . '" data-balloon-pos="down"><i></i></span>';
                $shortcode .= '</div>';*/

        //return $shortcode;

        return '<div class="cbxshortcodeplain-wrap"><span title="'.esc_html__('Click to clipboard', 'cbxtakeatour').'" id="cbxtakeatourshortcode-'.$post_id.'" class="cbxshortcode cbxshortcode-list cbxshortcode-'.$post_id.'">[cbxtakeatour id="'.$post_id.'"]</span><span class="button outline primary icon cbxballon_ctp" aria-label="'.esc_html__('Click to copy', 'cbxtakeatour').'" data-balloon-pos="down"><i></i></span></div>';
    }//end column_post_modified


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/
            $item['id']                //The value of the checkbox should be the record's id
        );
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
                return $item[$column_name];
            case 'post_author':
                return $item[$column_name];
            case 'post_title':
                return $item[$column_name];
            case 'post_date':
                return $item[$column_name];
            case 'post_modified':
                return $item[$column_name];
            case 'post_status':
                return $item[$column_name];
            default:
                //return print_r( $item, true ); //Show the whole array for troubleshooting purposes
                return apply_filters('cbxtakeatour_list_admin_column_default', $item, $column_name);
        }
    }//end column_default

    function get_columns()
    {
        $columns = [
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            //'id'            => esc_html__( 'ID', 'cbxtakeatour' ),
            'post_title'    => esc_html__('Title', 'cbxtakeatour'),
            'post_author'   => esc_html__('User', 'cbxtakeatour'),
            'post_status'   => esc_html__('Status', 'cbxtakeatour'),
            'post_date'     => esc_html__('Created', 'cbxtakeatour'),
            'post_modified' => esc_html__('Updated', 'cbxtakeatour'),
            'shortcode'     => esc_html__('Shortcode', 'cbxtakeatour'),
        ];

        return apply_filters('cbxtakeatour_list_admin_columns', $columns);
    }//end get_columns


	/**
	 * Get sortable columns
	 *
	 * @return array|mixed|null
	 */
    function get_sortable_columns()
    {
        $sortable_columns = [
            //'id'            => array( 'logs.ID', false ), //true means it's already sorted
            'post_title'    => ['logs.post_title', false],
            'post_author'   => ['logs.post_author', false],
            'post_status'   => ['logs.post_status', false],
            'post_date'     => ['logs.post_date', false],
            'post_modified' => ['logs.post_modified', false]
        ];

        return apply_filters('cbxtakeatour_list_admin_sortable_columns', $sortable_columns);
    }//end get_sortable_columns

	/**
	 * Get sortable keys
	 *
	 * @return mixed|null
	 */
	function get_sortable_keys(){
		$sortable_keys = [
			//'id'            => 'logs.ID',
			'post_title'    => 'logs.post_title',
			'post_author'   => 'logs.post_author',
			'post_status'   => 'logs.post_status',
			'post_date'     => 'logs.post_date',
			'post_modified' => 'logs.post_modified'
		];

		return apply_filters('cbxtakeatour_list_admin_sortable_keys', $sortable_keys);
	}//end method get_sortable_keys


    /**
     * Bulk action method
     *
     * @return array|mixed|void
     */
    function get_bulk_actions()
    {
        $status_arr = [];

        $post_status = isset($_GET['post_status']) ? $_GET['post_status'] : 'all';

        if ($post_status != 'trash') {
            $status_arr['trash'] = esc_html__('Move to Trash', 'cbxtakeatour');
        }


        if ($post_status == 'trash') {
            $status_arr['delete'] = esc_html__('Permanent Delete', 'cbxtakeatour');
        }

        $post_statuses = get_post_statuses();
        foreach ($post_statuses as $key => $label) {

            $status_arr[$key] = sprintf(esc_html__('Change to %s', 'cbxtakeatour'), $label);
        }


        return apply_filters('cbxtakeatour_list_admin_bulk_actions', $status_arr);
    }//end get_bulk_actions

    /**
     * Process bulk action
     */
    function process_bulk_action()
    {

        $new_status = $this->current_action();

        if ($new_status == -1) {
            return;
        }


        //Detect when a bulk action is being triggered...
        if ( ! empty($_REQUEST['cbxtakeatour'])) {
            global $wpdb;

            //$cbxtakeatour_activity_table = $wpdb->prefix . 'cbxtakeatour_activity';

            $results = $_REQUEST['cbxtakeatour'];


            foreach ($results as $id) {
                $id = intval($id);

                if ('trash' === $new_status) {
                    do_action('cbxtakeatour_listing_trash_before', $id);
                    $delete_status = wp_trash_post($id);

                    if ($delete_status === false) {
                        do_action('cbxtakeatour_listing_trash', $id);
                    }
                }//end trash

                if ('delete' === $new_status) {
                    do_action('cbxtakeatour_listing_delete_before', $id);
                    $delete_status = wp_delete_post($id, true);

                    if ($delete_status === false) {
                        do_action('cbxtakeatour_listing_delete', $id);
                    }
                }//end trash

                $post_statuses = array_keys(get_post_statuses());

                if (in_array($new_status, $post_statuses)) {

                    $my_post = [
                        'post_status' => $new_status,
                        'ID'          => $id
                    ];

                    $post_id = wp_update_post($my_post);
                    if ( ! is_wp_error($post_id)) {
                        //ok
                    } else {
                        //failed
                    }
                }

            }
        }

        do_action('cbxtakeatour_list_admin_bulk_action', $new_status);


    }//end process_bulk_action


    /**
     * Prepare the review log items
     */
    function prepare_items()
    {
        $user   = get_current_user_id();
        $screen = get_current_screen();

        $current_page = $this->get_pagenum();

        $option_name = $screen->get_option('per_page', 'option'); //the core class name is WP_Screen


        $per_page = intval(get_user_meta($user, $option_name, true));

        if ($per_page == 0) {
            $per_page = intval($screen->get_option('per_page', 'default'));
        }


        $columns  = $this->get_columns();
        $hidden   = get_hidden_columns($this->screen);
        $sortable = $this->get_sortable_columns();


        $this->_column_headers = [$columns, $hidden, $sortable];


        $this->process_bulk_action();

        $search = (isset($_REQUEST['s']) && $_REQUEST['s'] != '') ? esc_attr(sanitize_text_field($_REQUEST['s'])) : '';
        //$id      = ( isset( $_REQUEST['id'] ) && $_REQUEST['id'] != 0 ) ? intval( $_REQUEST['id'] ) : 0;
        //$post_id = ( isset( $_REQUEST['post_id'] ) && $_REQUEST['post_id'] != 0 ) ? intval( $_REQUEST['post_id'] ) : 0;
        //$user_id = ( isset( $_REQUEST['user_id'] ) && $_REQUEST['user_id'] != 0 ) ? intval( $_REQUEST['user_id'] ) : 0;
        $order   = (isset($_REQUEST['order']) && $_REQUEST['order'] != '') ? strtoupper(esc_attr($_REQUEST['order'])) : 'DESC';
        $orderby = (isset($_REQUEST['orderby']) && $_REQUEST['orderby'] != '') ? esc_attr($_REQUEST['orderby']) : 'logs.id';


	    //take care the order and order by
	    //order by
	    $sortable_keys = array_values( $this->get_sortable_keys() );

	    if ( ! in_array( $orderby, $sortable_keys ) ) {
		    $orderby = 'logs.id';
	    }

	    //order
	    if(!in_array($order, ['DESC', 'ASC'])){
		    $order = 'DESC';
	    }
	    //end take care the order and order by

        $post_status = isset($_GET['post_status']) ? esc_attr(wp_unslash($_GET['post_status'])) : 'all';

        $data = $this->getLogData($search, $post_status, $orderby, $order, $per_page, $current_page);

        $total_items = intval($this->getLogDataCount($search, $post_status));

        $this->items = $data;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args([
            'total_items' => $total_items,                      //WE have to calculate the total number of items
            'per_page'    => $per_page,                         //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)     //WE have to calculate the total number of pages
        ]);

    }

    /**
     * Get tour logs
     *
     * @param $search
     * @param $post_status
     * @param $orderby
     * @param $order
     * @param $per_page
     * @param $page
     *
     * @return array|object|stdClass[]|null
     */
    public function getLogData($search = '', $post_status = 'all', $orderby = 'logs.id', $order = 'DESC', $per_page = 20, $page = 1)
    {

        global $wpdb;

        $posts_table = $wpdb->prefix.'posts';


        $sql_select = "logs.*, logs.ID AS id ";

        $sql_select = apply_filters('cbxtakeatour_list_admin_select', $sql_select, $search, $orderby, $order, $per_page, $page);

        $join = $where_sql = '';

        $where_sql = " logs.post_type  = 'cbxtour' ";

        if ($post_status == 'all') {
            //$where_sql .= " AND logs.post_status  IN ('publish', 'draft', 'pending', 'trash') ";
        } else {
            $where_sql .= $wpdb->prepare(" AND logs.post_status  = '%s' ", $post_status);
        }

        //$join = " LEFT JOIN $table_users AS users ON users.ID = logs.user_id ";

        $join = apply_filters('cbxtakeatour_list_admin_join', $join, $search, $orderby, $order, $per_page, $page);

        if ($search != '') {
            if ($where_sql != '') {
                $where_sql .= ' AND ';
            }

            $where_sql .= $wpdb->prepare(" logs.post_content LIKE '%%%s%%' OR logs.post_title LIKE '%%%s%%' OR logs.post_name LIKE '%%%s%%' ", $search, $search, $search);
        }


        $where_sql = apply_filters('cbxtakeatour_list_admin_where', $where_sql, $search, $orderby, $order, $per_page, $page);

        if ($where_sql == '') {
            $where_sql = '1';
        }

        $start_point = ($page * $per_page) - $per_page;
        $limit_sql   = "LIMIT";
        $limit_sql   .= ' '.$start_point.',';
        $limit_sql   .= ' '.$per_page;

        $sortingOrder = " ORDER BY $orderby $order ";

        return $wpdb->get_results("SELECT $sql_select FROM $posts_table AS logs $join  WHERE  $where_sql $sortingOrder  $limit_sql", 'ARRAY_A');
    }//end getLogData

    /**
     * Get total count
     *
     * @param $search
     * @param $post_status
     *
     * @return string|null
     */
    public function getLogDataCount($search = '', $post_status = 'all')
    {

        global $wpdb;

        $posts_table = $wpdb->prefix.'posts';

        $sql_select = "SELECT COUNT(*) FROM $posts_table as logs";

        $join = $where_sql = '';

        $where_sql = " logs.post_type  = 'cbxtour' ";

        if ($post_status == 'all') {
            //$where_sql .= " AND logs.post_status  IN ('publish', 'draft', 'pending', 'trash') ";
        } else {
            $where_sql .= $wpdb->prepare(" AND logs.post_status  = '%s' ", $post_status);
        }

        $join = apply_filters('cbxtakeatour_list_admin_join_total', $join, $search);

        if ($search != '') {
            if ($where_sql != '') {
                $where_sql .= ' AND ';
            }

            $where_sql .= $wpdb->prepare(" logs.post_content LIKE '%%%s%%' OR logs.post_title LIKE '%%%s%%' OR logs.post_name LIKE '%%%s%%' ", $search, $search, $search);
        }


        $where_sql = apply_filters('cbxtakeatour_list_admin_where_total', $where_sql, $search);

        if ($where_sql == '') {
            $where_sql = '1';
        }


        return $wpdb->get_var("$sql_select $join  WHERE  $where_sql");
    }//end getLogDataCount


    /**
     * Generates content for a single row of the table
     *
     * @param  object  $item  The current item
     *
     * @since  3.1.0
     * @access public
     *
     */
    public function single_row($item)
    {
        $row_class = 'cbxtakeatour_list_row';
        $row_class = apply_filters('cbxtakeatour_list_row_class', $row_class, $item);
        echo '<tr id="cbxtakeatour_list_row_'.$item['id'].'" class="'.$row_class.'">';
        $this->single_row_columns($item);
        echo '</tr>';
    }//end single_row

    /**
     * Message to be displayed when there are no items
     *
     * @since  3.1.0
     * @access public
     */
    public function no_items()
    {
        echo '<div class="notice notice-warning inline "><p>'.esc_html__('No logs found. Please change your search criteria for better result.', 'cbxtakeatour').'</p></div>';
    }//end no_items

    /**
     * Pagination
     *
     * @param  string  $which
     */
    protected function pagination($which)
    {

        if (empty($this->_pagination_args)) {
            return;
        }

        $total_items     = $this->_pagination_args['total_items'];
        $total_pages     = $this->_pagination_args['total_pages'];
        $infinite_scroll = false;
        if (isset($this->_pagination_args['infinite_scroll'])) {
            $infinite_scroll = $this->_pagination_args['infinite_scroll'];
        }

        if ('top' === $which && $total_pages > 1) {
            $this->screen->render_screen_reader_content('heading_pagination');
        }

        $output = '<span class="displaying-num">'.sprintf(_n('%s item', '%s items', $total_items), number_format_i18n($total_items)).'</span>';

        $current              = $this->get_pagenum();
        $removable_query_args = wp_removable_query_args();

        $current_url = set_url_scheme('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

        $current_url = remove_query_arg($removable_query_args, $current_url);

        $page_links = [];

        $total_pages_before = '<span class="paging-input">';
        $total_pages_after  = '</span></span>';

        $disable_first = $disable_last = $disable_prev = $disable_next = false;

        if ($current == 1) {
            $disable_first = true;
            $disable_prev  = true;
        }
        if ($current == 2) {
            $disable_first = true;
        }
        if ($current == $total_pages) {
            $disable_last = true;
            $disable_next = true;
        }
        if ($current == $total_pages - 1) {
            $disable_last = true;
        }

        $pagination_params = [];

        $search = isset($_REQUEST['s']) ? esc_attr(wp_unslash($_REQUEST['s'])) : '';
        //$logdate = ( isset( $_REQUEST['logdate'] ) && $_REQUEST['logdate'] != '' ) ? sanitize_text_field( $_REQUEST['logdate'] ) : '';

        if ($search != '') {
            $pagination_params['s'] = $search;
        }

        /*if ($logdate != '') {
            $pagination_params['logdate'] = $logdate;
        }*/


        $pagination_params = apply_filters('cbxtakeatour_list_pagination_log_params', $pagination_params);

        if ($disable_first) {
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
        } else {
            $page_links[] = sprintf("<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url(remove_query_arg('paged', $current_url)),
                __('First page'),
                '&laquo;'
            );
        }

        if ($disable_prev) {
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
        } else {
            $pagination_params['paged'] = max(1, $current - 1);

            $page_links[] = sprintf("<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url(add_query_arg($pagination_params, $current_url)),
                __('Previous page'),
                '&lsaquo;'
            );
        }

        if ('bottom' === $which) {
            $html_current_page  = $current;
            $total_pages_before = '<span class="screen-reader-text">'.__('Current Page').'</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
        } else {
            $html_current_page = sprintf("%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
                '<label for="current-page-selector" class="screen-reader-text">'.__('Current Page').'</label>',
                $current,
                strlen($total_pages)
            );
        }
        $html_total_pages = sprintf("<span class='total-pages'>%s</span>", number_format_i18n($total_pages));
        $page_links[]     = $total_pages_before.sprintf(_x('%1$s of %2$s', 'paging'), $html_current_page, $html_total_pages).$total_pages_after;

        if ($disable_next) {
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
        } else {
            $pagination_params['paged'] = min($total_pages, $current + 1);

            $page_links[] = sprintf("<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url(add_query_arg($pagination_params, $current_url)),
                __('Next page'),
                '&rsaquo;'
            );
        }

        if ($disable_last) {
            $page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
        } else {
            $pagination_params['paged'] = $total_pages;

            $page_links[] = sprintf("<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
                esc_url(add_query_arg($pagination_params, $current_url)),
                __('Last page'),
                '&raquo;'
            );
        }

        $pagination_links_class = 'pagination-links';
        if ( ! empty($infinite_scroll)) {
            $pagination_links_class = ' hide-if-js';
        }
        $output .= "\n<span class='$pagination_links_class'>".join("\n", $page_links).'</span>';

        if ($total_pages) {
            $page_class = $total_pages < 2 ? ' one-page' : '';
        } else {
            //$page_class = ' no-pages';
            $page_class = ' ';
        }
        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

        echo $this->_pagination;
    }

    /**
     * Helper to create links to edit.php with params.
     *
     * @param  string[]  $args  Associative array of URL parameters for the link.
     * @param  string  $link_text  Link text.
     * @param  string  $css_class  Optional. Class attribute. Default empty string.
     *
     * @return string The formatted link string.
     * @since 4.4.0
     *
     */
    protected function get_edit_link($args, $link_text, $css_class = '')
    {
        $url = add_query_arg($args, 'edit.php');

        $class_html   = '';
        $aria_current = '';

        if ( ! empty($css_class)) {
            $class_html = sprintf(
                ' class="%s"',
                esc_attr($css_class)
            );

            if ('current' === $css_class) {
                $aria_current = ' aria-current="page"';
            }
        }

        return sprintf(
            '<a href="%s"%s%s>%s</a>',
            esc_url($url),
            $class_html,
            $aria_current,
            $link_text
        );
    }
}//end class CBXWPBookmark_List_Table