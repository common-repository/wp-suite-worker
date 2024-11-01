/*
WP Suite - Admin Javascript
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

var debug = true;
var now, time, total;
function debug_log(msg) {
  if (debug) {
    time = new Date().getTime();
    total += ((time-now)/1000);
    console.log(msg + ' process time: ' + ((time-now)/1000) + ' seconds');
  }
}


function clone_progress(msg) {
  time = new Date().getTime();
  total += ((time-now)/1000);
  
  var log = jQuery('.log','#wps-backup-suite-clone-progress');
  
  jQuery(log).append(msg + ' process time: ' + ((time-now)/1000) + ' seconds' + '<br/>');
}


function clone_data(msg) {
  var log = jQuery('.log','#wps-backup-suite-clone-progress');
  jQuery(log).append(msg + '<br/>');
}


jQuery(document).ready(function($){
  
  // Datepicker
  $(".maintenance_until").datepicker();
  
  // Font Selector
  $('input.fonts').fontselect();
  
  // Switch
  $(".switch").bootstrapSwitch();
  $(".switch").on('switchChange.bootstrapSwitch', function(event, state) { 
  	if (state == true) {
	  // Turn On
	  $.post(ajaxurl, {action: 'wps_maintenance_state', state:'on'}, function(response){
		$('#state-status').html('Maintenance mode is currently enabled.');
	  });
  	} else {
	  // Turn Off
	  $.post(ajaxurl, {action: 'wps_maintenance_state', state:'off'}, function(response){
		$('#state-status').html('Maintenance mode is currently disabled.');
	  });
  	}
  });

  // Tabs
  if ($('.wps-tabs').length) {
    $(".wps-tabs").tabs();
    $( ".wps-tabs" ).on( "tabsactivate", function( event, ui ) {
      window.location.hash = $('a', ui.newTab).attr('href');
    });
    
  }

  // Clone Site Button
  $('.wps-clone-site').on('click', function(e){
    e.preventDefault();

    $("#wps-backup-suite-clone").dialog({
      'dialogClass'   : 'wp-dialog',
      'title'         : 'Site Clone - Setup',
      'width'         : '800',
      'modal'         : true,
      'autoOpen'      : false,
      'closeOnEscape' : true,
      'buttons'       : [
        {
          text: 'Clone',
          "class": 'button button-primary',
          click: function() {

            var hash = '';
            var name = $('input[name="instance-name"]', '#wps-backup-suite-clone').val();
            var clone_type = $('select[name="clone-type"]', '#wps-backup-suite-clone').val();
            
            // Account info
            var account_hash = "";
            var account_password = "";
            
            now = new Date().getTime();
            time = 0;

            // Clone init - first generates table row and inserts pending row to mysql
            $.post(ajaxurl, {action:'wps_setup_clone', name:name, clone_type:clone_type}, function(response){
              if (response.success) {
                
               

              }
            });

            $(this).dialog('close');
          }
        },
        {
          text: 'Close',
          click: function() {
            if (confirm('Are you sure you wish to cancel?')) {
              $(this).dialog('close');
            }
          }
        }
      ]
    }).dialog('open');

  });


  // Generate File Backup
  $('.wps-generate-file-backup').on('click', function(e){
    e.preventDefault();

    $('.wps-tabs ul li a[href="#file-backups"]').trigger('click');
    $('#wps-file-backup-table tbody #no-results').hide();

    $.post(ajaxurl, {action:'wps_generate_file_table_row'}, function(response){
      if (response.success) {

        $('#wps-file-backup-table tbody').prepend(response.data[0].html);

        $.post(ajaxurl, {action:'wps_generate_file_backup', filename:response.data[0].filename, hash:response.data[0].hash}, function(response){
          console.log('response');
        }).complete(function(response){
          console.log('complete');
        }).done(function(response){
          console.log('done');
        }).success(function(response){
          if (response.success) {
            var progress = $('#wps-file-backup-table tbody tr#wps-backup-' + response.data.hash).find('.wps-backup-status.in-progress');
            var progress_parent = $(progress).parent();

            $(progress).remove();
            $(progress_parent).append('<span class="wps-backup-status done">Complete</span>');
            $('#wps-file-backup-table tbody td#wps-filesize-' + response.data.hash).html(response.data.size + 'MB');
            $('#wps-file-backup-table tbody td#wps-ended-' + response.data.hash).html(response.data.ended);
          }
        });

      } else {
        alert('Undocumented error occured, please refresh the page and try again.');
      }

    });


  });


  // Generate Database Backup
  $('.wps-generate-database-backup').on('click', function(e){
    e.preventDefault();

    $('.wps-tabs ul li a[href="#database-backups"]').trigger('click');
    $('#wps-database-backup-table tbody #no-results').hide();

    $.post(ajaxurl, {action:'wps_generate_database_table_row'}, function(response){
      if (response.success) {

        $('#wps-database-backup-table tbody').prepend(response.data[0].html);

        $.post(ajaxurl, {action:'wps_generate_database_backup', filename:response.data[0].filename, hash:response.data[0].hash}, function(response){
          console.log('response');
        }).complete(function(response){
          console.log('complete');
        }).done(function(response){
          console.log('done');
        }).success(function(response){
          if (response.success) {
            var progress = $('#wps-database-backup-table tbody tr#wps-backup-' + response.data.hash).find('.wps-backup-status.in-progress');
            var progress_parent = $(progress).parent();

            $(progress).remove();
            $(progress_parent).append('<span class="wps-backup-status done">Complete</span>');
            $('#wps-database-backup-table tbody td#wps-filesize-' + response.data.hash).html(response.data.size + 'MB');
            $('#wps-database-backup-table tbody td#wps-ended-' + response.data.hash).html(response.data.ended);
          }
        });

      } else {
        alert('Undocumented error occured, please refresh the page and try again.');
      }

    });


  });


});