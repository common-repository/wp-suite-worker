<?php
/*
WP Suite - Logs
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_log extends wps_wsw {
  
  
  static function check_table() {
    global $wpdb;
    if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . WPS_WSW_LOG . "'"))==1) {
      // Exists
    } else {
      self::create_log_table();
    }
  } // check_table


  static function create_log_table() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS `" . WPS_WSW_LOG . "` (
    `ID` int(11) NOT NULL AUTO_INCREMENT,
    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `action` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
    `message` text COLLATE utf8_unicode_ci NOT NULL,
    `file` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
    `meta` text COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`ID`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  } // create_log_table
  
  
  static function write_log($file, $action, $message, $meta = '') {
    global $wpdb;
    
    self::check_table();
     
    if (!empty($meta)) {
      $meta = serialize($meta);
    }
    
    $query = $wpdb->prepare("INSERT INTO " . WPS_WSW_LOG . " (action, message, file, meta) VALUES (%s, %s, %s, %s)", array($action, $message, $file, $meta));
    $query = $wpdb->query($query);
  } // write_log


} // wps_wsw_log