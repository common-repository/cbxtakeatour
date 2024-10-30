<?php
/**
 * This template provides the tour add/edit page of the plugin
 *
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    cbxtakeatour
 * @subpackage cbxtakeatour/templates/admin
 */
if ( ! defined('WPINC')) {
    die;
}

$post_title  = isset($tour->post_title) ? $tour->post_title : '';
$post_status = isset($tour->post_status) ? $tour->post_status : 'draft';

//write_log($post_status);

$post_id = intval($id);

?>

<div class="wrap cbx-chota cbxtakeatour-page-wrapper cbxtakeatour-tours-add-wrapper" id="cbxtakeatour-tours-add">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2></h2>
                <?php do_action('cbxtakeatour_wpheading_wrap_before', 'tours-add-edit'); ?>
                <div class="wp-heading-wrap">
                    <div class="wp-heading-wrap-left pull-left">
                        <?php do_action('cbxtakeatour_wpheading_wrap_left_before', 'tours-add-edit'); ?>
                        <h1 class="wp-heading-inline wp-heading-inline-cbxtakeatour">
                            <?php esc_html_e('Tours', 'cbxtakeatour'); ?>
                        </h1>
                        <?php do_action('cbxtakeatour_wpheading_wrap_left_after', 'tours-add-edit'); ?>
                    </div>
                    <div class="wp-heading-wrap-right pull-right">
                        <?php do_action('cbxtakeatour_wpheading_wrap_right_before', 'tours-add-edit'); ?>
                        <a href="<?php echo admin_url('admin.php?page=cbxtakeatour-listing'); ?>" class="button secondary"><?php esc_html_e('Back', 'cbxtakeatour'); ?></a>
                        <a href="<?php echo admin_url('admin.php?page=cbxtakeatour-settings'); ?>" class="button outline primary"><?php esc_html_e('Global Settings', 'cbxtakeatour'); ?></a>
                        <?php do_action('cbxtakeatour_wpheading_wrap_right_after', 'tours-add-edit'); ?>
                    </div>
                </div>
                <?php do_action('cbxtakeatour_wpheading_wrap_after', 'tours-add-edit'); ?>
            </div>
        </div>
    </div>
    <div class="container">
        <?php do_action('cbxtakeatour_tours_add_before_postbox', $post_id); ?>
        <?php do_action('cbxtakeatour_tours_add_before', $post_id); ?>
        <form data-busy="0" id="cbxtakeatour_add" method="post" class="cbx_form_wrapper cbx_form_wrapper_tour" novalidate>
            <?php do_action('cbxtakeatour_tours_add_form_start', $post_id); ?>
            <div class="row">
                <div class="col-8">
                    <div class="postbox">
                        <div class="inside">
                            <div class="cbxtakeatour-form-field">
                                <label for="post_title"><?php esc_html_e('Tour Title', 'cbxtakeatour'); ?></label>
                                <input required id="post_title" type="text" name="post_title" value="<?php echo esc_attr($post_title); ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="postbox">
                        <div class="inside">
                            <?php

                            $meta = get_post_meta($post_id, '_cbxtourmeta', true);

                            $layout     = isset($meta['layout']) ? esc_attr(wp_unslash($meta['layout'])) : 'basic';
                            $auto_start = isset($meta['auto_start']) ? intval($meta['auto_start']) : 0;

                            $display      = isset($meta['display']) ? intval($meta['display']) : 1;
                            $redirect_url = (isset($meta['redirect_url']) && $meta['redirect_url'] != '') ? esc_url($meta['redirect_url']) : '';

                            $tour_button_text = isset($meta['tour_button_text']) ? esc_attr(wp_unslash($meta['tour_button_text'])) : esc_html__('Take a tour',
                                'cbxtakeatour');

                            $tour_button_block = isset($meta['tour_button_block']) ? intval($meta['tour_button_block']) : 0;
                            $tour_button_align = isset($meta['tour_button_align']) ? esc_attr(wp_unslash($meta['tour_button_align'])) : 'undefined';

                            //new
                            $dialog_animate        = isset($meta['dialog_animate']) ? intval($meta['dialog_animate']) : 1;
                            $hide_prev             = isset($meta['hide_prev']) ? intval($meta['hide_prev']) : 0;
                            $hide_next             = isset($meta['hide_next']) ? intval($meta['hide_next']) : 0;
                            $backdrop_animate      = isset($meta['backdrop_animate']) ? intval($meta['backdrop_animate']) : 1;
                            $show_step_dots        = isset($meta['show_step_dots']) ? intval($meta['show_step_dots']) : 1;
                            $show_step_progress    = isset($meta['show_step_progress']) ? intval($meta['show_step_progress']) : 1;
                            $keyboard_controls     = isset($meta['keyboard_controls']) ? intval($meta['keyboard_controls']) : 1;
                            $exit_on_escape        = isset($meta['exit_on_escape']) ? intval($meta['exit_on_escape']) : 1;
                            $exit_on_click_outside = isset($meta['exit_on_click_outside']) ? intval($meta['exit_on_click_outside']) : 1;
                            $close_button          = isset($meta['close_button']) ? intval($meta['close_button']) : 1;
                            $dev_debug             = isset($meta['dev_debug']) ? intval($meta['dev_debug']) : 0;

                            $new_fields = [
                                'dialog_animate'        => [
                                    'label' => esc_html__('Animate Dialog', 'cbxtakeatour'),
                                    'value' => $dialog_animate,
                                ],
                                'hide_prev'             => [
                                    'label' => esc_html__('Hide Prev Button', 'cbxtakeatour'),
                                    'value' => $hide_prev,
                                ],
                                'hide_next'             => [
                                    'label' => esc_html__('Hide Next Button', 'cbxtakeatour'),
                                    'value' => $hide_next,
                                ],
                                'backdrop_animate'      => [
                                    'label' => esc_html__('Animate Backdrop', 'cbxtakeatour'),
                                    'value' => $backdrop_animate,
                                ],
                                'show_step_dots'        => [
                                    'label' => esc_html__('Show Step Dots', 'cbxtakeatour'),
                                    'value' => $show_step_dots,
                                ],
                                'show_step_progress'    => [
                                    'label' => esc_html__('Show Step Progress', 'cbxtakeatour'),
                                    'value' => $show_step_progress,
                                ],
                                'keyboard_controls'     => [
                                    'label' => esc_html__('Enable Keyboard Shortcuts', 'cbxtakeatour'),
                                    'value' => $keyboard_controls,
                                ],
                                'exit_on_escape'        => [
                                    'label' => esc_html__('Exit on ESC', 'cbxtakeatour'),
                                    'value' => $exit_on_escape,
                                ],
                                'exit_on_click_outside' => [
                                    'label' => esc_html__('Exit on Click Outside', 'cbxtakeatour'),
                                    'value' => $exit_on_click_outside,
                                ],
                                'close_button'          => [
                                    'label' => esc_html__('Show Close Button', 'cbxtakeatour'),
                                    'value' => $close_button,
                                ],
                                'dev_debug'             => [
                                    'label' => esc_html__('Enable Tour JS Debug', 'cbxtakeatour'),
                                    'value' => $dev_debug,
                                ],
                            ];


                            $cbxtour_meta_tabs = apply_filters('cbxtour_meta_tabs',
                                [
                                    'cbxtakeatour_steps'   => esc_html__('Tour Steps', 'cbxtakeatour'),
                                    'cbxtakeatour_setting' => esc_html__('Tour Settings', 'cbxtakeatour'),
                                ],
                                $post_id);

                            //nonce security field
                            //wp_nonce_field( 'cbxtourmetabox', 'cbxtourmeta[nonce]' );


                            echo '<div class="tour_postbox" id="tour_postbox">';


                            $html = '<nav class="tabs setting-tabs setting-tabs-nav">';

                            $i = 0;
                            foreach ($cbxtour_meta_tabs as $meta_tab_key => $meta_tab_title) {
                                $active_class = ($i === 0) ? 'active' : '';

                                //$extra_tab_class = ( $i === 0 ) ? 'nav-tab-active' : '';
                                //echo '<a href="#' . esc_attr( $meta_tab_key ) . '" class="nav-tab ' . esc_attr( $extra_tab_class ) . '" id="' . esc_attr( $meta_tab_key ) . '_tab">' . esc_attr( $meta_tab_title ) . '</a>';
                                //$i ++;

                                $html .= sprintf('<a data-tabid="'.$meta_tab_key.'" href="#%1$s" class="%3$s" id="%1$s-tab">%2$s</a>', $meta_tab_key, $meta_tab_title, $active_class);
                                $i++;
                            }
                            $html .= '</nav>';

                            echo $html;

                            echo '<div class="metabox-holder takeatour_meta_holder">';
                            ?>
                            <?php
                            do_action('cbxtour_meta_tab_content_start', $post_id, $tour, $meta);
                            ?>

                            <div id="cbxtakeatour_steps" class="global_setting_group">
                                <?php do_action('cbxtour_meta_tab_content_steps_start', $post_id, $tour, $meta); ?>
                                <?php
                                $totalEntries = (( ! empty($meta['steps'])) ? count($meta['steps']) : 0);

                                echo '<div id="cbxtourmetabox_entries">';

                                for ($i = 0; $i < $totalEntries; $i++) {
                                    $element = isset($meta['steps'][$i]['element']) ? esc_attr($meta['steps'][$i]['element']) : '';
                                    $title   = isset($meta['steps'][$i]['title']) ? esc_html($meta['steps'][$i]['title']) : '';
                                    $content = isset($meta['steps'][$i]['content']) ? $meta['steps'][$i]['content'] : '';
                                    $state   = isset($meta['steps'][$i]['state']) ? intval($meta['steps'][$i]['state']) : 1;

                                    ?>

                                    <div class="cbxtourmetabox_entry cbxtourmetabox_entry-<?php echo $i; ?>"
                                         data-entry-no="<?php echo $i; ?>">
                                        <div class="cbxtourmetabox_step_heading"><span
                                                    class="dashicons dashicons-editor-justify draggable"></span>
                                            <h3 class="step_heading_title"
                                                style="display: inline"><?php esc_html_e('Step', 'cbxtakeatour'); ?>
                                                #<?php echo $i + 1; ?>
                                                : <?php echo $title; ?></h3><a href="#"
                                                                               class="dashicons dashicons-post-trash delete-step"
                                                                               title="<?php esc_html_e('Delete Step',
                                                                                   'cbxtakeatour') ?>"></a>
                                        </div>

                                        <div class="cbxtourmetabox_wrap">
                                            <table class="form-table">
                                                <tbody>
                                                <tr>
                                                    <th scope="row"><label
                                                                for="cbxtourmetabox_fields_element_<?php echo $i; ?>"><?php esc_html_e('Element',
                                                                'cbxtakeatour') ?></label>
                                                    </th>
                                                    <td>
                                                        <input id="cbxtourmetabox_fields_element_<?php echo $i; ?>"
                                                               autocomplete="new-password"
                                                               type="text" name="cbxtourmeta[steps][<?php echo $i; ?>][element]"
                                                               placeholder="<?php esc_html_e('#html_element_id',
                                                                   'cbxtakeatour') ?>"
                                                               value="<?php echo $element; ?>"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><label
                                                                for="cbxtourmetabox_fields_title_<?php echo $i; ?>"><?php esc_html_e('Title',
                                                                'cbxtakeatour') ?></label>
                                                    </th>
                                                    <td>
                                                        <input id="cbxtourmetabox_fields_title_<?php echo $i; ?>"
                                                               autocomplete="new-password"
                                                               type="text" name="cbxtourmeta[steps][<?php echo $i; ?>][title]"
                                                               placeholder="<?php esc_html_e('Title', 'cbxtakeatour') ?>"
                                                               value="<?php echo $title; ?>"/>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><label
                                                                for="cbxtourmetabox_fields_content_<?php echo $i; ?>"><?php esc_html_e('Content',
                                                                'cbxtakeatour') ?></label></th>
                                                    <td>
                                                        <?php
                                                        $editor_id = 'cbxtourmetabox_fields_content_'.$i;
                                                        $setting   = [
                                                            'textarea_name' => 'cbxtourmeta[steps]['.$i.'][content]',
                                                            'teeny'         => true,
                                                            'media_buttons' => true,
                                                            'editor_class'  => '',
                                                            'textarea_rows' => 5,
                                                            'quicktags'     => false,
                                                            'menubar'       => false,
                                                        ];
                                                        wp_editor($content, $editor_id, $setting);
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">

                                                        <?php esc_html_e('Enable/Disable', 'cbxtakeatour') ?>

                                                    </th>
                                                    <td>
                                                        <input class="minitoggle_trigger"
                                                               type="hidden" name="cbxtourmeta[steps][<?php echo $i; ?>][state]"
                                                               value="<?php echo $state; ?>" <?php checked($state, true); ?> />
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <?php
                                }//end loop
                                echo '</div>';

                                echo '<p id="cbxtourmetaboxaddstep_wrap"><a data-counter="'.$totalEntries.'" id="cbxtourmetaboxaddstep" class="button secondary" href="#">'.esc_html__('Add Step',
                                        'cbxtakeatour').'</a> <a '.(($totalEntries == 0) ? ' style="display: none;" ' : '').' id="cbxtourmetaboxremstep" class="button outline primary" href="#">'.esc_html__('Remove All',
                                        'cbxtakeatour').'</a></p>';

                                $template = '
