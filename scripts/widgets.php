<?php

/**
 * Register widget areas
 */
function vuewp_widget_areas() {
    global $vuewp_areas;
    foreach ($vuewp_areas as $area) {
        register_sidebar($area);
    }
}
add_action('widgets_init', 'vuewp_widget_areas');

/**
 * Print HTML for wp widget areas
 *
 * @return void
 */
function vuewp_add_areas() {
    global $vuewp_areas;
    ?>
    <div id="wp-sidebars">
        <?php foreach ($vuewp_areas as $area) { ?>
            <?php if (is_active_sidebar($area['id'])) { ?>
            <div class="move-to-app" data-to="#<?php print $area['id']; ?>">
                <?php dynamic_sidebar($area['id']); ?>
            </div>
            <?php } ?>
        <?php } ?>
    </div>
    <?php
}

/**
 * Add Copyright widget.
 */
class Copyright extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'copyright',
			'Copyright',
			['description' => __('Copyright information', 'vuewp')]
		);
	}

	public function widget($args, $instance) {
		extract($args);
		print $before_widget;
		if (!empty($instance['name'])) {
            $year = date('Y');
			$txt = "&copy;{$year} {$instance['name']}";
			if (!empty($instance['link'])) {
				$txt = '<a href="' . $instance['link'] . '">' . $txt . '</a>';
			}
			print $txt;
		}
		print $after_widget;
	}

	public function form($instance) {
		$name = $instance['name'] ?? '';
		$link = $instance['link'] ?? '';
		?>
		<p>
			<label for="<?php print $this->get_field_name('name'); ?>"><?php _e('Owner name:'); ?></label>
			<input class="widefat" id="<?php print $this->get_field_id('name'); ?>" name="<?php print $this->get_field_name('name'); ?>" type="text" value="<?php print esc_attr($name); ?>" />
		 </p>
		<p>
			<label for="<?php print $this->get_field_name('link'); ?>"><?php _e('Link (optional):'); ?></label>
			<input class="widefat" id="<?php print $this->get_field_id('link'); ?>" name="<?php print $this->get_field_name('link'); ?>" type="text" value="<?php print esc_attr($link); ?>" />
		 </p>
		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['name'] = (!empty( $new_instance['name'])) ? strip_tags($new_instance['name']) : '';
		$instance['link'] = (!empty( $new_instance['link'])) ? strip_tags($new_instance['link']) : '';
		return $instance;
	}
}

function vuewp_register_widgets() {
    register_widget('Copyright');
}
add_action('widgets_init', 'vuewp_register_widgets');