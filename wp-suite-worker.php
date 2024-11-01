<?php
/*
Plugin name: WP Suite Worker
Version: 1.2.7
Author: Premium WP Suite & Hawkeye Design
Author URI: http://www.premiumwpsuite.com
Plugin URI: http://www.premiumwpsuite.com
Description: Easy to setup and use WordPress backup,security and monitoring plugin. Offering you automated backups, backups of specific posts or pages, backup of database, backup of wordpress files, site monitoring, cloning, automation, updates manager, benchmarking manager, events logger.. and much more.
*/

// Various URL
define('WPS_WSW_MANAGER_URL', 'http://manager.premiumwpsuite.com');
define('WPS_WSW_PINGER_URL', 'http://ping.premiumwpsuite.com/ping.php');
define('WPS_WSW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPS_WSW_PLUGIN_URL', plugins_url('/', __FILE__));

// Plugin deps
require_once 'config.php';
require_once 'menus.php';
require_once 'pages.php';
require_once 'scripts.php';
require_once 'types.php';
require_once 'functions.php';

// Ajax
require_once 'ajax.php';

// API
require_once 'api/ajax/ajax.account.php';
require_once 'api/ajax/ajax.backup.php';
require_once 'api/ajax/ajax.clone.php';
require_once 'api/plugin/backups.api.php';
require_once 'api/plugin/mysql-dump.php';
require_once 'api/common.php';
require_once 'api/clone.log.php';
require_once 'api/backup.log.php';
require_once 'api/log.php';

// Handshake
require_once 'addons/handshake/handshake.php';

// Security Suite
require_once 'addons/security-suite/security-suite.php';

// Management Suite
require_once 'addons/management/management.php';

// Maintenance Suite
require_once 'addons/maintenance/maintenance.php';

// Benchmark Suite
require_once 'addons/benchmark/benchmark.php';

// Events Suite
require_once 'addons/events/events.php';


class wps_wsw {

  static $version = '1.2.7';

  static function init() {

  	
	// Setup constants
	$upload_dir = wp_upload_dir();
	define('WPS_WSW_FOLDER', $upload_dir['basedir'] . '/backup-suite/');
	define('WPS_WSW_FILES_FOLDER', $upload_dir['basedir'] . '/backup-suite/files/');
	define('WPS_WSW_CLONE_FILES_FOLDER', $upload_dir['basedir'] . '/backup-suite/clones/');
	define('WPS_WSW_CLONE_URL', $upload_dir['baseurl'] . '/backup-suite/clones/');
	define('WPS_WSW_DATABASE_FOLDER', $upload_dir['basedir'] . '/backup-suite/database/');

	// Check if backup folders exists, if not create them
	wps_wsw_ajax_backup::check_folders();

	if (is_admin()) {

	  // Setup 
	  add_action('admin_menu',
		array('wps_wsw_menus', 'menu'));

	  // Enqueue Scripts
	  add_action('admin_enqueue_scripts',
		array('wps_wsw_scripts', 'admin'));

	  wps_ajax_account::register_calls();
	  self::register_ajax_calls();

	  // Clone dialog
	  add_action('admin_print_footer_scripts', array('wps_wsw_pages', 'clone_dialog'));
	  add_action('admin_print_footer_scripts', array('wps_wsw_pages', 'clone_progress_dialog'));
	  add_action('admin_print_footer_scripts', array('wps_wsw_scripts', 'paths'));

	  // Notices
	  add_action('admin_notices', array(__CLASS__, 'notice_connect_manager'));
	}

	// Register post types
	wps_wsw_types::register();

	// Create schedule hooks
	$schedules = get_posts(array('post_type' => 'wps-backup-schedules', 'posts_per_page' => '-1'));

	if ($schedules) {
	  foreach ($schedules as $schedule) {
		$type = get_post_meta($schedule->ID, 'type', true);
		add_action('wps_backup_schedule_' . $schedule->ID . '_' . $type,
		  array(__CLASS__, 'wps_backup_schedule_run_' . $type));
	  } // foreach $schedules
	} // if ($schedules)

	// Setup Cron
	add_action('wps_backup_database', array('wps_wsw_ajax', 'cron_backup_database'), 10, 4);
	add_action('wps_backup_theme', array('wps_wsw_ajax', 'cron_backup_theme'), 10, 4);
	add_action('wps_backup_plugins', array('wps_wsw_ajax', 'cron_backup_plugins'), 10, 4);
	add_action('wps_backup_uploads', array('wps_wsw_ajax', 'cron_backup_uploads'), 10, 4);

	// Schedule Filter
	add_filter('cron_schedules', array(__CLASS__, 'add_schedules')); 
  } // init


