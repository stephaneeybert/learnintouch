<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_CLIENT);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $clientId = LibEnv::getEnvHttpPOST("clientId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $url = LibEnv::getEnvHttpPOST("url");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $url = LibString::cleanString($url);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[4]);
  }

  // Validate the url
  if ($url && LibUtils::isInvalidUrl($url)) {
    array_push($warnings, $mlText[21]);
  }

  // Format the url
  $url = LibUtils::formatUrl($url);

  if (count($warnings) == 0) {

    if ($client = $clientUtils->selectById($clientId)) {
      $client->setName($name);
      $client->setDescription($description);
      $client->setUrl($url);
      $clientUtils->update($client);
    } else {
      $client = new Client();
      $client->setName($name);
      $client->setDescription($description);
      $client->setUrl($url);
      $clientUtils->insert($client);
    }

    $str = LibHtml::urlRedirect("$gClientUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $clientId = LibEnv::getEnvHttpGET("clientId");

  $name = '';
  $description = '';
  $url = '';
  if ($clientId) {
    if ($client = $clientUtils->selectById($clientId)) {
      $name = $client->getName();
      $description = $client->getDescription();
      $url = $client->getUrl();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gClientUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='description'  value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='url' value='$url' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('clientId', $clientId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
