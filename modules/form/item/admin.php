<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);

$formId = LibEnv::getEnvHttpGET("formId");

if (!$formId) {
  $formId = LibSession::getSessionValue(FORM_SESSION_FORM);
} else {
  LibSession::putSessionValue(FORM_SESSION_FORM, $formId);
}

$formName = '';
if ($form = $formUtils->selectById($formId)) {
  $formName = $form->getName();
}

$panelUtils->setHeader($mlText[0], "$gFormUrl/admin.php");
$help = $popupUtils->getHelpPopup($mlText[11], 300, 200);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gFormUrl/item/edit.php?formId=$formId' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>"
  . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePreview' title='$mlText[12]'>", "$gFormUrl/preview.php?formId=$formId", 600, 600);
$panelUtils->addLine($panelUtils->addCell("$mlText[10]", "rb"), $formName, '', '', '', '');
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell("$mlText[5]", "nb"), $panelUtils->addCell("$mlText[9]", "nb"), $panelUtils->addCell("$mlText[4]", "nb"), $panelUtils->addCell("$mlText[6]", "nb"), $panelUtils->addCell("$mlText[7]", "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$currentLanguageCode = $languageUtils->getCurrentAdminLanguageCode();

$formItems = $formItemUtils->selectByFormId($formId);

$sortableLinesClass = 'sortableLines';
$strSortableLines = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
  $("tbody .$sortableLinesClass").sortable({
    cursor: 'move',
    update: function(ev, ui) {
      var sortableItemIds = [];
      $("tbody .$sortableLinesClass .sortableItem").each(function(index){
        var sortableItemId = $(this).attr("sortableItemId");
        sortableItemIds.push(sortableItemId);
      });
      $.post('$gFormUrl/item/list_order.php', {'formItemIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$panelUtils->openList($sortableLinesClass);
foreach ($formItems as $formItem) {
  $formItemId = $formItem->getId();
  $type = $formItem->getType();
  $name = $formItem->getName();

  $text = '';
  $languages = $languageUtils->getActiveLanguages();
  foreach ($languages as $language) {
    $languageId = $language->getId();
    $languageCode = $language->getCode();
    $strImage = $languageUtils->renderImage($languageId);
    $text .= '<div>' . $strImage . ' : ' . $languageUtils->getTextForLanguage($formItem->getText(), $languageCode) . '</div>';
  }

  $help = $formItem->getHelp();
  $defaultValue = $formItem->getDefaultValue();

  $typeName = $gFormItemTypes[$type];

  $strSortable = "<span class='sortableItem' sortableItemId='$formItemId'></span>";

  $strSwap = "<a href='$gFormUrl/item/swapup.php?formItemId=$formItemId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[31]'></a>"
    . " <a href='$gFormUrl/item/swapdown.php?formItemId=$formItemId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[30]'></a>";

  $strCommand = "<a href='$gFormUrl/item/edit.php?formItemId=$formItemId&formId=$formId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gFormUrl/valid/admin.php?formItemId=$formItemId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageList' title='$mlText[8]'></a>"
    . " <a href='$gFormUrl/item/delete.php?formItemId=$formItemId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine("$strSortable $strSwap $name", $text, $help, $typeName, $defaultValue, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
