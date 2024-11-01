<?php
/*
Security Suite - Test List
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_ss_test_list extends wps_security_suite {


  static function get_list() {
    $list = array();
                  
    $list['wp_readme'] = array('description' => 'Check if readme.html file is accessible via HTTP on the default location.',
                               'priority' => '10',
                               'failed' => 'File readme.html is accessible via HTTP.'); 
                                                                     
    $list['wp_license'] = array('description' => 'Check if license.txt file is accessible via HTTP on the default location.',
                                'priority' => '10',
                                'failed' => 'File license.txt is accessible via HTTP.');
                                                                                                        
    $list['php_headers'] = array('description' => 'Check if server response headers contain detailed PHP version info.',
                                 'priority' => '10',
                                 'failed' => 'Server response headers contain sensitive PHP info.');
                                                                                                                                           
    $list['anyone_can_register'] = array('description' => 'Check if "anyone can register" option is enabled.',
                                         'priority' => '10',
                                         'failed' => 'Anyone can register" option is enabled.');
                                   
    $list['version_check'] = array('description' => 'Check if you are using latest WordPress core.', 
                                   'priority' => '10',
                                   'failed' => 'Your WordPress is outdated, it\'s a security risk.');

    $list['automatic_updates'] = array('description' => 'Check if WordPress automatic updates are enabled.', 
                                       'priority' => '5',
                                       'failed' => 'Your WordPress does not have automatic updates turned on.');

    $list['plugin_updates'] = array('description' => 'Check if WordPress plugins are up to date.', 
                                    'priority' => '10',
                                    'failed' => 'Some WordPress plugins are outdated, it\'s a security risk.');

    $list['wp_meta'] = array('description' => 'Check if WordPress meta is reveiled to public.', 
                             'priority' => '5',
                             'failed' => 'Some WordPress meta is publicy visible, it\'s a security risk.');

    $list['file_editor'] = array('description' => 'Check if plugins/themes file editor is enabled.', 
                                 'priority' => '5',
                                 'failed' => 'File editor is enabled.');

    $list['uploads_browsable'] = array('description' => 'Check if uploads folder is browsable by browsers.', 
                                       'priority' => '5',
                                       'failed' => 'Your <a href="%s" target="_blank">Uploads folder</a> is browsable.');

    $list['admin_username'] = array('description' => 'Check if WordPress user named "admin" exists.',
                                  'priority' => '10',
                                  'failed' => 'WordPress user named "admin" exists, it\'s a security risk.');

    $list['db_prefix'] = array('description' => 'Check if default WordPress database prefix is set.',
                               'priority' => '1',
                               'failed' => 'WordPress is using default database prefix, it\'s a security risk.');

    $list['config_file_lock'] = array('description' => 'Check if WordPress config.php file is locked from modifications.',
                                      'priority' => '10',
                                      'failed' => 'Your config.php file is writable from outsite and it can be modified from outside.');


    return $list;
  } // get_list
  
  /*
  Ajax Success
  */
  static function send_success($params = array(), $die = true) {
    if ($die) {
      wp_send_json_success($params);
    } else {
      return 'OK';
    }
  } // send_success
  
  
  /*
  Ajax Error
  */
  static function send_error($params = array(), $die = true) {
    if ($die) {
      wp_send_json_error($params);
    } else {
      return 'BAD';
    }
  } // send_error
  
  
  /*
  Write test results
  */
  static function write_results($key, $result) {
    $results = get_option(WPS_SS_SCAN_RESULTS);
    $results[$key] = $result;
    update_option(WPS_SS_SCAN_RESULTS, $results);
  } // write_results
  

  /*
  Check WordPress Version
  */
  static function version_check($die = true) {
    // Get core
    if (!function_exists('get_preferred_from_update_core') ) {
      require_once(ABSPATH . 'wp-admin/includes/update.php');
    }

    // Get current version
    wp_version_check();

    // Get prefered update core
    $latest_core_update = get_preferred_from_update_core();

    if (empty($latest_core_update) || $latest_core_update == false) {
      self::write_results('version_check', 'BAD');
      $output = self::send_error(array(), $die);
    } else {
      self::write_results('version_check', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }
    
    return $output;
  } // version_check  


  /*
  Check WordPress Automatic updates
  */
  static function automatic_updates($die = true) {

    if ( (defined('AUTOMATIC_UPDATER_DISABLED') && AUTOMATIC_UPDATER_DISABLED) 
    || (defined('WP_AUTO_UPDATE_CORE') && WP_AUTO_UPDATE_CORE != 'minor')) {
      self::write_results('automatic_updates', 'BAD');
      $output = self::send_error(array(), $die);
    } else {
      self::write_results('automatic_updates', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }

    return $output;
  } // automatic_updates  


  /*
  Check WordPress Plugins pending updates
  */
  static function plugin_updates($die = true) {

    // get current plugins waiting for updates
    $current = get_site_transient('update_plugins');

    if (!is_object($current)) {
      $current = new stdClass;
    }

    // set new transient
    set_site_transient('update_plugins', $current);

    wp_update_plugins();

    // again fetch because of wp_update_plugins()
    $current = get_site_transient('update_plugins');

    
    $plugins_to_update = 0;
    
    if (isset($current->response) && is_array($current->response) ) {
      $plugins_to_update = count($current->response);
    }

    if($plugins_to_update > 0){
      self::write_results('plugin_updates', 'BAD');
      $output = self::send_error(array(), $die);
    } else {
      self::write_results('plugin_updates', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }
    
    return $output;
  } // plugin_updates  


  /*
  Check is WordPress Meta tag present in header
  */
  static function wp_meta($die = true) {

    $response = wp_remote_get(get_bloginfo('wpurl'));

    if (!empty($response['body'])) { 
      $html = $response['body'];
      // extract content in <head> tags
      $start = strpos($html, '<head');
      $len = strpos($html, 'head>', $start + strlen('<head'));
      $html = substr($html, $start, $len - $start + strlen('head>'));
      // find all Meta Tags
      preg_match_all('#<meta([^>]*)>#si', $html, $matches);
      $meta_tags = $matches[0];

      foreach ($meta_tags as $meta_tag) {
        if (stripos($meta_tag, 'generator') !== false &&
            stripos($meta_tag, get_bloginfo('version')) !== false) {
          self::write_results('wp_meta', 'BAD');
          $output = self::send_error(array('msg' => 'Error occured'), $die);
          break;
        }
      }
      self::write_results('wp_meta', 'BAD');
      $output = self::send_error(array('msg' => 'Error occured'), $die);
    }
  
    // error
    self::write_results('wp_meta', 'OK');
    $output = self::send_success(array('msg' => 'OK'), $die);
    
    return $output;
  } // wp_meta  


  /*
  Check if user with username admin exists
  */
  static function admin_username($die = true) {
    if (username_exists('admin')) {
      self::write_results('admin_exists', 'BAD');
      $output = self::send_error(array('msg' => 'Error occured'), $die);
    } else {
      self::write_results('admin_exists', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }
    
    return $output;
  } // admin_username  

  
  /*
  Check WordPress database prefix
  */
  static function db_prefix($die = true) {
    global $wpdb;

    if ($wpdb->prefix == 'wp_' || $wpdb->prefix == 'wordpress_' || $wpdb->prefix == 'wp3_') {
      self::write_results('db_prefix', 'BAD');
      $output = self::send_error(array(), $die);
    } else {
      self::write_results('db_prefix', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }

    return $output;
  } // db_prefix  


  /*
  Check if config.php is writable
  */
  static function config_file_lock($die = true) {

    if (file_exists(ABSPATH . '/wp-config.php')) {
      $mode = substr(sprintf('%o', @fileperms(ABSPATH . '/wp-config.php')), -4);
    } else {
      $mode = substr(sprintf('%o', @fileperms(ABSPATH . '/../wp-config.php')), -4);
    }
    
    if (!$mode) {
      self::write_results('config_file_lock', 'BAD');
      $output = self::send_error(array(), $die);
    } elseif (substr($mode, -1) != 0) {
      self::write_results('config_file_lock', 'BAD');
      $output = self::send_error($mode, $die);
    } else {
      self::write_results('config_file_lock', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }

    return $output;
  } // config_file_lock
  

  /*
  Check if readme.html exists
  */
  static function wp_readme($die = true) {
    $return = array();
    $url = get_bloginfo('wpurl') . '/readme.html?rnd=' . rand();
    $response = wp_remote_get($url);

    if(is_wp_error($response)) {
      self::write_results('wp_readme', 'BAD');
      $output = self::send_error(array(), $die);
    } elseif ($response['response']['code'] == 200) {
      self::write_results('wp_readme', 'BAD');
      $output = self::send_error(array(), $die);
    } else {
      self::write_results('wp_readme', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }
    
    return $output;
  } // wp_readme  
  

  /*
  Check if license.txt exists
  */
  static function wp_license($die = true) {
    $return = array();
    $url = get_bloginfo('wpurl') . '/license.txt?rnd=' . rand();
    $response = wp_remote_get($url);

    if(is_wp_error($response)) {
      self::write_results('wp_license', 'BAD');
      $output = self::send_error(array(), $die);
    } elseif ($response['response']['code'] == 200) {
      self::write_results('wp_license', 'BAD');
      $output = self::send_error(array(), $die);
    } else {
      self::write_results('wp_license', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }
    
    return $output;
  } // wp_license  
  

  /*
  Check if php_headers
  */
  static function php_headers($die = true) {
    if (!class_exists('WP_Http')) {
      require( ABSPATH . WPINC . '/class-http.php' );
    }

    $http = new WP_Http();
    $response = (array) $http->request(home_url());

    if((isset($response['headers']['server']) && stripos($response['headers']['server'], phpversion()) !== false) || (isset($response['headers']['x-powered-by']) && stripos($response['headers']['x-powered-by'], phpversion()) !== false)) {
      self::write_results('php_headers', 'BAD');
      $output = self::send_error(array(), $die);
    } else {
      self::write_results('php_headers', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }
    
    return $output;
  } // php_headers  

  
  /*
  Check if anyone can register option is enabled
  */
  static function anyone_can_register($die = true) {
    $test = get_option('users_can_register');

     if ($test) {
       self::write_results('anyone_can_register', 'OK');
       $output = self::send_success(array('msg' => 'OK'), $die);
     } else {
       self::write_results('anyone_can_register', 'BAD');
       $output = self::send_error(array(), $die);
     }
     
     return $output;
  } // anyone_can_register
  
  
  /*
  Check if file editor is enabled
  */
  static function file_editor($die = true) {
    if (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
      self::write_results('file_editor', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    } else {
      self::write_results('file_editor', 'BAD');
      $output = self::send_error(array(), $die);
    }
    
    return $output;
  } // file_editor  
  
  
  /*
  Check if uploads folder is browsable
  */
  static function uploads_browsable($die = true) {
    $upload_dir = wp_upload_dir();

    $args = array('method' => 'GET', 'timeout' => 5, 'redirection' => 0,
                  'httpversion' => 1.0, 'blocking' => true, 'headers' => array(), 'body' => null, 'cookies' => array());
    $response = wp_remote_get(rtrim($upload_dir['baseurl'], '/') . '/?nocache=' . rand(), $args);

    if (is_wp_error($response)) {
      self::write_results('uploads_browsable', 'BAD');
      $output = self::send_error(array(), $die);
    } elseif ($response['response']['code'] == '200' && stripos($response['body'], 'index') !== false) {
      self::write_results('uploads_browsable', 'BAD');
      $output = self::send_error(array(), $die);
    } else {
      self::write_results('file_editor', 'OK');
      $output = self::send_success(array('msg' => 'OK'), $die);
    }
    
    return $output;
  } // uploads_browsable


  // does WP install.php file exist?
  static function install_file_check() {
    $return = array();
    $url = get_bloginfo('wpurl') . '/wp-admin/install.php?rnd=' . rand();
    $response = wp_remote_get($url);

    if(is_wp_error($response)) {
      $return['status'] = 5;
    } elseif ($response['response']['code'] == 200) {
      $return['status'] = 0;
    } else {
      $return['status'] = 10;
    }

    return $return;
  } // install_file_check


  // does WP install.php file exist?
  static function upgrade_file_check() {
    $return = array();
    $url = get_bloginfo('wpurl') . '/wp-admin/upgrade.php?rnd=' . rand();
    $response = wp_remote_get($url);

    if(is_wp_error($response)) {
      $return['status'] = 5;
    } elseif ($response['response']['code'] == 200) {
      $return['status'] = 0;
    } else {
      $return['status'] = 10;
    }

    return $return;
  } // upgrade_file_check


} // wps_ss_test_list