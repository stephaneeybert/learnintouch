<?PHP

require_once("website.php");

// The active admin language is used when creating preferences
// Therefore to retrieve the default value of a multi language preference it is
// necessary to first activate the admin language before loading anew the preferences

$resetMlTextPreference = LibEnv::getEnvHttpGET("resetMlTextPreference");
if ($resetMlTextPreference) {
  $languageCode = LibEnv::getEnvHttpGET("languageCode");
  if ($languageCode) {
    $languageUtils->setCurrentAdminLanguageCode($languageCode);
    }
  }

?>
