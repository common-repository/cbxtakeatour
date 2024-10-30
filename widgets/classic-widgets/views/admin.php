<?php
/**
 * Provide a admin widget view for the plugin
 *
 * This file is used to markup the admin facing widget form
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXLatestTweets
 * @subpackage CBXLatestTweets/widgets/views
 */

if ( ! defined('WPINC')) {
    die;
}
?>
<?php
do_action('cbxtakeatour_widget_form_before_admin', $instance, $this);
?>
    <!-- Custom  Title Field -->
    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'cbxtakeatour'); ?></label>

        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
               name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
    </p>
<?php
do_action('cbxtakeatour_widget_form_start_admin', $instance, $this);
?>

    <!-- tour_id -->
    <p>
        <label for="<?php echo $this->get_field_id('tour_id'); ?>">
            <?php esc_html_e('Select Tour', 'cbxtakeatour'); ?>
        </label>
        <?php
        //$tours_arr = [];
        //global $post; //dont use this in admin
        $args = [
            'post_type'      => 'cbxtour',
            'orderby'        => 'ID',
            'order'          => 'DESC',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ];

        $tours_data = get_posts($args);

        ?>
        <select class="widefat" name="<?php echo $this->get_field_name('tour_id'); ?>"
                id="<?php echo $this->get_field_id('tour_id'); ?>">
            <option value="0" <?php selected($tour_id, 0, true); ?> ><?php esc_html_e('Select Tour', 'cbxtakeatour'); ?></option>
            <?php
            foreach ($tours_data as $post) :
                $post_id    = intval($post->ID);
                $post_title = get_the_title($post_id);

                echo '<option '.selected($post_id, $tour_id,
                        false).' value="'.intval($post_id).'">'.esc_attr($post_title).'</option>';
            endforeach; ?>
        </select>
    </p>

    <!-- button_text -->
    <p>
        <label for="<?php echo $this->get_field_id('button_text'); ?>">
            <?php esc_html_e('Button Text (Leave empty to use tour post meta setting)', "cbxtakeatour"); ?>
        </label>

        <input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text"
               value="<?php echo esc_attr($button_text); ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('display'); ?>">
            <?php esc_html_e('Display Button', "cbxtakeatour"); ?>
        </label>
        <select class="widefat" name="<?php echo $this->get_field_name('display'); ?>" id="<?php echo $this->get_field_id('display'); ?>">
            <option value="" <?php selected($display, '', true); ?>><?php esc_html_e('Use meta setting', 'cbxtakeatour'); ?></option>
            <?php
            $options = [
                '1' => esc_html__('Show', 'cbxtakeatour'),
                '0' => esc_html__('Hide', 'cbxtakeatour'),

            ];
            foreach ($options as $key => $value) {
                echo '<option value="'.intval($key).'"  '.selected($display,
                        $key, false).'>'.$value.'</option>';
            }
            ?>
        </select>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('auto_start'); ?>">
            <?php esc_html_e('Tour Auto-start', "cbxtakeatour"); ?>
        </label>
        <select class="widefat" name="<?php echo $this->get_field_name('auto_start'); ?>" id="<?php echo $this->get_field_id('auto_start'); ?>">
            <option value="" <?php selected($auto_start, '', true); ?>><?php esc_html_e('Use meta setting', 'cbxtakeatour'); ?></option>
            <?php
            $options = [
                '1' => esc_html__('Yes', 'cbxtakeatour'),
                '0' => esc_html__('No', 'cbxtakeatour'),

            ];
            foreach ($options as $key => $value) {
                echo '<option value="'.intval($key).'"  '.selected($auto_start,
                        $key, false).'>'.$value.'</option>';
            }
            ?>
        </select>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('block'); ?>">
            <?php esc_html_e('Block Button (Full Width)', "cbxtakeatour"); ?>
        </label>
        <select class="widefat" name="<?php echo $this->get_field_name('block'); ?>" id="<?php echo $this->get_field_id('block'); ?>">
            <option value="" <?php selected($block, '', true); ?>><?php esc_html_e('Use meta setting', 'cbxtakeatour'); ?></option>
            <?php
            $options = [
                '1' => esc_html__('Yes', 'cbxtakeatour'),
                '0' => esc_html__('No', 'cbxtakeatour'),

            ];
            foreach ($options as $key => $value) {
                echo '<option value="'.intval($key).'"  '.selected($block,
                        $key, false).'>'.$value.'</option>';
            }
            ?>
        </select>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('align'); ?>">
            <?php esc_html_e('Button Alignment', "cbxtakeatour"); ?>
        </label>
        <select class="widefat" name="<?php echo $this->get_field_name('align'); ?>" id="<?php echo $this->get_field_id('align'); ?>">
            <option value="" <?php selected($align, '', true); ?>><?php esc_html_e('Use meta setting', 'cbxtakeatour'); ?></option>
            <?php
            $options = [
                'center' => esc_html__('Center', 'cbxtakeatour'),
                'left'   => esc_html__('Left', 'cbxtakeatour'),
                'right'  => esc_html__('Right', 'cbxtakeatour'),
                'none'   => esc_html__('None', 'cbxtakeatour'),
            ];
            foreach ($options as $key => $value) {
                echo '<option value="'.esc_attr__($key).'"  '.selected($align,
                        $key, false).'>'.$value.'</option>';
            }

            ?>
        </select>
    </p>
<?php
do_action('cbxtakeatour_widget_form_end_admin', $instance, $this);
?>


    <input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>"
           name="<?php echo $this->get_field_name('submit'); ?>" value="1"/>

<?php
do_action('cbxtakeatour_widget_form_after_admin', $instance, $this);