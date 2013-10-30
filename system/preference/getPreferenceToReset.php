<?PHP


LibHtml::preventCaching();

$languageCode = LibEnv::getEnvHttpGET("languageCode");

// An ajax request parameter value is UTF-8 encoded
$languageCode = utf8_decode($languageCode);

?>
