<?php

$languageCode = $languageUtils->getCurrentAdminLanguageCode();

// Hack to work around the fact that InnovaStudio does not use standard language names
if ($languageCode == 'dk') {
  $languageName = 'da-DK';
  } else if ($languageCode == 'nl') {
  $languageName = 'nl-NL';
  } else if ($languageCode == 'fi') {
  $languageName = 'fi-FI';
  } else if ($languageCode == 'fr') {
  $languageName = 'fr-FR';
  } else if ($languageCode == 'de') {
  $languageName = 'de-DE';
  } else if ($languageCode == 'it') {
  $languageName = 'it-IT';
  } else if ($languageCode == 'no') {
  $languageName = 'nn-NO';
  } else if ($languageCode == 'Chinese (Simplified)') {
  $languageName = 'zh-CHS';
  } else if ($languageCode == 'Chinese (Traditional)') {
  $languageName = 'zh-CHT';
  } else if ($languageCode == 'sp') {
  $languageName = 'es-ES';
  } else if ($languageCode == 'se') {
  $languageName = 'sv-SE';
  } else {
  $languageName = 'en-US';
  }

$gInnovaHead = <<<HEREDOC
<script language="Javascript" type="text/javascript" src="$gJsUrl/editor/innova_3.6/scripts/language/$languageName/editor_lang.js"></script>
<script language="javascript" type="text/javascript" src="$gJsUrl/editor/innova_3.6/scripts/innovaeditor.js"></script>
HEREDOC;

$gInnovaJS = <<<HEREDOC
<script type='text/javascript'>
// Ping the server every 20 minutes to avoid a session time out
window.setInterval("ajaxAsynchronousRequest('$gUtilsUrl/ping.php', '')", 1200000);
</script>
HEREDOC;

?>
