jQuery(document).ready(function($){

  $('.wps-saas-send-backup-request').on('click', function(e){

	var site_hash = $(this).data('site-hash');
	var site_url = $(this).data('site-url');

	var already_done = [];
	var total_actions = 4;
	var done_actions = 0;
	var unique_ID = '';

	$.post(ajaxurl, {action:'wps_send_backup_request', site_hash:site_hash, site_url:site_url}, 
	  function(response){

		var json = '';
		unique_ID = response.data.unique_ID;
		
		var check = setInterval(function(){

		  $.post(ajaxurl, {action:'wps_send_remote_backup_status',
			unique_ID:response.data.unique_ID,
			site_url:site_url,
			site_hash:response.data.site_hash},
			function(response){

			  json = $.parseJSON(response.data);
			  $.each(json, function(k, v) {
			  	
			  	console.log(done_actions);

				if (k == 'themes' && v == '1') {

				  if ($.inArray('themes', already_done) == -1) {
					done_actions++;
					already_done.push('themes');
				  }

				} else if (k == 'plugins' && v == '1') {

				  if ($.inArray('plugins', already_done) == -1) {
					done_actions++;
					already_done.push('plugins');
				  }

				} else if (k == 'uploads' && v == '1') {

				  if ($.inArray('uploads', already_done) == -1) {
					done_actions++;
					already_done.push('uploads');
				  }

				} else if (k == 'database' && v == '1') {

				  if ($.inArray('database', already_done) == -1) {
					done_actions++;
					already_done.push('database');
				  }

				}

			  });

		  });

		  if (total_actions == done_actions) {
			clearInterval(check);
			$.post(ajaxurl, {action:'wps_send_remote_backup_finished',
			  unique_ID:unique_ID,
			  site_url:site_url,
			  site_hash:site_hash}, function(response) {
				

			});

		  }

		  }, 4000);

	});

  });

});