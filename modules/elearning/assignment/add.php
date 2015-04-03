<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
  $onlyOnce = LibEnv::getEnvHttpPOST("onlyOnce");
  $openingDate = LibEnv::getEnvHttpPOST("openingDate");
  $closingDate = LibEnv::getEnvHttpPOST("closingDate");
  $parentUrl = LibEnv::getEnvHttpPOST("parentUrl");

  $onlyOnce = LibString::cleanString($onlyOnce);
  $openingDate = LibString::cleanString($openingDate);
  $closingDate = LibString::cleanString($closingDate);

  // The exercise is required
  if (!$elearningExerciseId) {
    array_push($warnings, $mlText[4]);
  }

  // Validate the opening date
  if ($openingDate && !$clockUtils->isLocalNumericDateValid($openingDate)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  // Validate the closing date
  if ($closingDate && !$clockUtils->isLocalNumericDateValid($closingDate)) {
    array_push($warnings, $mlText[21] . ' ' . $clockUtils->getDateNumericFormatTip());
  }

  if ($openingDate) {
    $openingDate = $clockUtils->localToSystemDate($openingDate);
  }
  if ($closingDate) {
    $closingDate = $clockUtils->localToSystemDate($closingDate);
  }

  // The closing date must be after the opening date
  if ($closingDate && $openingDate && $clockUtils->systemDateIsGreater($openingDate, $closingDate)) {
    array_push($warnings, $mlText[34]);
  }

  if (count($warnings) == 0) {

    foreach ($_POST as $inputName => $inputValue) {
      if (strstr($inputName, 'subscriptionId_') && $inputValue == 1) {
        $elearningSubscriptionId = substr($inputName, 15, strlen($inputName) - 15);
        if (is_numeric($elearningSubscriptionId)) {
          // Check that the assignment is not already assigned to the participant
          if (!$elearningAssignment = $elearningAssignmentUtils->selectBySubscriptionIdAndExerciseId($elearningSubscriptionId, $elearningExerciseId)) {
            $elearningAssignment = new ElearningAssignment();
            $elearningAssignment->setElearningSubscriptionId($elearningSubscriptionId);
            $elearningAssignment->setElearningExerciseId($elearningExerciseId);
            $elearningAssignment->setOnlyOnce($onlyOnce);
            $elearningAssignment->setOpeningDate($openingDate);
            $elearningAssignment->setClosingDate($closingDate);
            $elearningAssignmentUtils->insert($elearningAssignment);
          }
        }
      }
    }
  
    $str = LibHtml::urlRedirect($parentUrl);
    printContent($str);
    return;

  }

} else {

  $elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");
  $elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");

  if (!$elearningExerciseId) {
    $elearningExerciseId = LibEnv::getEnvHttpPOST("elearningExerciseId");
  }

  $referer = LibEnv::getEnvSERVER('HTTP_REFERER');
  if (strstr($referer, "exercise/admin.php")) {
    $parentUrl = "$gElearningUrl/exercise/admin.php";
  } else if (strstr($referer, "lesson/admin.php")) {
    $parentUrl = "$gElearningUrl/lesson/admin.php";
  } else {
    $parentUrl = "$gElearningUrl/assignment/admin.php";
  }

}

$className = '';
if ($class = $elearningClassUtils->selectById($elearningClassId)) {
  $className = $class->getName();
}

$exerciseName = '';
if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {
  $exerciseName = $elearningExercise->getName();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], $parentUrl);
$help = $popupUtils->getHelpPopup($mlText[10], 300, 500);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/exercise/suggestExercises.php", "elearningExerciseName", "elearningExerciseId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningExerciseId', $elearningExerciseId);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' id='elearningExerciseName' value='$exerciseName' size='40' />");
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/subscription/suggest.php", "participantName", "elearningSubscriptionId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningSubscriptionId', '');
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' id='participantName' name='participantName' value='' />");
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/class/suggest.php", "className", "elearningClassId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' id='className' value='$className' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[6], $mlText[3], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, 'nbr'), "<div id='checkboxes' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='onlyOnce' value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[11], $mlText[12], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='openingDate' id='openingDate' value='' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[13], $mlText[14], 300, 500);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='closingDate' id='closingDate' value='' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('parentUrl', $parentUrl);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$strJsUpdateSubscriptions = <<<HEREDOC
<script type='text/javascript'>
$(document).ready(function() {

  function getSubscriptions() {
    var elearningSubscriptionId = $('#elearningSubscriptionId').val();
    var elearningClassId = $('#elearningClassId').val();
    var url = '$gElearningUrl/subscription/get_list.php?elearningSubscriptionId='+elearningSubscriptionId+'&elearningClassId='+elearningClassId;
    ajaxAsynchronousRequest(url, renderSubscriptions);
  }

  $('#elearningSubscriptionId').change(function() {
    getSubscriptions();
  });

  $('#elearningClassId').change(function() {
    getSubscriptions();
  });

  $("#okButton").click(function() {
    $('#checkboxes').find(".subscriptionId").each(function() {
      $('#edit').append($(this));
    });
    $('#checkboxes').empty();
  });

function renderSubscriptions(responseText) {
  var response = eval('(' + responseText + ')');
  var subscriptions = response.subscriptions;
  $('#checkboxes').empty();
  if (subscriptions.length > 0) {
    $('#checkboxes').append(
    $(document.createElement("div")).attr("select", false).html("$mlText[15]").click(function() {
      if ($(this).attr("select") == 'true') {
        $(this).attr("select", false);
        $(".subscriptionId").attr('checked', true);
        $(this).html("$mlText[15]");
      } else {
        $(this).attr("select", true);
        $(".subscriptionId").attr('checked', false);
        $(this).html("$mlText[7]");
      }
    })
    );
    for (var i in subscriptions) {
      var elearningSubscriptionId = subscriptions[i].elearningSubscriptionId;
      var firstname = subscriptions[i].firstname;
      var lastname = subscriptions[i].lastname;
      var courseName = subscriptions[i].courseName;
      if (elearningSubscriptionId > 0) {
        var name = firstname + ' ' + lastname;
        if (courseName) {
          name = name + ' : ' + courseName;
        }
        var divElement = $(document.createElement("div")).html(name).append(
          $(document.createElement("input")).addClass("subscriptionId").attr('name', 'subscriptionId_' + elearningSubscriptionId).attr('value', 1).attr('type', 'checkbox').attr('checked', true)
        );
        $('#checkboxes').append(divElement);
      }
    }
  }
}

});
</script>
HEREDOC;
$panelUtils->addContent($strJsUpdateSubscriptions);

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#openingDate").datepicker({ dateFormat:'mm/dd/yy' });
  $("#closingDate").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#openingDate").datepicker({ dateFormat:'dd-mm-yy' });
  $("#closingDate").datepicker({ dateFormat:'dd-mm-yy' });
});
</script>
HEREDOC;
}

$languageCode = $languageUtils->getCurrentAdminLanguageCode();
$code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $.datepicker.setDefaults($.datepicker.regional['$code']);
});
</script>
HEREDOC;
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
