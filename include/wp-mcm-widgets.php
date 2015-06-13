<?php
/**
 * The WordPress Media Category Management Plugin.
 *
 * @package   WP_MediaCategoryManagement
 * @author    De B.A.A.T. <wp-mcm@de-baat.nl>
 * @license   GPL-3.0+
 * @link      https://www.de-baat.nl/WP_MCM
 * @copyright 2015 De B.A.A.T.
 */

/**
 * WP_MCM_Widget_Categories class.
 *
 * @package   WP_MCM_Widget_Categories
 * @author    De B.A.A.T. <wp-mcm@de-baat.nl>
 */

/**
 * MCM Categories widget class
 *
 * @since 1.6.0
 */
class WP_MCM_Widget_Categories extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'mcm_widget_categories', 'description' => __( "A list or dropdown of MCM categories.", MCM_LANG ) );
		parent::__construct('mcm_categories', __('MCM Categories', MCM_LANG), $widget_ops);
	}

	public function widget( $args, $instance ) {

		// Get media taxonomy to use
		$media_taxonomy = mcm_get_media_taxonomy();

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'MCM Categories', MCM_LANG ) : $instance['title'], $instance, $this->id_base );

		$c = ! empty( $instance['count'] )        ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] )     ? '1' : '0';

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$media_cat_args = mcm_get_media_category_options($media_taxonomy);
		$media_cat_args['show_count']   = $c;
		$media_cat_args['hierarchical'] = $h;
		$media_cat_args = array(
			'taxonomy'     => $media_taxonomy,
			'orderby'      => 'name',
			'show_count'   => $c,
			'hierarchical' => $h,
		);

		if ( $d ) {
			static $first_dropdown = true;

			$dropdown_id = ( $first_dropdown ) ? $media_taxonomy : "{$this->id_base}-dropdown-{$this->number}";
			$first_dropdown = false;

			echo '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '">' . $title . '</label>';

			$media_cat_args['show_option_none'] = __( 'Select MCM Category', MCM_LANG );
			$media_cat_args['id'] = $dropdown_id;
//			$media_cat_args['slug'] = $dropdown_id;
			$media_cat_args['value_field'] = 'slug';

			/**
			 * Filter the arguments for the Categories widget drop-down.
			 *
			 * @since 1.6.0
			 *
			 * @see wp_dropdown_categories()
			 *
			 * @param array $media_cat_args An array of MCM Categories widget drop-down arguments.
			 */
			wp_dropdown_categories( apply_filters( 'mcm_widget_categories_dropdown_args', $media_cat_args ) );
?>

<script type='text/javascript'>
/* <![CDATA[ */
(function() {
	var dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id ); ?>" );
	function onMediaCatChange() {
		if ( dropdown.options[ dropdown.selectedIndex ].value !== -1 ) {
			location.href = "<?php echo home_url() . '/' . $media_taxonomy; ?>/" + dropdown.options[ dropdown.selectedIndex ].value;
		}
	}
	dropdown.onchange = onMediaCatChange;
})();
/* ]]> */
</script>

<?php
		} else {
?>
		<ul>
<?php
		$media_cat_args['title_li'] = '';

		/**
		 * Filter the arguments for the Media Categories widget.
		 *
		 * @since 1.6.0
		 *
		 * @param array $media_cat_args An array of Media Categories widget options.
		 */
		wp_list_categories( apply_filters( 'mcm_widget_categories_args', $media_cat_args ) );
?>
		</ul>
<?php
		}

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = strip_tags($new_instance['title']);
		$instance['count']        = !empty($new_instance['count'])        ? 1 : 0;
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['dropdown']     = !empty($new_instance['dropdown'])     ? 1 : 0;

		return $instance;
	}

	public function form( $instance ) {
		//Defaults
		$instance     = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title        = esc_attr( $instance['title'] );
		$count        = isset($instance['count'])          ? (bool) $instance['count']        : false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown     = isset( $instance['dropdown'] )     ? (bool) $instance['dropdown']     : false;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', MCM_LANG ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
			<label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e( 'Display as dropdown', MCM_LANG ); ?></label>
		<br />

			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts', MCM_LANG ); ?></label>
		<br />

			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
			<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy', MCM_LANG ); ?></label>
		</p>
<?php
	}

}

/**
 * Register all of the WP MCM widgets on startup.
 *
 * Calls 'widgets_init' action after all of the widgets have been registered.
 *
 * @since 1.6.0
 */
function wp_mcm_widgets_init() {

	register_widget('WP_MCM_Widget_Categories');

}

add_action('init', 'wp_mcm_widgets_init', 1);
