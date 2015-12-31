<?PHP

// Inject the  services

$commonUtils->profileUtils = $profileUtils;

$clockUtils->languageUtils = $languageUtils;
$clockUtils->preferenceUtils = $preferenceUtils;
$clockUtils->propertyUtils = $propertyUtils;

$uniqueTokenUtils->clockUtils = $clockUtils;

$colorboxUtils->languageUtils = $languageUtils;

$googleUtils->commonUtils = $commonUtils;
$googleUtils->popupUtils = $popupUtils;
$googleUtils->profileUtils = $profileUtils;
$googleUtils->userUtils = $userUtils;
$googleUtils->preferenceUtils = $preferenceUtils;

$twitterUtils->commonUtils = $commonUtils;
$twitterUtils->popupUtils = $popupUtils;
$twitterUtils->profileUtils = $profileUtils;
$twitterUtils->userUtils = $userUtils;
$twitterUtils->preferenceUtils = $preferenceUtils;

$fileUploadUtils->languageUtils = $languageUtils;

$popupUtils->templateUtils = $templateUtils;

$playerUtils->languageUtils = $languageUtils;

$lexiconEntryUtils->languageUtils = $languageUtils;
$lexiconEntryUtils->preferenceUtils = $preferenceUtils;
$lexiconEntryUtils->commonUtils = $commonUtils;
$lexiconEntryUtils->userUtils = $userUtils;

$socialInviterUtils->profileUtils = $profileUtils;

$contentImportUtils->languageUtils = $languageUtils;
$contentImportUtils->clockUtils = $clockUtils;
$contentImportUtils->adminUtils = $adminUtils;
$contentImportUtils->uniqueTokenUtils = $uniqueTokenUtils;
$contentImportUtils->profileUtils = $profileUtils;
$contentImportUtils->contentImportHistoryUtils = $contentImportHistoryUtils;

$lexiconImportUtils->lexiconEntryUtils = $lexiconEntryUtils;

$panelUtils->languageUtils = $languageUtils;

$languageUtils->commonUtils = $commonUtils;
$languageUtils->propertyUtils = $propertyUtils;
$languageUtils->adminUtils = $adminUtils;

$profileUtils->languageUtils = $languageUtils;
$profileUtils->preferenceUtils = $preferenceUtils;
$profileUtils->propertyUtils = $propertyUtils;

$adminUtils->languageUtils = $languageUtils;
$adminUtils->preferenceUtils = $preferenceUtils;
$adminUtils->clockUtils = $clockUtils;
$adminUtils->adminModuleUtils = $adminModuleUtils;
$adminUtils->adminOptionUtils = $adminOptionUtils;
$adminUtils->mailUtils = $mailUtils;
$adminUtils->userUtils = $userUtils;
$adminUtils->smsUtils = $mailUtils;
$adminUtils->mailHistoryUtils = $mailHistoryUtils;
$adminUtils->smsHistoryUtils = $smsHistoryUtils;
$adminUtils->dynpageUtils = $dynpageUtils;
$adminUtils->websiteOptionUtils = $websiteOptionUtils;
$adminUtils->propertyUtils = $propertyUtils;

$preferenceUtils->languageUtils = $languageUtils;

$rssFeedUtils->languageUtils = $languageUtils;
$rssFeedUtils->rssFeedLanguageUtils = $rssFeedLanguageUtils;

$adminModuleUtils->languageUtils = $languageUtils;
$adminModuleUtils->moduleUtils = $moduleUtils;
$adminModuleUtils->adminUtils = $adminUtils;
$adminModuleUtils->websiteUtils = $websiteUtils;
$adminModuleUtils->adminOptionUtils = $adminOptionUtils;
$adminModuleUtils->websiteOptionUtils = $websiteOptionUtils;

$adminOptionUtils->websiteOptionUtils = $websiteOptionUtils;
$adminOptionUtils->websiteUtils = $websiteUtils;

$userUtils->languageUtils = $languageUtils;
$userUtils->preferenceUtils = $preferenceUtils;
$userUtils->popupUtils = $popupUtils;
$userUtils->profileUtils = $profileUtils;
$userUtils->clockUtils = $clockUtils;
$userUtils->templateUtils = $templateUtils;
$userUtils->addressUtils = $addressUtils;
$userUtils->uniqueTokenUtils = $uniqueTokenUtils;
$userUtils->propertyUtils = $propertyUtils;
$userUtils->facebookUtils = $facebookUtils;
$userUtils->guestbookUtils = $guestbookUtils;
$userUtils->mailListUserUtils = $mailListUserUtils;
$userUtils->smsListUserUtils = $smsListUserUtils;
$userUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;
$userUtils->elearningCourseUtils = $elearningCourseUtils;
$userUtils->shopOrderUtils = $shopOrderUtils;
$userUtils->fileUploadUtils = $fileUploadUtils;

$dynpageNavmenuUtils->dynpageUtils = $dynpageUtils;

$socialUserUtils->commonUtils = $commonUtils;
$socialUserUtils->profileUtils = $profileUtils;
$socialUserUtils->facebookUtils = $facebookUtils;
$socialUserUtils->linkedinUtils = $linkedinUtils;
$socialUserUtils->twitterUtils = $twitterUtils;

$linkedinUtils->commonUtils = $commonUtils;
$linkedinUtils->profileUtils = $profileUtils;
$linkedinUtils->userUtils = $userUtils;

$facebookUtils->commonUtils = $commonUtils;
$facebookUtils->profileUtils = $profileUtils;
$facebookUtils->userUtils = $userUtils;

