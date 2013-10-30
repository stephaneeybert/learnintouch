<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0]);
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);

$icons = array();

$strIcon = "<a href='$gDynpageUrl/select.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonMenuImagesUrl/$gIconDynpage' width='48' height='48' title='$mlText[10]'><br>$mlText[11]</a>";
array_push($icons, $strIcon);

$strIcon = "<a href='$gFormUrl/select.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonMenuImagesUrl/$gIconForm' width='48' height='48' title='$mlText[33]'><br>$mlText[32]</a>";
array_push($icons, $strIcon);

$strIcon = "<a href='$gTemplateUrl/selectSystemPage.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonMenuImagesUrl/$gIconSyspage' width='48' height='48' title='$mlText[27]'><br>$mlText[26]</a>";
array_push($icons, $strIcon);

$strIcon = "<a href='$gLanguageUrl/select.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonMenuImagesUrl/$gIconLanguage' width='48' height='48' title='$mlText[31]'><br>$mlText[30]</a>";
array_push($icons, $strIcon);

if ($websiteUtils->isCurrentWebsiteOption('OPTION_SHOP')) {
  $strIcon = "<a href='$gShopUrl/select.php' $gJSNoStatus>" . "<img border='0' src='$gCommonMenuImagesUrl/$gIconShop' width='48' height='48' title='$mlText[12]'><br>$mlText[13]</a>";
  array_push($icons, $strIcon);
}

if ($websiteUtils->isCurrentWebsiteOption('OPTION_ELEARNING')) {
  $strIcon = "<a href='$gElearningUrl/exercise/select.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonMenuImagesUrl/$gIconElearning' width='48' height='48' title='$mlText[14]'><br>$mlText[15]</a>";
  array_push($icons, $strIcon);
}

if ($websiteUtils->isCurrentWebsiteModule('MODULE_NEWS')) {
  $strIcon = "<a href='$gNewsUrl/newsPaper/select.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonMenuImagesUrl/$gIconNews' width='48' height='48' title='$mlText[19]'><br>$mlText[18]</a>";
  array_push($icons, $strIcon);
}

if ($websiteUtils->isCurrentWebsiteModule('MODULE_PHOTO')) {
  $strIcon = "<a href='$gPhotoUrl/album/select.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonMenuImagesUrl/$gIconPhoto' width='48' height='48' title='$mlText[21]'><br>$mlText[20]</a>";
  array_push($icons, $strIcon);
}

if ($websiteUtils->isCurrentWebsiteModule('MODULE_PEOPLE')) {
  $strIcon = "<a href='$gPeopleUrl/category/select.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonMenuImagesUrl/$gIconStaff' width='48' height='48' title='$mlText[23]'><br>$mlText[22]</a>";
  array_push($icons, $strIcon);
}

if ($websiteUtils->isCurrentWebsiteModule('MODULE_LINK')) {
  $strIcon = "<a href='$gLinkUrl/category/select.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonMenuImagesUrl/$gIconLink' width='48' height='48' title='$mlText[25]'><br>$mlText[24]</a>";
  array_push($icons, $strIcon);
}

if ($websiteUtils->isCurrentWebsiteModule('MODULE_DOCUMENT')) {
  $strIcon = "<a href='$gDocumentUrl/select.php' $gJSNoStatus>"
    . "<img border='0' src='$gCommonMenuImagesUrl/$gIconDocument' width='48' height='48' title='$mlText[29]'><br>$mlText[28]</a>";
  array_push($icons, $strIcon);
}

$strIcons = "\n<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n<tr>";

$i = 0;
foreach ($icons as $icon) {
  if ($i >= 5) {
    $strIcons .= "\n</tr>\n<tr><td><br><br></td>\n</tr>\n<tr>";
    $i = 0;
  }
  $strIcons .= "\n<td align='center' valign='top'>";
  $strIcons .= "\n$icon";
  $strIcons .= "\n</td>";
  $i++;
}

$strIcons .= "\n</tr>\n</table>";

$panelUtils->addLine($strIcons);

$str = $panelUtils->render();

printAdminPage($str);

?>
