<?php

require_once __DIR__ . '/../dist/app/library/Staff.class.php';
require_once __DIR__ . './../dist/app/library/database.php';

use PHPUnit\Framework\TestCase;

class StaffTest extends TestCase
{
  public function getFailedLoginsCount($domain, $email): int
  {
    global $myDbLink;
    $domain = mysqli_real_escape_string($myDbLink, $domain);
    $email = mysqli_real_escape_string($myDbLink, $email);
    $q = "SELECT s.failedLoginsCount
    FROM staff s
    LEFT JOIN users u ON u.id = s.userId
    WHERE s.email = '$email'
      AND u.domain = '$domain'
      AND s.isActive = 1
      AND s.deleted IS NULL";

    $result = mysqli_query($myDbLink, $q);
    $failedLoginsCount = mysqli_fetch_assoc($result)['failedLoginsCount'];
    return $failedLoginsCount;
  }

  public function testAttemptLoginSuccess(): void
  {
    $S = new Staff();
    $result = $S->attemptLogin('localhost', 'demo', 'demo');

    self::assertTrue($result, 'Using right domain did not return true');
    self::assertEquals('demo', $S->getEmail(), 'Email was not correct');
    self::assertEquals(1, $S->getId(), 'Email was not correct');
  }

  public function testAttemptLoginWrongDomainReturnsFalse(): void
  {
    $S = new Staff();
    $result = $S->attemptLogin('wronghost', 'demo', 'demo');

    self::assertFalse($result, 'Using wrong domain did not return false');
    self::assertNull($S->getFailedLoginsCount(), 'Using wrong domain incorrectly set failedLoginsCount');
  }

  public function testAttemptLoginWrongEmailReturnsFalse(): void
  {
    $S = new Staff();
    $result = $S->attemptLogin('wronghost', 'wrongemail', 'demo');

    self::assertFalse($result, 'Using wrong email did not return false');
    self::assertNull($S->getFailedLoginsCount(), 'Using wrong email incorrectly set failedLoginsCount');
  }

  public function testAttemptLoginWrongPasswordThenSuccess(): void
  {
    $S = new Staff();
    $curFailedLoginsCount = $this->getFailedLoginsCount('localhost', 'demo');
    $result = $S->attemptLogin('localhost', 'demo', 'wrongpassword');

    self::assertFalse($result, 'Using wrong credentials did not return false');
    self::assertEquals($curFailedLoginsCount + 1, $this->getFailedLoginsCount('localhost', 'demo'), 'Using wrong credentials did not increment failedLoginsCount from 0 to 1');

    $result = $S->attemptLogin('localhost', 'demo', 'demo');
    self::assertTrue($result, 'Using right credentials did not return true');
    self::assertEquals(0, $this->getFailedLoginsCount('localhost', 'demo'), 'Using right credentials did not reset failedLoginsCount to 0');
  }

  public function testAttemptLoginWrongPasswordTenTimes(): void
  {
    $S = new Staff();
    for ($i = 0; $i < 10; ++$i)
    {
      $curFailedLoginsCount = $this->getFailedLoginsCount('localhost', 'demo');
      $result = $S->attemptLogin('localhost', 'demo', 'wrongpassword');

      self::assertFalse($result, 'Using wrong credentials did not return false');
      self::assertEquals($curFailedLoginsCount + 1, $this->getFailedLoginsCount('localhost', 'demo'), 'Failed login did not increment failedLoginsCount');
    }
    $resetHash = $S->getResetPasswordHash();
    self::assertNotNull($resetHash, '10 failed login attempts did not set resetPasswordHash'); // test reset password hash

    $result = $S->attemptLogin('localhost', 'demo', 'wrongpassword');
    self::assertFalse($result, 'Failed 11th attemptLogin did not return false');
    self::assertEquals($resetHash, $S->getResetPasswordHash(), 'Failed 11th attemptLogin changed resetPasswordHash');
    self::assertEquals(10, $this->getFailedLoginsCount('localhost', 'demo'), 'Failed 11th attemptLogin incremented failedLoginsCount');

    $result = $S->attemptLogin('localhost', 'demo', 'demo');
    self::assertFalse($result, 'Using correct credentials past the 10th wrong attempt did not return false');
    self::assertEquals(10, $this->getFailedLoginsCount('localhost', 'demo'), 'Correct credentials past the 10th attempt changed failedLoginsCount');
  }

  protected function tearDown()
  {
    global $myDbLink;
    
    $teardown = "UPDATE staff s
    LEFT JOIN users u ON u.id = s.userId
    SET failedLoginsCount = 0, resetPasswordHash = ''
    WHERE s.email = 'demo'
      AND u.domain = 'localhost'
      AND s.isActive = 1
      AND s.deleted IS NULL";
    $myDbLink->query($teardown);
  }
}
