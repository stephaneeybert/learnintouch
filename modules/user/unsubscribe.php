<?php

require_once("website.php");

$mlText = $languageUtils->getWebsiteText(__FILE__);

$email = LibEnv::getEnvHttpGET("email");

// Update the user profile
if ($user = $userUtils->selectByEmail($email)) {
  $user->setMailSubscribe(false);
  $userUtils->update($user);
  $userId = $user->getId();

  // Delete the user from the mail lists if any
  if ($mailListUsers = $mailListUserUtils->selectByUserId($userId)) {
    foreach ($mailListUsers as $mailListUser) {
      $mailListUserId = $mailListUser->getId();
      $mailListUserUtils->delete($mailListUserId);
      }
    }
  }

// Update the mail address lists if any
if ($mailAddress = $mailAddressUtils->selectByEmail($email)) {
  // Update the mail address
  $mailAddressId = $mailAddress->getId();
  $mailAddress->setSubscribe(false);
  $mailAddressUtils->update($mailAddress);

  // Delete the mail address from the mail lists if any
  if ($mailListAddresses = $mailListAddressUtils->selectByMailAddressId($mailAddressId)) {
    foreach ($mailListAddresses as $mailListAddress) {
      $mailListAddressId = $mailListAddress->getId();
      $mailListAddressUtils->delete($mailListAddressId);
      }
    }
  }

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$mlText[0]</div>";

$str .= "\n<br>";

$str .= "\n<div class='system_comment'>$mlText[1]</div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
