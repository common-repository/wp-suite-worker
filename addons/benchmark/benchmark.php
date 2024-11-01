<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_saas_benchmark {


  public static function init() {

	if (is_admin()) {

	  // Hooks for WP Suite tabs
	  add_action('wps_addon_tabs', array(__CLASS__, 'add_tabs'));
	  add_action('wps_addon_tab_content', array(__CLASS__, 'add_tab_content'));

	  // Ajax
	  add_action('wp_ajax_wps_run_benchmark', array(__CLASS__, 'run_benchmark'));

	  // Enqueues
	  add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueues'));

	} 

  } // init


  public static function admin_enqueues() {
	$screen = get_current_screen();

	if ($screen->base == 'toplevel_page_wps-backup-suite') {
	  wp_enqueue_script(WPS_WSW_SLUG . '-benchmark-js', WPS_WSW_PLUGIN_URL . 'addons/benchmark/benchmark.js', array('jquery'), wps_wsw::$version, true);
	}
  } // admin_enqueues
  
  
  public static function get_opinion($param, $value) {
	
	$opinions = array();
	$opinions['server_response_time'] = array('10' => 'Awesome!', 
	  	  	  	  	  	  	  	  	  	  	  '30' => 'Excellent!', 
	  	  	  	  	  	  	  	  	  	  	  '50' => 'Very good!', 
	  	  	  	  	  	  	  	  	  	  	  '80' => 'Good!',
	  	  	  	  	  	  	  	  	  	  	  '100' => 'Could be better!',
	  	  	  	  	  	  	  	  	  	  	  '150' => 'Change server location!');
	  	  	  	  	  	  	  	  	  	  	  
	// Output opinion
	$output = '';
	
	switch ($param){
	  case 'server_response_time':
	    
	    if ($value <= 10) {
		  $output = '<span class="badge-green">Awesome!</span>';
	    } else if ($value <= 30) {
		  $output = '<span class="badge-green">Excellent!</span>';
	    } else if ($value <= 50) {
	      $output = '<span class="badge-yellow">Very good!</span>';
		} else if ($value <= 80) {
		  $output = '<span class="badge-orange">Good!</span>';
		} else if ($value <= 100) {
		  $output = '<span class="badge-red">Could be better!</span>';
		} else if ($value <= 150) {
		  $output = '<span class="badge-red">Change server location!</span>';
		}
	    
	  break;
	}
	
	return $output;
	
  } // get_opinion


  public static function run_benchmark($output = false) {
	$benchmark = get_option('wps_benchmark');

	global $wpdb;
	$mysql_version = $wpdb->get_results("SELECT VERSION() as mysql_version"); 
	$mysql_version = $mysql_version[0]->mysql_version;
	
	// New WP Version
	$wp_request = wp_remote_post('http://api.wordpress.org/core/version-check/1.7/');
	if (!is_wp_error($wp_request) && !empty($wp_request['body'])) {
	  $wp_request = json_decode($wp_request['body']);
	  $latest_version = $wp_request->offers[0]->version;
	} else {
	  $latest_version = 'Unknown';
	}

	$benchmark['server_response_time'] = self::ping();
	$benchmark['minified'] = self::is_html_minified();
	$benchmark['php_version'] = phpversion();
	$benchmark['mysql_version'] = $mysql_version;
	$benchmark['active_plugins'] = self::get_active_plugins();
	$benchmark['pending_update_plugins'] = self::get_update_plugins();
	$benchmark['gzip'] = ini_get('zlib.output_compression');
	$benchmark['wp_version'] = get_bloginfo('version');
	$benchmark['wp_latest_version'] = $latest_version;
	$benchmark['memory_usage'] = round(memory_get_usage()/1048576,2);
	$benchmark['memory_limit'] = ini_get('memory_limit');

	update_option('wps_benchmark', $benchmark);
	
	if (!$output) {
	  wp_send_json_success();
	} else {
	  return json_encode($benchmark);
	}
  } // run_benchmark
  
  
  public static function ping() {
	$site = site_url('/');
	
	$ping = wp_remote_post(WPS_WSW_PINGER_URL, array('body' => array('site' => $site)));
	
	$ping = json_decode($ping['body']);
	
	if($ping->success == 'true') {
	  return $ping->msg;
	} else {
	  return '0';
	}
  } // ping


  public static function is_html_minified() {
	$site = site_url('/');

	$site = wp_remote_post($site);

	$site = htmlspecialchars($site['body']);

	$search = array(
	  '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
	  '/[^\S ]+\</s',  // strip whitespaces before tags, except space
	  '/(\s)+/s'       // shorten multiple whitespace sequences
	);

	$replace = array(
	  '>',
	  '<',
	  '\\1'
	);

	$cleaned = preg_replace($search, $replace, $site);

	$tollerance = 500; // tollerate up to 500 excess chars 
	$original = strlen($site)-$tollerance;
	$minified = strlen($cleaned);

	if ($original > $minified) {
	  return false;
	} else {
	  return true;
	}

  } // is_html_minified


  public static function get_active_plugins() {

	// Get plugins
	wp_update_plugins();
	$all_plugins = get_plugins();
	$plugin_updates = get_site_transient('update_plugins');
	$plugin_updates = $plugin_updates->response;

	$active_plugins = array();

	foreach ($all_plugins as $key => $data) {
	  $is_active         = is_plugin_active($key);
	  $is_active_network = is_plugin_active_for_network($key);

	  if ($is_active || $is_active_network) {
		$active_plugins[$key] = $data;
	  }
	}

	return count($active_plugins);
  } // get_active_plugins


  public static function get_update_plugins() {

	// Get plugins
	wp_update_plugins();
	$plugin_updates = get_site_transient('update_plugins');
	$plugin_updates = $plugin_updates->response;

	return count($plugin_updates);
  } // get_active_plugins



  public static function add_tabs() {
	echo '<li><a href="#benchmark">Benchmark</a></li>';
  } // add_tabs


  public static function add_tab_content() {
	require_once 'pages/init.php';
  } // add_tab_content


} // wps_saas_benchmark

wps_saas_benchmark::init();