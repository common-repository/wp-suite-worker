jQuery(document).ready(function($){
  
  
  $('#start-benchmark').on('click', function(e){
	e.preventDefault();
	
	$('body').prepend('<div class="window-overlay"><div class="wo-inner">Benchmark in progress...<p>This can take few minutes...</p></div></div>');
	$.post(ajaxurl, {action:'wps_run_benchmark'}, function(response){
	  
	  location.reload();
	  
	});
  });
  
  
});