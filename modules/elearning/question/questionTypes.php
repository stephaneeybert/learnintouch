<?PHP

$mlText = $languageUtils->getMlText(__FILE__);

// The types of questions
// These array values are used in a database table so do not modify them here!
$gElearningQuestionTypes = array(
  'SELECT_LIST' => $mlText[0],
  'RADIO_BUTTON_LIST_H' => $mlText[1],
  'RADIO_BUTTON_LIST_V' => $mlText[5],
  'SOME_CHECKBOXES' => $mlText[4],
  'ALL_CHECKBOXES' => $mlText[2],
  'WRITE_IN_QUESTION' => $mlText[3],
  'SELECT_LIST_IN_TEXT' => $mlText[11],
  'WRITE_IN_TEXT' => $mlText[10],
  'WRITE_TEXT' => $mlText[13],
  'DRAG_ANSWER_IN_QUESTION' => $mlText[6],
  'DRAG_ANSWER_IN_ANY_QUESTION' => $mlText[7],
  'DRAG_ANSWERS_UNDER_ANY_QUESTION' => $mlText[12],
  'DRAG_ORDER_SENTENCE' => $mlText[8],
  'DRAG_ANSWER_IN_TEXT_HOLE' => $mlText[9],
);

?>
