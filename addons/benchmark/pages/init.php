<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="benchmark" class="wps-form" style="display: none;">
<div class="wrap">

<h3>WordPress Benchmark</h3>
<p>In this test you can check common WordPress issues and get optimization suggestions.</p>

<a href="#" class="button button-primary" id="start-benchmark">Start Benchmark</a>
<br/>
<br/>

<?php
 // Various checks
 $checks = get_option('wps_benchmark');
?>

  <table id="wps-benchmark-table" class="wp-list-table wps-table widefat fixed striped">
    <thead>
    <tr>
      <th>Test</th>
      <th>Description</th>
      <th>Value</th>
      <th>Opinion</th>
    </tr>
    </thead>


    <tbody>
    
      <tr <?php if ($checks['server_response_time'] > 100) { echo 'class=""'; } ?>>
        <td>Server Response Time</td>
        <td>Your server response time should always be under 100ms.</td>
        <td><?php echo $checks['server_response_time']; ?>ms</td>
        <td><?php echo wps_saas_benchmark::get_opinion('server_response_time', $checks['server_response_time']); ?></td>
      </tr>
    
      <tr <?php $opinion = '<span class="badge-green">OK</span>'; if (version_compare($checks['php_version'], '5.5.37', '<')) { echo 'class=""'; $opinion = '<span class="badge-red">Bad</span>'; } ?>>
        <td>PHP Version</td>
        <td>You should always be using latest stable PHP Version, we recommend using 5.5.37.</td>
        <td><?php echo $checks['php_version']; ?></td>
        <td><?php echo $opinion; ?></td>
      </tr>
      
      <tr <?php $opinion = '<span class="badge-green">OK</span>';  if (version_compare($checks['mysql_version'], '5.5.44-MariaDB-cll-lve', '<')
           || version_compare($checks['mysql_version'], '5.5.1', '<')) {
             echo 'class=""';
              $opinion = '<span class="badge-red">Bad</span>';
		   }
		   ?>>
        <td>MySQL Version</td>
        <td>You should always be using latest stable MySQL Version.</td>
        <td><?php echo $checks['mysql_version']; ?></td>
        <td><?php echo $opinion; ?></td>
      </tr>
      
      <tr <?php $opinion = '<span class="badge-green">OK</span>'; if ($checks['active_plugins'] > 15) { echo 'class=""'; $opinion = '<span class="badge-red">Bad</span>'; } ?>>
        <td>Installed Plugins</td>
        <td>Installing too many plugins on your WordPress Site can make your site load slower than usual, we recommend at maximum of 15 plugins active.</td>
        <td>You have <?php echo $checks['active_plugins'];?> Plugins active.</td>
        <td><?php echo $opinion; ?></td>
      </tr>
      
      <tr <?php $opinion = '<span class="badge-green">OK</span>'; if ($checks['pending_update_plugins'] > 0) { echo 'class=""'; $opinion = '<span class="badge-red">Bad</span>'; } ?>>
        <td>Pending Update Plugins</td>
        <td>It's recommended to update your plugins as usually updates contain various code improvements which affect site loading speed.</td>
        <td>You have <?php echo $checks['pending_update_plugins'];?> Plugins pending update.</td>
        <td><?php echo $opinion; ?></td>
      </tr>
      
      <tr <?php $opinion = '<span class="badge-green">OK</span>'; if (!$checks['minified']) { echo 'class=""'; $opinion = '<span class="badge-red">Bad</span>'; } ?>>
        <td>Minify HTML/CSS/JS</td>
        <td>By Minifying HTML/CSS/JS you can save bandwith and improve page speed loading.</td>
        <td><?php if ($checks['minified']) { echo 'Minified'; } else { echo 'Not Minified'; } ?></td>
        <td><?php echo $opinion; ?></td>
      </tr>
      
      <tr>
        <td>GZIP Compression</td>
        <td>By enabling GZIP Compression you can save bandwith and improve page speed loading.</td>
        <td><?php echo $checks['gzip']; ?></td>
        <td><?php echo $opinion; ?></td>
      </tr>
    </tbody>


    <tfoot>
    <tr>
      <th>Test</th>
      <th>Description</th>
      <th>Value</th>
      <th>Opinion</th>
    </tr>
    </tfoot>
  </table>

</div>
</div>