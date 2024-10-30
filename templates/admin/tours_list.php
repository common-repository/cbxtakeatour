<?php
/**
 * This template provides the tour listing page of the plugin
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
?>
<?php

$tour_log_list = new CBXTakeaTour_Listing();


//Fetch, prepare, sort, and filter CBXSCRatingReviewLog data
$tour_log_list->prepare_items();
?>

<div class="wrap cbx-chota cbxtakeatour-page-wrapper cbxtakeatour-tours-listing-wrapper" id="cbxtakeatour-tours-listing">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2></h2>
                <?php do_action('cbxtakeatour_wpheading_wrap_before', 'tours-list'); ?>
                <div class="wp-heading-wrap">
                    <div class="wp-heading-wrap-left pull-left">
                        <?php do_action('cbxtakeatour_wpheading_wrap_left_before', 'tours-list'); ?>
                        <h1 class="wp-heading-inline wp-heading-inline-cbxtakeatour">
                            <?php esc_html_e('Tours', 'cbxtakeatour'); ?>
                            <?php if (cbxtakeatour_allow_create_tour()): ?>
                                <a id="create-new-tour" href="<?php echo admin_url('admin.php?page=cbxtakeatour-listing&view=add'); ?>" class="button secondary ml-10"><?php esc_html_e('Add New', 'cbxtakeatour'); ?></a>
                            <?php endif; ?>
                        </h1>
                        <?php do_action('cbxtakeatour_wpheading_wrap_left_after', 'tours-list'); ?>
                    </div>
                    <div class="wp-heading-wrap-right  pull-right">
                        <?php do_action('cbxtakeatour_wpheading_wrap_right_before', 'tours-list'); ?>
                        <a id="clean-auto-drafts" href="#" class="button error"><?php esc_html_e('Clean Auto-Draft', 'cbxtakeatour'); ?></a>
                        <a href="<?php echo admin_url('admin.php?page=cbxtakeatour-settings'); ?>" class="button outline primary"><?php esc_html_e('Global Settings', 'cbxtakeatour'); ?></a>
                        <?php do_action('cbxtakeatour_wpheading_wrap_right_after', 'tours-list'); ?>
                    </div>
                </div>
                <?php do_action('cbxtakeatour_wpheading_wrap_after', 'tours-list'); ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php do_action('cbxtakeatour_tours_listing_before_postbox'); ?>
                <div class="postbox">
                    <div class="inside">
                        <?php do_action('cbxtakeatour_tours_listing_before'); ?>
                        <form id="cbxtakeatour_logs" method="post" class="cbx-wplisttable">
                            <?php do_action('cbxtakeatour_tours_listing_form_start'); ?>
                            <?php $tour_log_list->views(); ?>
                            <input type="hidden" name="page" value="<?php echo esc_attr(wp_unslash($_REQUEST['page'])) ?>"/>
                            <div id="cbxtakeatour_listing_filters_wrap" class="cbxtakeatour_wplisting_filters_wrap">
                                <!--<div class="cbxtakeatour_signs_filters pull-left">
									<?php
                                /*									$sign_status = isset( $_REQUEST['post_status'] ) ? esc_attr( wp_unslash( $_REQUEST['post_status'] ) ) : 'all';

                                                                    $post_statuses = get_post_statuses();
                                                                    */ ?>
                                    <select name="sign_status">
                                        <option value="all" <?php /*selected( $sign_status, 'all' ); */ ?>><?php /*esc_html_e( 'Status(All)', 'cbxtakeatour' ); */ ?></option>
										<?php
                                /*										foreach ( $post_statuses as $sign_status_key => $sign_status_label ) {
                                                                            echo '<option ' . selected( $sign_status, $sign_status_key ) . ' value="' . esc_attr( $sign_status_key ) . '">' . esc_html( $sign_status_label ) . '</option>';
                                                                        }
                                                                        */ ?>
                                    </select>
                                </div>-->
                                <?php
                                $count_posts = wp_count_posts('cbxtour');


                                $listing_url = admin_url('admin.php?page=cbxtakeatour-listing');
                                if (is_object($count_posts)) {
                                    echo '<ul class="subsubsub pull-left">';

                                    echo '<li class="all"><a href="'.CBXTakeaTourHelper::listing_url_by_status('all').'">'.esc_html__('All', 'cbxtakeatour').'</a></li>';

                                    if ($count_posts->publish > 0) {
                                        echo '<li class="publish">| <a href="'.CBXTakeaTourHelper::listing_url_by_status('publish').'">'.esc_html__('Publish', 'cbxtakeatour').'<span class="count">('.$count_posts->publish.')</span></a></li>';
                                    }

                                    if ($count_posts->private > 0) {
                                        echo '<li class="private">| <a href="'.CBXTakeaTourHelper::listing_url_by_status('private').'">'.esc_html__('Private', 'cbxtakeatour').'<span class="count">('.$count_posts->private.')</span></a></li>';
                                    }

                                    if ($count_posts->draft > 0) {
                                        echo '<li class="draft">| <a href="'.CBXTakeaTourHelper::listing_url_by_status('draft').'">'.esc_html__('Draft', 'cbxtakeatour').'<span class="count">('.$count_posts->draft.')</span></a></li>';
                                    }

                                    if ($count_posts->pending > 0) {
                                        echo '<li class="pending">| <a href="'.CBXTakeaTourHelper::listing_url_by_status('pending').'">'.esc_html__('Pending', 'cbxtakeatour').'<span class="count">('.$count_posts->pending.')</span></a></li>';
                                    }

                                    if ($count_posts->trash > 0) {
                                        echo '<li class="trash">| <a href="'.CBXTakeaTourHelper::listing_url_by_status('trash').'">'.esc_html__('Trash', 'cbxtakeatour').'<span class="count">('.$count_posts->trash.')</span></a></li>';
                                    }

                                    echo '</ul>';
                                }


                                ?>
                                <?php $tour_log_list->search_box(esc_attr__('Search', 'cbxtakeatour'), 'cbxtourlistsearch'); ?>
                            </div>
                            <?php $tour_log_list->display() ?>
                            <?php do_action('cbxtakeatour_tours_listing_form_end'); ?>
                        </form>
                        <?php do_action('cbxtakeatour_tours_listing_after'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>