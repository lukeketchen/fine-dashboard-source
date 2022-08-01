<?php
/*
 * Plugin Name: Fine Dashboard - Source
 * Description: Create a source for the Fine Dashboard
 * Tags: dashboard, notice, admin, logged in, alert
 * Version: 1.0
 * Author: Ketchlabs
 * Author URI: https://lukeketchen.com/
 * Text Domain: fine-dashboard-source
 * License GPL2
 */



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
     die;
}

define( 'FINE_DASH_SOURCE_PLUGIN_NAME',               'Fine Dashboard - Source');
define( 'FINE_DASH_SOURCE_FD_FILE',                  __FILE__ );
define( 'FINE_DASH_SOURCE_PLUGIN_FOLDER',             plugin_dir_path( __FILE__ ));

class FineDashboardSource
{
	private static $instance;

	// Get instance of plugin
	static function GetInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	// Initialize the plugin
	function InitPlugin()
	{
		add_action('init', array($this, 'fdbs_register_custom_post_types'));
		add_action('admin_menu', array($this, 'fdbs_PluginMenu'));
		add_action('admin_enqueue_scripts', array($this, 'fdbs_load_custom_wp_admin_style'));
		add_action( 'add_meta_boxes_fdbscpt_widget', array($this, 'meta_box_for_fdbscpt_widget'));
		add_action( 'save_post_fdbscpt_widget', array($this, 'fdbscpt_widget_save_meta_boxes_data'), 10, 2);
		add_filter('manage_fdbscpt_widget_posts_columns', array($this, 'fdbs_posts_columns_id'), 5);
		add_action('manage_fdbscpt_widget_posts_custom_column', array($this,  'fdbs_posts_custom_id_columns'), 5, 2);

	}

	// Load the plugin menu
	function fdbs_PluginMenu()
	{
		add_menu_page(
			FINE_DASH_SOURCE_PLUGIN_NAME,
			__(FINE_DASH_SOURCE_PLUGIN_NAME,'wordpress'),
			'manage_options',
			'fine_dashboard_source',
			array($this, 'RenderAdminPage'),
		);
		global $menu,$submenu;
		$submenu['fine_dashboard_source'][0] =  $menu[901];
		unset($menu[901]);
	}

	// Register the custom post type
	function fdbs_register_custom_post_types(){

		// 'menu_position'=>26,
		register_post_type('fdbscpt_widget',array(
			'labels'=>
				array(
					'name'=>'Widgets',
					'singular_name'=>'Widget',
					'add_new'=>'Add Widget',
					'add_new_item'=>'Add New Widget',
					'edit_item'=>'Edit Widget',
					'new_item'=>'New Widget',
					'view_item'=>'View Widgets',
					'search_items'=>'Search Widgets',
					'not_found'=>'No Widget Found',
					'not_found_in_trash'=>'No Widgets Found in Trash',
					'menu_name' => 'Widgets',
					'name_admin_bar'=> 'Widgets',
				),
			'public'=>true,
			'show_in_rest'=>true,
			'description'=>'Fine Dashboard Source Widget',
			'exclude_from_search'=>false,
			'show_ui'=>true,
			'show_in_menu'=>true,
			'menu_position'=>901,
			// 'menu_icon'=>"dashicons-admin-links",
			'supports'=>array('title', 'editor' ,'custom_fields', 'revisions'),
		));
	}

	function fdbs_posts_columns_id($defaults){
		$defaults['wps_post_id'] = __('Widget ID');
		return $defaults;
	}

	function fdbs_posts_custom_id_columns($column_name, $id){
		if($column_name === 'wps_post_id'){
				echo $id;
		}
	}

	function meta_box_for_fdbscpt_widget( $post ){
		add_meta_box(
			'my_meta_box_custom_id',
			'Settings',
			array($this,'fdbscpt_widget_custom_meta_box_html_output'),
			'fdbscpt_widget',
			'normal',
			'high'
		);
	}

	function fdbscpt_widget_custom_meta_box_html_output( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'my_custom_meta_box_nonce' ); //used later for security
		?>
			<p><input type="checkbox" name="_show_this_widget" value="checked" <?php echo get_post_meta($post->ID, 'show_widget', true) ?>  /> <label for="show_this_widget">Show Widget?</label></p>
		<?php
	}

	function fdbscpt_widget_save_meta_boxes_data( $post_id ){
		// check for nonce to top xss
		if ( !isset( $_POST['my_custom_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['my_custom_meta_box_nonce'], basename( __FILE__ ) ) ){
			return;
		}
		// check for correct user capabilities - stop internal xss from customers
		if ( ! current_user_can( 'edit_post', $post_id ) ){
			return;
		}

		// update fields
		update_post_meta( $post_id, 'show_widget', sanitize_text_field(  $_POST['_show_this_widget'] ));
	}



	//	Fine dashboard admin page
	function RenderAdminPage()
	{
	}

	//	Fine dashboard admin page
	function RenderToolsPage()
	{
		include("modules/tools_page.php");
	}

	// Load custom admin page styles
	function fdbs_load_custom_wp_admin_style($hook)
	{
		// Load only on ?page=fine-dashboard
		if( $hook != 'tools_page_fine-dashboard-source/fine-dashboard-source' ) {
			return;
		}
		wp_enqueue_style( 'fine_admin_css', plugin_dir_url( FINE_DASH_SOURCE_FD_FILE ).'assets/css/admin-style.css', array(), null );
		wp_enqueue_script( 'fine_admin_js', plugin_dir_url( FINE_DASH_SOURCE_FD_FILE ).'assets/js/admin.js', 'fine_admin_js', true  );
	}
}

$FineDashboard = FineDashboardSource::GetInstance();
$FineDashboard->InitPlugin();



add_action( 'rest_api_init', function() {
	register_rest_field( 'fdbscpt_widget',
		'show_widget',
		array(
			'get_callback'    => 'slug_get_post_meta_cb',
			'update_callback' => 'slug_update_post_meta_cb',
			'schema'          => null,
		)
	);
});

function slug_get_post_meta_cb( $object, $field_name, $request ) {
    return get_post_meta( $object[ 'id' ], $field_name );
}

function slug_update_post_meta_cb( $value, $object, $field_name ) {
    return update_post_meta( $object[ 'id' ], $field_name, $value );
}
