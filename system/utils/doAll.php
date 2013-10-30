<?PHP

require_once("website.php");

$adminUtils->checkForStaffLogin();


$currentLanguageCode = $languageUtils->getCurrentAdminLanguageCode();

$nbWebpages = 0;

$webpages = $dynpageUtils->selectAll();
foreach ($webpages as $webpage) {
  $content = $webpage->getContent();
  if (strstr($content, 'class="lexicon_entry')) {
    $updatedContent = addLexiconAttribute($content);
  if ($updatedContent && $updatedContent != $content) {
    $webpage->setContent($updatedContent);
    $dynpageUtils->update($webpage);
    $nbWebpages++;
  }
}
}

$nbElearningLessons = 0;

$elearningLessons = $elearningLessonUtils->selectAll();
foreach ($elearningLessons as $elearningLesson) {
  $content = $elearningLesson->getIntroduction();
  if (strstr($content, 'class="lexicon_entry')) {
    $updatedContent = addLexiconAttribute($content);
  if ($updatedContent && $updatedContent != $content) {
    $elearningLesson->setIntroduction($updatedContent);
    $elearningLessonUtils->update($elearningLesson);
    $nbElearningLessons++;
  }
}
}

$nbElearningLessonParagraphs = 0;

$elearningLessonParagraphs = $elearningLessonParagraphUtils->selectAll();
foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
  $content = $elearningLessonParagraph->getBody();
  if (strstr($content, 'class="lexicon_entry')) {
    $updatedContent = addLexiconAttribute($content);
  if ($updatedContent && $updatedContent != $content) {
    $elearningLessonParagraph->setBody($updatedContent);
    $elearningLessonParagraphUtils->update($elearningLessonParagraph);
    $nbElearningLessonParagraphs++;
  }
}
}

$nbElearningExercises = 0;

$elearningExercises = $elearningExerciseUtils->selectAll();
foreach ($elearningExercises as $elearningExercise) {
  $content = $elearningExercise->getIntroduction();
  if (strstr($content, 'class="lexicon_entry')) {
    $updatedContent = addLexiconAttribute($content);
  if ($updatedContent && $updatedContent != $content) {
    $elearningExercise->setIntroduction($updatedContent);
    $elearningExerciseUtils->update($elearningExercise);
    $nbElearningExercises++;
  }
}
}

$nbElearningExercisePages = 0;

$elearningExercisePages = $elearningExercisePageUtils->selectAll();
foreach ($elearningExercisePages as $elearningExercisePage) {
  $content = $elearningExercisePage->getText();
  if (strstr($content, 'class="lexicon_entry')) {
    $updatedContent = addLexiconAttribute($content);
  if ($updatedContent && $updatedContent != $content) {
    $elearningExercisePage->setText($updatedContent);
    $elearningExercisePageUtils->update($elearningExercisePage);
    $nbElearningExercisePages++;
  }
}
}

print("<br>nbWebpages: $nbWebpages");
print("<br>nbElearningLessons: $nbElearningLessons");
print("<br>nbElearningLessonParagraphs: $nbElearningLessonParagraphs");
print("<br>nbElearningExercises: $nbElearningExercises");
print("<br>nbElearningExercisePages: $nbElearningExercisePages");

function addLexiconAttribute($content) {
  if (preg_match_all("/(<span[^>]*class=\"lexicon_entry\") (id=\"lexicon_entry_dom_id_)([0-9]*)(_[a-zA-Z0-9]{3}\")[^>]*>/i", $content, $matches)) {
    $completes = $matches[0];
    $classes = $matches[1];
    $doms = $matches[2];
    $lexiconIds = $matches[3];
    $suffixes = $matches[4];
    if (count($completes) > 0) {
      for ($i = 0; $i < count($completes); $i++) {
        $complete = $completes[$i];
        if (!strstr($complete, "lexicon_entry_id")) {
          $class = $classes[$i];
          $dom = $doms[$i];
          $lexiconId = $lexiconIds[$i];
          $suffix = $suffixes[$i];
          error_log("pattern: " . $complete);
          error_log("replace: " . $class . " lexicon_entry_id=\"$lexiconId\" " . $dom . $lexiconId . $suffix . '>');
          error_log('');
          $content = str_replace($complete, $class . " lexicon_entry_id=\"$lexiconId\" " . $dom . $lexiconId . $suffix . '>', $content);
        }
      }
    }
  }

  if (preg_match_all("/(<span[^>]*)(id=\"lexicon_entry_dom_id_)([0-9]*)(_[a-zA-Z0-9]{3}\")[^>]*(class=\"lexicon_entry\")[^>]*>/i", $content, $matches)) {
    $completes = $matches[0];
    $spans = $matches[1];
    $doms = $matches[2];
    $lexiconIds = $matches[3];
    $suffixes = $matches[4];
    $classes = $matches[5];
    if (count($completes) > 0) {
      for ($i = 0; $i < count($completes); $i++) {
        $complete = $completes[$i];
        if (!strstr($complete, "lexicon_entry_id")) {
          $span = $spans[$i];
          $dom = $doms[$i];
          $lexiconId = $lexiconIds[$i];
          $suffix = $suffixes[$i];
          $class = $classes[$i];
          error_log("pattern: " . $complete);
          error_log("replace: " . $span . " lexicon_entry_id=\"$lexiconId\" " . $dom . $lexiconId . $suffix . $class . '>');
          error_log('');
          $content = str_replace($complete, $span . $class . " lexicon_entry_id=\"$lexiconId\" " . $dom . $lexiconId . $suffix . '>', $content);
        }
      }
    }
  }

  return($content);
}

?>
