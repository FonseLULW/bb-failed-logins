<?php

require_once __DIR__ . '/../dist/app/library/Staff.class.php';
require_once __DIR__ . './../dist/app/library/database.php';

use PHPUnit\Framework\TestCase;

class StaffTest extends TestCase
{
  private $S;
  private $correctDomain;
  private $correctEmail;
  private $correctPassword;

  /**
   * Set up variables before every test.
   */
  protected function setUp(): void
  {
    $this->S = new Staff();
    $this->correctDomain = 'localhost';
    $this->correctEmail = 'demo';
    $this->correctPassword = 'demo';
  }

  /**
   * Reset staff table in the database after every test.
   */
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

  /**
   * Get failedLoginsCount from mySQL using domain and email.
   */
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

  /**
   * Test successful logins.
   * 
   * Test passes when attemptLogin:
   *  1) Returns true
   *  2) Loads Staff object
   *  3) Resets failedLoginsCount in both the Staff object and the database
   */
  public function testAttemptLoginSuccess(): void
  {
    $result = $this->S->attemptLogin($this->correctDomain, $this->correctEmail, $this->correctPassword);
    self::assertTrue($result, 'Using right domain did not return true');
    self::assertEquals($this->correctEmail, $this->S->getEmail(), 'Email was not correctly loaded on success');
    self::assertEquals(0, $this->S->getFailedLoginsCount($this->correctDomain, $this->correctEmail), 'Using right credentials did not reset failedLoginsCount to 0');
    self::assertEquals(0, $this->getFailedLoginsCount($this->correctDomain, $this->correctEmail), 'Using right credentials did not reset failedLoginsCount to 0');
  }

  /**
   * Test logins using an incorrect domain.
   * 
   * Test passes when attemptLogin:
   *  1) Returns false
   *  2) Does not set failedLoginsCount
   */
  public function testAttemptLoginWrongDomainReturnsFalse(): void
  {
    $result = $this->S->attemptLogin('wronghost', $this->correctEmail, $this->correctPassword);

    self::assertFalse($result, 'Using wrong domain did not return false');
    self::assertNull($this->S->getFailedLoginsCount(), 'Using wrong domain incorrectly set failedLoginsCount');
  }

  /**
   * Test logins using an incorrect email but correct domain.
   * 
   * Test passes when attemptLogin:
   *  1) Returns false
   *  2) Does not set failedLoginsCount
   */
  public function testAttemptLoginWrongEmailReturnsFalse(): void
  {
    $result = $this->S->attemptLogin($this->correctDomain, 'wrongemail', $this->correctPassword);

    self::assertFalse($result, 'Using wrong email did not return false');
    self::assertNull($this->S->getFailedLoginsCount(), 'Using wrong email incorrectly set failedLoginsCount');
  }

  /**
   * Test a login using a wrong password then another login that is a success.
   * 
   * Test passes when logging in with a wrong password:
   *  1) Returns false
   *  2) Increments failedLoginsCount in the database
   * 
   * Test passes when logging successfully afterwards:
   *  1) Returns true
   *  2) Resets failedLoginsCount in the Staff object and in the database 
   */
  public function testAttemptLoginWrongPasswordThenSuccess(): void
  {
    $result = $this->S->attemptLogin($this->correctDomain, $this->correctEmail, 'wrongpassword');

    self::assertFalse($result, 'Using wrong credentials did not return false');
    self::assertEquals(1, $this->getFailedLoginsCount($this->correctDomain, $this->correctEmail), 'Using wrong credentials did not increment failedLoginsCount from 0 to 1');

    $result = $this->S->attemptLogin($this->correctDomain, $this->correctEmail, $this->correctPassword);
    self::assertTrue($result, 'Using right credentials did not return true');
    self::assertEquals(0, $this->S->getFailedLoginsCount($this->correctDomain, $this->correctEmail), 'Using right credentials did not reset failedLoginsCount to 0');
    self::assertEquals(0, $this->getFailedLoginsCount($this->correctDomain, $this->correctEmail), 'Using right credentials did not reset failedLoginsCount to 0');
  }

  /**
   * Test ten failed attempts using a wrong password.
   * 
   * Test passes when:
   *  1) The first 9 unsuccessful attempts increments failedLoginsCount and returns false.
   *  2) The 10th unsuccessful attempt sets resetPasswordHash in both the Staff object and in the database, and returns false.
   *  3) Any attempts afterwards (successful or unsuccessful) always return false without changing resetPasswordHash and failedLoginsCount.
   */
  public function testAttemptLoginWrongPasswordTenTimes(): void
  {
    for ($i = 0; $i < 10; ++$i)
    {
      $result = $this->S->attemptLogin($this->correctDomain, $this->correctEmail, 'wrongpassword');
    }
    $resetHash = $this->S->getResetPasswordHash();
    self::assertNotNull($resetHash, '10 failed login attempts did not set resetPasswordHash');

    $result = $this->S->attemptLogin($this->correctDomain, $this->correctEmail, 'wrongpassword');
    self::assertFalse($result, 'Failed 11th attemptLogin did not return false');
    self::assertEquals($resetHash, $this->S->getResetPasswordHash(), 'Failed 11th attemptLogin changed resetPasswordHash');
    self::assertEquals(10, $this->getFailedLoginsCount($this->correctDomain, $this->correctEmail), 'Failed 11th attemptLogin incremented failedLoginsCount');

    $result = $this->S->attemptLogin($this->correctDomain, $this->correctEmail, $this->correctPassword);
    self::assertFalse($result, 'Using correct credentials past the 10th wrong attempt did not return false');
    self::assertEquals(10, $this->getFailedLoginsCount($this->correctDomain, $this->correctEmail), 'Correct credentials past the 10th attempt changed failedLoginsCount');
  }
}
