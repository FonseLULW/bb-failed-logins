<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/PageAccess.class.php';

class Staff
{
  private $id;
  private $userId;
  private $name;
  private $email;
  private $password;
  private $salt;
  private $isActive;
  private $created;
  private $deleted;
  private $failedLoginsCount;
  private $resetPasswordHash;

  const MAX_FAILED_LOGINS_COUNT = 10;
  const RESET_PASSWORD_HASH_ENCRYPTION_METHOD = 'AES-128-ECB';
  const RESET_PASSWORD_HASH_KEY               = 'someRandomIrrelevantPassword__YDL,:h#>(A4jU5RA_Euf(M*+*Q~N2t*';

  /**
   *  Load a staff member.
   *
   *  @param integer $id               The id of the staff member to load.
   *  @param boolean $includeDeleted   If should allow loading a deleted user (want to when looking at who created an order, dont want to when resetting password)
   */
  public function load($id, $includeDeleted = false)
  {
    global $myDbLink;

    $q = "WHERE st.id = '" . mysqli_real_escape_string($myDbLink, $id) . "'";
    $this->loadGeneric($q, $includeDeleted);
  }

  /**
   *  Load a staff member.
   *
   *  @param email $string      The email address of the staff member to load.
   *  @param integer $userId      The user ID that the staffer belongs to (as email could be re-used across domains).
   */
  public function loadByEmail($email, $userId)
  {
    global $myDbLink;

    $q = "WHERE st.email = '" . mysqli_real_escape_string($myDbLink, $email) . "'
    AND u.id = '" . mysqli_real_escape_string($myDbLink, $userId) . "'";
    $this->loadGeneric($q);
  }

  public function loadByHash($resetPasswordHash, $userId)
  {
    global $myDbLink;

    $q = "WHERE st.resetPasswordHash = '" . mysqli_real_escape_string($myDbLink, $resetPasswordHash) . "'
        AND u.id = '" . mysqli_real_escape_string($myDbLink, $userId) . "'";
    $this->loadGeneric($q);
  }

  private function loadGeneric($wherePartOfQuery, $includeDeleted = false)
  {
    global $myDbLink;

    $q = 'SELECT st.id,
        st.userId,
        st.name,
        st.email,
        st.password,
        st.salt,
        st.isActive,
        st.created,
        st.deleted,
        st.failedLoginsCount,
        st.resetPasswordHash
        FROM staff st
        LEFT JOIN users u ON u.id = st.userId
        ' . $wherePartOfQuery;

    if (!$includeDeleted) {
      $q .= " AND st.deleted IS NULL";
    }


    $result = mysqli_query($myDbLink, $q);

    if (mysqli_num_rows($result) === 1) {
      $row = mysqli_fetch_assoc($result);

      $this->id                = $row['id'];
      $this->userId            = $row['userId'];
      $this->name              = $row['name'];
      $this->email             = $row['email'];
      $this->password          = $row['password'];
      $this->salt              = $row['salt'];
      $this->isActive          = $row['isActive'];
      $this->created           = $row['created'];
      $this->deleted           = $row['deleted'];
      $this->failedLoginsCount = $row['failedLoginsCount'];
      $this->resetPasswordHash = $row['resetPasswordHash'];
    }
  }

  public function save()
  {
    global $myDbLink;

    $q = "UPDATE staff
        SET name = '" . mysqli_real_escape_string($myDbLink, $this->name) . "',
        email = '" . mysqli_real_escape_string($myDbLink, $this->email) . "',
        password = '" . mysqli_real_escape_string($myDbLink, $this->password) . "',
        salt = '" . mysqli_real_escape_string($myDbLink, $this->salt) . "',
        isActive = '" . mysqli_real_escape_string($myDbLink, $this->isActive) . "',
        failedLoginsCount = " . mysqli_real_escape_string($myDbLink, $this->failedLoginsCount) . ",
        resetPasswordHash = '" . mysqli_real_escape_string($myDbLink, $this->resetPasswordHash) . "'
        WHERE id = '" . mysqli_real_escape_string($myDbLink, $this->id) . "'";
    return $myDbLink->query($q);
  }

