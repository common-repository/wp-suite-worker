<?php
/*
WP Suite - Common Functions
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_backup_log {
  
  
  static function action_started($unique_ID, $what) {
    $backup = get_posts(array('post_type' => 'wps-remote-backups',
      'meta_key' => 'wps-unique-ID',
      'meta_value' => $unique_ID));
      
    $site_hash = get_post_meta($backup[0]->ID, 'wps-site-hash', true);

    if ($backup) {

      wps_saas_handshake::backup_handshake(array('site_hash' => $site_hash, 
                                                 'unique_ID' => $unique_ID, 
                                                 'total_actions' => 4, 
                                                 'actions_left' => $number_of_actions,
                                                 'action' => $what,
                                                 'status' => '0'));

    }

  } // action_ended


  static function action_ended($unique_ID, $what) {
    $backup = get_posts(array('post_type' => 'wps-remote-backups',
      'meta_key' => 'wps-unique-ID',
      'meta_value' => $unique_ID));
      
    $site_hash = get_post_meta($backup[0]->ID, 'wps-site-hash', true);
    

    if ($backup) {
      $number_of_actions = get_post_meta($backup[0]->ID, 'wps-backup-actions-left', true);
      $number_of_actions = ($number_of_actions-1);
      
      wps_saas_handshake::backup_handshake(array('site_hash' => $site_hash, 
                                                 'unique_ID' => $unique_ID, 
                                                 'total_actions' => 4, 
                                                 'actions_left' => $number_of_actions,
                                                 'action' => $what,
                                                 'status' => '1'));
      
      wps_management_suite::write_log('id ' . $backup[0]->ID . ' ' . $number_of_actions . ' action ended', 'debug');
      update_post_meta($backup[0]->ID, 'wps-backup-actions-left', $number_of_actions);
    }

  } // action_ended


  static function check_status($unique_ID) {
    wp_reset_postdata();
    wp_reset_query();
    $backup = get_posts(array('post_type' => 'wps-remote-backups',
      'meta_key' => 'wps-unique-ID',
      'meta_value' => $unique_ID));


    wps_management_suite::write_log('id ' . $backup[0]->ID, 'debug');

    if ($backup) {
      $noa_end = 0;
      $noa_end = get_post_meta($backup[0]->ID, 'wps-backup-actions-left', true);

      wps_management_suite::write_log('actions left ' . $noa_end . ' end', 'debug');
      if ($noa_end == 0) {
        return true;
      } else {
        return false;
      }
    }
  } // check_status


} // wps_wsw_backup_log