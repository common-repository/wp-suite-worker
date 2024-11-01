<div class="wps-header-bar">
  <img src="<?php echo WPS_WSW_URL . 'images/header-logo.jpg'; ?>" alt="WP Suite"/>
  <div class="wps-header-bar-right">
    <a href="#">Support <i class="dashicons dashicons-email"></i></a>
    <a href="#">Hire us <i class="dashicons dashicons-wordpress"></i></a>
  </div>
</div>


<div class="wrap">

  <?php wps_wsw_pages::backup_controls(); ?>
  <div class="wps-space"></div>

  <table class="wp-list-table widefat fixed striped">
    <thead>
      <tr>
        <th>Action</th>
        <th>Started</th>
        <th>Ended</th>
        <th>Execution time</th>
        <th>Total size</th>
      </tr>
    </thead>
    <?php
    $hash = sanitize_text_field($_GET['clone_ID']);
    $clone = get_posts(array('post_type' => 'wps-clones', 'meta_key' => 'wps_clone_hash', 'meta_value' => $hash));
    $desc = get_post_meta($clone[0]->ID, 'wps_clone_status_description', true);
    $clone_name = get_post_meta($clone[0]->ID, 'wps_clone_name', true);

    foreach ($desc as $what => $data) {

      $started = $desc[$what]['started']['time'];
      $ended = $desc[$what]['ended']['time'];
      $msg_started = $desc[$what]['started']['msg'];
      $msg_ended = $desc[$what]['ended']['msg'];
      ?>

      <tbody>
        <tr>
          <td>
          <?php 
            switch ($what) {
              case 'cpanel':
                echo 'cPanel Account Creation';
              break;
              case 'database':
                echo 'Database Backup';
              break;
              case 'uploads':
                echo 'Uploads Backup';
              break;
              case 'themes':
                echo 'Theme Backup';
              break;
              case 'plugins':
                echo 'Plugins Backup';
              break;
            }
          ?>
          </td>
          <td>
            <?php 
            if (!empty($desc[$what]['started']['msg'])) {
              echo 'Yes';
            } else {
              echo 'No';
            }
            ?>
          </td>
          <td>
            <?php 
            if (!empty($desc[$what]['ended']['msg'])) {
              echo 'Yes';
            } else {
              echo 'No';
            }
            ?>
          </td>
          <td>
            <?php echo round($ended-$started, 2); ?> seconds
          </td>
          <td>
            <?php
            if ($what != 'cpanel') {
              if (file_exists(WPS_WSW_CLONE_FILES_FOLDER . $clone_name  . '-' . $what . '.zip')) {
                $size = filesize(WPS_WSW_CLONE_FILES_FOLDER . $clone_name  . '-' . $what . '.zip');
                $size = $size/1024;
                $size = number_format($size, 2);
                echo $size . ' KB';
              } else {
                echo 'File missing!';
              }
            } else {
              echo '/';
            }
            ?>
          </td>
        </tr>


        <?php
      }
      ?>
    </tbody>

  </table>

</div>