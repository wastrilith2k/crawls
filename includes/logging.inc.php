<?php

function watchdog() {  
  $args = func_get_args();
  if (count($args) >= 2) {
    $message = call_user_func_array('sprintf', $args);
  }
  print $message . "\n";
}