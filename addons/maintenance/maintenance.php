<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define('WPS_WSW_MAINTENANCE_TEMPLATES', WPS_WSW_PLUGIN_DIR . 'addons/maintenance/templates/');
define('WPS_WSW_MAINTENANCE_TEMPLATES_URI', WPS_WSW_PLUGIN_URL . 'addons/maintenance/templates/');
define('WPS_WSW_MAINTENANCE_BASE', WPS_WSW_PLUGIN_URL . '/');
define('WPS_WSW_MAINTENANCE_BOOTSTRAP', WPS_WSW_PLUGIN_URL . '/assets/bootstrap/');

class wps_saas_maintenance {


  public static function init() {

	if (is_admin()) {

	  // Hooks for WP Suite tabs
	  add_action('wps_addon_tabs', array(__CLASS__, 'add_tabs'));
	  add_action('wps_addon_tab_content', array(__CLASS__, 'add_tab_content'));

	} else {

	  add_action('wp_enqueue_scripts', array('wps_wsw_scripts', 'frontend'));
	  
	  // Maintenance mode 
	  if (self::maintenance_enabled()) {
	  	
	  	$maintenance_options = get_option('wps_maintenance_options');
	  	if (strtotime($maintenance_options['until']) < time()) {
		  update_option('wps_maintenance_state', 'off');
	  	} else {
		  add_action('template_redirect', array(__CLASS__, 'maintenance_init'));
		}
	  }

	}

  } // init


  public static function maintenance_init() {
	global $maintenance_options;

	if (!is_admin() && !is_user_logged_in()) {
	  ob_start();
	  $maintenance_options = get_option('wps_maintenance_options');
	  require_once WPS_WSW_MAINTENANCE_TEMPLATES . 'template_01/index.php';
	  ob_flush();

	  exit();
	}

  } // maintenance_init


  public static function maintenance_enabled() {
	$maintenance = get_option('wps_maintenance_state');

	if ($maintenance == 'on') {
	  return true;
	} else {
	  return false;
	}
  } // maintenance_enabled


  public static function ajax_state() {
	update_option('wps_maintenance_state', sanitize_text_field($_POST['state']));
	wp_send_json_success();
  } // ajax_state


  public static function add_tabs() {
	echo '<li><a href="#maintenance">Maintenance</a></li>';
  } // add_tabs


  public static function add_tab_content() {
	require_once 'templates/admin/init.php';
  } // add_tab_content


} // wps_saas_maintenance

wps_saas_maintenance::init();