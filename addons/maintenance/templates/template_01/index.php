<?php global $maintenance_options; ?>
<!DOCTYPE html>
<html>
  <head>
	<title><?php echo $maintenance_options['page-title']; ?></title>

	<!-- BootStrap -->
	<script type="text/javascript" src="<?php echo WPS_WSW_MAINTENANCE_TEMPLATES_URI; ?>maintenance.jquery.js"></script>
	<script type="text/javascript" src="<?php echo WPS_WSW_MAINTENANCE_BOOTSTRAP; ?>js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo WPS_WSW_MAINTENANCE_BOOTSTRAP; ?>css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo WPS_WSW_MAINTENANCE_TEMPLATES_URI; ?>main.css"/>
	
	<?php if (!empty($maintenance_options['font'])) { ?>
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=<?php echo $maintenance_options['font']; ?>" rel="stylesheet">
	<style type="text/css">
	h1, h2, h3, h4, h5, p, span {
	  font-family: '<?php echo str_replace('+', ' ', $maintenance_options['font']); ?>', sans-serif;
	}
	</style>
	<?php } ?>
	
	<link rel="stylesheet" type="text/css" href="<?php echo WPS_WSW_MAINTENANCE_TEMPLATES_URI; ?>template_01/style.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo WPS_WSW_MAINTENANCE_BASE; ?>assets/countdown/css/flipclock.css"/>
	
	<script type="text/javascript" src="<?php echo WPS_WSW_MAINTENANCE_BASE; ?>assets/countdown/js/flipclock.js"></script>

	<?php if (!empty($maintenance_options['until'])) { ?>
	<script type="text/javascript">
	  var clock;

	  jQuery(document).ready(function($) {
	  	
		// Grab the current date
		var currentDate = new Date();
		
		<?php
		  $date = explode('/', $maintenance_options['until']);
		?>
		
		var date_y = <?php echo $date[2]; ?>;
		var date_m = <?php echo $date[0]-1; ?>;
		var date_d = <?php echo $date[1]; ?>;
		
		// Set some date in the future. In this case, it's always Jan 1
		var futureDate  = new Date(date_y, date_m, date_d);
		
		// Calculate the difference in seconds between the future and current date
		var diff = futureDate.getTime() / 1000 - currentDate.getTime() / 1000;
		
		// Instantiate a coutdown FlipClock
		clock = $('.clock').FlipClock(diff, {
		  clockFace: 'DailyCounter',
		  countdown: true
		});

	  });
	</script>
	<?php } ?>
  </head>

  <body>

	<div class="container full-height">
	  <div class="col-lg-12 text-center in-middle">
	  
		<h1 class="page-txt-header"><?php echo $maintenance_options['header-text']; ?></h1>
		<h3 class="page-txt-subheader"><?php echo $maintenance_options['subheader-text']; ?></h3>
		
		<?php if (!empty($maintenance_options['until'])) { ?>
		<div class="text-center clock-wrapper">
		  <div class="clock" data-until="<?php echo $maintenance_options['until']; ?>"></div>
		</div>
		<?php } ?>
		
	  </div>
	</div>

  </body>

</html>