<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="maintenance" class="wps-form" style="display: none;">
<div class="wrap">
  <?php
  	// Save
  	if (!empty($_POST)) {
  	  
	  $maintenance = $_POST['maintenance'];
	  foreach ($maintenance as $key => $option) {
		$maintenance[$key] = sanitize_text_field($option);
	  }
	  
	  update_option('wps_maintenance_options', $maintenance);
  	}
  ?>

  <?php
    // Get Options
    $maintenance = get_option('wps_maintenance_options');
  
  	// Get State
  	$state = get_option('wps_maintenance_state');
  	
  	$checked = '';
  	if ($state == 'on') {
	  $checked = 'checked="checked"';
  	}
  	
  ?>

  <div class="box">
	<label class="bold-label">Maintenance:&nbsp;</label>
	<input type="checkbox" class="switch" <?php echo $checked; ?> name="maintenance[state]" value="1" id="maintenance-state" />
  </div>
  <br/>
  
  <form method="POST" action="admin.php?page=wps-backup-suite#maintenance">
  <div class="box">
	<label class="bold-label">Font:&nbsp;</label><br/>
	<input class="fonts" name="maintenance[font]" value="<?php echo $maintenance['font']; ?>"/>
  </div>
  
  <br/>
  <div class="box">
	<label class="bold-label">Maintenance Page Title:&nbsp;</label>
	<br/>
	<input type="text" name="maintenance[page-title]" value="<?php echo $maintenance['page-title']; ?>" class="widefat" />
  </div>
  
  <br/>
  <div class="box">
	<label class="bold-label">Maintenance Header Text:&nbsp;</label>
	<br/>
	<input type="text" name="maintenance[header-text]" value="<?php echo $maintenance['header-text']; ?>" class="widefat" />
  </div>
  
  <br/>
  <div class="box">
	<label class="bold-label">Maintenance Sub-Header Text:&nbsp;</label>
	<br/>
	<input type="text" name="maintenance[subheader-text]" value="<?php echo $maintenance['subheader-text']; ?>" class="widefat" />
  </div>

  <br/>
  <div class="box">
	<label class="bold-label">Maintenance Until:&nbsp;</label>
	<br/>
	<input type="text" name="maintenance[until]" value="<?php echo $maintenance['until']; ?>" class="maintenance_until"/>
  </div>
  
  <br/>
  <div class="box">
	<input type="submit" name="submit" class="button button-primary" value="Save" />
  </div>
  </form>

</div>
</div>