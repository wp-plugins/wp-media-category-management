<?php
/**
 * The WordPress Media Category Management Plugin.
 *
 * @package   WP_MediaCategoryManagement\ShortCode
 * @author    De B.A.A.T. <wp-mcm@de-baat.nl>
 * @license   GPL-3.0+
 * @link      https://www.de-baat.nl/WP_MCM
 * @copyright 2014 De B.A.A.T.
 */

class WP_MCM_Shortcodes {

	var $page_title, $menu_title, $capability, $menu_slug, $count;

	function __construct() {

		// Set some variables
		$this->page_title = 'WP MCM';
		$this->menu_title = __('MCM Shortcodes', MCM_LANG);
		$this->capability = 'manage_categories';
		$this->menu_slug = 'wp-mcm-shortcodes';
		$this->count = 1;

		// Define the wp_mcm_shortcodes, including version independent of upper or lower case
		$wp_mcm_shortcodes = $this->get_wp_mcm_shortcodes();
		foreach ($wp_mcm_shortcodes as $shortcode) {
			$shortcode_lc = strtolower($shortcode['label']);
			$shortcode_uc = strtoupper($shortcode['label']);
			add_shortcode($shortcode['label'], array($this, $shortcode['function']));
			add_shortcode($shortcode_lc, array($this, $shortcode['function']));
			add_shortcode($shortcode_uc, array($this, $shortcode['function']));
		}

		// Add a menu entry to the WP_MediaCategoryManagement plugin menu
        add_filter('add_wp_mcm_menu_items',array($this,'add_menu_items'),90);

	}

    /**
     * Add the shortcode menu for this page
     *
     * @param mixed[] $menuItems
     * @return mixed[]
     */
    function add_menu_items($menuItems) {
        return array_merge(
                    $menuItems,
                    array(
                        array(
                            'label'     => $this->menu_title,
                            'slug'      => $this->menu_slug,
                            'class'     => $this,
                            'function'  => 'render_options'
                        ),
                    )
                );
    }

    /**
     * Get all shortcodes defined for WP Media Category Management
     *
     * @return $shortcodes[]
     */
    function get_wp_mcm_shortcodes() {
		$this->debugMP('msg',__FUNCTION__);
        return array (
					array(
						'label'       => 'WP_MCM',
						'description' => __('The basic shortcode to show a gallery of all media for the taxonomy and categories specified in the parameters.', MCM_LANG),
						'class'       => $this,
						'parameters'  => '<ul>' .
											'<li>' .
											__('<strong>taxonomy</strong>="&lt;slug&gt;"', MCM_LANG) .
											'<br/>' .
											__('The <code>slug</code> of the taxonomy to be used to filter the media to show.', MCM_LANG) .
											'<br/>' .
											sprintf(__('The default value is as defined in MCM Settings for <strong>Media Taxonomy To Use</strong>, currently defined as <code>%s</code>.', MCM_LANG), mcm_get_media_taxonomy()) .
											'</li>' .
											'<li>' .
											__('<strong>category</strong>="&lt;slugs&gt;"', MCM_LANG) .
											'<br/>' .
											__('A comma separated list of category <code>slugs</code> to be used to filter the media to show.', MCM_LANG) .
											'<br/>' .
											sprintf(__('The default value is as defined in MCM Settings for <strong>Default Media Category</strong>, currently defined as <code>%s</code>.', MCM_LANG), mcm_get_option('wp_mcm_default_media_category')) .
											'</li>' .
											'<li>' .
											__('<strong>alternative_shortcode</strong>="&lt;alternative&gt;"', MCM_LANG) .
											'<br/>' .
											__('This parameter can be used to overrule the default <code>gallery</code> shortcode as used by WordPress and most plugins.', MCM_LANG) .
											'<br/>' .
											__('The default value is <code>gallery</code>.', MCM_LANG) .
											'</li>' .
										 '</ul>',
						'function'    => 'wp_mcm_shortcode'
					),
					array(
						'label'       => 'WP-MCM',
						'description' => __('A shortcode with the same functionality as <code>WP_MCM</code> listed above.', MCM_LANG),
						'class'       => $this,
						'parameters'  => 'See the parameters of the <code>WP_MCM</code> shortcode above.',
						'function'    => 'wp_mcm_shortcode'
					),
				);
    }

