<?PHP

require_once("website.php");

$adminUtils->checkAdminLogin();

$mlText = $languageUtils->getMlText(__FILE__);

// Get the language flag bar
$languageFlags = $languageUtils->renderAdminLanguageBar();

$panelUtils->setHeader($mlText[0]);
$panelUtils->setMainMenu();

$panelUtils->addLine($panelUtils->addCell($languageFlags, "r"));

$icons = array();

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_TEMPLATE)) {
  $strIcon = "<a href='$gTemplateUrl/design/model/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconTemplate' title='$mlText[124]'></br>$mlText[33]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_DYNPAGE)) {
  $strIcon = "<a href='$gDynpageUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconDynpage' title='$mlText[105]'><br>$mlText[17]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_ELEARNING)) {
  $strIcon = "<a href='$gElearningUrl/subscription/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconElearning' title='$mlText[113]'><br>$mlText[24]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_NEWS)) {
  $strIcon = "<a href='$gNewsUrl/newsStory/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconNews' title='$mlText[116]'><br>$mlText[16]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_CONTACT)) {
  $strIcon = "<a href='$gContactUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconContact' title='$mlText[123]'><br>$mlText[26]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_FORM)) {
  $strIcon = "<a href='$gFormUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconForm' title='$mlText[136]'><br>$mlText[135]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_MAIL)) {
  $strIcon = "<a href='$gMailUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconMail' title='$mlText[114]'><br>$mlText[20]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_SMS)) {
  $strIcon = "<a href='$gSmsUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconSms' title='$mlText[134]'><br>$mlText[21]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_USER)) {
  $strIcon = "<a href='$gUserUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconUser' title='$mlText[103]'><br>$mlText[7]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_PHOTO)) {
  $strIcon = "<a href='$gPhotoUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconPhoto' title='$mlText[115]'><br>$mlText[11]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_GUESTBOOK)) {
  $strIcon = "<a href='$gGuestbookUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconGuestbook' title='$mlText[110]'><br>$mlText[6]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_PEOPLE)) {
  $strIcon = "<a href='$gPeopleUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconStaff' title='$mlText[109]'><br>$mlText[19]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_LINK)) {
  $strIcon = "<a href='$gLinkUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconLink' title='$mlText[111]'><br>$mlText[9]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_CLIENT)) {
  $strIcon = "<a href='$gClientUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconClient' title='$mlText[112]'><br>$mlText[5]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_DOCUMENT)) {
  $strIcon = "<a href='$gDocumentUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconDocument' title='$mlText[119]'><br>$mlText[28]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_SHOP)) {
  $strIcon = "<a href='$gShopUrl/order/admin.php' $gJSNoStatus>" . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconShop' title='$mlText[133]'><br>$mlText[30]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_STATISTICS)) {
  $strIcon = "<a href='$gStatisticsUrl/admin.php' $gJSNoStatus>"
    . "\n<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconStatistics' title='$mlText[126]'></span></br>$mlText[35]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_FLASH)) {
  $strIcon = "<a href='$gFlashUrl/intro.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconFlash' title='$mlText[125]'></span></br>$mlText[34]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_BACKUP)) {
  $strIcon = "<a href='$gBackupUrl/admin.php' $gJSNoStatus>"
    . "\n<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconBackup' title='$mlText[118]'><br>$mlText[12]</a>";
  array_push($icons, $strIcon);
}

$strIcon = "<a href='$gAdminUrl/list.php' $gJSNoStatus>"
. "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconAdmin' title='$mlText[102]'><br>$mlText[4]</a>";
array_push($icons, $strIcon);

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_PROFILE)) {
  $strIcon = "<a href='$gProfileUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconProfile' title='$mlText[108]'><br>$mlText[1]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_LANGUAGE)) {
  $strIcon = "<a href='$gLanguageUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconLanguage' title='$mlText[107]'></span></br>$mlText[18]</a>";
  array_push($icons, $strIcon);
}

if ($adminModuleUtils->moduleGrantedToAdmin(MODULE_CLOCK)) {
  $strIcon = "<a href='$gClockUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconClock' title='$mlText[127]'><br>$mlText[36]</a>";
  array_push($icons, $strIcon);
}

if ($adminUtils->isStaff()) {
  $strIcon = "<a href='$gWebsiteUrl/admin.php' $gJSNoStatus>"
    . "<img class='tooltip' border='0' src='$gCommonMenuImagesUrl/$gIconConfig' title='$mlText[120]'><br>$mlText[25]</a>";
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
