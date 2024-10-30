<?php
/**
 * This template provides the dashboard error page of the plugin
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

if ( ! isset($error_text)) {
    $error_text = esc_html__('Something went wrong', 'cbxtakeatour');
}
?>

<div class="wrap cbx-chota cbxtakeatour-page-wrapper cbxtakeatour-tours-listing-wrapper" id="cbxtakeatour-tours-listing">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2></h2>
                <?php do_action('cbxtakeatour_wpheading_wrap_before', 'error'); ?>
                <div class="wp-heading-wrap">
                    <div class="wp-heading-wrap-left pull-left">
                        <?php do_action('cbxtakeatour_wpheading_wrap_left_before', 'error'); ?>
                        <h1 class="wp-heading-inline wp-heading-inline-cbxtakeatour">
                            <?php esc_html_e('Tours: Error', 'cbxtakeatour'); ?>
                        </h1>
                        <?php do_action('cbxtakeatour_wpheading_wrap_left_after', 'error'); ?>
                    </div>
                    <div class="wp-heading-wrap-right pull-right">
                        <?php do_action('cbxtakeatour_wpheading_wrap_right_before', 'error'); ?>
                        <a href="<?php echo admin_url('admin.php?page=cbxtakeatour-settings'); ?>" class="button outline primary pull-right"><?php esc_html_e('Global Settings', 'cbxtakeatour'); ?></a>
                        <?php do_action('cbxtakeatour_wpheading_wrap_right_after', 'error'); ?>
                    </div>
                </div>
                <?php do_action('cbxtakeatour_wpheading_wrap_after', 'error'); ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php do_action('cbxtakeatour_tours_error_before_postbox'); ?>
                <div class="postbox">
                    <div class="inside">
                        <?php do_action('cbxtakeatour_tours_error_before'); ?>
                        <div class="notice notice-error inline">
                            <p><?php echo $error_text; ?></p>
                        </div>
                        <?php do_action('cbxtakeatour_tours_error_after'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>