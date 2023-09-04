<?php
require __DIR__ . '/../../back/include/Main.php';
require_once __DIR__ . '/../../library/Common.class.php';
require_once __DIR__ . '/../../library/PageAccess.class.php';
require_once __DIR__ . '/../../library/Staff.class.php';
require_once __DIR__ . '/../../library/User.class.php';

usleep(500000);

$_POST = json_decode(file_get_contents('php://input'), true);

if (isset($_POST['username'], $_POST['password'])) {
  $domain = $_SERVER['SERVER_NAME'];
  $S = new Staff();
  $success = $S->attemptLogin($domain, $_POST['username'], $_POST['password']);

  if ($success) {
    $PA = new PageAccess($S->getId());

    $_SESSION['domain']     = $domain;
    $_SESSION['userId']     = $U->getId();
    $_SESSION['staffId']    = $S->getId();
    $_SESSION['isLoggedIn'] = true;

    Common::sendResponse(200, ['urlAfterLogin' => $PA->getUrlAfterLogin()]);
  }
}
sleep(3);
Common::sendResponse(401);
