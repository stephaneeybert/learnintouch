<?PHP

require_once("website.php");

$clockUtils->loadPreferences();
$preferenceUtils->init($clockUtils->preferences, "$gClockUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$lexiconEntryUtils->loadPreferences();
$preferenceUtils->init($lexiconEntryUtils->preferences);
$preferenceUtils->addTypeUpdate();

$adminUtils->loadPreferences();
$preferenceUtils->init($adminUtils->preferences, "$gAdminUrl/list.php");
$preferenceUtils->addTypeUpdate();

$flashUtils->loadPreferences();
$preferenceUtils->init($flashUtils->preferences, "$gFlashUrl/intro.php");
$preferenceUtils->addTypeUpdate();

$contactUtils->loadPreferences();
$preferenceUtils->init($contactUtils->preferences, "$gContactUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$peopleUtils->loadPreferences();
$preferenceUtils->init($peopleUtils->preferences, "$gPeopleUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$clientUtils->loadPreferences();
$preferenceUtils->init($clientUtils->preferences, "$gClientUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$backupUtils->loadPreferences();
$preferenceUtils->init($backupUtils->preferences, "$gBackupUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$statisticsVisitUtils->loadPreferences();
$preferenceUtils->init($statisticsVisitUtils->preferences, "$gStatisticsUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$mailUtils->loadPreferences();
$preferenceUtils->init($mailUtils->preferences, "$gMailUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$guestbookUtils->loadPreferences();
$preferenceUtils->init($guestbookUtils->preferences, "$gGuestbookUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$userUtils->loadPreferences();
$preferenceUtils->init($userUtils->preferences, "$gUserUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$photoUtils->loadPreferences();
$preferenceUtils->init($photoUtils->preferences, "$gPhotoUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$dynpageUtils->loadPreferences();
$preferenceUtils->init($dynpageUtils->preferences, "$gDynpageUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$profileUtils->loadPreferences();
$preferenceUtils->init($profileUtils->preferences, "$gProfileUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$newsStoryUtils->loadPreferences();
$preferenceUtils->init($newsStoryUtils->preferences, "$gNewsUrl/newsStory/admin.php");
$preferenceUtils->addTypeUpdate();

$formUtils->loadPreferences();
$preferenceUtils->init($formUtils->preferences, "$gFormUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$smsUtils->loadPreferences();
$preferenceUtils->init($smsUtils->preferences, "$gSmsUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$elearningExerciseUtils->loadPreferences();
$preferenceUtils->init($elearningExerciseUtils->preferences, "$gElearningUrl/subscription/admin.php");
$preferenceUtils->addTypeUpdate();

$shopItemUtils->loadPreferences();
$preferenceUtils->init($shopItemUtils->preferences, "$gShopUrl/order/admin.php");
$preferenceUtils->addTypeUpdate();

$linkUtils->loadPreferences();
$preferenceUtils->init($linkUtils->preferences, "$gLinkUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$documentUtils->loadPreferences();
$preferenceUtils->init($documentUtils->preferences, "$gDocumentUrl/admin.php");
$preferenceUtils->addTypeUpdate();

$templateUtils->loadPreferences();
$preferenceUtils->init($templateUtils->preferences, "$gTemplateUrl/design/model/admin.php");
$preferenceUtils->addTypeUpdate();

print("<br><br>Preference type update done !!");

?>
