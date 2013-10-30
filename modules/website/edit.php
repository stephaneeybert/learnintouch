<?PHP

require_once("website.php");

$adminUtils->checkForStaffLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $websiteId = LibEnv::getEnvHttpPOST("websiteId");
  $name = LibEnv::getEnvHttpPOST("name");
  $systemName = LibEnv::getEnvHttpPOST("systemName");
  $dbName = LibEnv::getEnvHttpPOST("dbName");
  $domainName = LibEnv::getEnvHttpPOST("domainName");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $email = LibEnv::getEnvHttpPOST("email");
  $diskSpace = LibEnv::getEnvHttpPOST("diskSpace");
  $package = LibEnv::getEnvHttpPOST("package");

  $name = LibString::cleanString($name);
  $systemName = LibString::cleanString($systemName);
  $dbName = LibString::cleanString($dbName);
  $domainName = LibString::cleanString($domainName);
  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $email = LibString::cleanString($email);
  $diskSpace = LibString::cleanString($diskSpace);
  $package = LibString::cleanString($package);

  // The site name is required
  if (!$name) {
    array_push($warnings, $mlText[14]);
  }

  // The system name is required
  if (!$systemName) {
    array_push($warnings, $mlText[6]);
  }

  $systemName = LibString::wordSubtract($systemName, 1);

  // The database name is required
  if (!$dbName) {
    array_push($warnings, $mlText[7]);
  }

  // The domain name is required
  if (!$domainName) {
    array_push($warnings, $mlText[12]);
  }

  // The email must have an email format
  if ($email && !LibEmail::validate($email)) {
    array_push($warnings, $mlText[13]);
  }

  // Format the domain name
  // The sub domains are forbidden except for ...
  if (!strstr($domainName, "thalasoft")) {
    $domainName = LibUtils::formatUrl($domainName);
  }

  // Create the account directories and setup files
  $accountPath = $gAccountPath . '/';
  if (!@is_dir($accountPath)) {
  }

  if (count($warnings) == 0) {

    if ($website = $websiteUtils->selectById($websiteId)) {
      $website->setName($name);
      $website->setSystemName($systemName);
      $website->setDbName($dbName);
      $website->setDomainName($domainName);
      $website->setFirstname($firstname);
      $website->setLastname($lastname);
      $website->setEmail($email);
      $website->setDiskSpace($diskSpace);
      $website->setPackage($package);
      $websiteUtils->update($website);
    } else {
      $website = new Website();
      $website->setName($name);
      $website->setSystemName($systemName);
      $website->setDbName($dbName);
      $website->setDomainName($domainName);
      $website->setFirstname($firstname);
      $website->setLastname($lastname);
      $website->setEmail($email);
      $website->setDiskSpace($diskSpace);
      $website->setPackage($package);
      $websiteUtils->insert($website);
    }

    $str = LibHtml::urlRedirect("$gWebsiteUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $websiteId = LibEnv::getEnvHttpGET("websiteId");

  $name = '';
  $systemName = '';
  $dbName = '';
  $domainName = '';
  $firstname = '';
  $lastname = '';
  $email = '';
  $diskSpace = '';
  $package = '';
  if ($websiteId) {
    if ($website = $websiteUtils->selectById($websiteId)) {
      $name = $website->getName();
      $systemName = $website->getSystemName();
      $dbName = $website->getDbName();
      $domainName = $website->getDomainName();
      $firstname = $website->getFirstname();
      $lastname = $website->getLastname();
      $email = $website->getEmail();
      $diskSpace = $website->getDiskSpace();
      $package = $website->getPackage();
    }
  }

  // Set a default disk space value
  if ($diskSpace == 0) {
    $diskSpace = 100;
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$packages = $websiteUtils->getPackageNames();
$strSelectPackage = LibHtml::getSelectList("package", $packages, $package);

$panelUtils->setHeader($mlText[0], "$gWebsiteUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[10], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='firstname' value='$firstname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='lastname' value='$lastname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='email' value='$email' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "<input type='text' name='domainName' value='$domainName' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strSelectPackage);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[15], "nbr"), "<input type='text' name='diskSpace' value='$diskSpace' size='4' maxlength='4'> $mlText[16]");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), "<input type='text' name='systemName' value='$systemName' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='dbName' value='$dbName' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('websiteId', $websiteId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
