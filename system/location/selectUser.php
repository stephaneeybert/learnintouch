<?PHP

require_once("website.php");

require_once($gLocationPath . "selectController.php");

$mlText = $languageUtils->getMlText(__FILE__);

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$mlText[0]</div>";

require_once($gLocationPath . "selectList.php");

$str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

$str .= "\n<tr valign=''>";
$str .= "\n<td class='system_label'>$mlText[6]</td>";
$str .= "\n<td class='system_field'>"
  . "<form action='$PHP_SELF' method='post'>"
  . $strSelectLocationCountry
  . "<input type='hidden' name='region' value='$region'>"
  . "<input type='hidden' name='state' value='$state'>"
  . "<input type=hidden name='zipCode' value='$zipCode'>"
  . "</form>"
  . "</td>";
$str .= "\n</tr>";

$str .= "\n<tr>";
$str .= "\n<td class='system_label'>$mlText[3]</td>";
$str .= "\n<td class='system_field'>"
  . "<form action='$PHP_SELF' method='post'>"
  . $strSelectLocationRegion
  . "<input type='hidden' name='country' value='$country'>"
  . "<input type='hidden' name='state' value='$state'>"
  . "<input type=hidden name='zipCode' value='$zipCode'>"
  . "</form>"
  . "</td>";
$str .= "\n</tr>";

$str .= "\n<tr>";
$str .= "\n<td class='system_label'>$mlText[4]</td>";
$str .= "\n<td class='system_field'>"
  . "<form action='$PHP_SELF' method='post'>"
  . $strSelectLocationState
  . "<input type='hidden' name='country' value='$country'>"
  . "<input type='hidden' name='region' value='$region'>"
  . "<input type=hidden name='zipCode' value='$zipCode'>"
  . "</form>"
  . "</td>";
$str .= "\n</tr>";

$str .= "\n<tr>";
$str .= "\n<td class='system_label'>$mlText[5]</td>";
$str .= "\n<td class='system_field'>"
  . "<form action='$PHP_SELF' method='post'>"
  . $strSelectLocationZipCode
  . "<input type='hidden' name='country' value='$country'>"
  . "<input type='hidden' name='region' value='$region'>"
  . "<input type='hidden' name='state' value='$state'>"
  . "</form>"
  . "</td>";
$str .= "\n</tr>";

$str .= "\n</table>";

$str .= "\n<form id='location' name='location' action='$PHP_SELF' method='post'>";

// An input field is required to have the browser submit the form on Enter key press
// Otherwise a form with more than one input field is not submitted
$str .= "<input type='submit' value='' style='display:none;' />"
  . "<a href='#' onclick=\"document.forms['location'].submit(); return false;\">" . $mlText[1] . "</a>";

$str .= "\n<input type=hidden name='formSubmitted' value='1'>";
$str .= "\n<input type=hidden name='country' value='$country'>";
$str .= "\n<input type=hidden name='region' value='$region'>";
$str .= "\n<input type=hidden name='state' value='$state'>";
$str .= "\n<input type=hidden name='zipCode' value='$zipCode'>";
$str .= "\n</form>";

$str .= "\n</div>";

print($templateUtils->renderPopup($str));

?>
