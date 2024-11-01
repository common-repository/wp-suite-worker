<?php
/*
WP Suite - Types
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/
if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_types {


  static function register() {
    add_action('add_meta_boxes', array(__CLASS__, 'clone_metabox'));
    self::register_clones_type();
    self::register_backups_type();
    self::register_remote_backups_type();
    self::register_schedules_type();
  } // register


  static function clone_metabox($post) {
    add_meta_box(
      'clone-meta-box',
      __( 'My Meta Box' ),
      array(__CLASS__, 'render_clone_metabox'),
      'wps-clones',
      'normal',
      'default');
  }


  static function render_clone_metabox() {
    global $post;
    $desc_meta = get_post_meta($post->ID, 'wps_clone_status_description', true);
    foreach ($desc_meta as $key => $data) {
      echo $key . ' - ';
      foreach ($data as $status => $d) {
        echo $status . ' - ' . date('d.m.Y H:i:s', $d['time']) .  ' - ' . $d['msg'] . "<br/>";
      }
      echo "<br/>";
    }
  }


  static function register_clones_type() {
    $labels = array(
      'name'               => __('Clones', WPS_WSW_TEXTDOMAIN),
      'singular_name'      => __('Clone', WPS_WSW_TEXTDOMAIN),
      'menu_name'          => __('Clones', WPS_WSW_TEXTDOMAIN),
      'name_admin_bar'     => __('Clone', WPS_WSW_TEXTDOMAIN),
      'add_new'            => __('Add New', WPS_WSW_TEXTDOMAIN),
      'add_new_item'       => __('Add New Clone', WPS_WSW_TEXTDOMAIN),
      'new_item'           => __('New Clone', WPS_WSW_TEXTDOMAIN),
      'edit_item'          => __('Edit Clone', WPS_WSW_TEXTDOMAIN),
      'view_item'          => __('View Clone', WPS_WSW_TEXTDOMAIN),
      'all_items'          => __('All Clone', WPS_WSW_TEXTDOMAIN),
      'search_items'       => __('Search Clone', WPS_WSW_TEXTDOMAIN),
      'parent_item_colon'  => __('Parent Clone:', WPS_WSW_TEXTDOMAIN),
      'not_found'          => __('No clones found.', WPS_WSW_TEXTDOMAIN),
      'not_found_in_trash' => __('No clones found in Trash.', WPS_WSW_TEXTDOMAIN)
   );

    $args = array(
      'labels'             => $labels,
      'description'        => __('All Clones.', WPS_WSW_TEXTDOMAIN),
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => false,
      'show_in_menu'       => false,
      'query_var'          => true,
      'rewrite'            => array('slug' => 'wps-clones'),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => null,
      'supports'           => array('title', 'editor', 'author')
   );

    register_post_type('wps-clones', $args);
  } // register_clones_type

  
  static function register_remote_backups_type() {
    $labels = array(
      'name'               => __('Remote Backups', WPS_WSW_TEXTDOMAIN),
      'singular_name'      => __('Remote Backup', WPS_WSW_TEXTDOMAIN),
      'menu_name'          => __('Remote Backups', WPS_WSW_TEXTDOMAIN),
      'name_admin_bar'     => __('Remote Backup', WPS_WSW_TEXTDOMAIN),
      'add_new'            => __('Add New', WPS_WSW_TEXTDOMAIN),
      'add_new_item'       => __('Add New Remote Backup', WPS_WSW_TEXTDOMAIN),
      'new_item'           => __('New Remote Backup', WPS_WSW_TEXTDOMAIN),
      'edit_item'          => __('Edit Remote Backup', WPS_WSW_TEXTDOMAIN),
      'view_item'          => __('View Remote Backup', WPS_WSW_TEXTDOMAIN),
      'all_items'          => __('All Remote Backup', WPS_WSW_TEXTDOMAIN),
      'search_items'       => __('Search Remote Backup', WPS_WSW_TEXTDOMAIN),
      'parent_item_colon'  => __('Parent Remote Backup:', WPS_WSW_TEXTDOMAIN),
      'not_found'          => __('No remote backups found.', WPS_WSW_TEXTDOMAIN),
      'not_found_in_trash' => __('No remote backups found in Trash.', WPS_WSW_TEXTDOMAIN)
   );

    $args = array(
      'labels'             => $labels,
      'description'        => __('All Remote Backups.', WPS_WSW_TEXTDOMAIN),
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => false,
      'show_in_menu'       => false,
      'query_var'          => true,
      'rewrite'            => array('slug' => 'wps-remote-backups'),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => null,
      'supports'           => array('title', 'editor', 'author', 'thumbnail')
   );

    register_post_type('wps-remote-backups', $args);
  } // register_remote_backups_type
  
  
  static function register_backups_type() {
    $labels = array(
      'name'               => __('Backups', WPS_WSW_TEXTDOMAIN),
      'singular_name'      => __('Backup', WPS_WSW_TEXTDOMAIN),
      'menu_name'          => __('Backups', WPS_WSW_TEXTDOMAIN),
      'name_admin_bar'     => __('Backup', WPS_WSW_TEXTDOMAIN),
      'add_new'            => __('Add New', WPS_WSW_TEXTDOMAIN),
      'add_new_item'       => __('Add New Backup', WPS_WSW_TEXTDOMAIN),
      'new_item'           => __('New Backup', WPS_WSW_TEXTDOMAIN),
      'edit_item'          => __('Edit Backup', WPS_WSW_TEXTDOMAIN),
      'view_item'          => __('View Backup', WPS_WSW_TEXTDOMAIN),
      'all_items'          => __('All Backup', WPS_WSW_TEXTDOMAIN),
      'search_items'       => __('Search Backup', WPS_WSW_TEXTDOMAIN),
      'parent_item_colon'  => __('Parent Backup:', WPS_WSW_TEXTDOMAIN),
      'not_found'          => __('No backups found.', WPS_WSW_TEXTDOMAIN),
      'not_found_in_trash' => __('No backups found in Trash.', WPS_WSW_TEXTDOMAIN)
   );

    $args = array(
      'labels'             => $labels,
      'description'        => __('All Backups.', WPS_WSW_TEXTDOMAIN),
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => false,
      'show_in_menu'       => false,
      'query_var'          => true,
      'rewrite'            => array('slug' => 'wps-backups'),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => null,
      'supports'           => array('title', 'editor', 'author', 'thumbnail')
   );

    register_post_type('wps-backups', $args);
  } // register_backups_type
  

  static function register_schedules_type() {
    $labels = array(
      'name'               => __('Schedules', WPS_WSW_TEXTDOMAIN),
      'singular_name'      => __('Schedule', WPS_WSW_TEXTDOMAIN),
      'menu_name'          => __('Schedules', WPS_WSW_TEXTDOMAIN),
      'name_admin_bar'     => __('Schedule', WPS_WSW_TEXTDOMAIN),
      'add_new'            => __('Add New', WPS_WSW_TEXTDOMAIN),
      'add_new_item'       => __('Add New Schedule', WPS_WSW_TEXTDOMAIN),
      'new_item'           => __('New Schedule', WPS_WSW_TEXTDOMAIN),
      'edit_item'          => __('Edit Schedule', WPS_WSW_TEXTDOMAIN),
      'view_item'          => __('View Schedule', WPS_WSW_TEXTDOMAIN),
      'all_items'          => __('All Schedule', WPS_WSW_TEXTDOMAIN),
      'search_items'       => __('Search Schedule', WPS_WSW_TEXTDOMAIN),
      'parent_item_colon'  => __('Parent Schedule:', WPS_WSW_TEXTDOMAIN),
      'not_found'          => __('No schedules found.', WPS_WSW_TEXTDOMAIN),
      'not_found_in_trash' => __('No schedules found in Trash.', WPS_WSW_TEXTDOMAIN)
   );

    $args = array(
      'labels'             => $labels,
      'description'        => __('All Schedules.', WPS_WSW_TEXTDOMAIN),
      'public'             => true,
      'publicly_queryable' => true,
      'show_ui'            => false,
      'show_in_menu'       => false,
      'query_var'          => true,
      'rewrite'            => array('slug' => 'wps-backup-schedules'),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => null,
      'supports'           => array('title', 'editor', 'author')
   );

    register_post_type('wps-backup-schedules', $args);
  } // register_schedules_type
  
  
  static function add_new_schedule() {
    if (!empty($_POST['wps-schedule'])) {
      $new_schedule = wp_insert_post(array('post_type' => 'wps-backup-schedules',
                                           'post_title' => sanitize_text_field($_POST['wps-schedule']['name']),
                                           'post_status' => 'publish'));
      
      if ($new_schedule) {
        update_post_meta($new_schedule, 'recurrance', sanitize_text_field($_POST['wps-schedule']['recurrance']));
        update_post_meta($new_schedule, 'type', sanitize_text_field($_POST['wps-schedule']['type']));
        update_post_meta($new_schedule, 'last-run', current_time('mysql'));
        
        wp_schedule_event(time(), sanitize_text_field($_POST['wps-schedule']['recurrance']), 'wps_backup_schedule_' . $new_schedule . '_' . sanitize_text_field($_POST['wps-schedule']['type']));
      }
      
    }
  } // add_new_schedule


} // wps_wsw_types