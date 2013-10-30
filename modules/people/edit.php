<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PEOPLE);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $peopleId = LibEnv::getEnvHttpPOST("peopleId");
  $firstname = LibEnv::getEnvHttpPOST("firstname");
  $lastname = LibEnv::getEnvHttpPOST("lastname");
  $email = LibEnv::getEnvHttpPOST("email");
  $workPhone = LibEnv::getEnvHttpPOST("workPhone");
  $mobilePhone = LibEnv::getEnvHttpPOST("mobilePhone");
  $profile = LibEnv::getEnvHttpPOST("profile");
  $categoryId = LibEnv::getEnvHttpPOST("categoryId");

  $firstname = LibString::cleanString($firstname);
  $lastname = LibString::cleanString($lastname);
  $email = LibString::cleanString($email);
  $workPhone = LibString::cleanString($workPhone);
  $mobilePhone = LibString::cleanString($mobilePhone);
  $profile = LibString::cleanString($profile);
  $categoryId = LibString::cleanString($categoryId);

  // The firstname is required
  if (!$firstname) {
    array_push($warnings, $mlText[39]);
  }

  // The lastname is required
  if (!$lastname) {
    array_push($warnings, $mlText[40]);
  }

  // The email must have an email format
  if ($email && !LibEmail::validate($email)) {
    array_push($warnings, $mlText[38]);
  }

  // The email is case insensitive
  $email = strtolower($email);

  if (count($warnings) == 0) {

    if ($people = $peopleUtils->selectById($peopleId)) {
      $people->setFirstname($firstname);
      $people->setLastname($lastname);
      $people->setEmail($email);
      $people->setWorkPhone($workPhone);
      $people->setMobilePhone($mobilePhone);
      $people->setProfile($profile);
      $people->setCategoryId($categoryId);
      $peopleUtils->update($people);
    } else {
      $people = new People();
      $people->setFirstname($firstname);
      $people->setLastname($lastname);
      $people->setEmail($email);
      $people->setWorkPhone($workPhone);
      $people->setMobilePhone($mobilePhone);
      $people->setProfile($profile);
      $people->setCategoryId($categoryId);
      $peopleUtils->insert($people);
      $peopleId = $peopleUtils->getLastInsertId();
    }

    $str = LibHtml::urlRedirect("$gPeopleUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $peopleId = LibEnv::getEnvHttpGET("peopleId");

  $firstname = '';
  $lastname = '';
  $email = '';
  $workPhone = '';
  $mobilePhone = '';
  $profile = '';
  if ($peopleId) {
    if ($people = $peopleUtils->selectById($peopleId)) {
      $firstname = $people->getFirstname();
      $lastname = $people->getLastname();
      $email = $people->getEmail();
      $workPhone = $people->getWorkPhone();
      $mobilePhone = $people->getMobilePhone();
      $profile = $people->getProfile();
    }
  }

}

$categoryId = LibSession::getSessionValue(PEOPLE_SESSION_CATEGORY);

$peopleCats = $peopleCategoryUtils->selectAll();
$catList = Array('' => '');
foreach ($peopleCats as $peopleCat) {
  $wCatId = $peopleCat->getId();
  $wName = $peopleCat->getName();
  $catList[$wCatId] = $wName;
}
$strSelect = LibHtml::getSelectList("categoryId", $catList, $categoryId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gPeopleUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strSelect);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='firstname'  value='$firstname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='lastname' value='$lastname' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='email' value='$email' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='workPhone' value='$workPhone' size='20' maxlength='20'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='mobilePhone' value='$mobilePhone' size='20' maxlength='20'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), "<textarea name='profile' cols='28' rows='7'>$profile</textarea>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('peopleId', $peopleId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
