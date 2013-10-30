<?PHP

$mlText = $languageUtils->getMlText(__FILE__);

// The different placements for the question hint
// These array values are used in a database table so do not modify them here!
$gElearningHintPlacements = array(
  '' => '',
  'ELEARNING_HINT_BEFORE' => $mlText[4],
  'ELEARNING_HINT_INSIDE' => $mlText[1],
  'ELEARNING_HINT_AFTER' => $mlText[0],
  'ELEARNING_HINT_END_OF_QUESTION' => $mlText[2],
  'ELEARNING_HINT_IN_POPUP' => $mlText[3],
  );

?>
