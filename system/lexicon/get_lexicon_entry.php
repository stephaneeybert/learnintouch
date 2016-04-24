<?PHP

require_once("website.php");

LibHtml::preventCaching();

$lexiconEntryId = LibEnv::getEnvHttpGET("lexiconEntryId");

if (!$lexiconEntryId) {
  return;
}

if ($lexiconEntry = $lexiconEntryUtils->selectById($lexiconEntryId)) {
  $name = $lexiconEntry->getName();
  $explanation = $lexiconEntry->getExplanation();
  $name = LibString::jsonEscapeLinebreak($name);
  $explanation = LibString::jsonEscapeLinebreak($explanation);
  $name = LibString::escapeDoubleQuotes($name);
  $explanation = LibString::escapeDoubleQuotes($explanation);
  $image = $lexiconEntry->getImage();
  if ($image) {
    $explanation .= '<div>' . $lexiconEntryUtils->renderImage($image) . '</div>';
  }

  $responseText = "<span class='lexicon_entry_name'>" . $name . '</span> : ' . "<span class='lexicon_entry_explanation'>" . $explanation . '</span>';
} else {
  $responseText = '';
}

print($responseText);

?>