$documentUtils->languageUtils = $languageUtils;
$documentUtils->preferenceUtils = $preferenceUtils;
$documentUtils->userUtils = $userUtils;

$documentCategoryUtils->languageUtils = $languageUtils;
$documentCategoryUtils->preferenceUtils = $preferenceUtils;
$documentCategoryUtils->documentUtils = $documentUtils;

$linkUtils->languageUtils = $languageUtils;
$linkUtils->preferenceUtils = $preferenceUtils;

$contactRefererUtils->languageUtils = $languageUtils;
$contactRefererUtils->contactUtils = $contactUtils;

$contactStatusUtils->contactUtils = $contactUtils;

$flashUtils->languageUtils = $languageUtils;
$flashUtils->preferenceUtils = $preferenceUtils;
$flashUtils->playerUtils = $playerUtils;
$flashUtils->dynpageUtils = $dynpageUtils;
$flashUtils->propertyUtils = $propertyUtils;
$flashUtils->fileUploadUtils = $fileUploadUtils;

$dynpageUtils->languageUtils = $languageUtils;
$dynpageUtils->preferenceUtils = $preferenceUtils;
$dynpageUtils->popupUtils = $popupUtils;
$dynpageUtils->flashUtils = $flashUtils;
$dynpageUtils->templateModelUtils = $templateModelUtils;
$dynpageUtils->templateUtils = $templateUtils;

$navlinkUtils->languageUtils = $languageUtils;
$navlinkUtils->navlinkItemUtils = $navlinkItemUtils;

$navlinkItemUtils->templateUtils = $templateUtils;

$navbarUtils->languageUtils = $languageUtils;
$navbarUtils->navbarLanguageUtils = $navbarLanguageUtils;
$navbarUtils->navbarItemUtils = $navbarItemUtils;

$navbarItemUtils->templateUtils = $templateUtils;

$navbarLanguageUtils->navbarItemUtils = $navbarItemUtils;

$navmenuUtils->languageUtils = $languageUtils;
$navmenuUtils->navmenuLanguageUtils = $navmenuLanguageUtils;
$navmenuUtils->navmenuItemUtils = $navmenuItemUtils;
$navmenuUtils->templateUtils = $templateUtils;

$navmenuLanguageUtils->navmenuItemUtils = $navmenuItemUtils;

$containerUtils->languageUtils = $languageUtils;

$templateUtils->languageUtils = $languageUtils;
$templateUtils->preferenceUtils = $preferenceUtils;
$templateUtils->commonUtils = $commonUtils;
$templateUtils->templateModelUtils = $templateModelUtils;
$templateUtils->websiteUtils = $websiteUtils;
$templateUtils->dynpageUtils = $dynpageUtils;
$templateUtils->userUtils = $userUtils;
$templateUtils->documentUtils = $documentUtils;
$templateUtils->newsPublicationUtils = $newsPublicationUtils;
$templateUtils->newsPaperUtils = $newsPaperUtils;
$templateUtils->formUtils = $formUtils;
$templateUtils->elearningExerciseUtils = $elearningExerciseUtils;
$templateUtils->elearningLessonUtils = $elearningLessonUtils;
$templateUtils->documentCategoryUtils = $documentCategoryUtils;
$templateUtils->shopCategoryUtils = $shopCategoryUtils;
$templateUtils->elearningLessonUtils = $elearningLessonUtils;
$templateUtils->photoAlbumUtils = $photoAlbumUtils;
$templateUtils->linkCategoryUtils = $linkCategoryUtils;
$templateUtils->peopleCategoryUtils = $peopleCategoryUtils;
$templateUtils->propertyUtils = $propertyUtils;
$templateUtils->templatePageUtils = $templatePageUtils;
$templateUtils->profileUtils = $profileUtils;
$templateUtils->clockUtils = $clockUtils;
$templateUtils->navmenuUtils = $navmenuUtils;
$templateUtils->adminUtils = $adminUtils;
$templateUtils->mailAddressUtils = $mailAddressUtils;
$templateUtils->clientUtils = $clientUtils;
$templateUtils->smsNumberUtils = $smsNumberUtils;
$templateUtils->templateElementLanguageUtils = $templateElementLanguageUtils;
$templateUtils->rssFeedUtils = $rssFeedUtils;

$templateModelUtils->languageUtils = $languageUtils;
$templateModelUtils->templatePropertySetUtils = $templatePropertySetUtils;
$templateModelUtils->templateContainerUtils = $templateContainerUtils;
$templateModelUtils->templateElementUtils = $templateElementUtils;
$templateModelUtils->templatePropertyUtils = $templatePropertyUtils;
$templateModelUtils->templatePageUtils = $templatePageUtils;
$templateModelUtils->templateUtils = $templateUtils;
$templateModelUtils->lexiconEntryUtils = $lexiconEntryUtils;
$templateModelUtils->profileUtils = $profileUtils;
$templateModelUtils->facebookUtils = $facebookUtils;
$templateModelUtils->linkedinUtils = $linkedinUtils;
$templateModelUtils->navlinkItemUtils = $navlinkItemUtils;
$templateModelUtils->navbarItemUtils = $navbarItemUtils;
$templateModelUtils->navmenuItemUtils = $navmenuItemUtils;
$templateModelUtils->documentUtils = $documentUtils;

$templateContainerUtils->languageUtils = $languageUtils;
$templateContainerUtils->templatePropertySetUtils = $templatePropertySetUtils;
$templateContainerUtils->templatePropertyUtils = $templatePropertyUtils;
$templateContainerUtils->templateModelUtils = $templateModelUtils;
$templateContainerUtils->templateElementUtils = $templateElementUtils;

