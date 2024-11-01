<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="security-checks" style="display: none;">

<a href="#" class="wps-button wps-ss-run-all-tests">Run all tests</a><br/><br/>

<table id="wps-ss-test-table" class="wp-list-table widefat striped">
  <thead>
    <tr>
      <th style="width:100px;">Priority</th>
      <th>Description</th>
      <th style="width:150px;">Result</th>
      <th style="width:150px;">Last Time Runned</th>
      <th style="width:100px;">Action</th>
    </tr>
  </thead>

  
  <tbody>
  <?php
    $tests = wps_ss_test_list::get_list();
    if (!empty($tests)) {
      
      // Get saved results
      $results = get_option(WPS_SS_SCAN_RESULTS);
      
      foreach ($tests as $key => $test) {
        
        // results
        if (empty($results[$key])) {
          $result = 'Never tested.';
        } else {
          $result = $results[$key];
        }
        
        // Priority
        $priority = '<span class="">Low/Medium/High</span>';
        switch ($test['priority']) {
          case '10':
            $priority = '<span class="wps-ss-label high">High</span>';
          break;
          case '5':
            $priority = '<span class="wps-ss-label medium">Medium</span>';
          break;
          default:
            $priority = '<span class="wps-ss-label low">Low</span>';
          break;
        }       
        
        // Result label
        switch ($result) {
          case 'OK':
            $result = '<span class="wps-ss-label success">OK</span>';
            $fix = '';
          break;
          case 'BAD':
            $result = '<span class="wps-ss-label high">BAD</span>';
            $fix = '<a href="#" class="wps-button wps-ss-fix-problem" data-test="' . $key  . '">Fix</a>';
          break;
          default:
            $result = '<span class="wps-ss-label low">Never tested</span>';
            $fix = '';
          break;
        }
        
        echo '<tr id="' . $key . '">';
        echo '<td>' . $priority . '</td>';
        echo '<td class="wps-ss-desc">' . $test['description'] . '</td>';
        echo '<td class="wps-ss-result">' . $result . '</td>';
        echo '<td class="wps-ss-result-time">' . date('d.m.Y H:i:s', time()) . '</td>';
        echo '<td style="width:150px;">
              <a href="#" class="wps-button wps-ss-run-single-test" data-test="' . $key  . '">Run</a>
              </td>';
        echo '</tr>';
      }
    }
  ?>
  </tbody>
  

  <tfoot>
    <tr>
      <th>Priority</th>
      <th>Description</th>
      <th>Result</th>
      <th>Last Time Runned</th>
      <th>Re-run</th>
    </tr>
  </tfoot>
</table>

</div>