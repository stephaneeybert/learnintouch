<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_USER);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  // Get the file characteristics
  // Note how the form parameter "userfile" creates several variables
  $uploaded_file = LibEnv::getEnvHttpFILE("userfile");
  $userfile = $uploaded_file['tmp_name'];
  $userfile_name = $uploaded_file['name'];
  $userfile_type = $uploaded_file['type'];
  $userfile_size = $uploaded_file['size'];

  // Check if a file has been specified...
  $fileUploadUtils->loadLanguageTexts();
  if ($str = $fileUploadUtils->checkFileName($userfile_name)) {
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->checkCSVFileType($userfile_name)) {
    // Check if the csv file name has a correct file type
    array_push($warnings, $str);
  }

  if ($userfile) {

    // Get the users data from the file
    $lines = file($userfile);

    // Guess the separator character
    if (strstr($lines[0], ",")) {
      $separator = ",";
    } else if (strstr($lines[0], ";")) {
      $separator = ";";
    } else if (strstr($lines[0], "/")) {
      $separator = "/";
    } else {
      array_push($warnings, $mlText[4]);
    }

    if (count($warnings) == 0) {

      // Create the users
      $listIsDeleted = false;
      $incorrectEmails = array();
      $existingEmails = array();

      // Create a mailing list containing all the imported users
      $systemDate = $clockUtils->getSystemDate();
      $systemTimestamp = $clockUtils->getSystemTimestamp();
      $localDate = $clockUtils->systemToLocalDate($systemDate);
      $localDate .= ' ' . $mlText[6] . ' ' . $clockUtils->timeStampToLocalTime($systemTimestamp);
      $strImportedOn = $mlText[7] . ' ' . $localDate;
      $mailList = new MailList();
      $mailList->setName($strImportedOn);
      $mailListUtils->insert($mailList);
      $mailListId = $mailListUtils->getLastInsertId();

      // Create an sms list containing all the imported users
      $smsList = new SmsList();
      $smsList->setName($strImportedOn);
      $smsListUtils->insert($smsList);
      $smsListId = $smsListUtils->getLastInsertId();

      foreach ($lines as $line) {
        // Check for the separator character
        if (!strstr($line, $separator)) {
          continue;
        }

        $fields = explode($separator, $line);
        if (count($fields) < 3) {
          continue;
        }

        $email = strtolower(trim($fields[0]));
        $firstname = ucfirst(trim($fields[1]));
        $lastname = ucfirst(trim($fields[2]));
        if (isset($fields[3])) {
          $organisation = ucfirst(trim($fields[3]));
        } else {
          $organisation = '';
        }
        if (isset($fields[4])) {
          $country = ucfirst(trim($fields[4]));
        } else {
          $country = '';
        }

        $passwordSalt = LibUtils::generateUniqueId(USER_PASSWORD_SALT_LENGTH);
        $password = LibUtils::generateUniqueId();
        $hashedPassword = md5($password . $passwordSalt);
        $subscribe = 1;

        if (LibEmail::validate($email)) {
          if (!$user = $userUtils->selectByEmail($email)) {
            // Empty the previous list of imported users
            // only if at least one user has been imported
            // The previous list is deleted here in the loop because it must not
            // be deleted if no import has taken place
            if (!$listIsDeleted) {
              $userUtils->resetImported();
              $listIsDeleted = true;
            }

            $firstname = LibString::databaseEscapeQuotes($firstname);
            $lastname = LibString::databaseEscapeQuotes($lastname);
            $organisation = LibString::databaseEscapeQuotes($organisation);
            $country = LibString::databaseEscapeQuotes($country);
            $email = LibString::databaseEscapeQuotes($email);

            $user = new User();
            $user->setImported(true);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setOrganisation($organisation);
            $user->setEmail($email);
            $user->setPassword($hashedPassword);
            $user->setPasswordSalt($passwordSalt);
            // The password is also stored in a readable format (non encrypted)
            // so that it'll be possible to mail it later to the user
            // The readable password is then removed at the first user login
            $user->setReadablePassword($password);
            $user->setMailSubscribe($subscribe);
            $systemDateTime = $clockUtils->getSystemDateTime();
            $user->setCreationDateTime($systemDateTime);

            if ($country) {
              $address = new Address();
              $address->setCountry($country);
              $addressUtils->insert($address);
              $addressId = $addressUtils->getLastInsertId();
              $user->setAddressId($addressId);
            }

            $userUtils->insert($user);
            $userId = $userUtils->getLastInsertId();

            // Add the user to the mailing list
            if (!$mailListUser = $mailListUserUtils->selectByMailListIdAndUserId($mailListId, $userId)) {
              $mailListUser = new MailListUser();
              $mailListUser->setUserId($userId);
              $mailListUser->setMailListId($mailListId);
              $mailListUserUtils->insert($mailListUser);
            }

            // Add the user to the sms list
            if (!$smsListUser = $smsListUserUtils->selectBySmsListIdAndUserId($smsListId, $userId)) {
              $smsListUser = new SmsListUser();
              $smsListUser->setUserId($userId);
              $smsListUser->setSmsListId($smsListId);
              $smsListUserUtils->insert($smsListUser);
            }
          } else {
            array_push($existingEmails, $email);
          }
        } else {
          array_push($incorrectEmails, $email);
        }
      }

      // Report about the incorrect emails if any
      if (count($incorrectEmails) > 0) {
        $strSubject = $mlText[3];
        $websiteEmail = $profileUtils->getProfileValue("website.email");
        $websiteName = $profileUtils->getProfileValue("website.name");

        $str = "<br>$mlText[8]<br>";
        foreach ($incorrectEmails as $email) {
          $str .= "<br>$email";
        }
        $str .= '<br><br>' . $mlText[11] . ' ' . count($incorrectEmails);

        if ($websiteEmail) {
          LibEmail::sendMail($websiteEmail, $websiteName, $strSubject, $str, $websiteEmail, $websiteName);
        }
      }

      // Report about the existing emails if any
      if (count($existingEmails) > 0) {
        $strSubject = $mlText[9];
        $websiteEmail = $profileUtils->getProfileValue("website.email");
        $websiteName = $profileUtils->getProfileValue("website.name");

        $str = "<br>$mlText[10]<br>";
        foreach ($existingEmails as $email) {
          $str .= "<br>$email";
        }
        $str .= '<br><br>' . $mlText[11] . ' ' . count($existingEmails);

        if ($websiteEmail) {
          LibEmail::sendMail($websiteEmail, $websiteName, $strSubject, $str, $websiteEmail, $websiteName);
        }
      }

      $str = LibJavascript::autoCloseWindow();
      printContent($str);
      return;
    }
  }

}

$fileUploadUtils->loadLanguageTexts();
$maxFileSize = $fileUploadUtils->maximumFileSize;

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$help = $popupUtils->getHelpPopup($mlText[1], 300, 500);
$panelUtils->setHelp($help);
$panelUtils->openMultipartForm($PHP_SELF);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nbr"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('max_file_size', $maxFileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
