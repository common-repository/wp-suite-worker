<?php
/*
WP Suite - Ajax Account
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_ajax_account {
  
  
  static function register_calls() {
    
    // Remote register account
    add_action('wp_ajax_wps_register_account', array(__CLASS__, 'register_account'));
    add_action('wp_ajax_wps_connect_account', array(__CLASS__, 'connect_account'));
    add_action('wp_ajax_wps_connect_account', array(__CLASS__, 'disconnect_account'));
    
  } // register_calls

  
  static function register_account() {
    
    // Send request
    $request = wp_remote_post(WPS_WSW_MANAGER_URL, 
      array(
        'timeout' => 240, 
        'body' => array('action' => 'register_account',
                        'site' => site_url('/'),
                        'meta' => wps_get_site_meta(),
                        'hash' => wps_hash(),
                        'username' => sanitize_text_field($_POST['username']),
                        'email' => sanitize_email($_POST['email']),
                        'password' => sanitize_text_field($_POST['password']))
    ));
    
    if (!is_wp_error($request)) {
      $body = json_decode($request['body']);
      if ($body->success) {
      	update_option('wps-wsw-creds', array('username' => sanitize_text_field($_POST['username']), 'password' => sanitize_text_field($_POST['password'])));
        update_option('wps-wsw-connected', 'true');
        wp_send_json_success();
      } else {
		
		if ($body->data->error_code == 'no-meet') {
		  wp_send_json_error('Please re-activate your plugin!');
		} else if ($body->data->error_code == 'registered') {
		  wp_send_json_error('Username already taken!');
		} else if ($body->data->error_code == 'wrong-password') {
		  wp_send_json_error('Password missmatch!');
		} else if ($body->data->error_code == 'unknown') {
		  wp_send_json_error('Uknown error!');
		}
		
      }
    }

    wp_send_json_error();
  } // register_account
  
  
  static function connect_account() {
    
    // Send request
    $request = wp_remote_post(WPS_WSW_MANAGER_URL, 
      array(
        'timeout' => 240, 
        'body' => array('action' => 'connect_account',
                        'hash' => wps_hash(),
                        'meta' => wps_get_site_meta(),
                        'username' => sanitize_text_field($_POST['username']),
                        'password' => sanitize_text_field($_POST['password']))
    ));
    
    
    if (!is_wp_error($request)) {
      $body = json_decode($request['body']);
      if ($body->success) {
        update_option('wps-wsw-creds', array('username' => sanitize_text_field($_POST['username']), 'password' => sanitize_text_field($_POST['password'])));
        update_option('wps-wsw-connected', 'true');
        wp_send_json_success();
      } else {

		if ($body->data->error_code == 'no-meet') {
		  wp_send_json_error('Please re-activate your plugin!');
		}
      }
    }
    
    wp_send_json_error();
  } // connect_account
    
  
  static function disconnect_account() {
  	
  	$account = get_option('wps-wsw-creds');
    
    // Send request
    $request = wp_remote_post(WPS_WSW_MANAGER_URL, 
      array(
        'timeout' => 240, 
        'body' => array('action' => 'disconnect_account',
                        'hash' => wps_hash(),
                        'username' => sanitize_text_field($account['username']),
                        'password' => sanitize_text_field($account['password']))
    ));
    
    if (!is_wp_error($request)) {
      $body = json_decode($request['body']);
      if ($body->success) {
        delete_option('wps-wsw-creds');
        delete_option('wps-wsw-connected');
      }
    }
  } // connect_account
  

} // wps_ajax_account