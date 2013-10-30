<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");

  // The course is required
  if (!$elearningCourseId) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    foreach ($_POST as $inputName => $inputValue) {
      if (strstr($inputName, 'subscriptionId_') && $inputValue == 1) {
        $elearningSubscriptionId = substr($inputName, 15, strlen($inputName) - 15);
        if (is_numeric($elearningSubscriptionId)) {
          if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
            $elearningSubscription->setCourseId($elearningCourseId);
            $elearningSubscriptionUtils->insert($elearningSubscription);
          }
        }
      }
    }
  
    $str = LibHtml::urlRedirect("$gElearningUrl/subscription/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");
  $elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");

  if (!$elearningCourseId) {
    $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  }

  if (!$elearningClassId) {
    $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
  }

}

$className = '';
if ($class = $elearningClassUtils->selectById($elearningClassId)) {
  $className = $class->getName();
}

$courseName = '';
if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
  $courseName = $elearningCourse->getName();
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/subscription/admin.php");
$help = $popupUtils->getHelpPopup($mlText[10], 300, 500);
$panelUtils->setHelp($help);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/course/suggest.php", "courseName", "elearningCourseId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' id='courseName' value='$courseName' size='40' />");
$panelUtils->addLine();
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gElearningUrl/class/suggest.php", "className", "elearningClassId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('elearningClassId', $elearningClassId);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' id='className' value='$className' />");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[6], $mlText[3], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, 'nbr'), "<div id='checkboxes' />");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$strJsUpdateSubscriptions = <<<HEREDOC
<script type='text/javascript'>
$(document).ready(function() {

  function getSubscriptions() {
    var elearningClassId = $('#elearningClassId').val();
    var url = '$gElearningUrl/subscription/get_list.php?elearningClassId='+elearningClassId;
    ajaxAsynchronousRequest(url, renderSubscriptions);
  }

  getSubscriptions();

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