$templateElementUtils->languageUtils = $languageUtils;
$templateElementUtils->commonUtils = $commonUtils;
$templateElementUtils->templateUtils = $templateUtils;
$templateElementUtils->templateTagUtils = $templateTagUtils;
$templateElementUtils->containerUtils = $containerUtils;
$templateElementUtils->navmenuUtils = $navmenuUtils;
$templateElementUtils->navbarUtils = $navbarUtils;
$templateElementUtils->navlinkUtils = $navlinkUtils;
$templateElementUtils->flashUtils = $flashUtils;
$templateElementUtils->rssFeedUtils = $rssFeedUtils;
$templateElementUtils->templateElementLanguageUtils = $templateElementLanguageUtils;
$templateElementUtils->adminModuleUtils = $adminModuleUtils;
$templateElementUtils->templateContainerUtils = $templateContainerUtils;
$templateElementUtils->templatePropertySetUtils = $templatePropertySetUtils;
$templateElementUtils->dynpageUtils = $dynpageUtils;
$templateElementUtils->newsFeedUtils = $newsFeedUtils;
$templateElementUtils->lexiconEntryUtils = $lexiconEntryUtils;
$templateElementUtils->elearningLessonUtils = $elearningLessonUtils;
$templateElementUtils->elearningExerciseUtils = $elearningExerciseUtils;

$templateElementLanguageUtils->languageUtils = $languageUtils;
$templateElementLanguageUtils->templateUtils = $templateUtils;
$templateElementLanguageUtils->templateElementUtils = $templateElementUtils;
$templateElementLanguageUtils->newsFeedUtils = $newsFeedUtils;
$templateElementLanguageUtils->dynpageUtils = $dynpageUtils;
$templateElementLanguageUtils->dynpageNavmenuUtils = $dynpageNavmenuUtils;
$templateElementLanguageUtils->linkCategoryUtils = $linkCategoryUtils;
$templateElementLanguageUtils->photoUtils = $photoUtils;

$templateTagUtils->languageUtils = $languageUtils;
$templateTagUtils->templateElementUtils = $templateElementUtils;
$templateTagUtils->templatePropertySetUtils = $templatePropertySetUtils;
$templateTagUtils->profileUtils = $profileUtils;

$templatePageUtils->languageUtils = $languageUtils;
$templatePageUtils->templateUtils = $templateUtils;
$templatePageUtils->templateElementUtils = $templateElementUtils;
$templatePageUtils->templatePageTagUtils = $templatePageTagUtils;
$templatePageUtils->templatePropertySetUtils = $templatePropertySetUtils;

$templatePageTagUtils->languageUtils = $languageUtils;
$templatePageTagUtils->templatePropertySetUtils = $templatePropertySetUtils;
$templatePageTagUtils->templateModelUtils = $templateModelUtils;
$templatePageTagUtils->templatePageUtils = $templatePageUtils;
$templatePageTagUtils->clientUtils = $clientUtils;
$templatePageTagUtils->guestbookUtils = $guestbookUtils;
$templatePageTagUtils->peopleCategoryUtils = $peopleCategoryUtils;
$templatePageTagUtils->peopleUtils = $peopleUtils;
$templatePageTagUtils->linkCategoryUtils = $linkCategoryUtils;
$templatePageTagUtils->photoAlbumUtils = $photoAlbumUtils;
$templatePageTagUtils->photoUtils = $photoUtils;
$templatePageTagUtils->documentUtils = $documentUtils;
$templatePageTagUtils->formUtils = $formUtils;
$templatePageTagUtils->newsPublicationUtils = $newsPublicationUtils;
$templatePageTagUtils->newsPaperUtils = $newsPaperUtils;
$templatePageTagUtils->newsStoryUtils = $newsStoryUtils;
$templatePageTagUtils->shopItemUtils = $shopItemUtils;
$templatePageTagUtils->shopOrderUtils = $shopOrderUtils;
$templatePageTagUtils->dynpageUtils = $dynpageUtils;
$templatePageTagUtils->elearningExerciseUtils = $elearningExerciseUtils;
$templatePageTagUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;
$templatePageTagUtils->elearningTeacherUtils = $elearningTeacherUtils;
$templatePageTagUtils->elearningLessonUtils = $elearningLessonUtils;

$templatePropertySetUtils->languageUtils = $languageUtils;
$templatePropertySetUtils->templatePropertyUtils = $templatePropertyUtils;

$linkCategoryUtils->languageUtils = $languageUtils;
$linkCategoryUtils->preferenceUtils = $preferenceUtils;
$linkCategoryUtils->commonUtils = $commonUtils;
$linkCategoryUtils->linkUtils = $linkUtils;
$linkCategoryUtils->fileUploadUtils = $fileUploadUtils;

$elearningLevelUtils->languageUtils = $languageUtils;

$elearningTeacherUtils->userUtils = $userUtils;
$elearningTeacherUtils->languageUtils = $languageUtils;
$elearningTeacherUtils->profileUtils = $profileUtils;
$elearningTeacherUtils->socialUserUtils = $socialUserUtils;
$elearningTeacherUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;

$elearningMatterUtils->elearningCourseUtils = $elearningCourseUtils;

$elearningScoringUtils->elearningExerciseUtils = $elearningExerciseUtils;
$elearningScoringUtils->elearningScoringRangeUtils = $elearningScoringRangeUtils;

