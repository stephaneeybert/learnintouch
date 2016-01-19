<?PHP

$gHomeUrl = $gSetupWebsiteUrl;

// Set the home path and url
if (isLocalhost()) {
  $gAccountPath = $gRootPath . "account/";
  $gAccountUrl = $gHomeUrl . "/account";
} else {
  $gAccountPath = $gRootPath . "account/";
  $gAccountUrl = $gHomeUrl . "/account";
}

// Set the php.ini file path
if (!isLocalhost()) {
  $gPhpIniFile = '-c /usr/local/lib64/php53/php.ini';
} else {
  $gPhpIniFile = '-c /home/stephane/programs/php-5.2.14/php.ini';
}

// The path to all the website accounts
if (!isLocalhost()) {
  $gWebsitesPath = '/home/learnintouch/www/';
} else {
  $gWebsitesPath = '/home/stephane/dev/php/learnintouch/www/';
}

// The account specific user files directory
$gDataPath = $gAccountPath . "data/";
$gDataUrl = $gAccountUrl . "/data";

$gEnginePath = $gRootPath . 'engine/';
$gEngineUrl = $gHomeUrl . '/engine';

$gEngineDataPath = $gEnginePath . 'data/';
$gEngineDataUrl = $gEngineUrl . '/data';

$gSetupPath = $gEnginePath . "setup/";
$gSetupUrl = $gEngineUrl . "/setup";

$gApiPath = $gEnginePath . 'api/';
$gApiUrl = $gEngineUrl . '/api';

$gJsPath = $gApiPath . 'js/';
$gJsUrl = $gApiUrl . '/js';

$gPearPath = $gApiPath . 'pear/';
$gPearUrl = $gApiUrl . '/pear';

$gLibPath = $gEnginePath . 'lib/';
$gLibUrl = $gEngineUrl . '/lib';

$gTestPath = $gEnginePath . 'test/';
$gTestUrl = $gEngineUrl . '/test';

$gSystemPath = $gEnginePath . 'system/';
$gSystemUrl = $gEngineUrl . '/system';

$gInnovaHtmlEditorPath = $gSystemPath . 'editor/innovaStudio_3.6/';
$gInnovaHtmlEditorUrl = $gSystemUrl . '/editor/innovaStudio_3.6';

$gHtmlEditorPath = $gSystemPath . 'editor/ckeditor/';
$gHtmlEditorUrl = $gSystemUrl . '/editor/ckeditor';

$gSqlPath = $gSystemPath . 'sql/';
$gSqlUrl = $gSystemUrl . '/sql';

$gPanelPath = $gSystemPath . 'panel/';
$gPanelUrl = $gSystemUrl . '/panel';

$gAdminPath = $gSystemPath . 'admin/';
$gAdminUrl = $gSystemUrl . '/admin';

$gAddressPath = $gSystemPath . 'address/';
$gAddressUrl = $gSystemUrl . '/address';

$gImagePath = $gApiPath . 'image/';
$gImageUrl = $gApiUrl . '/image';

$gCommonImagesPath = $gImagePath . 'common/';
$gCommonImagesUrl = $gImageUrl . '/common';

$gFlashPath = $gSystemPath . 'flash/';
$gFlashUrl = $gSystemUrl . '/flash';

$gNavbarPath = $gSystemPath . 'navbar/';
$gNavbarUrl = $gSystemUrl . '/navbar';

$gNavlinkPath = $gSystemPath . 'navlink/';
$gNavlinkUrl = $gSystemUrl . '/navlink';

$gNavmenuPath = $gSystemPath . 'navmenu/';
$gNavmenuUrl = $gSystemUrl . '/navmenu';

$gContainerPath = $gSystemPath . 'container/';
$gContainerUrl = $gSystemUrl . '/container';

$gPropertyPath = $gSystemPath . 'property/';
$gPropertyUrl = $gSystemUrl . '/property';

$gUtilsPath = $gSystemPath . 'utils/';
$gUtilsUrl = $gSystemUrl . '/utils';

$gPdfPath = $gSystemPath . 'pdf/';
$gPdfUrl = $gSystemUrl . '/pdf';

$gUniqueTokenPath = $gSystemPath . 'uniqueToken/';
$gUniqueTokenUrl = $gSystemUrl . '/uniqueToken';

$gLocationPath = $gSystemPath . 'location/';
$gLocationUrl = $gSystemUrl . '/location';

$gLexiconPath = $gSystemPath . 'lexicon/';
$gLexiconUrl = $gSystemUrl . '/lexicon';

$gXmlDomPath = $gSystemPath . 'xmlDom/';
$gXmlDomUrl = $gSystemUrl . '/xmlDom';

