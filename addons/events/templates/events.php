<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="events" style="display: none;">
<div class="wrap">
  <a href="<?php echo admin_url('admin.php?page=wps-backup-suite&wps_action=clear_events#events'); ?>" class="wps-button wps-events-clear-all">Clear all events</a><br/><br/>
  
  <table id="wps-ss-events-table" class="wp-list-table widefat fixed striped">
    <thead>
    <tr>
      <th>Description</th>
      <th>From IP</th>
      <th>Date</th>
    </tr>
    </thead>


    <tbody>
    <?php
      global $wpdb;
      $events = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . WPS_EVENTS_SUITE_LOG . " ORDER BY date DESC");

      if ($events) {
        foreach ($events as $event) {

          $style = '';
          if ($event->event_action == 'wp_login_failed') {
            $style = 'danger';
          }

        echo '<tr class="' . $style . '">';
        echo '<td>' . $event->description . '</td>';
        echo '<td>' . $event->ip . '</td>';
        echo '<td>' . date('d.m.Y', strtotime($event->date)) . ' @ ' . date('H:i', strtotime($event->date)) . '</td>';
        echo '</tr>';

      }
    } else {
	?>
	<tr>
	  <td colspan="3" style="text-align: center;">Currently you have no events!</td>
	</tr>
	<?php
    }
    ?>
    </tbody>


    <tfoot>
    <tr>
      <th>Description</th>
      <th>From IP</th>
      <th>Date</th>
    </tr>
    </tfoot>
  </table>

</div>
</div>