<?php
/*
Events Suite - API
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_es_api extends wps_events_suite {

  static $transient = 'wps-authorized';
  
  /**
   * Verify that asking remote account has access to this API
   * @param $site_key
   *
   * @return bool
   */
  static function verify($site_key) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $creds = get_option(WPS_WSW_CREDS);

    if (empty($site_key) || $site_key != $creds['key']) {
      return false;
    }

    if ($site_key == $creds['key']) {
      // todo: make better security
      set_transient(self::$transient, $ip, 300);
      return true;
    }

    return false;
  } // verify


  /**
   * Fetch all events, serialize and return to api
   * @param int $limit
   *
   * @return bool
   */
  static function fetch_events($limit = 100) {
    $creds = get_transient(self::$transient);
    if (!$creds) {
      // Not authorized, fail
      wp_send_json_error('Not authorized!');
    }

    global $wpdb;
    $events = $wpdb->get_results("SELECT date,ip,description 
                                  FROM " . $wpdb->prefix . WPS_EVENTS_SUITE_LOG . "
                                  ORDER BY date DESC LIMIT " . $limit);

    $return = array();

    if ($events) {
      foreach ($events as $event) {
        $return[] = array('description' => $event->description, 'date' => $event->date, 'ip' => $event->ip);
      }

      $return = json_encode($return);
      wp_send_json_success($return);
    } else {
      $return = array();
	  wp_send_json_success($return);
    }
    
    wp_send_json_error('Database error!');

    return false;
  } // fetch_events
  

  /**
   * Notify api
   * @param int $limit
   *
   * @return bool
   */
  static function notify_api($action) {
  	
  	$creds = get_option(WPS_WSW_CREDS);
  	
  	$notify = wp_remote_post(WPS_WSW_MANAGER_URL, array('body' => array('action' => 'notify_api', 
  	'event' => $action, 
  	'site_key' => $creds['key'], 
  	'email' => get_bloginfo('admin_email'),
  	'user_ip' => $_SERVER['REMOTE_ADDR'],
  	'user_agent' => $_SERVER['HTTP_USER_AGENT'])));

  } // notify_api
  
  
} // wps_es_api