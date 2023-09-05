<?php

require_once __DIR__ . '/../dist/app/library/Staff.class.php';

use PHPUnit\Framework\TestCase;

class StaffTest extends TestCase
{
  public function testAttemptLogin1(): void
  {
    $S = new Staff();
    $S->load(1);

    self::assertEquals($S->getName(), 'demo', 'Name was not correct');
  }

  public function testAttemptLoginRightDomainReturnsTrue(): void
  {
    $S = new Staff();
    $result = $S->attemptLogin('localhost', 'demo@example.com', ''); // need password

    self::assertTrue($result, 'Using right domain did not return true');
  }

  public function testAttemptLoginWrongDomainReturnsFalse(): void
  {
    $S = new Staff();
    $result = $S->attemptLogin('wronghost', 'demo@example.com', ''); // need password

    self::assertFalse($result, 'Using wrong domain did not return false');
  }

  public function testAttemptLoginWrongCredentials(): void
  {
    $S = new Staff();
    $result = $S->attemptLogin('localhost', 'demo@example.com', 'wrongpassword');

    self::assertFalse($result, 'Using wrong credentials did not return false');
    self::assertEquals($S->getFailedLoginsCount(), 1, 'Using wrong credentials did not increment failedLoginsCount from 0 to 1');

    $result = $S->attemptLogin('localhost', 'demo@example.com', ''); // need password
    self::assertTrue($result, 'Using right credentials did not return true');
    self::assertEquals($S->getFailedLoginsCount(), 0, 'Using right credentials did not reset failedLoginsCount to 0');
  }

  public function testAttemptLoginFailsTenTimes(): void
  {
    $S = new Staff();
    for ($i = 0; $i < 10; ++$i)
    {
      $result = $S->attemptLogin('localhost', 'demo@example.com', 'wrongpassword');

      self::assertFalse($result, 'Using wrong credentials did not return false');
      self::assertEquals($S->getFailedLoginsCount(), $i + 1, 'Failed login did not increment failedLoginsCount');
    }
    $resetHash = $S->getResetPasswordHash();
    self::assertNotNull($resetHash, '10 failed login attempts did not set resetPasswordHash'); // test reset password hash

    $result = $S->attemptLogin('localhost', 'demo@example.com', 'wrongpassword');
    self::assertFalse($result, 'Failed 11th attemptLogin did not return false');
    self::assertNotEquals($resetHash, $S->getResetPasswordHash(), 'Failed 11th attemptLogin changed resetPasswordHash');
    self::assertEquals($S->getFailedLoginsCount(), 10, 'Failed 11th attemptLogin incremented failedLoginsCount');

    $result = $S->attemptLogin('localhost', 'demo@example.com', ''); // need correct password
    self::assertFalse($result, 'Using correct credentials past the 10th wrong attempt did not return false');
  }




  // public function testAttemptLogin2(): void
  // {
  // }

  // ...
}
