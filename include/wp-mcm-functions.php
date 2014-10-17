<?php
/**
 * The WordPress Media Category Management Plugin.
 *
 * @package   WP_MediaCategoryManagement\Functions
 * @author    De B.A.A.T. <wp-mcm@de-baat.nl>
 * @license   GPL-3.0+
 * @link      http://www.de-baat.nl/WP_MCM
 * @copyright 2014 De B.A.A.T.
 */


function mcm_get_option($option_key = '') {
	$wp_mcm_options = get_option(WP_MCM_OPTIONS_NAME);
	return isset( $wp_mcm_options[$option_key] ) ? $wp_mcm_options[$option_key] : false;
}

function mcm_update_option($option_key = '', $option_value = '') {
	$wp_mcm_options = get_option(WP_MCM_OPTIONS_NAME);
	if ( isset( $wp_mcm_options[$option_key] ) ) {
		$wp_mcm_options[$option_key] = $option_value;
	}
	return update_option(WP_MCM_OPTIONS_NAME, $wp_mcm_options);
}

function mcm_get_option_bool($option_key = '') {
	$wp_mcm_options = get_option(WP_MCM_OPTIONS_NAME);
	if ( isset( $wp_mcm_options[$option_key] ) ) {
		return ( mcm_string_to_bool( $wp_mcm_options[$option_key] ) );
	} else {
		return false;
	}
}

function mcm_string_to_bool($value) {
	if ($value == true || $value == 'true' || $value == 'TRUE' || $value == '1') {
		return true;
	}
	else if ($value == false || $value == 'false' || $value == 'FALSE' || $value == '0') {
		return false;
	}
	else {
		return $value;
	}
}

function mcm_get_media_taxonomy() {

	// Check to use post taxonomy
	if (mcm_string_to_bool(mcm_get_option('wp_mcm_use_post_taxonomy'))) {
		return WP_MCM_POST_TAXONOMY;
	}
	/**
	 * else:
	 * Separate media categories from post categories
	 * Use a custom category called 'category_media' for the categories in the media library
	 */
	return WP_MCM_MEDIA_TAXONOMY;
}

/** Custom update_count_callback */
function mcm_get_category_ids( $mcm_atts = array() ) {

	// Get media taxonomy and use default category value
	$media_taxonomy = mcm_get_media_taxonomy();
	if ($media_taxonomy == WP_MCM_POST_TAXONOMY) {
		$media_categories = mcm_get_option( 'wp_mcm_default_post_category' );
	} else {
		$media_categories = mcm_get_option( 'wp_mcm_default_media_category' );
	}

	if (isset($mcm_atts['category']) && $mcm_atts['category'] != '') {
		$media_categories = explode(',', $mcm_atts['category']);
	}
	if ( !is_array($media_categories)) {
		$media_categories = array ( $media_categories );
	}
	mcm_debugMP('pr',__FUNCTION__ . ' taxonomy = ' . $media_taxonomy . ' categories = ', $media_categories);

	// Get the posts associated with the media_taxonomy
	$attachments_args = array(	'showposts' => -1,
								'post_type' => 'attachment',
								'post_parent' => null,
								'tax_query' => array(
									'relation' => 'OR',
									array(
//										'taxonomy' => $media_taxonomy,
										'taxonomy' => WP_MCM_POST_TAXONOMY,
										'field' => 'slug',
										'terms' => $media_categories
									),
									array(
//										'taxonomy' => $media_taxonomy,
										'taxonomy' => WP_MCM_MEDIA_TAXONOMY,
										'field' => 'slug',
										'terms' => $media_categories
									)
								),
	);

	// Use gallery options if available
	if (isset($mcm_atts['orderby']) && $mcm_atts['orderby'] != '') {
		$attachments_args['orderby'] = $mcm_atts['orderby'];
	}
	if (isset($mcm_atts['order']) && $mcm_atts['order'] != '') {
		$attachments_args['order'] = $mcm_atts['order'];
	}

	// Get the attachments for these arguments
	$attachments = get_posts($attachments_args);
	mcm_debugMP('pr',__FUNCTION__ . ' attachments found = ' . count($attachments) . ' with attachments_args = ', $attachments_args);

	// Get the post IDs for the attachments found for POST
	$attachment_ids = array();
	if ( $attachments ) {
		foreach ( $attachments as $post ) {
			setup_postdata( $post );
			$attachment_ids[] = $post->ID;
		}
		wp_reset_postdata();
	}

	$attachment_ids_result = implode(',', $attachment_ids);
	mcm_debugMP('pr',__FUNCTION__ . ' attachment_ids_result = ' . $attachment_ids_result . ' attachment_ids = ', $attachment_ids);

	return $attachment_ids_result;

}

/**
 * Simplify the plugin debugMP interface.
 *
 * Typical start of function call: $this->debugMP('msg',__FUNCTION__);
 *
 * @param string $type
 * @param string $hdr
 * @param string $msg
 */
function mcm_debugMP($type,$hdr,$msg='') {

	global $wp_mcm_plugin;
	if (!is_object($wp_mcm_plugin)) { return; }

	if (($type === 'msg') && ($msg!=='')) {
		$msg = esc_html($msg);
	}
	if (($hdr!=='')) {
		$hdr = 'Func:: ' . $hdr;
	}

	$wp_mcm_plugin->debugMP($type,$hdr,$msg,NULL,NULL,true);
}