$elearningExercisePageUtils->languageUtils = $languageUtils;
$elearningExercisePageUtils->preferenceUtils = $preferenceUtils;
$elearningExercisePageUtils->commonUtils = $commonUtils;
$elearningExercisePageUtils->playerUtils = $playerUtils;
$elearningExercisePageUtils->lexiconEntryUtils = $lexiconEntryUtils;
$elearningExercisePageUtils->elearningExerciseUtils = $elearningExerciseUtils;
$elearningExercisePageUtils->elearningQuestionUtils = $elearningQuestionUtils;
$elearningExercisePageUtils->elearningResultUtils = $elearningResultUtils;
$elearningExercisePageUtils->elearningAnswerUtils = $elearningAnswerUtils;
$elearningExercisePageUtils->elearningSolutionUtils = $elearningSolutionUtils;
$elearningExercisePageUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;
$elearningExercisePageUtils->elearningCourseUtils = $elearningCourseUtils;
$elearningExercisePageUtils->elearningAssignmentUtils = $elearningAssignmentUtils;
$elearningExercisePageUtils->fileUploadUtils = $fileUploadUtils;

$elearningLessonModelUtils->adminUtils = $adminUtils;
$elearningLessonModelUtils->elearningLessonUtils = $elearningLessonUtils;
$elearningLessonModelUtils->elearningLessonHeadingUtils = $elearningLessonHeadingUtils;

$elearningLessonHeadingUtils->preferenceUtils = $preferenceUtils;
$elearningLessonHeadingUtils->elearningLessonParagraphUtils = $elearningLessonParagraphUtils;

$elearningLessonUtils->languageUtils = $languageUtils;
$elearningLessonUtils->preferenceUtils = $preferenceUtils;
$elearningLessonUtils->commonUtils = $commonUtils;
$elearningLessonUtils->popupUtils = $popupUtils;
$elearningLessonUtils->userUtils = $userUtils;
$elearningLessonUtils->adminUtils = $adminUtils;
$elearningLessonUtils->playerUtils = $playerUtils;
$elearningLessonUtils->profileUtils = $profileUtils;
$elearningLessonUtils->websiteUtils = $websiteUtils;
$elearningLessonUtils->elearningResultUtils = $elearningResultUtils;
$elearningLessonUtils->elearningCourseUtils = $elearningCourseUtils;
$elearningLessonUtils->elearningCourseItemUtils = $elearningCourseItemUtils;
$elearningLessonUtils->elearningExerciseUtils = $elearningExerciseUtils;
$elearningLessonUtils->elearningResultUtils = $elearningResultUtils;
$elearningLessonUtils->elearningLessonParagraphUtils = $elearningLessonParagraphUtils;
$elearningLessonUtils->elearningLessonHeadingUtils = $elearningLessonHeadingUtils;
$elearningLessonUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;
$elearningLessonUtils->fileUploadUtils = $fileUploadUtils;

$elearningLessonParagraphUtils->languageUtils = $languageUtils;
$elearningLessonParagraphUtils->preferenceUtils = $preferenceUtils;
$elearningLessonParagraphUtils->playerUtils = $playerUtils;
$elearningLessonParagraphUtils->elearningLessonUtils = $elearningLessonUtils;
$elearningLessonParagraphUtils->elearningExerciseUtils = $elearningExerciseUtils;
$elearningLessonParagraphUtils->fileUploadUtils = $fileUploadUtils;

$elearningCourseUtils->languageUtils = $languageUtils;
$elearningCourseUtils->preferenceUtils = $preferenceUtils;
$elearningCourseUtils->adminUtils = $adminUtils;
$elearningCourseUtils->elearningExerciseUtils = $elearningExerciseUtils;
$elearningCourseUtils->elearningLessonUtils = $elearningLessonUtils;
$elearningCourseUtils->elearningCourseItemUtils = $elearningCourseItemUtils;
$elearningCourseUtils->elearningCourseInfoUtils = $elearningCourseInfoUtils;
$elearningCourseUtils->elearningSessionCourseUtils = $elearningSessionCourseUtils;
$elearningCourseUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;
$elearningCourseUtils->elearningLessonParagraphUtils = $elearningLessonParagraphUtils;
$elearningCourseUtils->elearningLessonHeadingUtils = $elearningLessonHeadingUtils;
$elearningCourseUtils->fileUploadUtils = $fileUploadUtils;

$elearningCourseInfoUtils->languageUtils = $languageUtils;
$elearningCourseInfoUtils->elearningCourseUtils = $elearningCourseUtils;

