<?php
/*
Events Suite
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define('WPS_EVENTS_SUITE_SLUG', 'wps-events-suite');
define('WPS_EVENTS_SUITE_LOG', 'wps_events_log');

require_once 'actions.php';
require_once 'log.php';
require_once 'api.php';

class wps_events_suite {
  
  
  static function init() {

    if (!empty($_POST['site_hash']) && !empty($_POST['wps_events'])) {
      $verify = wps_es_api::verify(sanitize_text_field($_POST['site_hash']));
      if ($verify) {
        wps_es_api::fetch_events();
      }
    }

    // Hooks for WP Suite tabs
    add_action('wps_addon_tabs', array(__CLASS__, 'add_tabs'));
    add_action('wps_addon_tab_content', array(__CLASS__, 'add_tab_content'));

    // Hook on all actions
    add_action('all', array(__CLASS__, 'watch_actions'), 9, 10);
    
    if (is_admin()) {
      if (!empty($_GET['wps_action'])) {
        if ($_GET['wps_action'] == 'clear_events') {
          global $wpdb;
          $events = $wpdb->get_results("DELETE FROM " . $wpdb->prefix . WPS_EVENTS_SUITE_LOG . "");
        }
      }
    }
    
  } // init


  static function add_tabs() {
    echo '<li><a href="#events">Events Logger</a></li>';
  } // add_tabs


  static function add_tab_content() {
    require_once 'templates/events.php';
  } // add_tab_content


  /**
   *
   */
  static function watch_actions() {

    $user_actions = array('user_register',
                          'wp_login_failed',
                          'profile_update',
                          'password_reset',
                          'retrieve_password',
                          'set_logged_in_cookie',
                          'clear_auth_cookie',
                          'delete_user',
                          'deleted_user',
                          'set_user_role',
                          'login_enqueue_scripts');
                   
    $file_editor_actions = array('wp_redirect');
                        
    $media_actions = array('add_attachment',
                           'edit_attachment',
                           'delete_attachment',
                           'wp_save_image_editor_file');

    $installer_actions = array('upgrader_process_complete',
                               'activate_plugin',
                               'deactivate_plugin',
                               'switch_theme',
                               '_core_updated_successfully');
                       
    $comments_actions = array('comment_flood_trigger',
                              'wp_insert_comment',
                              'edit_comment',
                              'delete_comment',
                              'trash_comment',
                              'untrash_comment',
                              'spam_comment',
                              'unspam_comment',
                              'transition_comment_status',
                              'comment_duplicate_trigger');
                      
    $settings_actions = array('whitelist_options',
                              'update_site_option',
                              'update_option_permalink_structure',
                              'update_option_category_base',
                              'update_option_tag_base');

    $posts_actions = array('transition_post_status',
                           'deleted_post');

    // Get args
    $args = func_get_args();
    
    // What is happening?
    if (in_array(current_action(), $user_actions)) {
      
      // User actions
      wps_es_actions::user_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $file_editor_actions)) {
      
      // File Editor Actions
      wps_es_actions::file_editor_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $media_actions)) {
      
      // Media Actions
      wps_es_actions::media_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $installer_actions)) {
      
      // Installer Actions
      wps_es_actions::installer_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $comments_actions)) {
      
      // Comments Actions
      wps_es_actions::comments_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $settings_actions)) {
      
      // Settings Actions
      wps_es_actions::settings_actions(current_action(), $args);
      
    } elseif (in_array(current_action(), $posts_actions)) {

      // Posts Actions
      wps_es_actions::posts_actions(current_action(), $args);
    }
    
  } // watch_actions


  static function install() {
    global $wpdb;

    $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . WPS_EVENTS_SUITE_LOG . " (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
	`user_ID` int(11) DEFAULT NULL,
	`event_type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
	`event_action` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
	`event_params` text COLLATE utf8_unicode_ci NOT NULL,
	`description` text COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  } // install
  
  
} // wps_events_suite

add_action('init', array('wps_events_suite', 'init'));