<div class="cbxtourmetabox_entry cbxtourmetabox_entry-COUNTER" data-entry-no="COUNTER">
	<div class="cbxtourmetabox_step_heading"><span class="dashicons dashicons-editor-justify draggable"></span>
	<h3 class="step_heading_title" style="display: inline">'.esc_html__('Step',
                                        'cbxtakeatour').'#COUNTERPLUS : '.esc_html__("Untitled",
                                        "cbxtakeatour").'</h3><a href="#" class="dashicons dashicons-post-trash delete-step" title="'.esc_html__("Delete Step",
                                        "cbxtakeatour").'"></a></div>
	<div class="cbxtourmetabox_wrap">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="cbxtourmetabox_fields_element_COUNTER">'.esc_html__('Element',
                                        'cbxtakeatour').'</label></th>
					<td>
						<input id="cbxtourmetabox_fields_element_COUNTER" autocomplete="new-password"  type="text" name="cbxtourmeta[steps][COUNTER][element]" placeholder="'.esc_html__('#HTML element id with(with hash at start) ',
                                        'cbxtakeatour').'" value="">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cbxtourmetabox_fields_title_COUNTER">'.esc_html__('Title', 'cbxtakeatour').'</label></th>
					<td>
						<input id="cbxtourmetabox_fields_title_COUNTER" autocomplete="new-password"  type="text" name="cbxtourmeta[steps][COUNTER][title]" placeholder="'.esc_html__('Title',
                                        'cbxtakeatour').'" value="Untitled">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cbxtourmetabox_fields_content_COUNTER">'.esc_html__('Content', 'cbxtakeatour').'</label></th>
					<td>
						<textarea class="all-options" id="cbxtourmetabox_fields_content_COUNTER" name="cbxtourmeta[steps][COUNTER][content]" placeholder="'.esc_html__('Content',
                                        'cbxtakeatour').'"></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cbxtourmetabox_fields_state_COUNTER">'.esc_html__('Enable/Disable', 'cbxtakeatour').'</label></th>
					<td>
						<input id="cbxtourmetabox_fields_state_COUNTER" class="minitoggle_trigger" type="hidden" name="cbxtourmeta[steps][COUNTER][state]"  value="1" checked="checked" />
					</td>
				</tr>
                
			</tbody>
		</table>
	</div>