$elearningExerciseUtils->languageUtils = $languageUtils;
$elearningExerciseUtils->preferenceUtils = $preferenceUtils;
$elearningExerciseUtils->commonUtils = $commonUtils;
$elearningExerciseUtils->popupUtils = $popupUtils;
$elearningExerciseUtils->profileUtils = $profileUtils;
$elearningExerciseUtils->clockUtils = $clockUtils;
$elearningExerciseUtils->playerUtils = $playerUtils;
$elearningExerciseUtils->fileUploadUtils = $fileUploadUtils;
$elearningExerciseUtils->lexiconEntryUtils = $lexiconEntryUtils;
$elearningExerciseUtils->userUtils = $userUtils;
$elearningExerciseUtils->socialUserUtils = $socialUserUtils;
$elearningExerciseUtils->adminUtils = $adminUtils;
$elearningExerciseUtils->mailAddressUtils = $mailAddressUtils;
$elearningExerciseUtils->templateUtils = $templateUtils;
$elearningExerciseUtils->templateModelUtils = $templateModelUtils;
$elearningExerciseUtils->websiteUtils = $websiteUtils;
$elearningExerciseUtils->elearningResultUtils = $elearningResultUtils;
$elearningExerciseUtils->elearningResultRangeUtils = $elearningResultRangeUtils;
$elearningExerciseUtils->elearningQuestionResultUtils = $elearningQuestionResultUtils;
$elearningExerciseUtils->elearningExercisePageUtils = $elearningExercisePageUtils;
$elearningExerciseUtils->elearningCourseUtils = $elearningCourseUtils;
$elearningExerciseUtils->elearningCourseItemUtils = $elearningCourseItemUtils;
$elearningExerciseUtils->elearningCourseInfoUtils = $elearningCourseInfoUtils;
$elearningExerciseUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;
$elearningExerciseUtils->elearningLessonUtils = $elearningLessonUtils;
$elearningExerciseUtils->elearningQuestionUtils = $elearningQuestionUtils;
$elearningExerciseUtils->elearningAnswerUtils = $elearningAnswerUtils;
$elearningExerciseUtils->elearningSessionUtils = $elearningSessionUtils;
$elearningExerciseUtils->elearningClassUtils = $elearningClassUtils;
$elearningExerciseUtils->elearningTeacherUtils = $elearningTeacherUtils;
$elearningExerciseUtils->elearningScoringUtils = $elearningScoringUtils;
$elearningExerciseUtils->elearningLevelUtils = $elearningLevelUtils;
$elearningExerciseUtils->elearningLessonParagraphUtils = $elearningLessonParagraphUtils;
$elearningExerciseUtils->elearningAssignmentUtils = $elearningAssignmentUtils;

$elearningQuestionUtils->preferenceUtils = $preferenceUtils;
$elearningQuestionUtils->playerUtils = $playerUtils;
$elearningQuestionUtils->elearningAnswerUtils = $elearningAnswerUtils;
$elearningQuestionUtils->elearningSolutionUtils = $elearningSolutionUtils;
$elearningQuestionUtils->elearningResultUtils = $elearningResultUtils;
$elearningQuestionUtils->elearningExerciseUtils = $elearningExerciseUtils;
$elearningQuestionUtils->elearningExercisePageUtils = $elearningExercisePageUtils;
$elearningQuestionUtils->elearningQuestionResultUtils = $elearningQuestionResultUtils;
$elearningQuestionUtils->fileUploadUtils = $fileUploadUtils;

$elearningAnswerUtils->preferenceUtils = $preferenceUtils;
$elearningAnswerUtils->playerUtils = $playerUtils;
$elearningAnswerUtils->elearningQuestionUtils = $elearningQuestionUtils;
$elearningAnswerUtils->elearningQuestionResultUtils = $elearningQuestionResultUtils;
$elearningAnswerUtils->elearningResultUtils = $elearningResultUtils;
$elearningAnswerUtils->elearningSolutionUtils = $elearningSolutionUtils;
$elearningAnswerUtils->fileUploadUtils = $fileUploadUtils;

$elearningSolutionUtils->elearningAnswerUtils = $elearningAnswerUtils;

$elearningResultUtils->commonUtils = $commonUtils;
$elearningResultUtils->languageUtils = $languageUtils;
$elearningResultUtils->preferenceUtils = $preferenceUtils;
$elearningResultUtils->clockUtils = $clockUtils;
$elearningResultUtils->profileUtils = $profileUtils;
$elearningResultUtils->uniqueTokenUtils = $uniqueTokenUtils;
$elearningResultUtils->userUtils = $userUtils;
$elearningResultUtils->adminUtils = $adminUtils;
$elearningResultUtils->templateUtils = $templateUtils;
$elearningResultUtils->elearningAssignmentUtils = $elearningAssignmentUtils;
$elearningResultUtils->elearningQuestionResultUtils = $elearningQuestionResultUtils;
$elearningResultUtils->elearningExercisePageUtils = $elearningExercisePageUtils;
$elearningResultUtils->elearningQuestionUtils = $elearningQuestionUtils;
$elearningResultUtils->elearningAnswerUtils = $elearningAnswerUtils;
$elearningResultUtils->elearningExerciseUtils = $elearningExerciseUtils;
$elearningResultUtils->elearningLessonUtils = $elearningLessonUtils;
$elearningResultUtils->elearningSolutionUtils = $elearningSolutionUtils;
$elearningResultUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;
$elearningResultUtils->elearningCourseUtils = $elearningCourseUtils;
$elearningResultUtils->elearningClassUtils = $elearningClassUtils;
$elearningResultUtils->elearningCourseItemUtils = $elearningCourseItemUtils;
$elearningResultUtils->elearningResultRangeUtils = $elearningResultRangeUtils;
$elearningResultUtils->elearningTeacherUtils = $elearningTeacherUtils;

$elearningResultRangeUtils->elearningExerciseUtils = $elearningExerciseUtils;

$elearningExercisePdf->languageUtils = $languageUtils;
$elearningExercisePdf->adminUtils = $adminUtils;
$elearningExercisePdf->lexiconEntryUtils = $lexiconEntryUtils;
$elearningExercisePdf->elearningExerciseUtils = $elearningExerciseUtils;
$elearningExercisePdf->elearningExercisePageUtils = $elearningExercisePageUtils;
$elearningExercisePdf->elearningExercisePagePdf = $elearningExercisePagePdf;

