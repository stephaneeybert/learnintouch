<?php

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$preferenceUtils->init($shopItemUtils->preferences);

$paymentCompleted = false;

error_log("In the payment notification page");
// Check if the payment was completed
if ($shopOrder = $shopOrderUtils->selectById($paymentShopOrderId)) {
  error_log("The order is found");
  if ($paymentStatusCompleted) {
    error_log("The paymentStatusCompleted is true");
    $email = $shopOrder->getEmail();
    $status = $shopOrder->getStatus();
    // Check the email of the payment against the email of the order
    error_log("email $email");
    error_log("paymentEmail $paymentEmail");
    error_log("status $status");
    if ($email == $paymentEmail && $status == SHOP_ORDER_STATUS_PENDING) {
      error_log("The payment is deemed completed.");
      $paymentCompleted = true;
    }
  }
}

// Change the status of the order as being paid
if ($paymentCompleted) {
  error_log("The payment is completed");
  $status = $shopOrder->getStatus();
  if ($status == SHOP_ORDER_STATUS_PENDING) {
    error_log("The status is pending");
    $totalToPay = $shopOrderUtils->getTotalToPay($shopOrder);
    error_log("totalToPay $totalToPay");
    error_log("paymentAmount $paymentAmount");
    error_log("paymentTransactionID $paymentTransactionID");

    // Register the payment with the transaction identifier so as to avoid
    // processing twice the order update after for one same transaction
    $shopOrder->setPaymentTransactionID($paymentTransactionID);

    // Change the order status only if it has been fully paid
    if ($paymentAmount >= $totalToPay) {
      error_log("Changing the status");
      $shopOrder->setStatus(SHOP_ORDER_STATUS_PAID);
      $shopOrderUtils->update($shopOrder);
    }
  }
}

$firstname = $shopOrder->getFirstname();
$lastname = $shopOrder->getLastname();
$organisation = $shopOrder->getOrganisation();
$email = $shopOrder->getEmail();
error_log("firstname $firstname");

$strOrderContent = $shopOrderUtils->renderOrderContent($paymentShopOrderId);

// Send an email when an order is received
$websiteName = $profileUtils->getProfileValue("website.name");
$siteEmail = $profileUtils->getProfileValue("website.email");

$mailOnPost = $preferenceUtils->getValue("SHOP_MAIL_ON_POST");
error_log("mailOnPost $mailOnPost");
if ($mailOnPost) {
  error_log("Mailing on post");
  // Thus create a one-time url for the link in the email
  // Generate a unique token and keep it for later use
  $tokenName = SHOP_CHECKOUT_TOKEN_NAME;
  $tokenDuration = $adminUtils->getLoginTokenDuration();
  $tokenValue = $uniqueTokenUtils->create($tokenName, $tokenDuration);

  $emailSubject = $websiteText[1] . ' ' . $firstname . ' ' . $lastname . ' ' . $websiteText[20] . ' ' . $websiteName;
  error_log("emailSubject $emailSubject");

  $emailBody = $websiteText[2] . ' ' . $firstname . ' ' . $lastname
    . "<br /><br />"
    . $websiteText[21] . " <a href='mailto:$email'>$email</a>";
  if ($organisation) {
    $emailBody .= ' [' . $organisation . ']';
  }
  $orderUrl = "$gShopUrl/order/edit.php?shopOrderId=$paymentShopOrderId&tokenName=$tokenName&tokenValue=$tokenValue";
  $emailBody .= "<br /><br />"
    . $websiteText[17] . ' "' . "<a href='$orderUrl' $gJSNoStatus>" . $paymentShopOrderId . "</a>" . '"'
    . " [<a href='$orderUrl' $gJSNoStatus>$websiteText[4]</a>]"
    . "<br /><br />"
    . $strOrderContent
    . "<br />"
    . "$websiteText[10]"
    . "<br /><br />"
    . "$websiteName"
    . "<br /><br />";

  if (LibEmail::validate($siteEmail)) {
    error_log("Sending the mail to the website to notify about the payment");
    LibEmail::sendMail($siteEmail, $websiteName, $emailSubject, $emailBody, $siteEmail, $websiteName);
  }
}

