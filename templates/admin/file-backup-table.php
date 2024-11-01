<table id="wps-file-backup-table" class="wp-list-table widefat fixed striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Backup Name</th>
      <th>Backup Status</th>
      <th>Total Size</th>
      <th>Time Started</th>
      <th>Time Ended</th>
      <th>Type</th>
      <th>Actions</th>
    </tr>
  </thead>

  <tbody>
    <?php
    $backups = get_posts(array('post_type' => 'wps-backups', 'posts_per_page' => '-1', 'meta_key' => 'wps_backup_format', 'meta_value' => 'file'));
    if ($backups) {
      foreach ($backups as $backup) {
        $backups_hash = get_post_meta($backup->ID, 'wps_filename_hash', true);
        $backups_filename = get_post_meta($backup->ID, 'wps_filename', true);
        $backups_status = get_post_meta($backup->ID, 'wps_backup_status', true);
        $backups_size = get_post_meta($backup->ID, 'wps_backup_size', true);
        $backups_started = get_post_meta($backup->ID, 'wps_backup_started', true);
        $backups_ended = get_post_meta($backup->ID, 'wps_backup_ended', true);
        $backups_type = get_post_meta($backup->ID, 'wps_backup_type', true);
        ?>
        <tr>
          <td>#<?php echo strtoupper($backups_hash); ?></td>
          <td><?php echo $backups_filename; ?></td>
          <td><?php 
            if ($backups_status == 'in-progress') {
          ?> 
          <span class="wps-backup-status in-progress">In progress...</span>
          <?php
            } else {
          ?>
          <span class="wps-backup-status done">Complete</span>
          <?php
            }
          ?></td>
          <td><?php echo $backups_size; ?> MB</td>
          <td><?php echo $backups_started; ?></td>
          <td><?php echo $backups_ended; ?></td>
          <td><?php echo $backups_type; ?></td>
          <td><a href="#">Restore</a> | <a href="#">Delete</a></td>
        </tr>
        <?php
      }
    } else {
      ?>
      <tr id="no-results">
        <td colspan="8" style="text-align: center;">
          <strong>No backups created yet.</strong><br/><br/>
          <a href="#" class="button button-primary btn-green wps-generate-file-backup">Create first backup</a>
        </td>
      </tr>
      <?php
    }
    ?>
  </tbody>

  <tfoot>
    <tr>
      <th>ID</th>
      <th>Backup Name</th>
      <th>Backup Status</th>
      <th>Total Size</th>
      <th>Time Started</th>
      <th>Time Ended</th>
      <th>Type</th>
      <th>Actions</th>
    </tr>
  </tfoot>
</table>