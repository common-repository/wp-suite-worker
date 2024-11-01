<?php
// Total backups made
// Total File backups made
// Total Database backups made

// Total Backup Disk Usage
// Total File Backup Disk Usage
// Total Database Backup Disk Usage

// Total Backup Time proccessing
// Total File Backup Time Processing
// Total Database Backup Time Processing

global $wpdb;

// Total Number of Backups
$total_backups = get_posts(array('post_type' => 'wps-backups', 'posts_per_page' => '-1'));

// Total Number of File Backups
$total_file_backups = get_posts(array('post_type' => 'wps-backups', 'posts_per_page' => '-1', 'meta_key' => 'wps_backup_format', 'meta_value' => 'file'));

// Total Number of Database Backups
$total_database_backups = get_posts(array('post_type' => 'wps-backups', 'posts_per_page' => '-1', 'meta_key' => 'wps_backup_format', 'meta_value' => 'database'));

// Calculate total backup disk usage
$options = get_option(WPS_WSW);
?>
<h3>Overall</h3>
<strong>Total Backups:</strong> <?php echo count($total_backups); ?><br/>
<strong>Total File Backups:</strong> <?php echo count($total_file_backups); ?><br/>
<strong>Total Database Backups:</strong> <?php echo count($total_database_backups); ?><br/>
<?php
foreach ($options as $date => $data) {
  $total_backup_disk_usage = $data['backup_total_size'];
  $total_file_backup_disk_usage = $data['backup_file_size'];
  $total_database_backup_disk_usage = $data['backup_database_size'];
  ?>
  <h3><?php echo date('jS F Y', strtotime($date)); ?></h3>
  <strong>Total Backups Disk Usage:</strong> <?php echo number_format($total_backup_disk_usage / 1048576, 2); ?><br/>
  <strong>Total File Backups Disk Usage:</strong> <?php echo number_format($total_file_backup_disk_usage/ 1048576, 2); ?><br/>
  <strong>Total Database Backups Disk Usage:</strong> <?php echo number_format($total_database_backup_disk_usage/ 1048576, 2); ?><br/>
  <?php 
}