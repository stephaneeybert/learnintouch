<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

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

  if (count($warnings) == 0) {

    // Get the data from the file
    $lines = file($userfile);

    // Guess the separator character
    if (strstr($lines[0], ",")) {
      $separator = ",";
    } else if (strstr($lines[0], ";")) {
      $separator = ";";
    } else if (strstr($lines[0], "/")) {
      $separator = "/";
    } else {
      $separator = ",";
    }

    // Create the sms addresses
    $listIsDeleted = false;
    $incorrectMobilePhones = array();
    foreach ($lines as $line) {
      // If there is a separator character then assume the mobile phone number is in
      // the first field, otherwise the mobile phone number is the line
      if ($separator && strstr($line, $separator)) {
        $fields = explode($separator, $line);
        $mobilePhone = strtolower(LibString::stripNonNumbers($fields[0]));
        if (count($fields) > 1) {
          $firstname = ucfirst(trim($fields[1]));
          if (count($fields) > 2) {
            $lastname = ucfirst(trim($fields[2]));
          } else {
            $lastname = '';
          }
        } else {
          $firstname = '';
        }
      } else {
        $mobilePhone = strtolower(LibString::stripNonNumbers($line));
        $firstname = '';
        $lastname = '';
      }

      if (is_numeric($mobilePhone)) {
        if (!$smsNumber = $smsNumberUtils->selectByMobilePhone($mobilePhone)) {
          // Empty the previous list
          // only if at least one had been imported
          // The previous list is deleted here in the loop because it must not
          // be deleted if no import has taken place
          if (!$listIsDeleted) {
            $smsNumberUtils->resetImported();
            $listIsDeleted = true;
          }

          $smsNumber = new SmsNumber();
          $smsNumber->setMobilePhone($mobilePhone);
          $smsNumber->setFirstname($firstname);
          $smsNumber->setLastname($lastname);
          $smsNumber->setSubscribe(1);
          $smsNumber->setImported(1);
          $smsNumberUtils->insert($smsNumber);
        }
      } else {
        array_push($incorrectMobilePhones, $mobilePhone);
      }
    }

    // Display the incorrect mobile phone numbers if any
    if (count($incorrectMobilePhones) > 0) {
      $str = "<br>$mlText[8]";
      foreach ($incorrectMobilePhones as $mobilePhone) {
        $str .= "<br>$mobilePhone";
      }

      $strSubject = $mlText[3];
      $websiteEmail = $profileUtils->getProfileValue("website.email");
      $websiteName = $profileUtils->getProfileValue("website.name");
      if (!$websiteName) {
        $websiteName = $websiteEmail;
      }
      if ($websiteEmail) {
        LibEmail::sendMail($websiteEmail, $websiteName, $strSubject, $str, $websiteEmail, $websiteName);
      }
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
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
