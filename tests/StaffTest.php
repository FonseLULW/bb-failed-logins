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

  // public function testAttemptLogin1(): void
  // {
  // }

  // public function testAttemptLogin2(): void
  // {
  // }

  // ...
}
