<?php
/*
WP Suite - Pages
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_pages {
  
  
  static function init_admin() {
    require_once 'templates/admin/init.php';
  } // init_admin
  
  
  static function notification() {
    echo '<div class="error">
          <p>Your last <strong>automated</strong> backup failed because you have exceeded your storage limit.</p>
          </div>';
  } // notification
  
  
  static function backup_controls() {
    require_once 'templates/admin/backup-controls.php';
  } // backup_controls
  
  
  static function tabs() {
    require_once 'templates/admin/tabs.php';
  } // tabs
  
  
  static function site_clones_table() {
    if (!wps_is_connected()) {
      require_once 'templates/admin/site-clones-init.php';
    } else {
      require_once 'templates/admin/site-clones-table.php';
    }
  } // file_backup_table  
    
  
  static function file_backup_table() {
    require_once 'templates/admin/file-backup-table.php';
  } // file_backup_table  
  
    
  static function database_backup_table() {
    require_once 'templates/admin/database-backup-table.php';
  } // database_backup_table  
    
    
  static function backup_analytics() {
    require_once 'templates/admin/backup-analytics.php';
  } // backup_analytics  
  
  
  static function settings() {
    require_once 'templates/admin/settings.php';
  } // settings  
  
  
  static function automated() {
    require_once 'templates/admin/automated.php';
  } // settings
  
  
  static function remote_management() {
    require_once 'templates/admin/remote-management.php';
  } // remote_management
  
  
  static function clone_dialog() {
    echo '<div id="wps-backup-suite-clone" style="display:none;">';
    echo '<table class="form-table">';
    echo '<tbody>';
    
    echo '<tr>';
    echo '<th>Your instance name:</th>';
    echo '<td>';
    echo '<input type="text" name="instance-name" class="widefat" value="" placeholder="Name of the instance..." />';
    echo '</td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th>Clone type:</th>';
    echo '<td>';
    echo '<select name="clone-type">';
    echo '<option value="clone-theme">Clone only WP Themes (Wihtout WP Core)</option>';
    echo '<option value="clone-plugins">Clone only WP Plugins (Wihtout WP Core)</option>';
    echo '<option value="clone-theme-and-plugins">Clone WP Themes and WP Plugins (Wihtout WP Core)</option>';
    echo '<option value="clone-all">Clone Everything (100% Identical)</option>';
    echo '</select>';
    echo '</td>';
    echo '</tr>';
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
  } // clone_dialog  
  
  
  static function clone_progress_dialog() {
    echo '<div id="wps-backup-suite-clone-progress" style="display:none;">';
    echo '<h3>Clone in progress...</h3>';
    echo '<p class="warning">Please do NOT close this dialog or refresh page until we complete cloning of your site...</p>';
    echo '<p>This procces can take few minutes depending on size of your site and speed of your server...</p>';
    echo '<strong>Progress Log</strong>';
    echo '<div class="log">';
    echo '</div>';
    echo '</div>';
  } // clone_progress_dialog
  
  
  static function clone_logs() {
    if (empty($_GET['clone_ID'])) {
      wp_die('Cheating, huh?');
    }
    
    require_once 'templates/clone-logs/clone-log.php';
  } // clone_logs
  
  
} // wps_wsw_pages