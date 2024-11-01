<?php
/*
WP Suite - Config
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

// Constants
global $wpdb;

define('WPS_WSW', 'wps-wsw-suite');
define('WPS_WSW_SLUG', 'wps-wsw-suite');
define('WPS_WSW_TEXTDOMAIN', 'wps-wsw-suite');

define('WPS_WSW_URL', plugins_url('/', __FILE__));
define('WPS_WSW_PATH', plugin_dir_path(__FILE__));

define('WPS_WSW_ANALYTICS_TABLE', 'wps_wsw_backup_analytics');
define('WPS_WSW_CREDS', 'wps-wsw-backup-creds');
define('WPS_WSW_EXCLUDE', 'wps-wsw-backup-exclude');

define('WPS_WSW_LOG', $wpdb->prefix . 'wps_wsw_log');