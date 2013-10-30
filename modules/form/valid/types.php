<?PHP

$mlText = $languageUtils->getMlText(__FILE__);

// The types of form items
// These array values are used in a database table so do not modify them here!
$gFormValidTypes = array(
  'FORM_VALID_NOT_EMPTY' => $mlText[0],
  'FORM_VALID_MAXLENGTH' => $mlText[1],
  'FORM_VALID_MINLENGTH' => $mlText[2],
  'FORM_VALID_MAXVALUE' => $mlText[3],
  'FORM_VALID_MINVALUE' => $mlText[4],
  'FORM_VALID_EMAIL' => $mlText[5],
  'FORM_VALID_BANKCARD' => $mlText[6],
  'FORM_VALID_IS_DATE' => $mlText[7],
  'FORM_VALID_IS_NUMERIC' => $mlText[8],
  );

?>