// Register the visitor as a user with a random password
$registerUser = $preferenceUtils->getValue("SHOP_REGISTER_USER");
error_log("The user shall be registered if necessary ? $registerUser");
// Get the user if any
if ($user = $userUtils->selectByEmail($email)) {
  $userId = $user->getId();
  error_log("The user $email was found ! Id : $userId");
  $password = '';
} else if ($registerUser) {
  $password = $shopOrderUtils->createRandomPassword();
  error_log("The user $email was not found and we created the password : $password");

  $telephone = $shopOrder->getTelephone();
  $mobilePhone = $shopOrder->getMobilePhone();

  $systemDateTime = $clockUtils->getSystemDate();

  $passwordSalt = LibUtils::generateUniqueId(USER_PASSWORD_SALT_LENGTH);
  $hashedPassword = md5($password . $passwordSalt);

  $user = new User();
  $user->setFirstname($firstname);
  $user->setLastname($lastname);
  $user->setEmail($email);
  $user->setHomePhone($telephone);
  $user->setMobilePhone($mobilePhone);
  $user->setPassword($hashedPassword);
  $user->setPasswordSalt($passwordSalt);
  $user->setCreationDateTime($systemDateTime);
  $userUtils->insert($user);
  $userId = $userUtils->getLastInsertId();
  error_log("Created a user with id $userId and email $email , firstname $firstname , lastname $lastname , telephone $telephone , mobilePhone $mobilePhone , password $password");
} else {
  $password = '';
  $userId = '';
}

// Register the user if any
if ($registerUser && $userId) {
  $shopOrder->setUserId($userId);
  $shopOrderUtils->update($shopOrder);
  error_log("Updated the order with the user");
}

// Create a one-time url for the link in the email
// Generate a unique token and keep it for later use
$tokenName = USER_TOKEN_NAME;
$tokenDuration = $userUtils->getLoginTokenDuration();
$tokenValue = $uniqueTokenUtils->create($tokenName, $tokenDuration);

// Send an email to the user
$emailSubject = "$websiteName $websiteText[5]";
$orderUrl = "$gShopUrl/order/display.php?shopOrderId=$paymentShopOrderId&tokenName=$tokenName&tokenValue=$tokenValue&email=$email";
$emailBody = "$websiteText[6] $firstname $lastname "
. "<br /><br />"
. $websiteText[7] . ' "' . "<a href='$orderUrl' $gJSNoStatus>" . $paymentShopOrderId . '</a>"'
. " [<a href='$orderUrl' $gJSNoStatus>$websiteText[15]</a>]"
. "<br />"
. $strOrderContent;
if ($password) {
  $emailBody .= "<br />"
    . $websiteText[12]
    . "<br /><br />"
    . $websiteText[13] . ' ' . $email
    . "<br /><br />"
    . $websiteText[14] . ' ' . $password
    . "<br /><br />"
    . $websiteText[3] . " <a href='$gUserUrl/login.php?email=$email' $gJSNoStatus>" . $websiteText[8] . '</a>'
    . "<br /><br />"
    . $websiteText[11] . " <a href='$gUserUrl/changePassword.php?email=$email' $gJSNoStatus>" . $websiteText[16] . '</a>';
}
$emailBody .= "<br /><br />"
. "$websiteText[9]"
. "<br /><br />"
. "$websiteText[10]"
. "<br /><br />"
. "$websiteName"
. "<br /><br />";

if (LibEmail::validate($email)) {
  error_log("Sending the mail to the user");
  LibEmail::sendMail($email, "$firstname $lastname", $emailSubject, $emailBody, $siteEmail, $websiteName);
}

// Empty the shopping cart after the order completion
$shopItemUtils->emptyCart();

?>
