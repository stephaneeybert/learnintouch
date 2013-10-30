<?PHP

if (!$country) {
  $country = LibEnv::getEnvHttpGET("country");
}

$locationCountries = $locationCountryUtils->selectAll();
$locationCountryList = Array('-1' => '');
foreach ($locationCountries as $wLocationCountry) {
  $wLocationCountryCode = $wLocationCountry->getCode();
  $wLocationCountryName = $wLocationCountry->getName();
  $locationCountryList[$wLocationCountryCode] = $wLocationCountryName;
}
$strSelectLocationCountry = LibHtml::getSelectList("country", $locationCountryList, $country, true);

$locationRegions = $locationRegionUtils->selectByCountry($country);
$locationRegionList = Array('-1' => '');
foreach ($locationRegions as $wLocationRegion) {
  $wLocationRegionCode = $wLocationRegion->getCode();
  $wLocationRegionName = $wLocationRegion->getName();
  $locationRegionList[$wLocationRegionCode] = $wLocationRegionName;
}
$strSelectLocationRegion = LibHtml::getSelectList("region", $locationRegionList, $region, true);

$locationStates = $locationStateUtils->selectByRegion($region);
$locationStateList = Array('-1' => '');
foreach ($locationStates as $wLocationState) {
  $wLocationStateCode = $wLocationState->getCode();
  $wLocationStateName = $wLocationState->getName();
  $locationStateList[$wLocationStateCode] = "$wLocationStateCode $wLocationStateName";
}
$strSelectLocationState = LibHtml::getSelectList("state", $locationStateList, $state, true);

$locationZipCodes = $locationZipCodeUtils->selectByCountryAndState($country, $state);
$locationZipCodeList = Array('-1' => '');
foreach ($locationZipCodes as $wLocationZipCode) {
  $wLocationZipCodeCode = $wLocationZipCode->getCode();
  $wLocationZipCodeName = $wLocationZipCode->getName();
  $locationZipCodeList[$wLocationZipCodeCode] = "$wLocationZipCodeCode $wLocationZipCodeName";
}
$strSelectLocationZipCode = LibHtml::getSelectList("zipCode", $locationZipCodeList, $zipCode, true);

?>
