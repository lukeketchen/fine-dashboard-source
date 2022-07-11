<?php
/**
 * Plugin Name: Fine Dashboard - Source
 * Description: Create a source for the Fine Dashboard
 * Author: Luke Ketchen
 * Version: 0.1
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
	public function InitPlugin()
	{
		add_action('admin_menu', array($this, 'PluginMenu'));
		add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_style') );
	}

	// Load the plugin menu
	public function PluginMenu()
	{
		add_submenu_page(
			'tools.php',
			FINE_DASH_SOURCE_PLUGIN_NAME,
			FINE_DASH_SOURCE_PLUGIN_NAME,
			'manage_options',
			FINE_DASH_SOURCE_FD_FILE,
			array($this, 'RenderPage'),
		);
	}

	//	Fine dashboard admin page
	public function RenderPage()
	{
		include("modules/admin_panel.php");
	}

	// Load custom admin page styles
	function load_custom_wp_admin_style($hook)
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
