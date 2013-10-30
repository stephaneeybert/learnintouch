<?PHP

require_once("website.php");

LibHtml::preventCaching();


$paragraphIds = LibEnv::getEnvHttpPOST("paragraphIds");

$listOrder = 1;
foreach ($paragraphIds as $paragraphId) {
  // An ajax request parameter value is UTF-8 encoded
  $paragraphId = utf8_decode($paragraphId);

  if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($paragraphId)) {
    $elearningLessonParagraph->setListOrder($listOrder);
    $elearningLessonParagraphUtils->update($elearningLessonParagraph);
    $listOrder++;
  }
}

?>
