<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_saas_backup {


  public function init() {

	if (is_admin()) {

	  // Hooks for WP Suite tabs
	  add_action('wps_addon_tabs', array(__CLASS__, 'add_tabs'));
	  add_action('wps_addon_tab_content', array(__CLASS__, 'add_tab_content'));

	  // Ajax
	  add_action('wp_ajax_wps_send_backup_request', array(__CLASS__, 'ajax_send_backup_request'));
	  add_action('wp_ajax_wps_send_remote_backup_status', array(__CLASS__, 'ajax_remote_backup_status'));
	  add_action('wp_ajax_wps_send_remote_backup_finished', array(__CLASS__, 'ajax_remote_backup_finished'));

	  // Enqueues
	  add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueues'));

	} 

  } // init


  public function ajax_remote_backup_status() {
	$manager_url = WPS_WSW_MANAGER_URL;

	$site_hash = sanitize_text_field($_POST['site_hash']);
	$unique_ID = sanitize_text_field($_POST['unique_ID']);
	$site_url = sanitize_text_field($_POST['site_url']);

	$request = wp_remote_post($manager_url,
	  array('timeout' => 300, 'body' =>
		array('action' => 'remote_backup_site_status',
		  'site_hash' => $site_hash,
		  'unique_ID' => $unique_ID,
		  'site_url' => $site_url)
	  )
	);

	$body = json_decode($request['body'],true);
	$body = $body['data'];

	wp_send_json_success($body);
  } // ajax_remote_backup_status  


  public function ajax_remote_backup_finished() {
	$manager_url = WPS_WSW_MANAGER_URL;

	$site_hash = sanitize_text_field($_POST['site_hash']);
	$unique_ID = sanitize_text_field($_POST['unique_ID']);
	$site_url = sanitize_text_field($_POST['site_url']);

	$request = wp_remote_post($manager_url,
	  array('timeout' => 300, 'body' =>
		array('action' => 'remote_backup_site_finished',
		  'site_hash' => $site_hash,
		  'owner_email' => get_bloginfo('admin_email'),
		  'unique_ID' => $unique_ID,
		  'site_url' => $site_url)
	  )
	);
  } // ajax_remote_backup_finished


  public function admin_enqueues() {
	$screen = get_current_screen();

	if ($screen->base == 'toplevel_page_wps-backup-suite') {
	  wp_enqueue_script(WPS_WSW_SLUG . '-backups-js', WPS_WSW_PLUGIN_URL . 'addons/backup/backup.js', array('jquery'), wps_wsw::$version, true);
	}
  } // admin_enqueues


  public function get_files($unique_ID, $post_ID = '') {
	$output = '';

	$site_hash = wps_hash();
	$site_url = site_url('/');

	$files = wp_remote_post(WPS_WSW_MANAGER_URL, array('body' =>
	  array(
		'remote_action' => 'get_backup_files',
		'unique_ID' => $unique_ID,
		'site_hash' => $site_hash,
		'site_url' => $site_url
	)));

	if (!is_wp_error($files) && !empty($files['body'])) {
	  $body = json_decode($files['body'], true);
	  $body = $body['data'];
	  
	  update_post_meta($post_ID, 'wps-backup-files', $body);

	  if (is_array($body)) {
		foreach ($body as $key => $value) {
		  $output .= '<a href="' . $value . '" class="wps-link">' . strtoupper($key) . '</a> | ';
		}
	  }
	}

	$output = rtrim($output, ' | ');

	return $output;
  } // get_files


  public function ajax_send_backup_request() {
	$manager_url = WPS_WSW_MANAGER_URL;

	$site_hash = sanitize_text_field($_POST['site_hash']);
	$site_url = sanitize_text_field($_POST['site_url']);

	$request = wp_remote_post($manager_url,
	  array('timeout' => 300, 'body' =>
		array('action' => 'remote_backup_site_manual',
		  'site_hash' => $site_hash,
		  'owner_email' => get_bloginfo('admin_email'),
		  'site_url' => $site_url)
	  )
	);

	if (!is_wp_error($request) && !empty($request['body'])) {
	  $body = $request['body'];
	  $body = json_decode($body);
	  $body = $body->data;

	  $post = wp_insert_post(array('post_type' => 'wps-backups', 'post_title' => $body->unique_ID, 'post_status' => 'publish'));
	  update_post_meta($post, 'wps-unique-ID', $body->unique_ID);
	  wp_send_json_success(array('site_url' => $body->site_url, 'site_hash' => $body->site_hash, 'unique_ID' => $body->unique_ID));
	}

	wp_send_json_success();
  } // ajax_send_backup_request


  public function add_tabs() {
	echo '<li><a href="#backups">Backups</a></li>';
  } // add_tabs


  public function add_tab_content() {
	require_once 'pages/init.php';
  } // add_tab_content


} // wps_saas_backup

wps_saas_backup::init();