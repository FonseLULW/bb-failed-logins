<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/database-pdo.php'; //defines 'pdoConnection'

class User
{
  private $id;
  private $domain;
  private $feePerOrderFE;
  private $feePerOrderBE;
  private $maxFeePerMonth;
  private $useTestStripe;
  private $stripeSecretKeyLive;
  private $stripeSecretKeyTest;
  private $stripePublishableKeyLive;
  private $stripePublishableKeyTest;
  private $payMeStripeId;
  private $joinDate;
  private $agreedToTermsDate;
  private $goLiveDate;
  private $isActive;
  private $firstName;
  private $lastName;
  private $companyName;
  private $address1;
  private $address2;
  private $city;
  private $province;
  private $postalCode;
  private $timeZone;
  private $phone;
  private $url;
  private $email;
  private $emailOrder;
  private $maxJobsMonday;
  private $maxJobsTuesday;
  private $maxJobsWednesday;
  private $maxJobsThursday;
  private $maxJobsFriday;
  private $maxJobsSaturday;
  private $maxJobsSunday;
  private $currency; //CAD, USD
  private $tax1;
  private $tax2;
  private $tax1Name;
  private $tax2Name;
  private $taxRegistrationTitle; //e.g. "GST Registration Number"
  private $taxRegistrationValue; //e.g. "123456789 RT 0001"
  private $logoPath;
  private $faviconPath;
  private $colour;
  private $notes;
  private $showHeader;
  private $defaultPaymentMethod; // see OrderItems::PMT_METHOD_xxx consts
  private $pickUpDateMandatory;
  private $cityText;
  private $dateText;
  private $serviceText;
  private $infoText;
  private $reviewText;
  private $confirmationText;
  private $termsAndConditions;

  /**
   *  Load a user of BinBooker.com (instantiate).
   *
   *  @param string $domain      The domain of the user to load.
   */
  public function load($domain)
  {
    $this->loadGeneric($domain, '');
  }


