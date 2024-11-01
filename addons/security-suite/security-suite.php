<?php
/*
Security Suite - Ajax
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/
if ( ! defined( 'ABSPATH' ) ) exit;

require_once 'test-list.php';
require_once 'ajax.php';

define('WPS_SS_SLUG', 'wps-ss');
define('WPS_SS_SCAN_RESULTS', 'wps-ss-scan-results');

class wps_security_suite {
  
  static $version = '1.0.0';
  
  
  static function init() {
  
    if (is_admin()) {
      
      // enqueues
      add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'));
      
      // hooks for WP Suite
      add_action('wps_addon_tabs', array(__CLASS__, 'add_tabs'));
      add_action('wps_addon_tab_content', array(__CLASS__, 'add_tab_content'));
      
      // Ajax run tests
      add_action('wp_ajax_wps_ss_fix_single_test', array('wps_ss_ajax', 'fix_single_test'));
      add_action('wp_ajax_wps_ss_run_single_test', array('wps_ss_ajax', 'run_single_test'));
      add_action('wp_ajax_wps_ss_run_tests', array('wps_ss_ajax', 'run_tests'));
      
    }
    
  } // init
  
  
  static function admin_enqueue_scripts() {
    wp_enqueue_style(WPS_SS_SLUG . '-admin', plugins_url('/css/admin.css', __FILE__), array(), self::$version);
    wp_enqueue_script(WPS_SS_SLUG . '-admin', plugins_url('/js/admin.js', __FILE__), array('jquery'), self::$version, true);
  } // admin_enqueue_scripts
  
  
  static function add_tabs() {
    echo '<li><a href="#security-checks">Security Checks</a></li>';
  } // add_tabs
  
  
  static function add_tab_content() {
    require_once 'templates/tests.php';
  } // add_tab_content
  

} // wps_security_suite

wps_security_suite::init();