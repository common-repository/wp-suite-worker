<?php
/*
WP Suite - Common Functions
© 2016 - All rights reserved PremiumWPSuite
Author: PremiumWPSuite
*/
if ( ! defined( 'ABSPATH' ) ) exit;


function decrypt($key, $text) {
  return rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $key ), base64_decode( $text ), MCRYPT_MODE_CBC, md5( md5( $key ) ) ), "\0");
} // decrypt

function encrypt($key, $text) {
  return base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $key ), $text, MCRYPT_MODE_CBC, md5( md5( $key ) ) ) );
} // encrypt