$elearningExercisePagePdf->languageUtils = $languageUtils;
$elearningExercisePagePdf->adminUtils = $adminUtils;
$elearningExercisePagePdf->lexiconEntryUtils = $lexiconEntryUtils;
$elearningExercisePagePdf->elearningExerciseUtils = $elearningExerciseUtils;
$elearningExercisePagePdf->elearningExercisePageUtils = $elearningExercisePageUtils;
$elearningExercisePagePdf->elearningQuestionUtils = $elearningQuestionUtils;
$elearningExercisePagePdf->elearningAnswerUtils = $elearningAnswerUtils;

$elearningLessonPdf->languageUtils = $languageUtils;
$elearningLessonPdf->adminUtils = $adminUtils;
$elearningLessonPdf->elearningLessonUtils = $elearningLessonUtils;
$elearningLessonPdf->elearningLessonParagraphUtils = $elearningLessonParagraphUtils;
$elearningLessonPdf->elearningExerciseUtils = $elearningExerciseUtils;
$elearningLessonPdf->lexiconEntryUtils = $lexiconEntryUtils;

$elearningQuestionResultUtils->elearningAnswerUtils = $elearningAnswerUtils;
$elearningQuestionResultUtils->elearningQuestionUtils = $elearningQuestionUtils;

$elearningAssignmentUtils->languageUtils = $languageUtils;
$elearningAssignmentUtils->clockUtils = $clockUtils;
$elearningAssignmentUtils->userUtils = $userUtils;
$elearningAssignmentUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;
$elearningAssignmentUtils->elearningClassUtils = $elearningClassUtils;
$elearningAssignmentUtils->elearningResultUtils = $elearningResultUtils;

$elearningImportUtils->languageUtils = $languageUtils;
$elearningImportUtils->commonUtils = $commonUtils;
$elearningImportUtils->propertyUtils = $propertyUtils;
$elearningImportUtils->clockUtils = $clockUtils;
$elearningImportUtils->lexiconImportUtils = $lexiconImportUtils;
$elearningImportUtils->elearningExerciseUtils = $elearningExerciseUtils;
$elearningImportUtils->elearningLessonUtils = $elearningLessonUtils;
$elearningImportUtils->elearningMatterUtils = $elearningMatterUtils;
$elearningImportUtils->elearningCourseUtils = $elearningCourseUtils;
$elearningImportUtils->elearningCourseItemUtils = $elearningCourseItemUtils;
$elearningImportUtils->elearningExercisePageUtils = $elearningExercisePageUtils;
$elearningImportUtils->elearningQuestionUtils = $elearningQuestionUtils;
$elearningImportUtils->elearningAnswerUtils = $elearningAnswerUtils;
$elearningImportUtils->elearningSolutionUtils = $elearningSolutionUtils;
$elearningImportUtils->elearningLessonParagraphUtils = $elearningLessonParagraphUtils;

$elearningSessionUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;
$elearningSessionUtils->elearningSessionCourseUtils = $elearningSessionCourseUtils;

$elearningClassUtils->elearningSubscriptionUtils = $elearningSubscriptionUtils;

$elearningSubscriptionUtils->languageUtils = $languageUtils;
$elearningSubscriptionUtils->popupUtils = $popupUtils;
$elearningSubscriptionUtils->clockUtils = $clockUtils;
$elearningSubscriptionUtils->userUtils = $userUtils;
$elearningSubscriptionUtils->elearningSessionUtils = $elearningSessionUtils;
$elearningSubscriptionUtils->elearningResultUtils = $elearningResultUtils;
$elearningSubscriptionUtils->elearningTeacherUtils = $elearningTeacherUtils;
$elearningSubscriptionUtils->elearningCourseUtils = $elearningCourseUtils;
$elearningSubscriptionUtils->elearningClassUtils = $elearningClassUtils;
$elearningSubscriptionUtils->elearningLessonParagraphUtils = $elearningLessonParagraphUtils;
$elearningSubscriptionUtils->elearningCourseItemUtils = $elearningCourseItemUtils;
$elearningSubscriptionUtils->elearningAssignmentUtils = $elearningAssignmentUtils;

$formUtils->languageUtils = $languageUtils;
$formUtils->preferenceUtils = $preferenceUtils;
$formUtils->commonUtils = $commonUtils;
$formUtils->popupUtils = $popupUtils;
$formUtils->templateUtils = $templateUtils;
$formUtils->formItemUtils = $formItemUtils;
$formUtils->formValidUtils = $formValidUtils;
$formUtils->fileUploadUtils = $fileUploadUtils;

$formItemUtils->languageUtils = $languageUtils;
$formItemUtils->formValidUtils = $formValidUtils;
$formItemUtils->formItemValueUtils = $formItemValueUtils;

$formItemValueUtils->formUtils = $formUtils;
$formItemValueUtils->formItemUtils = $formItemUtils;

$clientUtils->languageUtils = $languageUtils;
$clientUtils->preferenceUtils = $preferenceUtils;
$clientUtils->commonUtils = $commonUtils;
$clientUtils->fileUploadUtils = $fileUploadUtils;

$newsEditorUtils->adminUtils = $adminUtils;
$newsEditorUtils->newsStoryUtils = $newsStoryUtils;

$newsPublicationUtils->languageUtils = $languageUtils;
$newsPublicationUtils->clockUtils = $clockUtils;
$newsPublicationUtils->newsPaperUtils = $newsPaperUtils;

