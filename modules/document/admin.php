<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

$mlText = $languageUtils->getMlText(__FILE__);

$categoryId = LibEnv::getEnvHttpPOST("categoryId");

if (!$categoryId) {
  $categoryId = LibSession::getSessionValue(DOCUMENT_SESSION_CATEGORY);
} else {
  LibSession::putSessionValue(DOCUMENT_SESSION_CATEGORY, $categoryId);
}

$categorys = $documentCategoryUtils->selectAll();
$categoryList = Array('-1' => '');
foreach ($categorys as $category) {
  $wDocumentCategoryId = $category->getId();
  $wName = $category->getName();
  $categoryList[$wDocumentCategoryId] = $wName;
}
$strSelect = LibHtml::getSelectList("categoryId", $categoryList, $categoryId, true);
$strSelect = "<form action='$PHP_SELF' method='post'>"
. "$strSelect "
. "</form>";

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[13], 300, 300);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gDocumentUrl/category/admin.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageCategory' title='$mlText[6]'></a>"
. " <a href='$gDocumentUrl/preference.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[20]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), $panelUtils->addCell($strSelect, "n"), $panelUtils->addCell($strCommand, "nbr"));

$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'>", "$gDocumentUrl/file.php", 600, 600);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$documentList = array();

$documents = $documentUtils->selectByCategoryId($categoryId);

if ($categoryId) {
  $sortableLinesClass = 'sortableLines_' . $categoryId;

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
      $.post('$gDocumentUrl/list_order.php', {'documentIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
  $panelUtils->addContent($strSortableLines);

} else {
  $sortableLinesClass = true;
}

$panelUtils->openList($sortableLinesClass);
foreach ($documents as $document) {
  $documentId = $document->getId();
  $file = $document->getFile();
  $description = $document->getDescription();

  if ($file) {
    $filename = $documentUtils->filePath . $file;

    $strFile = "<a href='$gDocumentUrl/download.php?documentId=$documentId' $gJSNoStatus title='$mlText[7]'>$file</a>";
  } else {
    $strFile = '';
  }

  if ($categoryId) {
    $strSortable = "<span class='sortableItem' sortableItemId='$documentId'></span>";

    $strSwap = "<a href='$gDocumentUrl/swapleft.php?documentId=$documentId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[11]'></a>"
      . " <a href='$gDocumentUrl/swapright.php?documentId=$documentId' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";
  } else {
    $strSortable = '';
    $strSwap = '';
  }

  $strCommand = " <a href='$gDocumentUrl/edit.php?documentId=$documentId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageFile' title='$mlText[8]'>", "$gDocumentUrl/file.php?documentId=$documentId", 600, 600)
    . " <a href='$gDocumentUrl/delete.php?documentId=$documentId' $gJSNoStatus>"
    . " <img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine("$strSortable $strSwap $strFile", $description, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("document_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
