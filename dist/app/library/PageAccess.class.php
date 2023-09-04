<?php
//TODO: should i test that it doesnt allow a deleted user to do anything? a disabled user? actually that should go in PageAccess.class.php

require_once 'config.php';
require_once 'database.php';
require_once 'Staff.class.php';

class PageAccess
{
  private $S;
  private $staffId;
  private $schedule; //the different pages of the app
  private $truckSchedule;
  private $orders;
  private $createOrder;
  private $customers;
  private $manageBins;
  private $manageCoupons;
  private $manageHolidays;
  private $manageItems;
  private $manageServices;
  private $manageStaff;
  private $manageSettings;
  private $manageTrucks;
  private $manageZones;

  public function __construct($staffId)
  {
    global $myDbLink;

    $this->S = new Staff();
    $this->S->load($staffId);

    $this->setStaffId($staffId);

    $q = "SELECT schedule,
      truckSchedule,
      orders,
      createOrder,
      customers,
      manageBins,
      manageCoupons,
      manageHolidays,
      manageItems,
      manageServices,
      manageStaff,
      manageSettings,
      manageTrucks,
      manageZones
      FROM pageAccess
      WHERE staffId = '" . mysqli_real_escape_string($myDbLink, $staffId) . "'";

    $result = mysqli_query($myDbLink, $q);

    if (mysqli_num_rows($result) === 1) {
      $row = mysqli_fetch_assoc($result);

      $this->setSchedule($row['schedule']);
      $this->setTruckSchedule($row['truckSchedule']);
      $this->setOrders($row['orders']);
      $this->setCreateOrder($row['createOrder']);
      $this->setCustomers($row['customers']);
      $this->setManageBins($row['manageBins']);
      $this->setManageCoupons($row['manageCoupons']);
      $this->setManageHolidays($row['manageHolidays']);
      $this->setManageItems($row['manageItems']);
      $this->setManageServices($row['manageServices']);
      $this->setManageStaff($row['manageStaff']);
      $this->setManageSettings($row['manageSettings']);
      $this->setManageTrucks($row['manageTrucks']);
      $this->setManageZones($row['manageZones']);
    }
  }

  public function save()
  {
    //remove all existing entries
    global $myDbLink;

    $q = "DELETE
      FROM pageAccess
      WHERE staffId = '" . mysqli_real_escape_string($myDbLink, $this->staffId) . "'";
    $result = mysqli_query($myDbLink, $q);

    $q = "INSERT INTO pageAccess (staffId,
      schedule,
      truckSchedule,
      orders,
      createOrder,
      customers,
      manageBins,
      manageCoupons,
      manageHolidays,
      manageItems,
      manageServices,
      manageStaff,
      manageSettings,
      manageTrucks,
      manageZones)
    VALUES (
      '" . mysqli_real_escape_string($myDbLink, $this->staffId) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessSchedulePage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessTruckSchedulePage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessOrdersPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessCreateOrderPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessCustomersPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessManageBinsPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessManageCouponsPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessManageHolidaysPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessManageItemsPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessManageServicesPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessManageStaffPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessManageSettingsPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessManageTrucksPage()) . "',
      '" . mysqli_real_escape_string($myDbLink, (int) $this->canAccessManageZonesPage()) . "'
    )";

    $result = mysqli_query($myDbLink, $q);
  }

  /**
   * After someone logs in, return the URL of the back-end page that they should be redirected to.
   */
  public function getUrlAfterLogin()
  {
    if ($this->canAccessSchedulePage()) {
      return 'schedule';
    }

    if ($this->canAccessOrdersPage()) {
      return 'orders';
    }

    if ($this->canAccessCreateOrderPage()) {
      return 'create-order';
    }

    if ($this->canAccessCustomersPage()) {
      return 'customers';
    }

    if ($this->canAccessTruckSchedulePage()) {
      return 'truck-schedule';
    }

    if ($this->canAccessManageBinsPage()) {
      return 'bins';
    }

    if ($this->canAccessManageCouponsPage()) {
      return 'coupons';
    }

    if ($this->canAccessManageHolidaysPage()) {
      return 'holidays';
    }

    if ($this->canAccessManageItemsPage()) {
      return 'items';
    }

    if ($this->canAccessManageServicesPage()) {
      return 'services';
    }

    if ($this->canAccessManageStaffPage()) {
      return 'staff';
    }

    if ($this->canAccessManageSettingsPage()) {
      return 'settings';
    }

    if ($this->canAccessManageTrucksPage()) {
      return 'trucks';
    }

    if ($this->canAccessManageZonesPage()) {
      return 'zones';
    }

    return 'no-access';
  }

  /**
   * Getters
   */
  public function getStaffId()
  {
    return $this->staffId;
  }

  public function canAccessSchedulePage()
  {
    return $this->schedule && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessTruckSchedulePage()
  {
    return $this->truckSchedule && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessOrdersPage()
  {
    return $this->orders && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessCreateOrderPage()
  {
    return $this->createOrder && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessCustomersPage()
  {
    return $this->customers && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessManageBinsPage()
  {
    return $this->manageBins && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessManageCouponsPage()
  {
    return $this->manageCoupons && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessManageHolidaysPage()
  {
    return $this->manageHolidays && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessManageItemsPage()
  {
    return $this->manageItems && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessManageServicesPage()
  {
    return $this->manageServices && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessManageStaffPage()
  {
    return $this->manageStaff && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessManageSettingsPage()
  {
    return $this->manageSettings && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessManageTrucksPage()
  {
    return $this->manageTrucks && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  public function canAccessManageZonesPage()
  {
    return $this->manageZones && $this->S->getIsActive() && !$this->S->getIsDeleted();
  }

  /**
   * Setters
   */
  public function setStaffId($value)
  {
    // May need something like this:

    // if ($value === true ||
    //   $value === 'true' ||
    //   $value === 1 ||
    //   $value === '1') {
    //   $value = 1;
    // } else {
    //   $value = 0;
    // }
    $this->staffId = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
  }

  public function setSchedule($value)
  {
    $this->schedule = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setTruckSchedule($value)
  {
    $this->truckSchedule = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setOrders($value)
  {
    $this->orders = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setCreateOrder($value)
  {
    $this->createOrder = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setCustomers($value)
  {
    $this->customers = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setManageBins($value)
  {
    $this->manageBins = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setManageCoupons($value)
  {
    $this->manageCoupons = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setManageHolidays($value)
  {
    $this->manageHolidays = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setManageItems($value)
  {
    $this->manageItems = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setManageServices($value)
  {
    $this->manageServices = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setManageStaff($value)
  {
    $this->manageStaff = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setManageSettings($value)
  {
    $this->manageSettings = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setManageTrucks($value)
  {
    $this->manageTrucks = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  public function setManageZones($value)
  {
    $this->manageZones = filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }
}
