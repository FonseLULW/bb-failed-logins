<?php
require __DIR__ . '/StartSession.php';

// so local development doesnt show the annoying orange stack trace html table
if (function_exists('xdebug_disable')) {
  xdebug_disable();
}

if (!isset($_SESSION['domain'])) {
  $_SESSION['domain'] = '';
}

if (!isset($_SESSION['isLoggedIn'])) {
  $_SESSION['isLoggedIn'] = false;
}
