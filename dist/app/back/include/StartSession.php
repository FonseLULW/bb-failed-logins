<?php
$twoHours = 2 * 60 * 60;
$thirtyMins  = 30 * 60;

if (session_status() == PHP_SESSION_NONE) {
  ini_set('session.gc_maxlifetime', $twoHours);
  // ini_set('session.cookie_lifetime', $twoHours); didnt change anything
  session_set_cookie_params($twoHours);  // this seemed to work after 45 minutes!
  ini_set('session.gc_probability', 1);
  ini_set('session.gc_divisor', 100);
  ini_set('session.cookie_secure', false);
  ini_set('session.use_only_cookies', true);
  session_start();
}

//Below is from:
//http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes/1270960#1270960

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $twoHours)) {
  session_unset(); // unset $_SESSION variable for the run-time
  session_destroy(); // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

//Prevent session fixation
if (!isset($_SESSION['CREATED'])) {
  $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > $thirtyMins) {
  // session started more than 30 minutes ago
  session_regenerate_id(true); // change session ID for the current session and invalidate old session ID
  $_SESSION['CREATED'] = time(); // update creation time
}
