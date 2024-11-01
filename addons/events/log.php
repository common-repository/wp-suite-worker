<?php
/*
Events Suite - Log
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_es_log extends wps_events_suite {


  static function write($event_type, $event_action, $event_params, $description, $user_id) {
    global $wpdb;

    if (!empty($event_params)) {
      $event_params = json_encode($event_params);
    }

    $insert = $wpdb->insert($wpdb->prefix . WPS_EVENTS_SUITE_LOG,
              array(
                'date' => current_time('mysql'),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'user_ID' => $user_id,
                'event_type' => $event_type,
                'event_action' => $event_action,
                'event_params' => $event_params,
                'description' => $description),
              array('%s', '%s', '%d', '%s', '%s', '%s', '%s'));
              
    return;
  } // write_log
  
} // wps_es_log