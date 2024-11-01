<?php
/*
WP Suite - Functions
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

// Is connected with manager?
function wps_is_connected() {
  $connected = get_option('wps-wsw-connected');

  if (!empty($connected)) {
	return true;
  } else {
	return false;
  }

} // wps_is_connected


// Get site hash
function wps_hash() {
  $hash = get_option(WPS_WSW_CREDS);
  
  if (empty($hash['key']) || $hash['key'] == NULL || $hash['key'] == 'NULL') {
  	
	// Meet with manager 
	$request = wp_remote_post(WPS_WSW_MANAGER_URL, 
	  array(
		'timeout' => 30, 
		'body' => array(
		  'action' => 'meet',
		  'meta' => wps_get_site_meta(),
		  'admin' => get_bloginfo('admin_email'),
		  'site' => site_url())
	));

	if (!is_wp_error($request)) {
	  $hash = json_decode($request['body']);
	  update_option(WPS_WSW_CREDS, array('key' => $hash->data->hash));
	  return $hash->data->hash;
	}
  }

  return $hash['key'];
} // wps_hash





// Get site meta (plugins, themes, users...)
function wps_get_site_meta() {
  
  $json_output = array(
	'plugins' => array('total' => '', 'updates' => '', 'activated' => '', 'deactivated' => ''),
	'themes' => array('total' => '', 'updates' => '', 'activated' => ''),
	'wp' => array('url' => '', 'name' => '', 'description' => '', 'version' => ''));

  // WordPress Info
  $wp = array();
  $wp['url'] = get_bloginfo('url');
  $wp['name'] = get_bloginfo('name');
  $wp['description'] = get_bloginfo('description');
  $wp['version'] = get_bloginfo('version');

  // Plugins
  if (!function_exists('get_plugins')) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

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

  // Get themes
  $themes = wp_get_themes();
  $theme_updates = get_site_transient('update_themes');

  $themes_list = array();
  foreach ($themes as $key => $value) {
  	$data = parse_theme_data(WP_CONTENT_DIR . '/themes/' . $key . '/style.css');
	$themes_list[$key]['theme_name'] = $data['name'];
	$themes_list[$key]['theme_uri'] = $data['uri'];
  }

  // Get active theme
  $stylesheet = get_stylesheet();
  $active_theme_root = WP_CONTENT_DIR . '/themes/' . $stylesheet;
  $active_theme_name = wp_get_theme();
  $active_theme_name = $active_theme_name->get('Name') . ' - ' . $active_theme_name->get('Version');

  // Sort the output
  $json_output['plugins']['list'] = $all_plugins;
  $json_output['plugins']['active'] = $active_plugins;
  $json_output['plugins']['outdated'] = $plugin_updates;
  $json_output['plugins']['total'] = count($all_plugins);
  $json_output['plugins']['updates'] = count($plugin_updates);
  $json_output['plugins']['activated'] = count($active_plugins);
  $json_output['plugins']['deactivated'] = count($all_plugins) - count($active_plugins);
  //
  $json_output['themes']['list'] = $themes_list;
  $json_output['themes']['total'] = count($themes);
  $json_output['themes']['updates'] = count($theme_updates);
  $json_output['themes']['activated'] = $active_theme_name;
  //
  $json_output['wp']['name'] = $wp['name'];
  $json_output['wp']['description'] = $wp['description'];
  $json_output['wp']['version'] = $wp['version'];


  return json_encode($json_output);
} // wps_get_site_meta


function parse_theme_data($css) {
  $css = file_get_contents($css);
  $return = array();
  
  preg_match('/^[ \t\/*#@]*Theme URI:(.*)$/mi', $css, $matches); 
  $return['uri'] = $matches[1];
  
  preg_match('/^[ \t\/*#@]*Theme Name:(.*)$/mi', $css, $matches); 
  $return['name'] = $matches[1];
   
  return $return;
} // parse_theme_uri