  public function create()
  {
    global $myDbLink;

    $this->salt = self::generateSalt();
    $q          = "INSERT INTO staff(userId, name, email, password, salt, isActive, created, resetPasswordHash)
    VALUES(
      '" . mysqli_real_escape_string($myDbLink, $this->userId) . "',
      '" . mysqli_real_escape_string($myDbLink, $this->name) . "',
      '" . mysqli_real_escape_string($myDbLink, $this->email) . "',
      'none',
      '" . $this->salt . "',
      '" . mysqli_real_escape_string($myDbLink, $this->isActive) . "',
      NOW(),
      '" . mysqli_real_escape_string($myDbLink, $this->resetPasswordHash) . "'
    )";

    if ($myDbLink->query($q)) {
      $this->id = mysqli_insert_id($myDbLink);
      return true;
    }
    return false;
  }

  public function delete()
  {
    global $myDbLink;

    $q = "UPDATE staff
        SET deleted = NOW()
        WHERE id = '" . mysqli_real_escape_string($myDbLink, $this->id) . "'";

    return $myDbLink->query($q);
  }


  /**
   * Validate staff member and load their data on success.
   *  1.  Get the encrypted password from MySQL database using domain and email
   *  2.  Immediately return false if the failedLoginsCount >= 10
   *  3.  Else, check password
   *    a) If the staff member gave the correct password, 
   *        - Load the Staff object
   *        - Reset the staff member's failedLoginsCount to 0
   *        - Return true
   *    b) If the staff member gave an incorrect password,
   *        - Increment the staff member's failedLoginsCount if failedLoginsCount < 10
   *        - If failedLoginsCount reaches 10, set resetPasswordHash
   *        - Return false
   *
   * @return boolean true if their login credentials were correct, else false.
   */
  public function attemptLogin($domain, $email, $password)
  {
    global $myDbLink;

    // Get the encrypted password from MySQL database using domain and email
    $q = "SELECT s.password, s.failedLoginsCount
      FROM staff s
      LEFT JOIN users u ON u.id = s.userId
      WHERE s.email = '" . mysqli_real_escape_string($myDbLink, $email) . "'
      AND u.domain = '" . mysqli_real_escape_string($myDbLink, $domain) . "'
      AND s.isActive = 1
      AND s.deleted IS NULL";

    $result = mysqli_query($myDbLink, $q);

    if (mysqli_num_rows($result) === 1) {
      $resultArray = mysqli_fetch_assoc($result);

      // After 10 unsuccessful attempts, always return false
      if ($resultArray['failedLoginsCount'] >= self::MAX_FAILED_LOGINS_COUNT)
      {
        return false;
      }

      // Verify password
      if (password_verify($password, $resultArray['password']))
      {
        $q = "WHERE st.email = '" . mysqli_real_escape_string($myDbLink, $email) . "'
        AND st.password = '" . mysqli_real_escape_string($myDbLink, $resultArray['password']) . "'
        AND u.domain = '" . mysqli_real_escape_string($myDbLink, $domain) . "'";

        // Load the Staff object, reset failedLoginsCount, save to the database
        $this->loadGeneric($q);
        $this->setFailedLoginsCount(0);
        $this->save();

        return true;
      }

      // On the 10th wrong attempt, set resetPasswordHash
      $lock = null;
      if ($resultArray['failedLoginsCount'] == self::MAX_FAILED_LOGINS_COUNT - 1) {
        $this->setResetPasswordHash();
        $lock = ", resetPasswordHash = '" . mysqli_real_escape_string($myDbLink, $this->getResetPasswordHash()) . "'";
      }

      // Increment failedLoginsCount on unsuccessful attempts
      $update = "UPDATE staff s
        LEFT JOIN users u ON u.id = s.userId
        SET failedLoginsCount = failedLoginsCount + 1" . $lock . "
        WHERE s.email = '" . mysqli_real_escape_string($myDbLink, $email) . "'
        AND u.domain = '" . mysqli_real_escape_string($myDbLink, $domain) . "'
        AND s.deleted IS NULL";
      $myDbLink->query($update);
      return false;
      
    }

    return false;
  }

  private static function generateSalt($max = 32)
  {
    $characterList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?';
    $i             = 0;
    $salt          = '';
    while ($i < $max) {
      $salt .= $characterList[mt_rand(0, (strlen($characterList) - 1))];
      $i++;
    }
    return '$5$' . $salt;
  }

  private static function cryptString($str, $salt)
  {
    return crypt($str, $salt);
  }

