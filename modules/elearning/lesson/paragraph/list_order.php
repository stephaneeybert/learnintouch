<?PHP

require_once("website.php");

LibHtml::preventCaching();


$paragraphIds = LibEnv::getEnvHttpPOST("paragraphIds");

$listOrder = 1;
foreach ($paragraphIds as $paragraphId) {
  if ($elearningLessonParagraph = $elearningLessonParagraphUtils->selectById($paragraphId)) {
    $elearningLessonParagraph->setListOrder($listOrder);
    $elearningLessonParagraphUtils->update($elearningLessonParagraph);
    $listOrder++;
  }
}

?>