$newsPaperUtils->languageUtils = $languageUtils;
$newsPaperUtils->preferenceUtils = $preferenceUtils;
$newsPaperUtils->commonUtils = $commonUtils;
$newsPaperUtils->popupUtils = $popupUtils;
$newsPaperUtils->profileUtils = $profileUtils;
$newsPaperUtils->clockUtils = $clockUtils;
$newsPaperUtils->playerUtils = $playerUtils;
$newsPaperUtils->colorboxUtils = $colorboxUtils;
$newsPaperUtils->templateUtils = $templateUtils;
$newsPaperUtils->newsStoryUtils = $newsStoryUtils;
$newsPaperUtils->newsPublicationUtils = $newsPublicationUtils;
$newsPaperUtils->newsStoryImageUtils = $newsStoryImageUtils;
$newsPaperUtils->newsHeadingUtils = $newsHeadingUtils;
$newsPaperUtils->newsStoryUtils = $newsStoryUtils;

$newsHeadingUtils->preferenceUtils = $preferenceUtils;
$newsHeadingUtils->newsHeadingUtils = $newsHeadingUtils;
$newsHeadingUtils->newsStoryUtils = $newsStoryUtils;

$newsStoryUtils->languageUtils = $languageUtils;
$newsStoryUtils->preferenceUtils = $preferenceUtils;
$newsStoryUtils->commonUtils = $commonUtils;
$newsStoryUtils->popupUtils = $popupUtils;
$newsStoryUtils->clockUtils = $clockUtils;
$newsStoryUtils->colorboxUtils = $colorboxUtils;
$newsStoryUtils->playerUtils = $playerUtils;
$newsStoryUtils->templateModelUtils = $templateModelUtils;
$newsStoryUtils->newsStoryImageUtils = $newsStoryImageUtils;
$newsStoryUtils->newsStoryParagraphUtils = $newsStoryParagraphUtils;
$newsStoryUtils->newsPaperUtils = $newsPaperUtils;
$newsStoryUtils->newsPublicationUtils = $newsPublicationUtils;
$newsStoryUtils->newsEditorUtils = $newsEditorUtils;

$newsStoryParagraphUtils->newsPaperUtils = $newsPaperUtils;
$newsStoryParagraphUtils->newsStoryUtils = $newsStoryUtils;

$newsStoryImageUtils->newsStoryUtils = $newsStoryUtils;
$newsStoryImageUtils->newsPaperUtils = $newsPaperUtils;

$newsFeedUtils->languageUtils = $languageUtils;
$newsFeedUtils->preferenceUtils = $preferenceUtils;
$newsFeedUtils->commonUtils = $commonUtils;
$newsFeedUtils->profileUtils = $profileUtils;
$newsFeedUtils->clockUtils = $clockUtils;
$newsFeedUtils->templateUtils = $templateUtils;
$newsFeedUtils->newsStoryImageUtils = $newsStoryImageUtils;
$newsFeedUtils->newsStoryUtils = $newsStoryUtils;
$newsFeedUtils->newsPaperUtils = $newsPaperUtils;
$newsFeedUtils->newsEditorUtils = $newsEditorUtils;
$newsFeedUtils->fileUploadUtils = $fileUploadUtils;

$photoUtils->languageUtils = $languageUtils;
$photoUtils->preferenceUtils = $preferenceUtils;
$photoUtils->commonUtils = $commonUtils;
$photoUtils->photoAlbumUtils = $photoAlbumUtils;
$photoUtils->photoFormatUtils = $photoFormatUtils;
$photoUtils->photoAlbumFormatUtils = $photoAlbumFormatUtils;
$photoUtils->shopItemUtils = $shopItemUtils;
$photoUtils->fileUploadUtils = $fileUploadUtils;

$photoAlbumUtils->languageUtils = $languageUtils;
$photoAlbumUtils->preferenceUtils = $preferenceUtils;
$photoAlbumUtils->clockUtils = $clockUtils;
$photoAlbumUtils->photoUtils = $photoUtils;
$photoAlbumUtils->colorboxUtils = $colorboxUtils;

$smsUtils->languageUtils = $languageUtils;
$smsUtils->preferenceUtils = $preferenceUtils;
$smsUtils->smsHistoryUtils = $smsHistoryUtils;

$smsListUtils->smsListUserUtils = $smsListUserUtils;
$smsListUtils->smsListNumberUtils = $smsListNumberUtils;
$smsListUtils->smsHistoryUtils = $smsHistoryUtils;

$smsNumberUtils->languageUtils = $languageUtils;
$smsNumberUtils->preferenceUtils = $preferenceUtils;
$smsNumberUtils->smsListNumberUtils = $smsListNumberUtils;
$smsNumberUtils->smsListUtils = $smsListUtils;

$smsCategoryUtils->languageUtils = $languageUtils;
$smsCategoryUtils->preferenceUtils = $preferenceUtils;

$shopCategoryUtils->languageUtils = $languageUtils;
$shopCategoryUtils->flashUtils = $flashUtils;
$shopCategoryUtils->templateUtils = $templateUtils;

$shopAffiliateUtils->shopDiscountUtils = $shopDiscountUtils;
$shopAffiliateUtils->shopItemUtils = $shopItemUtils;
$shopAffiliateUtils->userUtils = $userUtils;

$shopItemUtils->languageUtils = $languageUtils;
$shopItemUtils->preferenceUtils = $preferenceUtils;
$shopItemUtils->popupUtils = $popupUtils;
$shopItemUtils->colorboxUtils = $colorboxUtils;
$shopItemUtils->shopCategoryUtils = $shopCategoryUtils;
$shopItemUtils->clockUtils = $clockUtils;
$shopItemUtils->photoFormatUtils = $photoFormatUtils;
$shopItemUtils->photoUtils = $photoUtils;
$shopItemUtils->shopItemImageUtils = $shopItemImageUtils;
$shopItemUtils->shopOrderItemUtils = $shopOrderItemUtils;
$shopItemUtils->shopDiscountUtils = $shopDiscountUtils;

