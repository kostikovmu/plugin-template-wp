<?php

defined('ABSPATH') || exit();

if( ! function_exists('debug')) {
  function debug($value) {
    if ( is_blank( $value ) ) {
      echo 'value is empty';
    }
    else {
      echo '<pre>'; print_r($value); echo '</pre>';
    }
  }
}

if ( ! function_exists('is_blank')) {
  function is_blank($value) {
    return empty($value) && !is_numeric($value);
  }
}


