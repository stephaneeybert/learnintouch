<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gPhotoUrl/admin.php");
$strCommand = "<a href='$gPhotoUrl/album/edit.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>"
  . " <a href='$gPhotoUrl/imageArchive.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageArchive' title='$mlText[12]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nb"), $panelUtils->addCell($mlText[6], "nb"), $panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[7], "nb"), $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$sortableLinesClass = true;

$photoAlbums = $photoAlbumUtils->selectAll();

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
      $.post('$gPhotoUrl/album/list_order.php', {'albumIds[]' : sortableItemIds}, function(data){
      });
    }
  }).disableSelection();
});
</script>
HEREDOC;
$panelUtils->addContent($strSortableLines);

$panelUtils->openList($sortableLinesClass);
foreach ($photoAlbums as $photoAlbum) {
  $photoAlbumId = $photoAlbum->getId();
  $name = $photoAlbum->getName();
  $event = $photoAlbum->getEvent();
  $location = $photoAlbum->getLocation();
  $publicationDate = $photoAlbum->getPublicationDate();

  $publicationDate = $clockUtils->systemToLocalNumericDate($publicationDate);

  $strSortable = "<span class='sortableItem' sortableItemId='$photoAlbumId'></span>";

  $strSwap = "<a href='$gPhotoUrl/album/swapup.php?photoAlbumId=$photoAlbumId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageUp'title='$mlText[11]'></a> <a href='$gPhotoUrl/album/swapdown.php?photoAlbumId=$photoAlbumId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[10]'></a>";

  $strCommand = "<a href='$gPhotoUrl/album/edit.php?photoAlbumId=$photoAlbumId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . " <a href='$gPhotoUrl/admin.php?photoAlbumId=$photoAlbumId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[9]'></a>"
    . " <a href='$gPhotoUrl/album/format/admin.php?photoAlbumId=$photoAlbumId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageFormat' title='$mlText[8]'></a>"
    . " <a href='$gPhotoUrl/album/delete.php?photoAlbumId=$photoAlbumId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine("$strSortable $strSwap $name", $event, $location, $publicationDate, $panelUtils->addCell($strCommand, "nbr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
