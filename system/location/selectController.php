<?PHP

$country = LibEnv::getEnvHttpPOST("country");
$region = LibEnv::getEnvHttpPOST("region");
$state = LibEnv::getEnvHttpPOST("state");
$zipCode = LibEnv::getEnvHttpPOST("zipCode");

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  if ($locationCountry = $locationCountryUtils->selectByCode($country)) {
    $countryName = $locationCountry->getName();
    $countryName = LibString::escapeQuotes($countryName);
  } else {
    $countryName = '';
  }

  if ($locationRegion = $locationRegionUtils->selectByCode($region)) {
    $regionName = $locationRegion->getName();
    $regionName = LibString::escapeQuotes($regionName);
  } else {
    $regionName = '';
  }

  if ($locationState = $locationStateUtils->selectByCode($state)) {
    $stateName = $locationState->getName();
    $stateName = LibString::escapeQuotes($stateName);
  } else {
    $stateName = '';
  }

  if ($locationZipCode = $locationZipCodeUtils->selectByCode($zipCode)) {
    $zipCodeName = $locationZipCode->getName();
    $zipCodeName = LibString::escapeQuotes($zipCodeName);
  } else {
    $zipCodeName = '';
  }

  $str = <<<HEREDOC
<script>
// Check if the form field exists before trying to update its value
// Otherwise the javascript stops dead on the first bump
if (window.opener.document.forms['edit'].elements['country']) {
  window.opener.document.forms['edit'].elements['country'].value = '$countryName';
  }
if (window.opener.document.forms['edit'].elements['region']) {
  window.opener.document.forms['edit'].elements['region'].value = '$regionName';
  }
if (window.opener.document.forms['edit'].elements['state']) {
  window.opener.document.forms['edit'].elements['state'].value = '$stateName';
  }
if (window.opener.document.forms['edit'].elements['zipCode']) {
  window.opener.document.forms['edit'].elements['zipCode'].value = '$zipCode';
  }
if (window.opener.document.forms['edit'].elements['city']) {
  window.opener.document.forms['edit'].elements['city'].value = '$zipCodeName';
  }
</script>
HEREDOC;

  printMessage($str);

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  exit;
}

?>
