<?php
/*
WP Suite - Ajax Clone
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_clone {

  static $clone_name;


  static function ajax_clone_init($data = '') {
    global $wpdb;

    if (empty($data)) {
      $data = $_POST;
    } 

    if (empty($data['name'])) {
      self::$clone_name = date('d-m-Y_H:i:s');
    } else {
      $hash = substr(md5(date('d-m-Y_H:i:s')), 2, 8);

      self::$clone_name = $data['name'] . '-' . $hash;
    }

    // Save clone
    $clone = wp_insert_post(array('post_type' => 'wps-clones', 'post_title' => self::$clone_name, 'post_status' => 'publish'));

    if ($clone) {

      update_post_meta($clone, 'wps_clone_name', self::$clone_name);
      update_post_meta($clone, 'wps_clone_hash', $hash);
      update_post_meta($clone, 'wps_clone_status', 'in-progress');
      update_post_meta($clone, 'wps_clone_started', current_time('mysql'));
      update_post_meta($clone, 'wps_clone_ended', '0');

      // HTML
      $output['html'] = '<tr id="wps-clone-' . $hash . '">
      <td>#' . $hash . '</td>
      <td>' . self::$clone_name . '</td>
      <td><span class="wps-backup-status in-progress">In progress...</span></td>
      <td>' . date('H:i d/m/Y', strtotime(current_time('mysql'))) . '</td>
      <td><a href="#">Restore</a> | <a href="#">Delete</a></td>
      </tr>';

      // Filename
      $output['clone_name'] = self::$clone_name;
      $output['hash'] = $hash;
      wp_send_json_success(array($output));

    } else {
      wp_send_json_error();
    }
  } // ajax_clone_init


  static function pack_themes($backup_file) {
    // Zip the files
    $backup = new wps_wsw_api();
    $backup->get_files(WP_CONTENT_DIR . '/themes');
    $backup->open_zip($backup_file);
    $backup->close_zip();
  } // pack_themes


  static function pack_plugins($backup_file) {
    // Zip the files
    $backup = new wps_wsw_api();
    $backup->get_files(WP_CONTENT_DIR . '/plugins');
    $backup->open_zip($backup_file);
    $backup->close_zip();
  } // pack_plugins  


  static function pack_uploads($backup_file) {
    // Zip the files
    $backup = new wps_wsw_api();
    $backup->get_files(WP_CONTENT_DIR . '/uploads');
    $backup->open_zip($backup_file);
    $backup->close_zip();
  } // pack_uploads  


  static function pack_database($backup_file) {
    // Backup API
    $backup = new wps_wsw_api();

    // MySQL Dump
    $dump = new Mysqldump('mysql:host=localhost;dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $dump->start(WPS_WSW_DATABASE_FOLDER . 'dump-' . self::$clone_name . '.sql');

    // Zip the SQL
    $backup->open_zip($backup_file, array('dump-' . self::$clone_name => WPS_WSW_DATABASE_FOLDER . 'dump-' . self::$clone_name . '.sql'));
    $backup->close_zip();
    $backup->delete_file(WPS_WSW_DATABASE_FOLDER . 'dump-' . self::$clone_name . '.sql');
  } // pack_plugins


  static function ajax_clone_site($data = '') {
    global $wpdb;

    if (empty($data)) {
      $data = $_POST;
      foreach ($data as $key => $value) {
		$data[$key] = sanitize_text_field($value);
      }
    } 

    // Set clone name
    if (empty($data['name'])) {
      self::$clone_name = date('d-m-Y_H:i:s');
    } else {
      $hash = trim($data['hash']);
      self::$clone_name = $data['name'] . '-' . $hash;
    }

    // Verify all backup folders exist
    wps_wsw_ajax::check_folders();

    // Name of backup file
    self::pack_themes(WPS_WSW_CLONE_FILES_FOLDER . self::$clone_name  . '-themes.zip');
    self::pack_plugins(WPS_WSW_CLONE_FILES_FOLDER . self::$clone_name  . '-plugins.zip');
    self::pack_database(WPS_WSW_CLONE_FILES_FOLDER . self::$clone_name  . '-database.zip');
    self::pack_uploads(WPS_WSW_CLONE_FILES_FOLDER . self::$clone_name  . '-uploads.zip');

    // todo: napraviti error check (filesize check itd)
    $find_post = get_posts(array('post_type' => 'wps-clones', 'meta_key' => 'wps_clone_hash', 'meta_value' => $hash));

    if ($find_post) {
      clearstatcache();

      update_post_meta($find_post[0]->ID, 'wps_clone_status', 'in-progress');

      if (!empty($_POST['name'])){
         
        $upload_dir = wp_upload_dir();
        // It's ajax
        wp_send_json_success(
          array(
            'hash' => $hash, 
            'upload_dir' => $upload_dir['basedir'],
            'themes_zip' => $upload_dir['baseurl'] . '/backup-suite/clones/' . self::$clone_name  . '-themes.zip', 
            'plugins_zip' => $upload_dir['baseurl'] . '/backup-suite/clones/' . self::$clone_name  . '-plugins.zip', 
            'database_zip' => $upload_dir['baseurl'] . '/backup-suite/clones/' . self::$clone_name  . '-database.zip')
        );
        
        die();
      } else {
        return true;
      }

    } else {
      if (!empty($_POST['name'])){
        // It's ajax
        wp_send_json_error();
        die();
      } else {
        return false;

      }
    }

  } // ajax_clone_site
  
  
  
  static function manager_send_clone($params = array()) {
    
    /*
    params
    - clone_hash
    - site_hash
    - clone_progress
    */
    
    $site_meta = wps_get_site_meta();
    
    $request = wp_remote_post(WPS_WSW_MANAGER_URL, array('body' => 
                 array('action' => 'clone_fetch',
                       'clone_hash' => $params['clone_hash'],
                       'site_hash' => $params['site_hash'],
                       'instance_hash' => $params['instance_hash'],
                       'clone_progress' => $params['clone_progress'],
                       'clone_meta' => $site_meta)
               ));
    
  } // manager_send_clone


} // wps_wsw_clone_ajax