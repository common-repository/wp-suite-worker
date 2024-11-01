<h3>New automated schedule</h3>
<?php
    wps_wsw_types::add_new_schedule();
?>
<form method="POST" action="#automated-backups">
  <table class="form-table">
    <tr>
      <th><label>Schedules Custom name (unique):</label></th>
      <td><input type="text" name="wps-schedule[name]" value="" /></td>
    </tr>
    <tr>
      <th><label>Schedules Reccurance:</label></th>
      <td>
        <select name="wps-schedule[recurrance]">
          <?php
          $schedules = wp_get_schedules();
          foreach ($schedules as $index => $schedule) {
            echo '<option value="' . $index . '">' . $schedule['display'] . '</option>';
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <th><label>Backup Type:</label></th>
      <td>
        <select name="wps-schedule[type]">
          <option value="file">Files Only</option>
          <option value="database">Database Only</option>
          <option value="all">Files & Database</option>
        </select>
      </td>
    </tr>
  </table>
  <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Schedule"></p>
</form>

</hr>
<table class="wp-list-table widefat fixed striped pages">
  <thead>
    <tr>
      <th>Name</th>
      <th>Reccurance</th>
      <th>Type</th>
      <th>Last Run Time</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $schedules = get_posts(array('post_type' => 'wps-backup-schedules', 'posts_per_page' => '-1'));
    
    if ($schedules) {
      foreach ($schedules as $schedule) {
        
        $last_run = get_post_meta($schedule->ID, 'last-run', true);
        $last_run = date('jS M Y H:i', strtotime($last_run));
        ?>
        <tr>
          <td><?php echo $schedule->post_title; ?></td>
          <td><?php echo get_post_meta($schedule->ID, 'recurrance', true); ?></td>
          <td><?php echo get_post_meta($schedule->ID, 'type', true); ?></td>
          <td><?php echo $last_run; ?></td>
          <td>Remove</td>
        </tr>
        <?php
      }
    } else {
      ?>
      <tr>
        <td colspan="4">No results.</td>
      </tr>
      <?php
    }
    ?>
  </tbody>
</table>