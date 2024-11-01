<?php
/*
Security Suite - Ajax
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_ss_ajax extends wps_security_suite {
  
  
  static function run_tests() {
    /** Loads the WordPress Environment and Template */
    require (ABSPATH . '/wp-includes/pluggable.php');
    
    $started = microtime(true);
    
    
    $tests = wps_ss_test_list::get_list();
    if (!empty($tests)) {
      foreach ($tests as $key => $test) {
        var_dump(wps_ss_test_list::$key(false));
      } // foreach $tests
    } // if
    
    
    $ended = microtime(true);
    $duration = number_format($ended-$started, 2);
    
    die();
  } // run_tests   
   
  
  static function remote_run_tests() {
    /** Loads the WordPress Environment and Template */
    require (ABSPATH . '/wp-includes/pluggable.php');
    
    $started = microtime(true);
    $tests = wps_ss_test_list::get_list();
    $result = array();
    
    if (!empty($tests)) {
      foreach ($tests as $key => $test) {
        $result[$key] = wps_ss_test_list::$key(false);
      } // foreach $tests
    } // if
    
    $ended = microtime(true);
    $duration = number_format($ended-$started, 2);
    
    $result['duration'] = $duration;
    wp_send_json_success(array($result));
  } // remote_run_tests  
  
  /*
  Run Single Test
  */
  static function run_single_test() {
    $key = sanitize_key($_POST['key']);
    $started = microtime(true);
    
    
    $tests = wps_ss_test_list::get_list();
    if (!empty($tests[$key])) {
      wps_ss_test_list::$key();
    } else {
      wp_send_json_error(array('Key ' . $key . ' not found!'));
    }
    
    
    $ended = microtime(true);
    $duration = number_format($ended-$started, 2);
  } // run_single_test  

} // wps_ss_ajax