<?php
/*
WP Suite - Common Functions
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_clone_log {


  static function update_clone_status($hash, $status, $what, $action, $msg) {
    $clone = get_posts(array('post_type' => 'wps-clones',
      'meta_key' => 'wps_clone_hash',
      'meta_value' => $hash));

    if ($clone) {
      $desc_meta = get_post_meta($clone[0]->ID, 'wps_clone_status_description', true);

      $desc_meta[$what][$action]['time'] = microtime(true);
      $desc_meta[$what][$action]['msg'] = $msg;

      update_post_meta($clone[0]->ID, 'wps_clone_status', $status);
      update_post_meta($clone[0]->ID, 'wps_clone_status_description', $desc_meta);

      if ($status == 'in-progress') {
        return false;
      } else {
        return true;
      }

    } // if $clone

  } // update_clone_status


  static function action_started($unique_ID, $what) {
    $backup = get_posts(array('post_type' => 'wps-clones',
      'meta_key' => 'wps-unique-ID',
      'meta_value' => $unique_ID));

    $site_hash = get_post_meta($backup[0]->ID, 'wps-site-hash', true);

    if ($backup) {

      wps_saas_handshake::clone_handshake(array('site_hash' => $site_hash, 
        'unique_ID' => $unique_ID, 
        'total_actions' => 4, 
        'actions_left' => $number_of_actions,
        'action' => $what,
        'status' => '0'));

    }

  } // action_ended


  static function action_ended($unique_ID, $what) {
    $clone = get_posts(array('post_type' => 'wps-clones',
      'meta_key' => 'wps-unique-ID',
      'meta_value' => $unique_ID));
      
    $site_hash = get_post_meta($clone[0]->ID, 'wps-site-hash', true);

    if ($clone) {
      $number_of_actions = get_post_meta($clone[0]->ID, 'wps_clone_actions_left', true);
      $number_of_actions = ($number_of_actions-1);
      
      wps_saas_handshake::clone_handshake(array('site_hash' => $site_hash, 
                                                'unique_ID' => $unique_ID, 
                                                'total_actions' => 4, 
                                                'actions_left' => $number_of_actions,
                                                'action' => $what,
                                                'status' => '1'));
      
      update_post_meta($clone[0]->ID, 'wps_clone_actions_left', $number_of_actions);
    }

  } // action_ended


  static function check_status($hash = '') {
    if (empty($hash)) {
      $hash = sanitize_text_field($_POST['hash']);
      $clone = get_posts(array('post_type' => 'wps-clones',
        'meta_key' => 'wps_clone_hash',
        'meta_value' => $hash));

      if ($clone) {
        $number_of_actions = get_post_meta($clone[0]->ID, 'wps_clone_actions_left', true);

        echo $number_of_actions;
        die();
      }
    }

    echo '404';
    die();
  } // check_status


} // wps_wsw_clone_log