  static function notice_connect_manager() {
	if (!wps_is_connected()) {
	  $class = 'notice notice-error';
	  // http://dev.wpbrickr.com/wp-admin/admin.php?page=wps-backup-suite#connect-manager
	  $message = 'Premium WP Suite - Please <a href="' . admin_url('admin.php?page=wps-backup-suite#connect-manager') . '">connect with remote manager</a> in order to activate this plugin! IT\'S FREE!!';

	  printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
  } // notice_connect_manager


  static function register_ajax_calls() {
  	// Maintenance On/Off
  	add_action('wp_ajax_wps_maintenance_state',
  	  array('wps_saas_maintenance', 'ajax_state'));
	  
	// Generate Database info for Cloning
	add_action('wp_ajax_wps_setup_clone',
	  array('wps_wsw_ajax', 'setup_clone'));

	// Cloning themes folder
	add_action('wp_ajax_wps_clone_theme',
	  array('wps_wsw_ajax', 'clone_theme'));

	// Cloning plugins folder
	add_action('wp_ajax_wps_clone_plugins',
	  array('wps_wsw_ajax', 'clone_plugins'));

	// Cloning uploads folder
	add_action('wp_ajax_wps_clone_uploads',
	  array('wps_wsw_ajax', 'clone_uploads'));

	// Transferring cloned files to remote location
	add_action('wp_ajax_wps_transfer_backup',
	  array('wps_wsw_ajax', 'transfer_backups'));

	// Check status of cloning
	add_action('wp_ajax_wps_check_status',
	  array('wps_wsw_clone_log', 'check_status'));

	// Ajax - Backup
	// Generate file backups
	add_action('wp_ajax_wps_generate_file_backup',
	  array('wps_wsw_ajax_backup', 'ajax_generate_file_backup'));

	// Generate database backup
	add_action('wp_ajax_wps_generate_database_backup',
	  array('wps_wsw_ajax_backup', 'ajax_generate_database_backup'));

	// Generate table row with info
	add_action('wp_ajax_wps_generate_file_table_row',
	  array('wps_wsw_ajax_backup', 'ajax_generate_file_table_row'));

	// Generate database row
	add_action('wp_ajax_wps_generate_database_table_row',
	  array('wps_wsw_ajax_backup', 'ajax_generate_database_table_row'));

	// Ajax Generate Exclude List
	add_action('wp_ajax_wps_generate_excludes', array('wps_wsw_ajax', 'ajax_generate_excludes'));
  } // register_ajax_calls


  static function add_schedules() {
	$schedules['weekly'] = array(
	  'interval' => 7 * 24 * 60 * 60, //7 days * 24 hours * 60 minutes * 60 seconds
	  'display' => __('Once Weekly', WPS_WSW_TEXTDOMAIN)
	);    

	$schedules['twice-hourly'] = array(
	  'interval' =>  30 * 60, //7 days * 24 hours * 60 minutes * 60 seconds
	  'display' => __('Every 30 Minutes', WPS_WSW_TEXTDOMAIN)
	);

	$schedules['two-weeks'] = array(
	  'interval' => 2 * 7 * 24 * 60 * 60, //7 days * 24 hours * 60 minutes * 60 seconds
	  'display' => __('Every Two Weeks', WPS_WSW_TEXTDOMAIN)
	);

	return $schedules;
  } // add_schedules


  static function install() {
	global $wpdb;

	// Update htaccess
	$htaccess = file_get_contents(ABSPATH . '.htaccess');

	if (strpos($htaccess, 'mod_security.c') === FALSE) {
	  $htaccess = "<IfModule mod_security.c>
	  # Turn the filtering engine On or Off
	  SecFilterEngine Off
	  </IfModule>\r\n" . $htaccess;
	  file_put_contents(ABSPATH . '.htaccess', $htaccess);
	}

	

	// Create tables
	$charset_collate = $wpdb->get_charset_collate();

	$log_table = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "wps_wsw_log` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`action` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
	`message` text COLLATE utf8_unicode_ci NOT NULL,
	`file` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
	`meta` text COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($log_table);
	
	// Install Events Table
	wps_events_suite::install();
  } // install  


  static function uninstall() {
	wps_ajax_account::disconnect_account();

	delete_option('wps-wsw-connected');
	delete_option(WPS_WSW_CREDS);
  } // uninstall


} // wps_wsw

add_action('init', array('wps_wsw', 'init'));
register_activation_hook(__FILE__, array('wps_wsw', 'install'));
register_deactivation_hook(__FILE__, array('wps_wsw', 'uninstall'));