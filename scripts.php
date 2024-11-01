<?php
/*
WP Suite - Scripts
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wps_wsw_scripts {


  static function frontend() {
	if (wps_saas_maintenance::maintenance_enabled()) {
	  // Countdown
	  wp_enqueue_script(WPS_WSW . '-frontend-js', plugins_url('js/maintenance.frontend.js', __FILE__), array('jquery'), wps_wsw::$version, true);
	  wp_enqueue_script(WPS_WSW . '-countdown-js', plugins_url('assets/countdown/js/faces/counter.js', __FILE__), array('jquery'), wps_wsw::$version, true);

	  wp_enqueue_style(WPS_WSW . '-countdown-css', plugins_url('assets/countdown/css/flipclock.css', __FILE__), '', wps_wsw::$version);
	}
  } // frontend


  static function admin() {

	if (is_admin()) {
	  $screen = get_current_screen();

	  if ($screen->base == 'toplevel_page_wps-backup-suite') {
		wp_dequeue_style('datepicker');
		wp_dequeue_style('wp-jquery-datepicker-css');
		wp_dequeue_style('jquery-datepicker');
		wp_dequeue_style('jquery-ui-datepicker');
	  

	  // jQuery Switch
	  wp_enqueue_script(WPS_WSW . '-switch-js', plugins_url('assets/switch/js/bootstrap-switch.js', __FILE__), array('jquery'), wps_wsw::$version, true);

	  wp_enqueue_style(WPS_WSW . '-switch-css', plugins_url('assets/switch/css/bootstrap3/bootstrap-switch.css', __FILE__), '', wps_wsw::$version);

	  // Datepicker
	  wp_enqueue_script('jquery-ui-datepicker');

	  // jQuery Font Selector
	  wp_enqueue_style(WPS_WSW . '-fontselector-css', plugins_url('assets/fontselect/fontselect.css', __FILE__), '', wps_wsw::$version);
	  wp_enqueue_script(WPS_WSW . '-fontselector-js', plugins_url('assets/fontselect/jquery.fontselect.min.js', __FILE__), array('jquery'), wps_wsw::$version, true);

	  // 
	  wp_enqueue_style(WPS_WSW . '-css', plugins_url('css/admin.css', __FILE__), '', wps_wsw::$version);

	  wp_enqueue_script(WPS_WSW . '-manager-connect-js', plugins_url('js/manager-connect.js', __FILE__), array('jquery'), wps_wsw::$version, true);
	  wp_enqueue_script(WPS_WSW . '-js', plugins_url('js/admin.js', __FILE__), array('jquery'), wps_wsw::$version, true);

	  // Google Fonts
	  wp_enqueue_style(WPS_WSW_SLUG . '-google-fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:400,300,400italic,300italic,600,600italic,700,700italic,800,800italic', array(), wps_wsw::$version);

	  wp_enqueue_style(WPS_WSW_SLUG . '-google-fonts-montserat', 'https://fonts.googleapis.com/css?family=Montserrat:400,700', array(), wps_wsw::$version);

	  // Font Icons
	  wp_enqueue_style(WPS_WSW_SLUG . '-fontawesome', plugins_url('css/fonts/css/font-awesome.min.css', __FILE__), array(), wps_wsw::$version);

	  // Scripts
	  wp_enqueue_script('jquery');
	  wp_enqueue_script('jquery-ui-core');
	  wp_enqueue_script('jquery-ui-tabs');
	  wp_enqueue_script('jquery-ui-dialog');

	  // Styles
	  wp_enqueue_style('jquery-ui');
	  wp_enqueue_style('jquery-ui-core');
	  wp_enqueue_style('jquery-ui-tabs');
	  wp_enqueue_style('wp-jquery-ui-dialog');
}
	}

  } // admin


  static function paths() {
	echo '<script type="text/javascript">';
	echo 'var wpb_wsw_url = "' . WPS_WSW_URL . '";';
	echo '</script>';
  } // paths


} // wps_wsw_scripts