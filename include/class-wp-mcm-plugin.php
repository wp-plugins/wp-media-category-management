<?php
/**
 * The WordPress Media Category Management Plugin.
 *
 * @package   WP_MediaCategoryManagement
 * @author    De B.A.A.T. <wp-mcm@de-baat.nl>
 * @license   GPL-3.0+
 * @link      http://www.de-baat.nl/WP_MCM
 * @copyright 2014 De B.A.A.T.
 */

/**
 * WP_MCM_Plugin class.
 *
 * @package   WP_MCM_Plugin
 * @author    De B.A.A.T. <wp-mcm@de-baat.nl>
 */
class WP_MCM_Plugin {

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'wp-mcm';
	protected $plugin_icon = '';

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	static $options = null;

	// Some local variables
	var $page_title, $menu_title, $capability, $menu_slug;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		// Set some variables
		$this->page_title = 'WP MCM';
		$this->menu_title = 'WP MCM';
		$this->capability = 'edit_theme_options';
		$this->menu_slug = 'wp-mcm';
		$this->plugin_icon = WP_MCM_URL . '/assets/icon-wp-mcm-18.png';

		// Load plugin text domain
		add_action( 'init',						array( $this, 'mcm_init' ) );
		add_action( 'dmp_addpanel',				array( $this, 'create_DMPPanels') );

