<?php
/*
WP Suite - Ajax
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_ajax {

  static $clone_name = '';
  static $WPS_WSW_MANAGER_URL = 'http://manager.premiumwpsuite.com';

  /*
  @name setup_clone
  User for setup of clone, basically an init function which
  inserts clone draft to post type and updates post meta for that draft.
  @return result is table row with init clone
  */
  static function setup_backup($output = 'html', $params = array()) {

	if (empty($params)) {
	  $site_hash = substr(md5(date('d-m-Y_H:i:s')), 2, 8);
	  $unique_ID = 'remote-backup' . '-' . $site_hash;
	  $backup_type = 'backup';
	  $backup_what = 'all';
	} else {
	  $site_hash = $params['site_hash'];
	  $unique_ID = $params['unique_ID'];
	  $backup_type = $params['type'];
	  $backup_what = $params['backup_what'];
	}

	// Setup Cron for Backups
	switch ($backup_what) {
	  default:
	  case 'all':
		wp_schedule_single_event(time(), 'wps_backup_theme', array($site_hash, $unique_ID, $backup_type));
		wp_schedule_single_event(time(), 'wps_backup_plugins', array($site_hash, $unique_ID, $backup_type));
		wp_schedule_single_event(time(), 'wps_backup_uploads', array($site_hash, $unique_ID, $backup_type));
		wp_schedule_single_event(time(), 'wps_backup_database', array($site_hash, $unique_ID, $backup_type));
		break;
	  case 'themes':
		wp_schedule_single_event(time(), 'wps_backup_theme', array($site_hash, $unique_ID, $backup_type));
		break;
	  case 'plugins':
		wp_schedule_single_event(time(), 'wps_backup_plugins', array($site_hash, $unique_ID, $backup_type));
		break;
	  case 'uploads':
		wp_schedule_single_event(time(), 'wps_backup_uploads', array($site_hash, $unique_ID, $backup_type));
		break;
	  case 'database':
		wp_schedule_single_event(time(), 'wps_backup_database', array($site_hash, $unique_ID, $backup_type));
		break;
	} 

	spawn_cron();
	wp_remote_post(site_url('wp-cron.php?doing_wp_cron'));

	if ($output == 'html') {
	  wp_send_json_success(array('html'));
	} else {

	  $upload_dir = wp_upload_dir();
	  
	  $files = array('theme' => $upload_dir['baseurl'] . '/backup-suite/files/' . $unique_ID . '-themes.zip',
		'plugins' => $upload_dir['baseurl'] . '/backup-suite/files/' . $unique_ID . '-plugins.zip',
		'uploads' => $upload_dir['baseurl'] . '/backup-suite/files/' . $unique_ID . '-uploads.zip',
		'database' => $upload_dir['baseurl'] . '/backup-suite/database/' . $unique_ID . '-database.zip');

	  return array('success' => 'true', 'site_url' => site_url('/'), 'site_hash' => wps_hash(), 'unique_ID' => $unique_ID, 'files' => $files);
	}

	wp_send_json_error();
  } // setup_backup


  /*
  @name setup_clone
  User for setup of clone, basically an init function which 
  inserts clone draft to post type and updates post meta for that draft.
  @return result is table row with init clone
  */
  static function setup_clone($params) {
  	
  	// Sanitize
  	foreach ($params as $key => $value) {
	  $params[$key] = sanitize_text_field($value);
  	}

	// Set backup
	self::setup_backup('txt', array('site_hash' => $params['site_hash'], 'unique_ID' => $params['unique_ID'], 'backup_type' => 'clone'));

	// Save clone
	$clone = wp_insert_post(array('post_type' => 'wps-clones', 'post_title' => $params['unique_ID'], 'post_status' => 'publish'));

	if ($clone) {

	  update_post_meta($clone, 'wps-unique-ID', $params['unique_ID']);
	  update_post_meta($clone, 'wps-site-hash', $params['site_hash']);
	  update_post_meta($clone, 'wps-start-time', microtime(true));
	  update_post_meta($clone, 'wps-status', '0');
	  update_post_meta($clone, 'wps_clone_actions_left', 4);

	  if ($params['output'] == 'html') {
		// HTML
		$output = array();
		$output['html'] = '<tr id="wps-clone-' . $params['site_hash'] . '">
		<td>#' . $params['site_hash'] . '</td>
		<td>' . $params['unique_ID'] . '</td>
		<td><span class="wps-backup-status in-progress">File copy started...</span></td>
		<td>' . date('H:i d/m/Y', strtotime(current_time('mysql'))) . '</td>
		<td>Still working...</td>
		<td><a href="#">Restore</a> | <a href="#">Delete</a></td>
		</tr>';

		// Filename
		$output['clone_name'] = $params['unique_ID'];
		$output['hash'] = $params['site_hash'];
		$output['unique_ID'] = $params['unique_ID'];
		wp_send_json_success($output);
	  } else {
		return array('clone_name' => $params['unique_ID'], 'site_hash' => $params['site_hash'], 'unique_ID' => $params['unique_ID'], 'clone_type' => '');
	  }

	}

	wp_send_json_error();
  } // setup_clone


  /*
  @name generate_account
  @return json
  */
  static function generate_account($site_url = '', $db_params = '', $output = 'json') {

	// Get Server IP
	$host= gethostname();
	$ip = gethostbyname($host);

	// SiteKey
	$creds = get_option(WPS_WSW_CREDS);

	// Send request
	$request = wp_remote_post(self::$WPS_WSW_MANAGER_URL, 
	  array(
		'timeout' => 240, 
		'body' => array(
		  'action' => 'generate_cpanel',
		  'site_url' => site_url(),
		  'site_hash' => $creds['key'],
		  'ip' => $ip,
		  'db_prefix' => $db_params['prefix']
		)
	));

	$body = $request['body'];
	$body = json_decode($body);

	if ($output == 'json') {
	  wp_send_json_success(array('account' => $body->data->account, 'password' => $body->data->account, 'hash' => $hash));
	} else {
	  return array('account' => $body->data->account, 'hash' => $hash);
	}

	#wp_send_json_error();
  } // generate_account  
  
  
  static function generate_md5($file, $md5file_loc) {
	$md5 = md5_file($file);
	$fopen = fopen($md5file_loc, 'w+');
	fwrite($fopen, $md5);
	fclose($fopen);
  } // generate_md5


  /*
  @name cron_backup_theme()
  Packs theme folder
  @return json to continue
  */
  static function cron_backup_theme($site_hash, $unique_ID, $type = 'clone') {
  	
	if ($type == 'backup') {
	  wps_wsw_backup_log::action_started($unique_ID, 'themes');
	} else {
	  wps_wsw_clone_log::action_started($unique_ID, 'themes');
	}

	// Execution time
	$time_start = microtime(true); 

	// File Location
	$location = WPS_WSW_CLONE_FILES_FOLDER;
	if ($type == 'clone') {
	  $location = WPS_WSW_CLONE_FILES_FOLDER;
	} else if ($type == 'backup') {
	  $location = WPS_WSW_FILES_FOLDER;
	}

	$file = $location . $unique_ID  . '-themes.zip';

	// Update log
	wps_wsw_clone_log::update_clone_status($site_hash, 'in-progress', 'themes', 'started', 'Started creating themes backup...');

	// Pack Themes
	wps_wsw_clone::pack_themes($file);


	// Execution end time
	$time_end = microtime(true);

	if ($type == 'backup') {
	  wps_wsw_backup_log::action_ended($unique_ID, 'themes');
	} else {
	  wps_wsw_clone_log::action_ended($unique_ID, 'themes');
	}

	// Update log
	wps_wsw_clone_log::update_clone_status($site_hash, 'in-progress', 'themes', 'ended', 'Ended creating themes backup...');
  } // clone_theme


  /*
  @name cron_backup_plugins()
  Packs plugins folder
  @return json to continue
  */
  static function cron_backup_plugins($site_hash, $unique_ID, $type = 'clone') {
  	
	if ($type == 'backup') {
	  wps_wsw_backup_log::action_started($unique_ID, 'plugins');
	} else {
	  wps_wsw_clone_log::action_started($unique_ID, 'plugins');
	}

	// Execution time
	$time_start = microtime(true); 

	// File Location
	$location = WPS_WSW_CLONE_FILES_FOLDER;
	if ($type == 'clone') {
	  $location = WPS_WSW_CLONE_FILES_FOLDER;
	} else if ($type == 'backup') {
	  $location = WPS_WSW_FILES_FOLDER;
	}

	$file = $location . $unique_ID  . '-plugins.zip';

	// Update log
	wps_wsw_clone_log::update_clone_status($site_hash, 'in-progress', 'plugins', 'started', 'Started creating plugins backup...');

	wps_wsw_clone::pack_plugins($file);

	// Execution end time
	$time_end = microtime(true);

	if ($type == 'backup') {
	  wps_wsw_backup_log::action_ended($unique_ID, 'plugins');
	} else {
	  wps_wsw_clone_log::action_ended($unique_ID, 'plugins');
	}

	// Update log
	wps_wsw_clone_log::update_clone_status($site_hash, 'in-progress', 'plugins', 'ended', 'Ended creating plugins backup...');
  } // clone_plugins  


  /*
  @name cron_backup_uploads()
  Packs uploads folder
  @return json to continue
  */
  static function cron_backup_uploads($site_hash, $unique_ID, $type = 'clone') {
  	
	if ($type == 'backup') {
	  wps_wsw_backup_log::action_started($unique_ID, 'uploads');
	} else {
	  wps_wsw_clone_log::action_started($unique_ID, 'uploads');
	}

	// Execution time
	$time_start = microtime(true); 

	// File Location
	$location = WPS_WSW_CLONE_FILES_FOLDER;
	if ($type == 'clone') {
	  $location = WPS_WSW_CLONE_FILES_FOLDER;
	} else if ($type == 'backup') {
	  $location = WPS_WSW_FILES_FOLDER;
	}

	$file = $location . $unique_ID  . '-uploads.zip';

	// Update log
	wps_wsw_clone_log::update_clone_status($site_hash, 'in-progress', 'uploads', 'started', 'Started creating uploads backup...');

	wps_wsw_clone::pack_uploads($file);

	// Execution end time
	$time_end = microtime(true);

	if ($type == 'backup') {
	  wps_wsw_backup_log::action_ended($unique_ID, 'uploads');
	} else {
	  wps_wsw_clone_log::action_ended($unique_ID, 'uploads');
	}

	// Update log
	wps_wsw_clone_log::update_clone_status($site_hash, 'in-progress', 'uploads', 'ended', 'Ended creating uploads backup...');
  } // clone_uploads


  /*
  @name cron_backup_database()
  Packs uploads folder
  @return json to continue
  */
  static function cron_backup_database($site_hash, $unique_ID, $type = 'clone') {
  	
	if ($type == 'backup') {
	  wps_wsw_backup_log::action_started($unique_ID, 'database');
	} else {
	  wps_wsw_clone_log::action_started($unique_ID, 'database');
	}

	// Execution time
	$time_start = microtime(true); 

	// File Location
	$location = WPS_WSW_CLONE_FILES_FOLDER;
	if ($type == 'clone') {
	  $location = WPS_WSW_CLONE_FILES_FOLDER;
	} else if ($type == 'backup') {
	  $location = WPS_WSW_DATABASE_FOLDER;
	}

	$file = $location . $unique_ID  . '-database.zip';

	// Update log
	wps_management_suite::write_log('Started DB Backup', 'init');
	wps_wsw_clone_log::update_clone_status($site_hash, 'in-progress', 'database', 'started', 'Started creating database backup...');

	// Backup API
	$backup = new wps_wsw_api();

	// MySQL Dump
	$dump = new Mysqldump('mysql:host=localhost;dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
	$dump->start(WPS_WSW_DATABASE_FOLDER . 'dump-' . $unique_ID . '.sql');

	// Zip the SQL
	$backup->open_zip($file, array('dump-' . $unique_ID => WPS_WSW_DATABASE_FOLDER . 'dump-' . $unique_ID . '.sql'));
	$backup->close_zip();
	$backup->delete_file(WPS_WSW_DATABASE_FOLDER . 'dump-' . $unique_ID . '.sql');

	// Execution end time
	$time_end = microtime(true);

	if ($type == 'backup') {
	  wps_wsw_backup_log::action_ended($unique_ID, 'database');
	} else {
	  wps_wsw_clone_log::action_ended($unique_ID, 'database');
	}

	// Update log
	wps_management_suite::write_log('Ended DB Backup', 'init');
	wps_wsw_clone_log::update_clone_status($site_hash, 'in-progress', 'database', 'ended', 'Ended creating database backup...');
  } // clone_uploads


} // wps_wsw_ajax