<?PHP

// Skip the reporting of sql errors as the schema creation is bound to trigger some
$skipReportError = true;

require_once("website.php");

if (!isLocalhost()) {
  $adminUtils->checkForStaffLogin();
}

$addressUtils->createTable();

$adminUtils->createTable();

$adminModuleUtils->createTable();

$adminOptionUtils->createTable();

$containerUtils->createTable();

$dynpageUtils->createTable();

$dynpageNavmenuUtils->createTable();

$flashUtils->createTable();

$preferenceUtils->createTable();

$profileUtils->createTable();

$propertyUtils->createTable();

$lexiconEntryUtils->createTable();

$statisticsVisitUtils->createTable();

$statisticsVisitUtils->createIndexVisitDateTime();

$statisticsVisitUtils->createIndexVisitorHostAddress();

$statisticsVisitUtils->createIndexVisitorBrowser();

$statisticsVisitUtils->createIndexVisitorReferer();

$statisticsPageUtils->createTable();

$statisticsRefererUtils->createTable();

$templatePropertySetUtils->createTable();

$templatePropertyUtils->createTable();

$templateModelUtils->createTable();

$templatePageUtils->createTable();

$templatePageTagUtils->createTable();

$templateContainerUtils->createTable();

$templateElementUtils->createTable();

$templateElementLanguageUtils->createTable();

$templateTagUtils->createTable();

$navbarUtils->createTable();

$navbarLanguageUtils->createTable();

$navbarItemUtils->createTable();

$navlinkUtils->createTable();

$navlinkItemUtils->createTable();

$navmenuUtils->createTable();

$navmenuItemUtils->createTable();

$navmenuLanguageUtils->createTable();

$uniqueTokenUtils->createTable();

$contentImportUtils->createTable();

$contentImportHistoryUtils->createTable();

$userUtils->createTable();

$facebookUtils->createTable();

$clientUtils->createTable();

$contactRefererUtils->createTable();

$contactStatusUtils->createTable();

$contactUtils->createTable();

$documentCategoryUtils->createTable();

$documentUtils->createTable();

$elearningCategoryUtils->createTable();

$elearningLevelUtils->createTable();

$elearningSubjectUtils->createTable();

$elearningTeacherUtils->createTable();

$elearningMatterUtils->createTable();

$elearningScoringUtils->createTable();

$elearningScoringRangeUtils->createTable();

$elearningExerciseUtils->createTable();

$elearningExercisePageUtils->createTable();

$elearningQuestionUtils->createTable();

$elearningAnswerUtils->createTable();

$elearningSolutionUtils->createTable();

$elearningSessionUtils->createTable();

$elearningCourseUtils->createTable();

$elearningCourseInfoUtils->createTable();

$elearningLessonModelUtils->createTable();

$elearningLessonHeadingUtils->createTable();

$elearningLessonUtils->createTable();

$elearningLessonParagraphUtils->createTable();

$elearningCourseItemUtils->createTable();

$elearningClassUtils->createTable();

$elearningSessionCourseUtils->createTable();

$elearningSubscriptionUtils->createTable();

$elearningResultUtils->createTable();

$elearningQuestionResultUtils->createTable();

$elearningAssignmentUtils->createTable();

$guestbookUtils->createTable();

$linkCategoryUtils->createTable();

$linkUtils->createTable();

$mailCategoryUtils->createTable();

$mailAddressUtils->createTable();

$mailUtils->createTable();

$mailListUtils->createTable();

$mailHistoryUtils->createTable();

$mailListUserUtils->createTable();

$mailListAddressUtils->createTable();

$mailOutboxUtils->createTable();

$formUtils->createTable();

$formItemUtils->createTable();

$formItemValueUtils->createTable();

$formValidUtils->createTable();

$newsPublicationUtils->createTable();

$newsHeadingUtils->createTable();

$newsPaperUtils->createTable();

$newsEditorUtils->createTable();

$newsStoryUtils->createTable();

$newsStoryImageUtils->createTable();

$newsStoryParagraphUtils->createTable();

$newsFeedUtils->createTable();

$rssFeedUtils->createTable();

$rssFeedLanguageUtils->createTable();

$peopleCategoryUtils->createTable();

$peopleUtils->createTable();

$photoFormatUtils->createTable();

$photoAlbumUtils->createTable();

$photoAlbumFormatUtils->createTable();

$photoUtils->createTable();

$shopCategoryUtils->createTable();

$shopItemUtils->createTable();

$shopItemImageUtils->createTable();

$shopOrderUtils->createTable();

$shopOrderItemUtils->createTable();

$shopAffiliateUtils->createTable();

$shopDiscountUtils->createTable();

$smsCategoryUtils->createTable();

$smsUtils->createTable();

$smsNumberUtils->createTable();

$smsListUtils->createTable();

$smsHistoryUtils->createTable();

$smsListUserUtils->createTable();

$smsListNumberUtils->createTable();

$smsOutboxUtils->createTable();

print("Schema created !!");

?>
