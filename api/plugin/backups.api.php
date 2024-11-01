<?php
/*
WP Suite - Backups API
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_api {

  static $total_written_bytes;
  static $path;
  static $file_count;
  static $file_list;
  static $zip;


  public function __construct() {
    self::$total_written_bytes = 0;
    self::$file_count = 0;
    self::$zip = new ZipArchive();
  } // __construct


  static function get_files($source, $return = false) {

    set_time_limit(60);
    $memory = ini_get('memory_limit');
    $memory = rtrim($memory, 'M');
    if ((int)$memory < 64) {
      ini_set('memory_limit','256M');
      define('WP_MEMORY_LIMIT', '256M');
    }

    $dir_iterator = new RecursiveDirectoryIterator($source);
    $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);


    if (!is_dir(WPS_WSW_FOLDER)) {
      mkdir(WPS_WSW_FOLDER);
    }

    if (!is_dir(WPS_WSW_FILES_FOLDER)) {
      mkdir(WPS_WSW_FILES_FOLDER);
    }

    if (!is_dir(WPS_WSW_DATABASE_FOLDER)) {
      mkdir(WPS_WSW_DATABASE_FOLDER);
    }

    self::$file_count = count($iterator);

    foreach ($iterator as $file) {
      if (strpos($file, 'uploads/backup-suite')) continue;
      self::$file_list[] = $file;
    } // foreach

    return self::$file_list;
  } // get_files


  static function open_zip($destination, $files = array()) {
    $rootPath = realpath($destination);

    // Initialize archive object
    $zip = self::$zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    if (empty($files)) {
    foreach (self::$file_list as $file) {
    if (!is_dir($file)) {
    self::add_file($file, $rootPath);
    }
    }
    } else {
    foreach ($files as $file_key => $file_path) {
    $fileinfo = new SplFileInfo($file_path);
    self::add_file($fileinfo, $file_key . '.sql');
    }
    }
  } // open_zip


  static function add_file($path, $new_path = '') {
    // Get real and relative path for current file
    $filePath = $path->getRealPath();

    if (empty($new_path)) {
      $relativePath = substr($filePath, strlen($_SERVER['DOCUMENT_ROOT']) + 1);

      if (strpos($filePath, 'wp-config.php')) {
        $relativePath = '_from_backup-wp-config.php';
      }
    } else {
      $relativePath = $new_path;
    }

    // Add current file to archive
    self::$zip->addFile($filePath, $relativePath);
  } // add_file


  static function close_zip() {
    // Zip archive will be created only after closing object
    self::$zip->close();
  } // close_zip


  static function delete_file($file_path) {
    if (!is_dir($file_path) && file_exists($file_path)) {
      unlink($file_path);
    }
  } // delete_file


} // wps_wsw_api