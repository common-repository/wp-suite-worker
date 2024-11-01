<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
  $site_hash = wps_hash();
  $site_url = site_url('/');
?>

<div id="backups" style="display: none;">

  <a href="#" class="wps-button wps-saas-send-backup-request" data-site-hash="<?php echo $site_hash; ?>" data-site-url="<?php echo $site_url; ?>">Run Backup</a><br/><br/>

  <table class="wp-list-table widefat striped">
	<thead>
	  <tr>
		<th style="width:200px;">Created</th>
		<th style="">Files</th>
	  </tr>
	</thead>


	<tbody>
	  <?php
	  $posts = get_posts(array('post_type' => 'wps-backups'));
	  if ($posts) {
		foreach ($posts as $post) {
		  $unique_ID = get_post_meta($post->ID, 'wps-unique-ID', true);
		  
		  $files = get_post_meta($post->ID, 'wps-backup-files', true);

		  if (!$files) {
			$output = wps_saas_backup::get_files($unique_ID, $post->ID);
		  } else {
		  	$output = '';
			foreach ($files as $key => $file) {
			  $output .= '<a href="' . $file . '" class="wps-link">' . ucfirst($key) . '</a> | ';
			}
			$output = rtrim($output, ' | ');
		  }
		  
		  
	  ?>
	  
	    <tr>
	      <td><?php echo date('d.m.Y', strtotime($post->post_date)) . ' @ ' . date('H:i', strtotime($post->post_date)); ?></td>
	      <td><?php echo $output; ?></td>
	    </tr>
	  
	  <?php
		}
	  }
	  ?>
	</tbody>


	<tfoot>
	  <tr>
		<th style="width:200px;">Created</th>
		<th style="">Files</th>
	  </tr>
	</tfoot>
  </table>

</div>