/*
WP Suite - Manager Connect Javascript
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/

jQuery(document).ready(function($){

  
  $('.already-have-account').on('click', function(){
    $('#register-box').slideUp(500, function(){
      $('#connect-box').slideDown();
    });
  });  
  
  
  $('.dont-have-account').on('click', function(){
    $('#connect-box').slideUp(500, function(){
      $('#register-box').slideDown();
    });
  });
  
  // Remote Register
  $('#register-box').on('submit', function(){
    var form = $(this);
    $('input[type="submit"]', form).attr('disabled', 'disabled');
    $('input[type="submit"]', form).attr('value', 'Working...');
    
    $.post(ajaxurl, {action:'wps_register_account', 
                     username:$('input[name="username"]', form).val(),
                     email:$('input[name="email"]', form).val(),
                     password:$('input[name="password"]', form).val()}, 
    function(response){
      
      if (response.success) {
        alert('Connected!');
        window.location.reload();
      } else {
        alert(response.data);
      }
      
    });
    
    return false;
  });    
  
  // Remote Connect
  $('#connect-box').on('submit', function(){
    var form = $(this);
    $('input[type="submit"]', form).attr('disabled', 'disabled');
    $('input[type="submit"]', form).attr('value', 'Working...');
    
    $.post(ajaxurl, {action:'wps_connect_account', 
                     username:$('input[name="username"]', form).val(),
                     password:$('input[name="password"]', form).val()}, 
    function(response){
      
      if (response.success) {
        window.location.hash = 'site-clones';
        window.location.reload();
      } else {
        alert(response.data);
      }
      
    });
    
    return false;
  });  
  
});