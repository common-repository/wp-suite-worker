<?php
/*
WP Suite - Ajax Backup
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_ajax_backup {

  static $filename;


  static function ajax_generate_file_backup($data = '') {
    global $wpdb;

    if (empty($data)) {
      $data = $_POST;
    } 

    if (empty($data['filename'])) {
      self::$filename = date('d-m-Y_H:i:s');
    } else {
      $hash = trim($data['hash']);
      self::$filename = $data['filename'] . '-' . $hash;
    }

    self::check_folders();


    $backup_file = WPS_WSW_FILES_FOLDER . self::$filename  . '.zip';

    // Start backup
    $backup = new wps_wsw_api();
    $backup->get_files(ABSPATH);
    $backup->open_zip($backup_file);
    $backup->close_zip();

    // todo: napraviti error check (filesize check itd)
    $find_post = get_posts(array('post_type' => 'wps-backups',
                                 'meta_key' => 'wps_filename_hash',
                                 'meta_value' => $hash));

    if ($find_post) {
      clearstatcache();
      $ended = current_time('mysql');
      $filesize = filesize($backup_file);
      $entry_size = number_format($filesize / 1048576, 2);

      // Calculate backup run time
      $entry_started = get_post_meta($find_post[0]->ID, 'wps_backup_started', true);
      $entry_totaltime = strtotime($ended) - strtotime($entry_started);

      update_post_meta($find_post[0]->ID, 'wps_backup_size', $entry_size);
      update_post_meta($find_post[0]->ID, 'wps_backup_status', 'done');
      update_post_meta($find_post[0]->ID, 'wps_backup_ended', $ended);

      if (!empty($_POST['filename'])){
        // It's ajax
        wp_send_json_success(array('hash' => $hash, 'ended' => $ended, 'size' => $entry_size));
        die();
      } else {
        return true;
      }

    } else {
      if (!empty($_POST['filename'])){
        // It's ajax
        wp_send_json_error();
        die();
      } else {
        return false;

      }
    }

  } // ajax_generate_backup


  static function ajax_generate_database_backup($data = '') {
    global $wpdb;

    if (empty($data)) {
      $data = $_POST;
      foreach ($data as $key => $value) {
		$data[$key] = sanitize_text_field($value);
      }
    }

    if (empty($data['filename'])) {
      self::$filename = date('d-m-Y_H:i:s');
    } else {
      $hash = trim($data['hash']);
      self::$filename = $data['filename'] . '-' . $hash;
    }

    $backup_file = WPS_WSW_DATABASE_FOLDER . self::$filename  . '.zip';

    // Check if backup folders exists, if not create them
    self::check_folders();

    // Backup API
    $backup = new wps_wsw_api();

    // MySQL Dump
    $dump = new Mysqldump('mysql:host=localhost;dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $dump->start(WPS_WSW_DATABASE_FOLDER . 'dump-' . self::$filename . '.sql');

    // Zip the SQL
    $backup->open_zip($backup_file, array('dump-' . self::$filename => WPS_WSW_DATABASE_FOLDER . 'dump-' . self::$filename . '.sql'));
    $backup->close_zip();
    $backup->delete_file(WPS_WSW_DATABASE_FOLDER . 'dump-' . self::$filename . '.sql');

    // todo: napraviti error check (filesize check itd)
    $find_post = get_posts(array('post_type' => 'wps-backups',
                                 'meta_key' => 'wps_filename_hash',
                                 'meta_value' => $hash));

    if ($find_post) {
      clearstatcache();
      $ended = current_time('mysql');
      $filesize = filesize($backup_file);
      $entry_size = number_format($filesize / 1048576, 2);

      // Calculate backup run time
      $entry_started = get_post_meta($find_post[0]->ID, 'wps_backup_started', true);
      $entry_totaltime = strtotime($ended) - strtotime($entry_started);

      update_post_meta($find_post[0]->ID, 'wps_backup_size', $entry_size);
      update_post_meta($find_post[0]->ID, 'wps_backup_status', 'done');
      update_post_meta($find_post[0]->ID, 'wps_backup_ended', $ended);

      if (!empty($_POST['filename'])){
        // It's ajax
        wp_send_json_success(array('hash' => $hash, 'ended' => $ended, 'size' => $entry_size));
        die();
      } else {
        return true;
      }
    } else {

      if (!empty($_POST['filename'])){
        // It's ajax
        wp_send_json_error();
        die();
      } else {
        return false;
      }

    }

  } // ajax_generate_database_backup


  // Ajax with Response for Table
  static function ajax_generate_file_table_row() {
    global $wpdb;

    self::$filename = date('d-m-Y_H:i:s');

    // Save backup
    $backup = wp_insert_post(array('post_type' => 'wps-backups', 'post_title' => self::$filename, 'post_status' => 'publish'));

    // Add analytics entry
    $wpdb->insert(WPS_ANALYTICS_TABLE, array('date' => current_time('mysql'),
                                             'backup_ID' => $backup,
                                             'entry_type' => 'file',
                                             'entry_started' => current_time('mysql'),
                                             'entry_ended' => '0',
                                             'entry_size' => '0'));

    $output = array();

    if ($backup) {
      $backup_hash = substr(md5(self::$filename), 2, 8);

      // Meta
      update_post_meta($backup, 'wps_filename', self::$filename);
      update_post_meta($backup, 'wps_filename_hash', $backup_hash);
      update_post_meta($backup, 'wps_backup_status', 'in-progress');
      update_post_meta($backup, 'wps_backup_size', '0');
      update_post_meta($backup, 'wps_backup_started', current_time('mysql'));
      update_post_meta($backup, 'wps_backup_ended', '0');
      update_post_meta($backup, 'wps_backup_type', 'manual');
      update_post_meta($backup, 'wps_backup_format', 'file');

      // HTML
      $output['html'] = '<tr id="wps-backup-' . $backup_hash . '">
      <td>#' . $backup_hash . '</td>
      <td>' . self::$filename . '</td>
      <td><span class="wps-backup-status in-progress">In progress...</span></td>
      <td id="wps-filesize-' . $backup_hash . '">0MB</td>
      <td>' . date('H:i d/m/Y', strtotime(current_time('mysql'))) . '</td>
      <td id="wps-ended-' . $backup_hash . '">Unkown</td>
      <td>Manual</td>
      <td><a href="#">Restore</a> | <a href="#">Delete</a></td>
      </tr>';

      // Filename
      $output['filename'] = self::$filename;
      $output['hash'] = $backup_hash;
      wp_send_json_success(array($output));

    } else {
      wp_send_json_error();
    }

    die();
  } // ajax_generate_table_row


  // Ajax with Response for Table
  static function ajax_generate_database_table_row() {
    self::$filename = date('d-m-Y_H:i:s');

    // Save backup
    $backup = wp_insert_post(array('post_type' => 'wps-backups',
                                   'post_title' => self::$filename,
                                   'post_status' => 'publish'));

    $output = array();

    if ($backup) {
      $backup_hash = substr(md5(self::$filename), 2, 8);

      // Meta
      update_post_meta($backup, 'wps_filename', self::$filename);
      update_post_meta($backup, 'wps_filename_hash', $backup_hash);
      update_post_meta($backup, 'wps_backup_status', 'in-progress');
      update_post_meta($backup, 'wps_backup_size', '0');
      update_post_meta($backup, 'wps_backup_started', current_time('mysql'));
      update_post_meta($backup, 'wps_backup_ended', '0');
      update_post_meta($backup, 'wps_backup_type', 'manual');
      update_post_meta($backup, 'wps_backup_format', 'database');

      // HTML
      $output['html'] = '<tr id="wps-backup-' . $backup_hash . '">
      <td>#' . $backup_hash . '</td>
      <td>' . self::$filename . '</td>
      <td><span class="wps-backup-status in-progress">In progress...</span></td>
      <td id="wps-filesize-' . $backup_hash . '">0MB</td>
      <td>' . date('H:i d/m/Y', strtotime(current_time('mysql'))) . '</td>
      <td id="wps-ended-' . $backup_hash . '">Unkown</td>
      <td>Manual</td>
      <td><a href="#">Restore</a> | <a href="#">Delete</a></td>
      </tr>';

      // Filename
      $output['filename'] = self::$filename;
      $output['hash'] = $backup_hash;
      wp_send_json_success(array($output));

    } else {
      wp_send_json_error();
    }

    die();
  } // wps_generate_database_table_row


  static function check_folders() {
    // Create Backup Dirs
    if (!is_dir(WPS_WSW_FOLDER)) {
      mkdir(WPS_WSW_FOLDER);
    }
    
    if (!is_dir(WPS_WSW_CLONE_FILES_FOLDER)) {
      mkdir(WPS_WSW_CLONE_FILES_FOLDER);
    }    

    if (!is_dir(WPS_WSW_FILES_FOLDER)) {
      mkdir(WPS_WSW_FILES_FOLDER);
    }

    if (!is_dir(WPS_WSW_DATABASE_FOLDER)) {
      mkdir(WPS_WSW_DATABASE_FOLDER);
    }
  } // check_folders


} // wps_wsw_ajax