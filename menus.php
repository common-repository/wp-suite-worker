<?php
/*
WP Suite - Menus
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_menus {
  
  
  static function menu() {
    add_menu_page('WP Suite', 'WP Suite', 'manage_options', 'wps-backup-suite',
                  array('wps_wsw_pages', 'init_admin'), WPS_WSW_URL . 'images/small.png', 60);

    add_submenu_page('wps-backup-suite', 'WP Suite', 'WP Suite', 'manage_options',
                     'wps-backup-suite', array('wps_wsw_pages', 'init_admin'));
  } // menu
  
  
} // wps_wsw_menus