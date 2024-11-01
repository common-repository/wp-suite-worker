<?php
/*
Management Suite - Ajax
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/
if ( ! defined( 'ABSPATH' ) ) exit;

define('WPS_MANAGEMENT_SLUG', 'wps-management');

class wps_management_suite {

  static $version = '1.0.0';


  static function init() {
    global $wps_site_key;
    if (!empty($_POST['site_hash'])) {

      if (empty($_POST['action']) || (!empty($_POST['action']) && $_POST['action'] == 'verify_site')) {
        $action = 'register';
      } else {
        $action = sanitize_text_field($_POST['action']);
      }

      $creds = get_option(WPS_WSW_CREDS);
      $site_hash = sanitize_text_field($_POST['site_hash']);

      if ($site_hash == $creds['key']) {

        if (method_exists('wps_management_suite', $action)) {
          $wps_site_key = $site_hash;
          self::$action($_POST);
        }

      }

    }

  } // init

  /***** DEBUG ******/
  static function write_log($log_message, $action = '') {
    $log_file = ABSPATH . 'logfile.txt';

    if (!file_exists($log_file)) {
      $log = fopen($log_file, 'a');
      fclose($log);
    }

    $log = file_get_contents($log_file);
    $log .= '[Action ' . $action . ' ' . date('d/m/Y H:i:s') . '] ' . $log_message . "\r";

    file_put_contents($log_file, $log);
  } // write_log
  /***** DEBUG ******/


  static function security_scan() {
    ini_set('memory_limit', '256M');
    error_reporting(0);

    wps_ss_ajax::remote_run_tests();
  } // security_scan


  static function update_clone_status($site_key, $status_msg = '') {
    $status = get_option('wps-clone-' . $site_key);

    if (empty($status)) {
      update_option('wps-clone-' . $site_key, array('Initiated clone...'));
    } else {
      if (!in_array($status_msg, $status)) {
        $status[] = $status_msg;
        update_option('wps-clone-' . $site_key, $status);
      }
    }

  } // update_clone_status


  static function remote_backup_get_files() {
    
    $unique_ID = sanitize_text_field($_POST['unique_ID']);
    $upload_dir = wp_upload_dir();
    
    
    $theme = $upload_dir['baseurl'] . '/backup-suite/clones/' . $unique_ID  . '-themes.zip';
    $uploads = $upload_dir['baseurl'] . '/backup-suite/clones/' . $unique_ID  . '-uploads.zip';
    $plugins = $upload_dir['baseurl'] . '/backup-suite/clones/' . $unique_ID  . '-plugins.zip';
    $database = $upload_dir['baseurl'] . '/backup-suite/clones/' . $unique_ID  . '-database.zip';
    
    wp_send_json_success(array('files' => array('theme' => $theme, 
                                                'uploads' => $uploads, 
                                                'plugins' => $plugins, 
                                                'database' => $database)));
    
  } // remote_backup_get_files
  
  
  static function delete_backup_files() {
  	
  	$unique_ID = sanitize_text_field($_POST['unique_ID']);
  	
  	$upload_dir = wp_upload_dir();
  	
  	$theme = $upload_dir['baseurl'] . '/backup-suite/files/' . $unique_ID  . '-themes.zip';
    $uploads = $upload_dir['baseurl'] . '/backup-suite/files/' . $unique_ID  . '-uploads.zip';
    $plugins = $upload_dir['baseurl'] . '/backup-suite/files/' . $unique_ID  . '-plugins.zip';
    $database = $upload_dir['baseurl'] . '/backup-suite/database/' . $unique_ID  . '-database.zip';
    
    if (file_exists($theme)) {
	  unlink($theme);
    }
    
    if (file_exists($uploads)) {
	  unlink($uploads);
    }
    
    if (file_exists($plugins)) {
	  unlink($plugins);
    }
    
    if (file_exists($database)) {
	  unlink($database);
    }
    
    exit();
  	
  } // delete_backup_files
  
  static function remote_backup_site() {
    ini_set('memory_limit', '256M');
    error_reporting(0);

    self::write_log('Remote Backup Site Init #108', 'init');

    require_once ABSPATH . 'wp-includes/pluggable.php';

    // set vars
    $site_hash = sanitize_text_field($_POST['site_hash']);
    $unique_ID = sanitize_text_field($_POST['unique_ID']);
    $type = sanitize_text_field($_POST['backup_what']);

    // Create remote backup post in custom post type
    $backup = wp_insert_post(array('post_type' => 'wps-remote-backups', 'post_title' => $unique_ID, 'post_status' => 'publish'));

    if ($backup) {
      update_post_meta($backup, 'wps-unique-ID', $unique_ID);
      update_post_meta($backup, 'wps-site-hash', $site_hash);
      update_post_meta($backup, 'wps-start-time', microtime(true));
      update_post_meta($backup, 'wps-status', '0');
      self::write_log('Creadted actions left', 'debug');
      update_post_meta($backup, 'wps-backup-actions-left', 4);
    }

    // Start moving files
    self::write_log('Started backups files #119', 'init');

    // Trigger Backup setup
    if ($type == 'all' || $type == '') {
      $backups = wps_wsw_ajax::setup_backup('txt', array('unique_ID' => $unique_ID, 'site_hash' => $site_hash, 'type' => 'backup', 'backup_what' => 'backup'));
	} else {
	  $backups = wps_wsw_ajax::setup_backup('txt', array('unique_ID' => $unique_ID, 'site_hash' => $site_hash, 'type' => 'backup', 'backup_what' => $type));
	}

    self::write_log('Ended backup files #134 Backup What - ' . $type, 'init');
    self::write_log('Remote Backup Site Init - END #136', 'init');

    wp_send_json_success($backups);
  } // remote_backup_site


  static function remote_clone_site() {
    global $wpdb;
    $time = microtime(true);
    
    ini_set('memory_limit', '256M');
    error_reporting(0);

    self::write_log('Remote Clone Site Init #108', 'init');

    require_once ABSPATH . 'wp-includes/pluggable.php';

    // set vars
    $site_hash = sanitize_text_field($_POST['site_hash']);
    $unique_ID = sanitize_text_field($_POST['unique_ID']);

    // Setup cloning environment
    $setup = wps_wsw_ajax::setup_clone(array('output' => 'txt', 'site_hash' => $site_hash, 'unique_ID' => $unique_ID)); // slati mu clone_type

    // Get DB params
    $db_params['prefix'] = $wpdb->prefix;
    
    // Setup remote cPanel Account
    $cpanel = wps_wsw_ajax::generate_account(site_url(), $db_params, 'txt'); // slati mu hash, name, clone_Type

    wp_send_json_success(array('account' => $cpanel['account'], 'database_prefix' => $db_params['prefix'], 'site_hash' => $site_hash, 'unique_ID' => $unique_ID, 'site_url' => site_url()));
  } // clone


  static function register($params) {
    // todo: add two step authentification (send confirm link to admin email)

    $site_hash = preg_replace('#^https?://#', '', $params['site_url']);
    $site_hash = str_replace('.','',$site_hash);

    if (strlen($site_hash) > 7) {
      $site_hash = substr($site_hash, 0, 7);
    }

    $creds = get_option(WPS_WSW_CREDS);
    $creds['authorized'] = sanitize_text_field($site_hash);

    update_option(WPS_WSW_CREDS, $creds);

    wp_send_json_success(array('url' => get_bloginfo('url'),
      'name' => get_bloginfo('name'),
      'description' => get_bloginfo('description'),
      'wp_version' => get_bloginfo('version'),
      'site_key' => $creds['key']));
  } // register  


  static function update_details() {

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

    // Get active theme
    $stylesheet = get_stylesheet();
    $active_theme_root = WP_CONTENT_DIR . '/themes/' . $stylesheet;
    $active_theme_name = wp_get_theme();
    $active_theme_name = $active_theme_name->get('Name') . ' - ' . $active_theme_name->get('Version');

    wp_send_json_success(array('all_plugins' => $all_plugins, 
      'active_plugins' => $active_plugins, 
      'plugin_updates' => $plugin_updates, 
      'all_themes' => $themes, 
      'theme_updates' => $theme_updates, 
      'active_theme_root' => $active_theme_root, 
      'active_theme_name' => $active_theme_name));
  } // update_details


  static function get_wp_version() {
    $wp = array();
    $wp['url'] = get_bloginfo('url');
    $wp['name'] = get_bloginfo('name');
    $wp['description'] = get_bloginfo('description');
    $wp['version'] = get_bloginfo('version');
    wp_send_json_success(array('data' => $wp));
  } // get_wp_version


  static function get_site_meta() {
    wp_send_json_success(wps_get_site_meta());
  } // get_site_meta
  
  
  static function get_maintenance() {
  	// Maintenance Suite
 	require_once WP_CONTENT_DIR . '/plugins/wp-suite-worker/addons/maintenance/maintenance.php';
  	$maintenance = get_option('wps_maintenance_state');
    wp_send_json_success($maintenance);
  } // set_maintenance  
  
  
  static function set_maintenance() {
  	
  	$until = sanitize_text_field($_POST['until']);
  	$status = sanitize_text_field($_POST['status']);
  	
  	if ($status == 'true') {
	  $status = 'on';
  	} else {
	  $status = 'off';
  	}

  	// Maintenance Suite
 	require_once WP_CONTENT_DIR . '/plugins/wp-suite-worker/addons/maintenance/maintenance.php';

 	$options = get_option('wps_maintenance_options');
 	$options['until'] = $until;
 	
  	update_option('wps_maintenance_options', $options);
  	update_option('wps_maintenance_state', sanitize_text_field($status));
  	
    wp_send_json_success($status);
  } // set_maintenance
  

  static function get_site_benchmark() {
  	// Benchmark Suite
 	require_once WP_CONTENT_DIR . '/plugins/wp-suite-worker/addons/benchmark/benchmark.php';

  	$benchmark = new wps_saas_benchmark();
  	$benchmark_r = $benchmark::run_benchmark(true);
    wp_send_json_success($benchmark_r);
  } // get_site_benchmark
  

  static function get_plugin_list() {
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    wp_update_plugins();
    $all_plugins = get_plugins();
    $updates = get_site_transient('update_plugins');

    wp_send_json_success(array('list' => $all_plugins, 'updates' => $updates));
  } // get_all_plugins


  static function get_theme_list() {
    $themes = wp_get_themes();
    $updates = get_site_transient('update_themes');

    wp_send_json_success(array('list' => $themes, 'updates' => $updates));
  } // get_theme_list


  static function get_active_plugin_list() {
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $all_plugins = get_plugins();

    foreach ($all_plugins as $key => $data) {
      if (!is_plugin_active($key)) {
        unset($all_plugins[$key]);
      }
    }

    wp_send_json_success(array('list' => $all_plugins));
  } // get_active_plugin_list


  static function get_active_theme_list() {
    $stylesheet = get_stylesheet();
    $theme_root = WP_CONTENT_DIR . '/themes/' . $stylesheet;

    wp_send_json_success(array('item' => $theme_root));
  } // get_active_plugin_list  


  static function get_users_list() {
    $users = get_users();
    wp_send_json_success(array('list' => $users));
  } // get_users_list


  static function update_plugin() {
    if (empty($_POST['item_key'])) {
      wp_send_json_error('no plugin checked');
    }

    $plugin = sanitize_text_field($_POST['item_key']);

    include_once ( ABSPATH . 'wp-admin/includes/admin.php' );
    include_once ( ABSPATH . 'wp-includes/pluggable.php' );
    include_once ABSPATH.'wp-admin/includes/file.php';
    include_once ABSPATH.'wp-admin/includes/plugin.php';
    include_once ABSPATH.'wp-admin/includes/theme.php';
    include_once ABSPATH.'wp-admin/includes/misc.php';
    include_once ABSPATH.'wp-admin/includes/template.php';
    require_once ( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
    include_once 'ext/class.wps.plugin-upgrader-skin.php';


    if (!function_exists('wp_update_plugins')) {
      include_once ABSPATH.'wp-includes/update.php';
    }

    $is_active         = is_plugin_active($plugin);
    $is_active_network = is_plugin_active_for_network($plugin);

    $skin = new WPS_ManagementSuite_Plugin_Upgrader_Skin();
    $upgrader = new Plugin_Upgrader($skin);


    $result   = $upgrader->upgrade($plugin);

    if ( ! empty( $skin->error ) )

      return new WP_Error( 'plugin-upgrader-skin', $upgrader->strings[$skin->error] );

    else if ( is_wp_error( $result ) )

      return $result;

      else if ( ( ! $result && ! is_null( $result ) ) || $data )

        return new WP_Error( 'plugin-update', __( 'Unknown error updating plugin.', 'wpremote' ) );

        if ($is_active) {
      activate_plugin($plugin, '', $is_active_network, true);
    }

  } // update_plugin


} // wps_management_suite

wps_management_suite::init();