  /**
   *  Load a user of BinBooker.com (instantiate).  Only pass in one of the two args.
   *
   *  @param string $domain      The domain/user to load.
   *  @param string $id      The id of the user to load.
   */
  private function loadGeneric($domain, $id)
  {
    global $myDbLink;

    $q = 'SELECT id,
          domain,
          feePerOrderFE,
          feePerOrderBE,
          maxFeePerMonth,
          useTestStripe,
          stripeSecretKeyLive,
          stripeSecretKeyTest,
          stripePublishableKeyLive,
          stripePublishableKeyTest,
          payMeStripeId,
          joinDate,
          agreedToTermsDate,
          goLiveDate,
          isActive,
          firstName,
          lastName,
          companyName,
          address1,
          address2,
          city,
          province,
          postalCode,
          timeZone,
          phone,
          url,
          email,
          emailOrder,
          maxJobsMonday,
          maxJobsTuesday,
          maxJobsWednesday,
          maxJobsThursday,
          maxJobsFriday,
          maxJobsSaturday,
          maxJobsSunday,
          currency,
          tax1,
          tax2,
          tax1Name,
          tax2Name,
          taxRegistrationTitle,
          taxRegistrationValue,
          logoPath,
          faviconPath,
          colour,
          notes,
          showHeader,
          defaultPaymentMethod,
          pickUpDateMandatory,
          cityText,
          dateText,
          serviceText,
          infoText,
          reviewText,
          confirmationText,
          termsAndConditions,
          uqb.isSyncing,
          uqb.accessToken,
          uqb.refreshToken,
          uqb.realmId,
          uqb.servicesItemId,
          uqb.servicesItemTaxCodeId,
          uqb.depositToId,
          uqb.termsId,
          uqbpm.other,
          uqbpm.cash,
          uqbpm.visa,
          uqbpm.masterCard,
          uqbpm.americanExpress,
          uqbpm.dinersClub,
          uqbpm.discover,
          uqbpm.jcb,
          uqbpm.unionPay
        FROM users u
        LEFT JOIN usersQb uqb on uqb.userId = u.id
        LEFT JOIN usersQbPaymentMethodIds uqbpm on uqbpm.userId = u.id ';

    if (strlen($domain) > 0) {
      $q .= " WHERE domain = '" . mysqli_real_escape_string($myDbLink, $domain) . "'";
    } else {
      $q .= " WHERE id = '" . mysqli_real_escape_string($myDbLink, $id) . "'";
    }

    $result = mysqli_query($myDbLink, $q);
    $row    = mysqli_fetch_assoc($result);

    $this->id                               = $row['id'];
    $this->domain                           = $row['domain'];
    $this->feePerOrderFE                    = $row['feePerOrderFE'];
    $this->feePerOrderBE                    = $row['feePerOrderBE'];
    $this->maxFeePerMonth                   = $row['maxFeePerMonth'];
    $this->useTestStripe                    = $row['useTestStripe'];
    $this->stripeSecretKeyLive              = $row['stripeSecretKeyLive'];
    $this->stripeSecretKeyTest              = $row['stripeSecretKeyTest'];
    $this->stripePublishableKeyLive         = $row['stripePublishableKeyLive'];
    $this->stripePublishableKeyTest         = $row['stripePublishableKeyTest'];
    $this->payMeStripeId                    = $row['payMeStripeId'];
    $this->joinDate                         = $row['joinDate'];
    $this->agreedToTermsDate                = $row['agreedToTermsDate'];
    $this->goLiveDate                       = $row['goLiveDate'];
    $this->isActive                         = $row['isActive'];
    $this->firstName                        = $row['firstName'];
    $this->lastName                         = $row['lastName'];
    $this->companyName                      = $row['companyName'];
    $this->address1                         = $row['address1'];
    $this->address2                         = $row['address2'];
    $this->city                             = $row['city'];
    $this->province                         = $row['province'];
    $this->postalCode                       = $row['postalCode'];
    $this->timeZone                         = $row['timeZone'];
    $this->phone                            = $row['phone'];
    $this->url                              = $row['url'];
    $this->email                            = $row['email'];
    $this->emailOrder                       = $row['emailOrder'];
    $this->maxJobsMonday                    = $row['maxJobsMonday'];
    $this->maxJobsTuesday                   = $row['maxJobsTuesday'];
    $this->maxJobsWednesday                 = $row['maxJobsWednesday'];
    $this->maxJobsThursday                  = $row['maxJobsThursday'];
    $this->maxJobsFriday                    = $row['maxJobsFriday'];
    $this->maxJobsSaturday                  = $row['maxJobsSaturday'];
    $this->maxJobsSunday                    = $row['maxJobsSunday'];
    $this->currency                         = $row['currency'];
    $this->tax1                             = $row['tax1'];
    $this->tax2                             = $row['tax2'];
    $this->tax1Name                         = $row['tax1Name'];
    $this->tax2Name                         = $row['tax2Name'];
    $this->taxRegistrationTitle             = $row['taxRegistrationTitle'];
    $this->taxRegistrationValue             = $row['taxRegistrationValue'];
    $this->logoPath                         = $row['logoPath'];
    $this->faviconPath                      = $row['faviconPath'];
    $this->colour                           = $row['colour'];
    $this->notes                            = $row['notes'];
    $this->showHeader                       = $row['showHeader'];
    $this->defaultPaymentMethod             = $row['defaultPaymentMethod'];
    $this->pickUpDateMandatory              = $row['pickUpDateMandatory'];
    $this->cityText                         = $row['cityText'];
    $this->dateText                         = $row['dateText'];
    $this->serviceText                      = $row['serviceText'];
    $this->infoText                         = $row['infoText'];
    $this->reviewText                       = $row['reviewText'];
    $this->confirmationText                 = $row['confirmationText'];
    $this->termsAndConditions               = $row['termsAndConditions'];
    $this->qbIsSyncing                      = $row['isSyncing'];
    $this->qbAccessToken                    = $row['accessToken'];
    $this->qbRefreshToken                   = $row['refreshToken'];
    $this->qbRealmId                        = $row['realmId'];
    $this->qbServicesItemId                 = $row['servicesItemId'];
    $this->qbServicesItemTaxCodeId          = $row['servicesItemTaxCodeId'];
    $this->qbDepositToId                    = $row['depositToId'];
    $this->qbTermsId                        = $row['termsId'];
    $this->qbOtherPaymentMethodId           = $row['other'];
    $this->qbCashPaymentMethodId            = $row['cash'];
    $this->qbVisaPaymentMethodId            = $row['visa'];
    $this->qbMasterCardPaymentMethodId      = $row['masterCard'];
    $this->qbAmericanExpressPaymentMethodId = $row['americanExpress'];
    $this->qbDinersClubPaymentMethodId      = $row['dinersClub'];
    $this->qbDiscoverPaymentMethodId        = $row['discover'];
    $this->qbJcbPaymentMethodId             = $row['jcb'];
    $this->qbUnionPayPaymentMethodId        = $row['unionPay'];

    if (strlen($this->timeZone) > 0) {
      date_default_timezone_set($this->timeZone);
    } else {
      date_default_timezone_set('America/Toronto');
    }
  }

  /**
   * Getters
   */
  public function getId()
  {
    return $this->id;
  }
}
