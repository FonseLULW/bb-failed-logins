<?php
require_once 'secrets.php';

//These are first defined in config.test.php (used in phpunit)
if (!defined('EMAIL_ERRORS')) {
  define('EMAIL_ERRORS', 'fake@gmail.com');
  define('EMAIL_FROM', 'fake@gmail.com.com');
  define('EMAIL_SMTP_HOST', 'smtp.gmail.com');
  define('EMAIL_SMTP_USERNAME', SECRET_EMAIL_SMTP_USERNAME);
  define('EMAIL_SMTP_PASSWORD', SECRET_EMAIL_SMTP_PASSWORD);

  define('TEMP_DIR', $_SERVER['DOCUMENT_ROOT'] . '/jamietemp');
  define('_MPDF_TTFONTDATAPATH', TEMP_DIR . '/'); //needed for mpdf

  define('MY_PRIVATE_STRIPE_ID', SECRET_STRIPE_PRIVATE_KEY);

  define('DAY_START_HOUR', 0); // for all users the day starts at midnight so they can have up to 24 jobs in a day

  define('CUSTOMERS_PER_PAGE', 20); //in the back-end on the 'customers' page
  define('ORDERS_PER_PAGE', 20); //in the back-end on the 'orders' page

  define('ALLOWED_TAGS', '<br><br /><p><i><em><strong><u><b><ul><ol><li><div>');

  define('NO_PICKUP_DATE', '2100-01-01'); // This is also in constants.jsx. When they didnt choose a pick-up date, use this date to calculate bin availability
}
