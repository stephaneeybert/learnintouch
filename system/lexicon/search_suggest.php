<?PHP

require_once("website.php");

LibHtml::preventCaching();

$searchText = LibEnv::getEnvHttpGET("searchText");
$lexiconSearchId = LibEnv::getEnvHttpGET("lexiconSearchId");

if (!$searchText) {
  return;
}

// Ajax treats its data as UTF-8
$searchText = utf8_decode($searchText);

// The name is stored in the database in a html encoded format
$searchText = LibString::cleanString($searchText);
$lexiconSearchId = LibString::cleanString($lexiconSearchId);

if (strlen($searchText) > 2) {
  $lexiconEntries = $lexiconEntryUtils->selectLikePattern($searchText);
} else {
  $lexiconEntries = array();
}

$content = '';
$entries = '';

if (is_array($lexiconEntries)) {
  foreach ($lexiconEntries as $lexiconEntry) {
    $lexiconEntryId = $lexiconEntry->getId();
    $name = $lexiconEntry->getName();
    $explanation = $lexiconEntry->getExplanation();
    $name = LibString::decodeHtmlspecialchars($name);
    $explanation = LibString::decodeHtmlspecialchars($explanation);
    $explanation = LibString::jsonEscapeLinebreak($explanation);
    $explanation = LibString::escapeDoubleQuotes($explanation);
    $content .= "<div class='lexicon_entry'><span class='lexicon_entry_name'>" . $name . '</span> : ' . "<span class='lexicon_entry_explanation'>" . $explanation . '</span>';
    $image = $lexiconEntry->getImage();
    if ($image) {
      $content .= '<div>' . $lexiconEntryUtils->renderImage($image) . '</div>';
    }
    $content .= '</div>';
    $entries .= "{lexiconEntryId : \"$lexiconEntryId\", name : \"$name\", explanation : \"$explanation\", image : \"$image\"},";
  }
}

$entries = substr($entries, 0, strlen($entries) - 1);

$responseText = <<<HEREDOC
{
"lexiconSearchId" : "$lexiconSearchId",
"content" : "$content",
"entries" : [ $entries ]
}
HEREDOC;

print($responseText);

?>