</div>';

                                ?>
                                <script type="text/javascript">
                                    var elementHTMLStep = <?php echo json_encode($template); ?>;
                                </script>
                                <?php
                                echo '<br>';
                                echo '<label style="font-weight: bold" for="cbxtourmetabox_fields_redirect_url">'.esc_html__('Redirect Url : ', "cbxtakeatour").'</label>';
                                echo '<input name="cbxtourmeta[redirect_url]" placeholder="https://example.com" id="cbxtourmetabox_fields_redirect_url" type="text"  value="'.$redirect_url.'" />';
                                echo '<p class="cbxtakeatoururldec">'.esc_html__('The URL link will redirect to the end of the tour.', 'cbxtakeatour').'</p>';
                                ?>
                                <?php do_action('cbxtour_meta_tab_content_steps_end', $post_id, $tour, $meta); ?>
                            </div>
                            <div id="cbxtakeatour_setting" class="global_setting_group" style="display: none;">
                                <?php do_action('cbxtour_meta_tab_content_setting_start', $post_id, $tour, $meta); ?>
                                <table class="form-table">
                                    <tbody>
                                    <?php
                                    do_action('cbxtour_meta_tab_content_setting_start_fields', $post_id, $tour, $meta);
                                    ?>
                                    <tr>
                                        <th scope="row">
                                            <label for="cbxtourmetabox_fields_display">
                                                <?php esc_html_e('Display tour button', 'cbxtakeatour') ?>
                                            </label>
                                        </th>
                                        <td>
                                            <input id="cbxtourmetabox_fields_display" class="minitoggle_trigger"
                                                   type="hidden" name="cbxtourmeta[display]"
                                                   value="<?php echo intval($display); ?>" <?php checked($display,
                                                true); ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label
                                                    for="cbxtourmetabox_fields_autostart"><?php esc_html_e('Auto-start',
                                                    'cbxtakeatour') ?></label>
                                        </th>
                                        <td>

                                            <input id="cbxtourmetabox_fields_autostart"
                                                   class="minitoggle_trigger"
                                                   type="hidden" name="cbxtourmeta[auto_start]"
                                                   value="<?php echo intval($auto_start); ?>" <?php checked($auto_start,
                                                true); ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label
                                                    for="cbxtourmetabox_fields_tour_button_block"><?php esc_html_e('Block Button(Full Width)',
                                                    'cbxtakeatour') ?></label>
                                        </th>
                                        <td>
                                            <input id="cbxtourmetabox_fields_tour_button_block"
                                                   class="minitoggle_trigger"
                                                   type="hidden" name="cbxtourmeta[tour_button_block]"
                                                   value="<?php echo intval($tour_button_block); ?>" <?php checked($tour_button_block,
                                                true); ?> />
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label
                                                    for="cbxtourmetabox_fields_tour_button_align"><?php esc_html_e('Button Align',
                                                    'cbxtakeatour') ?></label>
                                        </th>
                                        <td>
                                            <select id="cbxtourmetabox_fields_tour_tour_button_align" class=""
                                                    name="cbxtourmeta[tour_button_align]">
                                                <option <?php selected($tour_button_align, 'center'); ?>
                                                        value="center"><?php esc_html_e('Center', 'cbxtakeatour'); ?></option>
                                                <option <?php selected($tour_button_align, 'left'); ?>
                                                        value="left"><?php esc_html_e('Left', 'cbxtakeatour'); ?></option>
                                                <option <?php selected($tour_button_align, 'right'); ?>
                                                        value="right"><?php esc_html_e('Right', 'cbxtakeatour'); ?></option>
                                                <option <?php selected($tour_button_align, 'none'); ?>
                                                        value="none"><?php esc_html_e('None', 'cbxtakeatour'); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label
                                                    for="cbxtourmetabox_fields_tour_button_text"><?php esc_html_e('Tour Button text',
                                                    'cbxtakeatour') ?></label>
                                        </th>
                                        <td>
                                            <input id="cbxtourmetabox_fields_tour_button_text"

                                                   type="text" name="cbxtourmeta[tour_button_text]"
                                                   value="<?php echo esc_attr($tour_button_text); ?>"/>
                                        </td>
                                    </tr>

                                    <?php foreach ($new_fields as $new_field_key => $new_field): ?>
                                        <tr>
                                            <th scope="row"><label
                                                        for="cbxtourmetabox_fields_<?php echo esc_attr($new_field_key); ?>"><?php echo $new_field['label']; ?></label>
                                            </th>
                                            <td>

                                                <input id="cbxtourmetabox_fields_<?php echo esc_attr($new_field_key); ?>"
                                                       class="minitoggle_trigger"
                                                       type="hidden" name="cbxtourmeta[<?php echo esc_attr($new_field_key); ?>]"
                                                       value="<?php echo intval($new_field['value']); ?>" <?php checked($new_field['value'],
                                                    true); ?> />
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>


                                    <tr>
                                        <td colspan="2" style="padding-right: 0px; padding-left: 0px; font-weight: bold">
                                            <?php esc_html_e('Select Tour layout (Need to save the post)',
                                                'cbxtakeatour'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="cbxtourmetabox_layout"><?php esc_html_e('Layout',
                                                    'cbxtakeatour') ?></label></th>
                                        <td>
                                            <?php
                                            $layout_options = CBXTakeaTourHelper::cbxtakeatour_layouts();

                                            echo '<select name="cbxtourmeta[layout]" id="cbxtourmetabox_layout">';
                                            foreach ($layout_options as $layout_key => $option_name) {
                                                echo sprintf('<option value="%s" '.selected($layout,
                                                        $layout_key,
                                                        false).' >%s</option>',
                                                    esc_attr($layout_key),
                                                    esc_attr($option_name));

                                            }
                                            echo '</select>';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    do_action('cbxtour_meta_tab_content_setting_end_fields', $post_id, $tour, $meta);
                                    ?>
                                    </tbody>
                                </table>
                                <?php do_action('cbxtour_meta_tab_content_setting_end', $post_id, $tour, $meta); ?>
                            </div>

                            <?php
                            do_action('cbxtour_meta_tab_content_end', $post_id, $tour, $meta);
                            ?>
                            <?php
                            echo '</div>'; //.takeatour_meta_holder

                            echo '</div>'; // end of div postbox

                            echo '<div class="clear clearfix"></div>';
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="postbox">
                        <div class="postbox-header"><h2><?php esc_html_e('Tour Preview', 'cbxtakeatour'); ?></h2></div>
                        <div class="inside">
                            <?php


                            $meta = get_post_meta($post_id, '_cbxtourmeta', true);
                            if ( ! is_array($meta)) {
                                $meta = [];
                            }

                            /*$steps     = isset( $meta['steps'] ) ? $meta['steps'] : [];
                            $steps_new = [];



                            if ( is_array( $steps ) && sizeof( $steps ) > 0 ) {
                                foreach ( $steps as $index => $step ) {
                                    if ( isset( $step['element'] ) ) {
                                        $step['target'] = $step['element'];
                                    }

                                    $step['group'] = 'cbxtakeatour_group_' . $id;

                                    $steps_new[] = $step;
                                }
                            }


                            unset( $steps );*/


                            $meta['steps'] = [
                                [
                                    'element' => '#cbxtakeatour_add',
                                    'target'  => '#cbxtakeatour_add',
                                    'content' => esc_html__('Add tittle, add content and check settings', 'cbxtakeatour'),
                                    'title'   => esc_html__('Create Tour', 'cbxtakeatour'),
                                    'group'   => 'cbxtakeatour_group_'.$post_id,
                                    'state'   => 1,
                                ],
                                [
                                    'element' => '#post_title',
                                    'target'  => '#post_title',
                                    'content' => esc_html__('Title is like a post title', 'cbxtakeatour'),
                                    'title'   => esc_html__('Write Tour Title', 'cbxtakeatour'),
                                    'group'   => 'cbxtakeatour_group_'.$post_id,
                                    'state'   => 1,
                                ],
                                [
                                    'element' => '#cbxtakeatour_steps-tab',
                                    'target'  => '#cbxtakeatour_steps-tab',
                                    'content' => esc_html__('Put steps - #html id, step title, step information', 'cbxtakeatour'),
                                    'title'   => esc_html__('Write Steps', 'cbxtakeatour'),
                                    'group'   => 'cbxtakeatour_group_'.$post_id,
                                    'state'   => 1,
                                ],
                                [
                                    'element' => '#cbxtakeatour_setting-tab',
                                    'target'  => '#cbxtakeatour_setting-tab',
                                    'content' => esc_html__('Change tour settings as your need', 'cbxtakeatour'),
                                    'title'   => esc_html__('Tour Settings', 'cbxtakeatour'),
                                    'group'   => 'cbxtakeatour_group_'.$post_id,
                                    'state'   => 1,
                                ],

                            ];

                            if (defined('CBXTAKEATOURPRO_PLUGIN_NAME')) {
                                $meta['steps'][] = [
                                    'element' => '#cbxtakeatour_style-tab',
                                    'target'  => '#cbxtakeatour_style-tab',
                                    'content' => esc_html__('If you choose custom layout from setting then adjust custom styles as need from this tab.', 'cbxtakeatour'),
                                    'title'   => esc_html__('Custom Style', 'cbxtakeatour'),
                                    'group'   => 'cbxtakeatour_group_'.$post_id,
                                    'state'   => 1,
                                ];
                            }


                            $layout           = isset($meta['layout']) ? esc_attr(wp_unslash($meta['layout'])) : 'basic';
                            $tour_button_text = (isset($meta['tour_button_text']) && $meta['tour_button_text'] != '') ? esc_attr(wp_unslash($meta['tour_button_text'])) : esc_attr__('Take a tour',
                                'cbxtakeatour');

                            //button align
                            $tour_button_align = (isset($meta['tour_button_align']) && ($meta['tour_button_align'] != '')) ? esc_attr($meta['tour_button_align']) : 'center';


                            //for fail-safe
                            if ( ! in_array($tour_button_align, ['left', 'center', 'right', 'none'])) {
                                $tour_button_align = 'center';
                            }


                            $layouts = array_keys(CBXTakeaTourHelper::cbxtakeatour_layouts());

                            if ( ! in_array($layout, $layouts)) {
                                $layout = 'basic';
                            }

                            $layout_class = 'cbxtakeatour_popover_'.$layout;
                            $button_class = 'cbxtakeatour-btn-'.$layout;

                            $ready_layouts = CBXTakeaTourHelper::cbxtakeatour_layout_ready_styles();
                            if (array_key_exists($layout, $ready_layouts)) {
                                $custom_css = CBXTakeaTourHelper::add_custom_css($post_id, CBXTakeaTourHelper::cbxtakeatour_layout_ready_style($layout));

                                //wp_register_style( 'cbxtakeatour-admin-inline', false, array( 'cbxtakeatour-admin' ) );
                                //wp_enqueue_style( 'cbxtakeatour-admin-inline' );
                                //wp_add_inline_style( 'cbxtakeatour-admin-inline', $custom_css );

                                wp_register_style('cbxtakeatour-public-inline', false, ['cbxtakeatour-public']);
                                wp_enqueue_style('cbxtakeatour-public-inline');
                                wp_add_inline_style('cbxtakeatour-public-inline', $custom_css);
                            }

                            do_action('cbxtakeatour_display_tour_admin_enqueue', $layout, $post_id, $meta);

                            wp_add_inline_script('cbxtakeatour-public',
                                ' cbxtakeatour.steps['.$id.']='.json_encode($meta).'; ',
                                'before');

                            ?>
                            <div class="cbxtakeatour-preview">
                                <div class="cbxtakeatour_button">
                                    <h3><?php esc_html_e('Tour Button/Link Preview', 'cbxtakeatour'); ?></h3>
                                    <span class="cbxtakeatour-btn-parent cbxtakeatour-btn-parent-<?php echo esc_attr($tour_button_align); ?>">
                                                    <a href="#" data-tour-id="<?php echo $post_id; ?>"
                                                       class="cbxtakeatour  cbxtakeatour-btn <?php echo esc_attr($button_class); ?> cbxtakeatour-btn-<?php echo intval($post_id); ?>"><?php echo esc_attr($tour_button_text); ?></a>
                                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="postbox">
                        <div class="postbox-header"><h2><?php esc_html_e('Actions', 'cbxtakeatour'); ?></h2></div>
                        <div class="inside">
                            <div class="cbxtakeatour-form-field">
                                <label for="post_status"><?php esc_html_e('Status', 'cbxtakeatour'); ?></label>
                                <select name="post_status" id="post_status">
                                    <?php
                                    $status_arr = CBXTakeaTourHelper::allowed_status();

                                    foreach ($status_arr as $state_key => $state_name):
                                        echo '<option '.selected($post_status, $state_key, false).' value="'.esc_attr($state_key).'">'.esc_attr($state_name).'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>"/>
                            <input type="hidden" name="action" value="cbxtakeatour_save_tour_post"/>
                            <input type="hidden" name="security" value="<?php echo wp_create_nonce("cbxtakeatournonce"); ?>"/>

                            <div class="pull-wrap">
                                <div class="pull-left">
                                    <a class="button outline error" id="cbxtakeatour_trashit" data-post-id="<?php echo $post_id; ?>" href="#"><?php esc_html_e('Move to Trash', 'cbxtakeatour'); ?></a>
                                </div>
                                <div class="pull-right">
                                    <button class="button primary" id="cbxtakeatour_submit" type="submit"><?php esc_attr_e('Save Changes', 'cbxtakeatour'); ?></button>
                                </div>
                            </div>

                            <?php
                            do_action('cbxtakeatour_submit_extras', $post_id);
                            ?>


                        </div>
                    </div>
                    <div class="postbox">
                        <div class="postbox-header"><h2><?php esc_html_e('Shortcode', 'cbxtakeatour'); ?></h2></div>
                        <div class="inside">
                            <div class="cbxshortcode-wrap">
                                <?php
                                echo '<span data-clipboard-text=\'[cbxtakeatour id="'.$post_id.'"]\' title="'.esc_html__("Click to clipboard",
                                        "cbxtakeatour").'" id="cbxtakeatourshortcode-'.$post_id.'" class="cbxshortcode cbxshortcode-edit cbxshortcode-'.intval($post_id).'">[cbxtakeatour id="'.intval($post_id).'"]</span>';
                                echo '<span class="button outline primary icon cbxballon_ctp" aria-label="'.esc_html__('Click to copy', 'cbxtakeatour').'" data-balloon-pos="down"><i></i></span>';
                                ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    do_action('cbxtakeatour_submit_postbox_extras', $post_id);
                    ?>
                </div>
            </div>
            <?php do_action('cbxtakeatour_tours_add_form_end', $post_id); ?>
        </form>
        <?php do_action('cbxtakeatour_tours_add_after', $post_id); ?>
	    <?php do_action('cbxtakeatour_tours_add_after_postbox', $post_id); ?>
    </div>
</div>