  public static function getForDomain($domain)
  {
    global $myDbLink;

    $q = "SELECT s.id,
      s.name,
      s.email,
      s.isActive,
      s.created,
      s.deleted,
      pa.schedule,
      pa.truckSchedule,
      pa.orders,
      pa.createOrder,
      pa.customers,
      pa.manageBins,
      pa.manageCoupons,
      pa.manageHolidays,
      pa.manageItems,
      pa.manageServices,
      pa.manageSettings,
      pa.manageStaff,
      pa.manageTrucks,
      pa.manageZones
      FROM staff s
      LEFT JOIN users u ON u.id = s.userId
      LEFT JOIN pageAccess pa ON s.id = pa.staffId
      WHERE u.domain = '" . mysqli_real_escape_string($myDbLink, $domain) . "'
      AND s.deleted IS NULL
      ORDER BY s.name ASC";

    $resultArray = [];

    if ($result = $myDbLink->query($q)) {
      while ($row = $result->fetch_assoc()) {
        //put all the permissions grouped in an object
        $row['pageAccess'] = [
          'schedule'       => $row['schedule'] === '1',
          'truckSchedule'  => $row['truckSchedule'] === '1',
          'orders'         => $row['orders'] === '1',
          'createOrder'    => $row['createOrder'] === '1',
          'customers'      => $row['customers'] === '1',
          'manageBins'     => $row['manageBins'] === '1',
          'manageCoupons'  => $row['manageCoupons'] === '1',
          'manageHolidays' => $row['manageHolidays'] === '1',
          'manageItems'    => $row['manageItems'] === '1',
          'manageServices' => $row['manageServices'] === '1',
          'manageSettings' => $row['manageSettings'] === '1',
          'manageStaff'    => $row['manageStaff'] === '1',
          'manageTrucks'   => $row['manageTrucks'] === '1',
          'manageZones'    => $row['manageZones'] === '1',
        ];

        //the permissions were added to the root object... remove them to keep it organized
        unset($row['schedule']);
        unset($row['truckSchedule']);
        unset($row['orders']);
        unset($row['createOrder']);
        unset($row['customers']);
        unset($row['manageBins']);
        unset($row['manageCoupons']);
        unset($row['manageHolidays']);
        unset($row['manageItems']);
        unset($row['manageServices']);
        unset($row['manageSettings']);
        unset($row['manageStaff']);
        unset($row['manageTrucks']);
        unset($row['manageZones']);

        $resultArray[] = $row;
      }
    }

    $result->close();

    return $resultArray;
  }

  public static function emailAddressIsAlreadyInUse($domain, $emailAddress, $staffId = 0)
  {
    $staffArray = self::getForDomain($domain);

    for ($i = 0; $i < count($staffArray); $i++) {
      if ($staffArray[$i]['email'] === $emailAddress && $staffArray[$i]['id'] !== $staffId) {
        return true;
      }
    }
    return false;
  }

  public function decryptResetPasswordHash()
  {
    return openssl_decrypt(urldecode($this->resetPasswordHash), self::RESET_PASSWORD_HASH_ENCRYPTION_METHOD, self::RESET_PASSWORD_HASH_KEY);
  }

  /**
   * Getters
   */
  public function getId()
  {
    return $this->id;
  }

  public function getUserId()
  {
    return $this->userId;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function getIsActive()
  {
    $value = $this->isActive;
    if (
      $value === true ||
      $value === 'true' ||
      $value === 1 ||
      $value === '1'
    ) {
      return true;
    }
    return false;
  }

  public function getFailedLoginsCount()
  {
    return $this->failedLoginsCount;
  }

  public function getResetPasswordHash()
  {
    return $this->resetPasswordHash;
  }

  public function getIsDeleted()
  {
    return $this->deleted != null;
  }

  /**
   * Setters
   */
  public function setUserId($value)
  {
    $this->userId = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  }

  public function setName($value)
  {
    $this->name = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  }

  public function setEmail($value)
  {
    $this->email = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  }

  public function setPassword($value)
  {
    $this->password = self::cryptString($value, $this->salt);
  }

  public function setIsActive($value)
  {
    if (
      $value === true ||
      $value === 'true' ||
      $value === 1 ||
      $value === '1'
    ) {
      $value = 1;
    } else {
      $value = 0;
    }

    $this->isActive = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
  }

  public function setFailedLoginsCount($value)
  {
    $this->failedLoginsCount = $value;
  }

  public function setResetPasswordHash()
  {
    $value = urlencode(openssl_encrypt(time(), self::RESET_PASSWORD_HASH_ENCRYPTION_METHOD, self::RESET_PASSWORD_HASH_KEY));

    $this->resetPasswordHash = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
  }

  public function emptyResetPasswordHash()
  {
    $this->resetPasswordHash = '';
  }
}
