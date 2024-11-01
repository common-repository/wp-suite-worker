<div class="wps-header-bar">
  <img src="<?php echo WPS_WSW_URL . 'images/header-logo.jpg'; ?>" alt="WP Suite"/>
  <div class="wps-header-bar-right">
    <a href="#">Support <i class="dashicons dashicons-email"></i></a>
    <a href="#">Hire us <i class="dashicons dashicons-wordpress"></i></a>
  </div>
</div>


<div class="wrap">

  <?php wps_wsw_pages::notification(); ?>

  <?php wps_wsw_pages::backup_controls(); ?>
  <div class="wps-space"></div>
  <?php wps_wsw_pages::backup_table(); ?>

</div>