$shopItemImageUtils->languageUtils = $languageUtils;
$shopItemImageUtils->preferenceUtils = $preferenceUtils;
$shopItemImageUtils->shopItemUtils = $shopItemUtils;

$shopOrderUtils->languageUtils = $languageUtils;
$shopOrderUtils->preferenceUtils = $preferenceUtils;
$shopOrderUtils->addressUtils = $addressUtils;
$shopOrderUtils->clockUtils = $clockUtils;
$shopOrderUtils->propertyUtils = $propertyUtils;
$shopOrderUtils->shopItemUtils = $shopItemUtils;
$shopOrderUtils->shopItemUtils = $shopItemUtils;
$shopOrderUtils->shopOrderItemUtils = $shopOrderItemUtils;

$guestbookUtils->languageUtils = $languageUtils;
$guestbookUtils->userUtils = $userUtils;

$mailUtils->languageUtils = $languageUtils;
$mailUtils->preferenceUtils = $preferenceUtils;
$mailUtils->adminModuleUtils = $adminModuleUtils;
$mailUtils->adminUtils = $adminUtils;
$mailUtils->clockUtils = $clockUtils;
$mailUtils->profileUtils = $profileUtils;
$mailUtils->fileUploadUtils = $fileUploadUtils;

$mailAddressUtils->languageUtils = $languageUtils;
$mailAddressUtils->mailListAddressUtils = $mailListAddressUtils;
$mailAddressUtils->mailListUtils = $mailListUtils;

$mailListUtils->mailListUserUtils = $mailListUserUtils;
$mailListUtils->mailHistoryUtils = $mailHistoryUtils;
$mailListUtils->mailListAddressUtils = $mailListAddressUtils;

$mailOutboxUtils->propertyUtils = $propertyUtils;
$mailOutboxUtils->userUtils = $userUtils;

$peopleUtils->languageUtils = $languageUtils;
$peopleUtils->preferenceUtils = $preferenceUtils;
$peopleUtils->fileUploadUtils = $fileUploadUtils;

$peopleCategoryUtils->languageUtils = $languageUtils;
$peopleCategoryUtils->preferenceUtils = $preferenceUtils;
$peopleCategoryUtils->peopleUtils = $peopleUtils;

$contactUtils->languageUtils = $languageUtils;
$contactUtils->preferenceUtils = $preferenceUtils;
$contactUtils->clockUtils = $clockUtils;
$contactUtils->profileUtils = $profileUtils;
$contactUtils->mailAddressUtils = $mailAddressUtils;
$contactUtils->contactStatusUtils = $contactStatusUtils;
$contactUtils->adminUtils = $adminUtils;
$contactUtils->uniqueTokenUtils = $uniqueTokenUtils;
$contactUtils->contactRefererUtils = $contactRefererUtils;

$statisticsVisitUtils->languageUtils = $languageUtils;
$statisticsVisitUtils->preferenceUtils = $preferenceUtils;
$statisticsVisitUtils->clockUtils = $clockUtils;
$statisticsVisitUtils->statisticsRefererUtils = $statisticsRefererUtils;
$statisticsVisitUtils->templateUtils = $templateUtils;
$statisticsVisitUtils->statisticsPageUtils = $statisticsPageUtils;
$statisticsVisitUtils->adminModuleUtils = $adminModuleUtils;
$statisticsVisitUtils->propertyUtils = $propertyUtils;

$statisticsPageUtils->templateUtils = $templateUtils;
$statisticsPageUtils->clockUtils = $clockUtils;

$backupUtils->languageUtils = $languageUtils;
$backupUtils->preferenceUtils = $preferenceUtils;
$backupUtils->clockUtils = $clockUtils;
$backupUtils->websiteUtils = $websiteUtils;

$websiteUtils->languageUtils = $languageUtils;
$websiteUtils->websiteSubscriptionUtils = $websiteSubscriptionUtils;
$websiteUtils->sqlToolsUtils = $sqlToolsUtils;
$websiteUtils->moduleUtils = $moduleUtils;
$websiteUtils->websiteOptionUtils = $websiteOptionUtils;
$websiteUtils->websiteAddressUtils = $websiteAddressUtils;

$websiteSubscriptionUtils->languageUtils = $languageUtils;
$websiteSubscriptionUtils->clockUtils = $clockUtils;
$websiteSubscriptionUtils->websiteUtils = $websiteUtils;
$websiteSubscriptionUtils->websiteAddressUtils = $websiteAddressUtils;
$websiteSubscriptionUtils->addressUtils = $addressUtils;
$websiteSubscriptionUtils->shopItemUtils = $shopItemUtils;
$websiteSubscriptionUtils->shopOrderUtils = $shopOrderUtils;
$websiteSubscriptionUtils->shopOrderItemUtils = $shopOrderItemUtils;

$websiteOptionUtils->websiteUtils = $websiteUtils;
$websiteOptionUtils->moduleUtils = $moduleUtils;

$locationStateUtils->locationCountryUtils = $locationCountryUtils;

$locationZipCodeUtils->locationCountryUtils = $locationCountryUtils;
$locationZipCodeUtils->locationStateUtils = $locationStateUtils;

?>
