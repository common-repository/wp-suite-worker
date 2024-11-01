jQuery(document).ready(function($){
  
  /*
  Run all security tests at once
  */
  $('.wps-ss-run-all-tests').on('click', function(e){
    e.preventDefault();

    var table = $('#wps-ss-test-table');
    var rows = $('tr', table);
    
    $(rows).each(function(i, item) {
      var row = $(item);
      $('.wps-ss-run-single-test', row).trigger('click');
    });
    
  });  
  
  
  /*
  Run single test
  */
  $('.wps-ss-run-single-test').on('click', function(e){
    e.preventDefault();
    
    var key = $(this).data('test');
    $('.wps-ss-result', 'table#wps-ss-test-table tr#' + key).html('<img src="' + wpb_wsw_url + 'images/loader.gif" alt="Testing..." />');
    
    $.post(ajaxurl, {action:'wps_ss_run_single_test', key:key}, function(response){
      
      $('table#wps-ss-test-table tr#' + key).removeClass('error');
      
      if (response.success) {
        $('.wps-ss-result', 'table#wps-ss-test-table tr#' + key).html('<span class="wps-ss-label success">' + response.data.msg + '</span>');
        $('table#wps-ss-test-table tr#' + key + ' td.wps-ss-desc span.wps-ss-fix').remove();
      } else {
        $('.wps-ss-result', 'table#wps-ss-test-table tr#' + key).html('<span class="wps-ss-label high">BAD</span>');
        $('table#wps-ss-test-table tr#' + key).addClass('error');
        $('table#wps-ss-test-table tr#' + key + ' td.wps-ss-desc').append('<span class="wps-ss-fix"><i class="fa fa-question"></i></span>');
      }
    });
    
  });  
  
  
  /*
  Fix single test
  */
  $('.wps-ss-fix-problem').on('click', function(e){
    e.preventDefault();
    
    var key = $(this).data('test');
    $('.wps-ss-result', 'table#wps-ss-test-table tr#' + key).html('<img src="' + wpb_wsw_url + 'images/loader.gif" alt="Fixing..." />');
    
    $.post(ajaxurl, {action:'wps_ss_fix_single_test', key:key}, function(response){
      
      $('table#wps-ss-test-table tr#' + key).removeClass('error');
      
      if (response.success) {
        $('.wps-ss-result', 'table#wps-ss-test-table tr#' + key).html('<span class="wps-ss-label success">' + response.data.msg + '</span>');
        $('table#wps-ss-test-table tr#' + key + ' td.wps-ss-desc span.wps-ss-fix').remove();
      } else {
        $('.wps-ss-result', 'table#wps-ss-test-table tr#' + key).html('<span class="wps-ss-label high">BAD</span>');
        $('table#wps-ss-test-table tr#' + key).addClass('error');
        $('table#wps-ss-test-table tr#' + key + ' td.wps-ss-desc').append('<span class="wps-ss-fix"><i class="fa fa-question"></i></span>');
      }
    });
    
  });
  
  
});