		// Add the options page and menu item.
		add_action( 'admin_menu',				array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_init',				array( $this, 'mcm_admin_init' ) );
		add_action( 'admin_init',				array( $this, 'plugin_page_init' ) );

		// Load admin style sheet and scripts.
		add_action( 'admin_enqueue_scripts',	array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts',	array( $this, 'mcm_enqueue_media_action' ) );

		// Manage columns for attachments
		add_filter('manage_taxonomies_for_attachment_columns',	array($this,'mcm_filter_media_taxonomy_columns'), 10, 2);

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function mcm_init() {
		load_plugin_textdomain( MCM_LANG, FALSE, WP_MCM_BASENAME . '/lang/' );
		$this->debugMP('msg', __FUNCTION__ . ' ' . WP_MCM_BASENAME . '/lang/');

		// Configure some settings
		$this->mcm_register_media_taxonomy();

		$this->debugMP('msg', __FUNCTION__ . ' AFTER ' . WP_MCM_BASENAME . '/lang/');

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function mcm_admin_init() {
		load_plugin_textdomain( MCM_LANG, FALSE, WP_MCM_BASENAME . '/lang/' );
		$this->debugMP('msg', __FUNCTION__ . ' ' . WP_MCM_BASENAME . '/lang/');

		// Configure some settings
		$this->mcm_change_category_update_count_callback();

		$this->debugMP('msg', __FUNCTION__ . ' AFTER ' . WP_MCM_BASENAME . '/lang/');
	}

	/** register taxonomy for attachments */
	function mcm_register_media_taxonomy() {

		// Get media taxonomy
		$media_taxonomy = mcm_get_media_taxonomy();
		$use_media_taxonomy = $media_taxonomy != WP_MCM_POST_TAXONOMY;
		$this->debugMP('msg',__FUNCTION__ . ' taxonomy = ' . $media_taxonomy);

		// Register WP_MCM_MEDIA_TAXONOMY
		$args = array(
			'hierarchical'			=> true,  // hierarchical: true = display as categories, false = display as tags
			'show_ui'				=> $use_media_taxonomy,
			'show_admin_column'		=> $use_media_taxonomy,
			'update_count_callback'	=> 'mcm_update_count_callback',
			'labels' => array(
				'name'				=> __('Media Categories', MCM_LANG),
				'singular_name'		=> __('Media Category', MCM_LANG),
				'menu_name'			=> __('Media Categories', MCM_LANG),
				'all_items'			=> __('All Media Categories', MCM_LANG),
				'edit_item'			=> __('Edit Media Category', MCM_LANG),
				'view_item'			=> __('View Media Category', MCM_LANG),
				'update_item'		=> __('Update Media Category', MCM_LANG),
				'add_new_item'		=> __('Add New Media Category', MCM_LANG),
				'new_item_name'		=> __('New Media Category Name', MCM_LANG),
				'parent_item'		=> __('Parent Media Category', MCM_LANG),
				'parent_item_colon'	=> __('Parent Media Category:', MCM_LANG),
				'search_items'		=> __('Search Media Categories', MCM_LANG),
			),
		);
		register_taxonomy( WP_MCM_MEDIA_TAXONOMY, array( 'attachment' ), $args );

		// Register WP_MCM_POST_TAXONOMY for attachments
		$this->mcm_set_media_taxonomy_settings();
		register_taxonomy_for_object_type( WP_MCM_POST_TAXONOMY, 'attachment' );
	}

	/** Filter the columns shown depending on taxonomy choosen */
	function mcm_filter_media_taxonomy_columns( $columns, $post_type ) {

		// Get media taxonomy
		$media_taxonomy = mcm_get_media_taxonomy();
		$this->debugMP('pr',__FUNCTION__ . ' taxonomy = ' . $media_taxonomy . ' columns = ', $columns);

		// Find the columns to show
		$filtered = array();
		foreach ($columns as $key => $value) {
			switch ($value) {
				case WP_MCM_MEDIA_TAXONOMY:
					if ( $media_taxonomy == WP_MCM_MEDIA_TAXONOMY ) {
						$filtered[] = $value;
					}
					break;
				case WP_MCM_POST_TAXONOMY:
					if ( $media_taxonomy == WP_MCM_POST_TAXONOMY ) {
						$filtered[] = $value;
					}
					break;

				default:
					$filtered[] = $value;
					break;
			}
		}

		return $filtered;
	}

	/** change the settings for category taxonomy depending on taxonomy choosen */
	function mcm_set_media_taxonomy_settings() {

		// Get the post_ID and the corresponding post_type
		if ( isset( $_GET['post'] ) ) {
			$post_id = $post_ID = (int) $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = $post_ID = (int) $_POST['post_ID'];
		} else {
			$post_id = $post_ID = 0;
		}
		$post_type = get_post_type($post_id);
		$this->debugMP('msg',__FUNCTION__ . ' post_type = ' . $post_type);

		// Only limit post taxonomy for attachments
		if ( $post_type == 'attachment' ) {

			// Get media taxonomy
			$media_taxonomy = mcm_get_media_taxonomy();
			$use_post_taxonomy = $media_taxonomy == WP_MCM_POST_TAXONOMY;
			$this->debugMP('msg',__FUNCTION__ . ' taxonomy = ' . $media_taxonomy);

			// get the arguments of the already-registered taxonomy
			$category_args = get_taxonomy( WP_MCM_POST_TAXONOMY ); // returns an object

			// make changes to the args
			// in this example there are three changes
			// again, note that it's an object
			$category_args->show_ui = $use_post_taxonomy;
			$category_args->show_admin_column = $use_post_taxonomy;

			// re-register the taxonomy
			register_taxonomy( WP_MCM_POST_TAXONOMY, 'post', (array) $category_args );

		}

	}

	/** change default update_count_callback for category taxonomy */
	function mcm_change_category_update_count_callback() {
		global $wp_taxonomies;


		// Get media taxonomy
		$media_taxonomy = mcm_get_media_taxonomy();
		$this->debugMP('msg',__FUNCTION__ . ' taxonomy = ' . $media_taxonomy);

		if ( $media_taxonomy == WP_MCM_POST_TAXONOMY ) {
			if ( ! taxonomy_exists( WP_MCM_POST_TAXONOMY ) ) {
				return false;
			}

			$new_arg = &$wp_taxonomies[WP_MCM_POST_TAXONOMY]->update_count_callback;
			$new_arg = 'mcm_update_count_callback';
		}
	}

	/** Enqueue admin scripts and styles */
	function mcm_enqueue_media_action() {
		global $pagenow;
		$this->debugMP('msg',__FUNCTION__ . ' pagenow = ' . $pagenow . ', wp_script_is( media-editor ) = ' . wp_script_is( 'media-editor' ));
		if ( wp_script_is( 'media-editor' ) && (('upload.php' == $pagenow ) || ('post.php' == $pagenow ) )) {


			// Get media taxonomy
			$media_taxonomy = mcm_get_media_taxonomy();
			$this->debugMP('msg',__FUNCTION__ . ' taxonomy = ' . $media_taxonomy);

			$dropdown_options = array(
				'taxonomy'        => $media_taxonomy,
				'hide_empty'      => false,
				'hierarchical'    => true,
				'orderby'         => 'name',
				'show_count'      => ( $media_taxonomy == WP_MCM_POST_TAXONOMY ) ? false : true,
				'walker'          => new mcm_walker_category_mediagridfilter(),
				'value'           => 'id',
				'echo'            => false
			);
			$attachment_terms = wp_dropdown_categories( $dropdown_options );
			$attachment_terms = preg_replace( array( "/<select([^>]*)>/", "/<\/select>/" ), "", $attachment_terms );
			$this->debugMP('pr',__FUNCTION__ . ' attachment_terms = ', $attachment_terms);

			echo '<script type="text/javascript">';
			echo '/* <![CDATA[ */';
			echo 'var mcm_taxonomies = {"' . $media_taxonomy . '":{"list_title":"' . html_entity_decode( __( 'View all categories' ), ENT_QUOTES, 'UTF-8' ) . '","term_list":[' . substr( $attachment_terms, 2 ) . ']}};';
			echo '/* ]]> */';
			echo '</script>';

			wp_enqueue_script( 'mcm-media-views', WP_MCM_URL . '/js/wp-mcm-media-views.js', array( 'media-views' ), WP_MCM_VERSION, true );
		}
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     0.1.0
	 *
	 * @return    null    Return early if no admin page is registered.
	 */
	public function enqueue_admin_styles() {

		wp_enqueue_style( $this->plugin_slug .'-admin-styles', WP_MCM_URL . '/css/admin.css', array(), WP_MCM_VERSION );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {
		$this->debugMP('msg',__FUNCTION__ . ' Count(menuItems)=' . '...');

		if (current_user_can($this->capability)) {
			do_action('mcm_admin_menu_starting');

			// The main hook for the menu
			//
			add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->plugin_slug,
				array($this,'display_plugin_admin_page'),
				$this->plugin_icon
			);

			// Default menu items
			//
			$menuItems = array(
				array(
					'label'             => __('MCM Settings', MCM_LANG),
					'slug'              => $this->plugin_slug,
					'class'             => $this,
					'function'          => 'display_plugin_admin_page'
				)
			);

			// Get all additional menu items
			$menuItems = apply_filters('add_wp_mcm_menu_items', $menuItems);

			// Check the number of submenu_pages to add
			$this->debugMP('msg',__FUNCTION__ . ' Count(menuItems)=' . count($menuItems) . '...');
			$this->debugMP('pr',__FUNCTION__ . ' menuItems',$menuItems);

			// Attach Menu Items To Sidebar and Top Nav
			//
			foreach ($menuItems as $menuItem) {

				// Using class names (or objects)
				//
				if (isset($menuItem['class'])) {
					add_submenu_page(
						$this->plugin_slug,
						$menuItem['label'],
						$menuItem['label'],
						$this->capability,
						$menuItem['slug'],
						array($menuItem['class'],$menuItem['function'])
						);

				// Full URL or plain function name
				//
				} else {
					add_submenu_page(
						$this->plugin_slug,
						$menuItem['label'],
						$menuItem['label'],
						$this->capability,
						$menuItem['url']
						);
				}
			}

		}
	}

	/**
	 * Init the admin page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function plugin_page_init() {
		$this->debugMP('msg',__FUNCTION__ . ' ' . WP_MCM_BASENAME . '/lang/');
		register_setting( 'wp_mcm_option_group', WP_MCM_OPTIONS_NAME, array( $this, 'check_wp_mcm_option' ) );

		add_settings_section(
			'wp_mcm_section_id',
			__('Set your settings below:', MCM_LANG),
			array( $this, 'print_general_section_info' ),
			'wp-mcm-setting-admin'
		);

		add_settings_field(
			'wp_mcm_toggle_assign',
			__('Toggle Assign', MCM_LANG), 
			array( $this, 'create_wp_mcm_toggle_assign_field' ), 
			'wp-mcm-setting-admin',
			'wp_mcm_section_id',
			array( 'label_for' => 'wp_mcm_toggle_assign', 'field' => 'wp_mcm_toggle_assign' )
		);

		add_settings_field(
			'wp_mcm_use_post_taxonomy',
			__('Use Post Taxonomy', MCM_LANG),
			array( $this, 'create_wp_mcm_use_post_taxonomy_field' ),
			'wp-mcm-setting-admin',
			'wp_mcm_section_id',
			array( 'label_for' => 'wp_mcm_use_post_taxonomy', 'field' => 'wp_mcm_use_post_taxonomy' )
		);

		add_settings_field(
			'wp_mcm_use_default_category',
			__('Use Default Category', MCM_LANG),
			array( $this, 'create_wp_mcm_use_default_category_field' ),
			'wp-mcm-setting-admin',
			'wp_mcm_section_id',
			array( 'label_for' => 'wp_mcm_use_default_category', 'field' => 'wp_mcm_use_default_category' )
		);

		add_settings_field(
			'wp_mcm_default_media_category',
			__('Default Media Category', MCM_LANG),
			array( $this, 'create_wp_mcm_default_media_category_field' ),
			'wp-mcm-setting-admin',
			'wp_mcm_section_id',
			array( 'label_for' => 'wp_mcm_default_media_category', 'field' => 'wp_mcm_default_media_category' )
		);

		add_settings_field(
			'wp_mcm_default_post_category',
			__('Default Post Category', MCM_LANG),
			array( $this, 'create_wp_mcm_default_post_category_field' ),
			'wp-mcm-setting-admin',
			'wp_mcm_section_id',
			array( 'label_for' => 'wp_mcm_default_post_category', 'field' => 'wp_mcm_default_post_category' )
		);

	}

	function check_wp_mcm_option($input) {

		$newinput = array();

		// Check value of wp_mcm_toggle_assign
		$newinput['wp_mcm_toggle_assign'] = trim($input['wp_mcm_toggle_assign']);

		// Check value of wp_mcm_use_post_taxonomy
		$newinput['wp_mcm_use_post_taxonomy'] = trim($input['wp_mcm_use_post_taxonomy']);

		// Check value of wp_mcm_use_post_taxonomy
		$newinput['wp_mcm_use_default_category'] = trim($input['wp_mcm_use_default_category']);

		// Check value of wp_mcm_default_media_category
		$newinput['wp_mcm_default_media_category'] = sanitize_key(trim($input['wp_mcm_default_media_category']));
		$newinput['wp_mcm_default_post_category']  = sanitize_key(trim($input['wp_mcm_default_post_category']));

		return $newinput;
	}

	public function print_general_section_info(){
//		print __('Set your settings below:', MCM_LANG);
	}

	public function create_wp_mcm_toggle_assign_field(){
		$wp_mcm_toggle_assign = mcm_get_option('wp_mcm_toggle_assign');
		$wp_mcm_toggle_assign_name = WP_MCM_OPTIONS_NAME . '[wp_mcm_toggle_assign]';
		?><input type="checkbox" id="input_wp_mcm_toggle_assign" name="<?php echo $wp_mcm_toggle_assign_name; ?>" value="1" <?php checked('1', $wp_mcm_toggle_assign);?> /><?php  echo __(' Show category toggles in list view?', MCM_LANG);
	}

	public function create_wp_mcm_use_post_taxonomy_field(){
		$wp_mcm_use_post_taxonomy = mcm_get_option('wp_mcm_use_post_taxonomy');
		$wp_mcm_use_post_taxonomy_name = WP_MCM_OPTIONS_NAME . '[wp_mcm_use_post_taxonomy]';
		?><input type="checkbox" id="input_wp_mcm_use_post_taxonomy" name="<?php echo $wp_mcm_use_post_taxonomy_name; ?>" value="1" <?php checked('1', $wp_mcm_use_post_taxonomy);?> /><?php  echo __(' Use the same taxonomy as used for posts?', MCM_LANG);
	}

	public function create_wp_mcm_use_default_category_field(){
		$wp_mcm_use_default_category = mcm_get_option('wp_mcm_use_default_category');
		$wp_mcm_use_default_category_name = WP_MCM_OPTIONS_NAME . '[wp_mcm_use_default_category]';
		?><input type="checkbox" id="input_wp_mcm_use_default_category" name="<?php echo $wp_mcm_use_default_category_name; ?>" value="1" <?php checked('1', $wp_mcm_use_default_category);?> /><?php  echo __(' Use the default category when adding or editing an attachment?', MCM_LANG);
	}

	public function create_wp_mcm_default_media_category_field(){
		$wp_mcm_default_media_category = mcm_get_option('wp_mcm_default_media_category');
		$wp_mcm_default_media_category_name = WP_MCM_OPTIONS_NAME . '[wp_mcm_default_media_category]';
		$dropdown_options = array(
			'taxonomy'          => WP_MCM_MEDIA_TAXONOMY,
			'name'              => $wp_mcm_default_media_category_name,
			'selected'          => $wp_mcm_default_media_category,
			'hide_empty'        => 0,
			'hierarchical'      => true,
			'orderby'           => 'name',
			'walker'            => new mcm_walker_category_filter(),
			'show_count'        => false,
			'show_option_none'  => __('No default category', MCM_LANG),
			'option_none_value' => '',
		);
		wp_dropdown_categories( $dropdown_options );
		echo __(' Which media category should be used as default?', MCM_LANG);
	}

	public function create_wp_mcm_default_post_category_field(){
		$wp_mcm_default_post_category = mcm_get_option('wp_mcm_default_post_category');
		$wp_mcm_default_post_category_name = WP_MCM_OPTIONS_NAME . '[wp_mcm_default_post_category]';
		$dropdown_options = array(
			'taxonomy'          => WP_MCM_POST_TAXONOMY,
			'name'              => $wp_mcm_default_post_category_name,
			'selected'          => $wp_mcm_default_post_category,
			'hide_empty'        => 0,
			'hierarchical'      => true,
			'orderby'           => 'name',
			'walker'            => new mcm_walker_category_filter(),
			'value'             => 'slug',
			'show_count'        => false,
			'show_option_none'  => __('No default category', MCM_LANG),
			'option_none_value' => '',
		);
		wp_dropdown_categories( $dropdown_options );
		echo __(' Which post category should be used as default?', MCM_LANG);
	}

	/**
	 * Render the admin page for this plugin.
	 *
	 * @since    0.1.0
	 */
	public function display_plugin_admin_page() {
		$this->debugMP('msg',__FUNCTION__);
		include_once( WP_MCM_DIR . '/views/admin.php' );
	}


	/**
	 * Create a Debug My Plugin panel.
	 *
	 * @return null
	 */
	function create_DMPPanels() {
		if (!isset($GLOBALS['DebugMyPlugin'])) { return; }
		if (class_exists('DMPPanelWPMCMMain') == false) {
			require_once(dirname( __FILE__ ) . '/class.dmppanels.php');
		}
		$GLOBALS['DebugMyPlugin']->panels['wp-mcm'] = new DMPPanelWPMCMMain();
	}

	/**
	 * Add DebugMyPlugin messages.
	 *
	 * @param string $panel - panel name
	 * @param string $type - what type of debugging (msg = simple string, pr = print_r of variable)
	 * @param string $header - the header
	 * @param string $message - what you want to say
	 * @param string $file - file of the call (__FILE__)
	 * @param int $line - line number of the call (__LINE__)
	 * @param boolean $notime - show time? default true = yes.
	 * @return null
	 */
	function debugMP($type='msg', $header='Debug WP Media Category Management',$message='',$file=null,$line=null,$notime=false) {

		$panel='wp-mcm';

		// Panel not setup yet?  Return and do nothing.
		//
		if (
			!isset($GLOBALS['DebugMyPlugin']) ||
			!isset($GLOBALS['DebugMyPlugin']->panels[$panel])
		   ) {
			return;
		}

		if (($header!=='')) {
			$header = 'WPMCM:: ' . $header;
		}

		// Do normal real-time message output.
		//
		switch (strtolower($type)):
			case 'pr':
				$GLOBALS['DebugMyPlugin']->panels[$panel]->addPR($header,$message,$file,$line,$notime);
				break;
			default:
				$GLOBALS['DebugMyPlugin']->panels[$panel]->addMessage($header,$message,$file,$line,$notime);
		endswitch;
	}

}