
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table pageAccess
# ------------------------------------------------------------

CREATE TABLE `pageAccess` (
  `staffId` int(11) NOT NULL,
  `schedule` tinyint(1) DEFAULT '0' COMMENT 'has access to the "Schedule" page',
  `truckSchedule` tinyint(1) DEFAULT '0' COMMENT 'see the "truckScheduleAccess" table for which trucks the user can see',
  `orders` tinyint(1) DEFAULT '0',
  `createOrder` tinyint(1) DEFAULT '0',
  `customers` tinyint(1) DEFAULT '0',
  `manageBins` tinyint(1) DEFAULT '0',
  `manageCoupons` tinyint(1) NOT NULL DEFAULT '0',
  `manageHolidays` tinyint(1) DEFAULT NULL,
  `manageItems` tinyint(1) DEFAULT '0',
  `manageServices` tinyint(1) DEFAULT '0',
  `manageStaff` tinyint(1) DEFAULT '0',
  `manageSettings` tinyint(1) DEFAULT NULL,
  `manageTrucks` tinyint(4) DEFAULT '0',
  `manageZones` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`staffId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;





# Dump of table staff
# ------------------------------------------------------------

CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `salt` varchar(100) NOT NULL,
  `isActive` tinyint(1) DEFAULT '1' COMMENT 'so can disable',
  `created` date NOT NULL,
  `deleted` date DEFAULT NULL,
  `resetPasswordHash` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;





# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(50) NOT NULL,
  `feePerOrderFE` decimal(5,2) NOT NULL COMMENT '(front-end)',
  `feePerOrderBE` decimal(5,2) NOT NULL COMMENT '(back-end)',
  `maxFeePerMonth` decimal(5,2) NOT NULL,
  `useTestStripe` tinyint(1) DEFAULT '0',
  `stripeSecretKeyLive` varchar(150) NOT NULL COMMENT 'for updating Stripe Profiles',
  `stripeSecretKeyTest` varchar(150) NOT NULL,
  `stripePublishableKeyLive` varchar(150) NOT NULL COMMENT 'for generating Stripe tokens',
  `stripePublishableKeyTest` varchar(150) NOT NULL,
  `payMeStripeId` varchar(150) NOT NULL DEFAULT '' COMMENT 'where they pay me',
  `joinDate` datetime DEFAULT NULL,
  `agreedToTermsDate` date DEFAULT NULL,
  `goLiveDate` date NOT NULL,
  `isActive` tinyint(1) DEFAULT '1' COMMENT 'so can disable if dont pay bill',
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `companyName` varchar(100) NOT NULL COMMENT 'shows up on their clients credit card statement',
  `address1` varchar(100) NOT NULL,
  `address2` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `postalCode` varchar(50) NOT NULL,
  `timeZone` varchar(50) DEFAULT 'America/Vancouver',
  `phone` varchar(35) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `emailOrder` varchar(100) NOT NULL DEFAULT '',
  `maxJobsMonday` int(2) DEFAULT NULL,
  `maxJobsTuesday` int(2) DEFAULT NULL,
  `maxJobsWednesday` int(2) DEFAULT NULL,
  `maxJobsThursday` int(2) DEFAULT NULL,
  `maxJobsFriday` int(2) DEFAULT NULL,
  `maxJobsSaturday` int(2) DEFAULT NULL,
  `maxJobsSunday` int(2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'CAD',
  `tax1` decimal(6,5) NOT NULL,
  `tax2` decimal(6,5) NOT NULL,
  `tax1Name` varchar(15) NOT NULL,
  `tax2Name` varchar(10) NOT NULL,
  `taxRegistrationTitle` varchar(50) NOT NULL,
  `taxRegistrationValue` varchar(50) NOT NULL,
  `logoPath` varchar(50) NOT NULL,
  `faviconPath` varchar(50) DEFAULT NULL,
  `colour` varchar(7) DEFAULT '#337ab7',
  `notes` text,
  `showHeader` tinyint(1) NOT NULL DEFAULT '1',
  `defaultPaymentMethod` varchar(25) DEFAULT 'credit-card',
  `pickUpDateMandatory` tinyint(1) DEFAULT '1',
  `cityText` text,
  `dateText` text,
  `serviceText` text,
  `infoText` text,
  `reviewText` text,
  `confirmationText` text,
  `termsAndConditions` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO staff(id, userId, name, email, password, salt, isActive, created) VALUES(1, 1, 'demo', 'demo', '$5$dA8T%P*V*$SixYIp5u/W09psH3lgl9w/yvVCZd5vJYdDe/Da8NeP0', '$5$dA8T%P*V*$xp$q$RMolYWVRKmLJ2Beez', 1, NOW());
INSERT INTO users(
  id,
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
  termsAndConditions
) VALUES(
  1,
  'localhost',
  0,
  0,
  0,
  1,
  '',
  '',
  '',
  '',
  '',
  NOW(),
  NOW(),
  NOW(),
  1,
  'John',
  'Doe',
  'Acme Inc.',
  '123 Fake Street',
  '',
  'Toronto',
  'ON',
  'A1A1A1',
  'America/Toronto',
  '',
  'www.google.com',
  'demo@example.com',
  'demo@example.com',
  5,
  5,
  5,
  5,
  5,
  5,
  5,
  'CAD',
  0,
  0,
  '',
  '',
  '',
  '',
  '',
  '',
  '#3137cc',
  '',
  1,
  'invoice',
  1,
  '',
  '',
  '',
  '',
  '',
  '',
  ''
  );


# Dump of table usersQb
# ------------------------------------------------------------

CREATE TABLE `usersQb` (
  `userId` int(11) NOT NULL,
  `isSyncing` tinyint(4) NOT NULL,
  `accessToken` varchar(4096) NOT NULL,
  `refreshToken` varchar(512) NOT NULL,
  `realmId` varchar(50) NOT NULL,
  `servicesItemId` varchar(5) NOT NULL,
  `servicesItemTaxCodeId` varchar(5) NOT NULL,
  `depositToId` varchar(5) NOT NULL,
  `termsId` varchar(5) NOT NULL,
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table usersQbPaymentMethodIds
# ------------------------------------------------------------

CREATE TABLE `usersQbPaymentMethodIds` (
  `userId` int(11) NOT NULL,
  `other` varchar(5) NOT NULL,
  `cash` varchar(5) NOT NULL,
  `visa` varchar(5) NOT NULL,
  `masterCard` varchar(5) NOT NULL,
  `americanExpress` varchar(5) NOT NULL,
  `dinersClub` varchar(5) NOT NULL,
  `discover` varchar(5) NOT NULL,
  `jcb` varchar(5) NOT NULL,
  `unionPay` varchar(5) NOT NULL,
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
