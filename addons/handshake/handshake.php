<?php
/*
Handshake API
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_saas_handshake {
  
  
  /*
  Sends backup status handshake
  @param $params includes "site_hash", "unique_ID", "total_actions", "actions_left"
  */
  static function backup_handshake($params) {
    
    $request = wp_remote_post(WPS_WSW_MANAGER_URL, array('body' => array('site_hash' => $params['site_hash'],
                                                                 'unique_ID' => $params['unique_ID'],
                                                                 'total_actions' => $params['total_actions'],
                                                                 'actions_left' => $params['actions_left'],
                                                                 'handshake' => 'backup_handshake',
                                                                 'action' => $params['action'],
                                                                 'status' => $params['status'])));
    
  } // backup_handshake
  
  
  /*
  Sends clone status handshake
  @param $params includes "site_hash", "unique_ID", "total_actions", "actions_left"
  */
  static function clone_handshake($params) {
    
    $request = wp_remote_post(WPS_WSW_MANAGER_URL, array('body' => array('site_hash' => $params['site_hash'],
                                                                 'unique_ID' => $params['unique_ID'],
                                                                 'total_actions' => $params['total_actions'],
                                                                 'actions_left' => $params['actions_left'],
                                                                 'handshake' => 'clone_handshake',
                                                                 'action' => $params['action'],
                                                                 'status' => $params['status'])));
    
  } // clone_handshake
  
  
} // wps_saas_handshake