$gPlayerPath = $gSystemPath . 'player/';
$gPlayerUrl = $gSystemUrl . '/player';

$gSwfPlayerPath = $gApiPath . 'player/';
$gSwfPlayerUrl = $gApiUrl . '/player';

$gPreferencePath = $gSystemPath . 'preference/';
$gPreferenceUrl = $gSystemUrl . '/preference';

$gContentImportPath = $gSystemPath . 'contentImport/';
$gContentImportUrl = $gSystemUrl . '/contentImport';

$gSocialPath = $gSystemPath . 'social/';
$gSocialUrl = $gSystemUrl . '/social';

$gFacebookPath = $gSystemPath . 'social/facebook/';
$gFacebookUrl = $gSystemUrl . '/social/facebook';

$gLinkedinPath = $gSystemPath . 'social/linkedin/';
$gLinkedinUrl = $gSystemUrl . '/social/linkedin';

$gGooglePath = $gSystemPath . 'social/google/';
$gGoogleUrl = $gSystemUrl . '/social/google';

$gTwitterPath = $gSystemPath . 'social/twitter/';
$gTwitterUrl = $gSystemUrl . '/social/twitter';

$gInviterPath = $gSystemPath . 'social/inviter/';
$gInviterUrl = $gSystemUrl . '/social/inviter';

$gModulesPath = $gEnginePath . 'modules/';
$gModulesUrl = $gEngineUrl . '/modules';

$gWebsitePath = $gModulesPath . 'website/';
$gWebsiteUrl = $gModulesUrl . '/website';

$gTemplatePath = $gModulesPath . 'template/';
$gTemplateUrl = $gModulesUrl . '/template';

$gImageSetPath = $gTemplatePath . 'imageSet/';
$gImageSetUrl = $gTemplateUrl . '/imageSet';

$gTemplateImagePath = $gApiPath . 'image/template/';
$gTemplateImageUrl = $gApiUrl . '/image/template';

$gTemplateDesignPath = $gTemplatePath . 'design/';
$gTemplateDesignUrl = $gTemplateUrl . '/design';

$gLanguagePath = $gModulesPath . 'language/';
$gLanguageUrl = $gModulesUrl . '/language';

$gProfilePath = $gModulesPath . 'profile/';
$gProfileUrl = $gModulesUrl . '/profile';

$gClockPath = $gModulesPath . 'clock/';
$gClockUrl = $gModulesUrl . '/clock';

$gBackupPath = $gModulesPath . 'backup/';
$gBackupUrl = $gModulesUrl . '/backup';

$gFormPath = $gModulesPath . 'form/';
$gFormUrl = $gModulesUrl . '/form';

$gDynpagePath = $gModulesPath . 'dynpage/';
$gDynpageUrl = $gModulesUrl . '/dynpage';

$gStatisticsPath = $gModulesPath . 'statistics/';
$gStatisticsUrl = $gModulesUrl . '/statistics';

$gStatisticsImagePath = $gApiPath . 'image/statistics/';
$gStatisticsImageUrl = $gApiUrl . '/image/statistics';

$gUserPath = $gModulesPath . 'user/';
$gUserUrl = $gModulesUrl . '/user';

$gContactPath = $gModulesPath . 'contact/';
$gContactUrl = $gModulesUrl . '/contact';

$gGuestbookPath = $gModulesPath . 'guestbook/';
$gGuestbookUrl = $gModulesUrl . '/guestbook';

$gElearningPath = $gModulesPath . 'elearning/';
$gElearningUrl = $gModulesUrl . '/elearning';

$gDocumentPath = $gModulesPath . 'document/';
$gDocumentUrl = $gModulesUrl . '/document';

$gLinkPath = $gModulesPath . 'link/';
$gLinkUrl = $gModulesUrl . '/link';

$gShopPath = $gModulesPath . 'shop/';
$gShopUrl = $gModulesUrl . '/shop';

$gClientPath = $gModulesPath . 'client/';
$gClientUrl = $gModulesUrl . '/client';

$gNewsPath = $gModulesPath . 'news/';
$gNewsUrl = $gModulesUrl . '/news';

$gRssPath = $gModulesPath . 'rss/';
$gRssUrl = $gModulesUrl . '/rss';

$gPhotoPath = $gModulesPath . 'photo/';
$gPhotoUrl = $gModulesUrl . '/photo';

$gMailPath = $gModulesPath . 'mail/';
$gMailUrl = $gModulesUrl . '/mail';

$gSmsPath = $gModulesPath . 'sms/';
$gSmsUrl = $gModulesUrl . '/sms';

$gPeoplePath = $gModulesPath . 'people/';
$gPeopleUrl = $gModulesUrl . '/people';

?>