	function render_options() {
		$this->debugMP('msg',__FUNCTION__);

		$render_options_output = '';
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h3><?php echo __('Shortcodes', MCM_LANG); ?></h3>
			<?php
			if (isset($_REQUEST['settings-updated'])) {
				?>
				<div id="sip-return-message" class="updated"><?php echo __('Your Settings have been saved.', MCM_LANG); ?></div>
				<?php
			}
			?>
			<p>
				<?php echo __('This page shows the shortcodes supported by the WP Media Category Management plugin:', MCM_LANG); ?>
			</p>
			<div id='wp_mcm_table_wrapper'>
			<table id='wp_mcm_shortcodes_table' class='wp-mcm wp-list-table widefat fixed posts' cellspacing="0">

			<thead>
				<tr class="wp_mcm_shortcodes_row">
					<th class="wp_mcm_shortcodes_cell" width="15%"><code>[SHORTCODE]</code>&nbsp;</th>
					<th class="wp_mcm_shortcodes_cell" width="25%"><?php echo __('Description', MCM_LANG); ?>&nbsp;</th>
					<th class="wp_mcm_shortcodes_cell"><?php echo __('Parameters', MCM_LANG); ?>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$row_style = 'even';
				$wp_mcm_shortcodes = $this->get_wp_mcm_shortcodes();
				foreach ($wp_mcm_shortcodes as $shortcode) {
					$row_style = ($row_style == 'odd') ? 'even' : 'odd';
					$render_options_output = '';
					$render_options_output .= '<tr class="wp_mcm_shortcodes_row ' . $row_style . '">';
					$render_options_output .= '<td class="wp_mcm_shortcodes_cell"><code>[' . $shortcode['label'] . ']</code></td>';
					$render_options_output .= '<td class="wp_mcm_shortcodes_cell">' . $shortcode['description'] . '</td>';
					$render_options_output .= '<td class="wp_mcm_shortcodes_cell">' . $shortcode['parameters'] . '</td>';
					$render_options_output .= '</tr>';
					echo $render_options_output;
				}
				?>
			</tbody>
			</table>
		</div>
		<p>
			<?php
				echo sprintf (__('<br/>This plugin supports the shortcodes as shown above as well as in all upper and lower case.', MCM_LANG));
				echo sprintf (__('<br/>More information can be found <a href="%s">here</a>.', MCM_LANG), WP_MCM_LINK);
				echo sprintf (__('<br/>If you have suggestions to improve this plugin, please leave a comment on this same <a href="%s">page</a>.', MCM_LANG), WP_MCM_LINK);
			?>
		</p>

	</div>
	<?php
	}


	/**
	 * Generates an image specified in the DOT language. The short code takes the following arguments:
	 *  - image_caption: the caption of the image generated
	 *
	 * @param  $attr
	 * @return string
	 */
	function wp_mcm_shortcode($attr, $content) {
		$this->debugMP('msg',__FUNCTION__);

		$mcm_shortcode_output = '';

		// Get the shortcode_attributes
		$mcm_atts = shortcode_atts(array(
			'taxonomy' => '',
			'category' => '',
			'alternative_shortcode' => 'gallery',
		), $attr);


		$this->debugMP('pr',__FUNCTION__ . ' attributes',$attr);
		$this->debugMP('msg',__FUNCTION__ . ' content',esc_html($content));

		// Get the id's to include in the gallery to show
		$mcm_gallery_ids = mcm_get_attachment_ids($mcm_atts);

		// Check and use the alternative_shortcode
		if (isset($mcm_atts['alternative_shortcode']) && $mcm_atts['alternative_shortcode'] != '') {
			$mcm_alternative_shortcode = $mcm_atts['alternative_shortcode'];
			unset($mcm_atts['alternative_shortcode']);
		}
		$this->debugMP('pr',__FUNCTION__ . ' alternative_shortcode= ' . $mcm_alternative_shortcode . ', attributes:',$mcm_atts);

		// Use original attr to prepare gallery atts
		$mcm_gallery_atts = 'include="' . $mcm_gallery_ids . '"';
		if (is_array($attr)) {
			foreach ($attr as $key => $value) {
				$mcm_gallery_atts .= ' ' . $key . '="' . $value . '"';
			}
		}

		// Do the shortcode translation
		$mcm_gallery_shortcode = '[' . $mcm_alternative_shortcode . ' ' . $mcm_gallery_atts . ']';
		$mcm_shortcode_output = do_shortcode($mcm_gallery_shortcode);

		return $mcm_shortcode_output;
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
	function debugMP($type,$hdr,$msg='') {

		global $wp_mcm_plugin;
		if (!is_object($wp_mcm_plugin)) { return; }

		if (($type === 'msg') && ($msg!=='')) {
			$msg = esc_html($msg);
		}
		if (($hdr!=='')) {
			$hdr = 'AUI:: ' . $hdr;
		}

		$wp_mcm_plugin->debugMP($type,$hdr,$msg,NULL,NULL,true);
	}

}

add_action('init', 'init_wp_mcm_shortcodes');
function init_wp_mcm_shortcodes() {
	global $WP_MCM_Shortcodes;
	$WP_MCM_Shortcodes = new WP_MCM_Shortcodes();
}
