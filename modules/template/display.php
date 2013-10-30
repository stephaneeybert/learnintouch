<?php

require_once("website.php");

$templateUtils->storeRequestedUrl();

$pageId = LibEnv::getEnvHttpGET("pageId");

// Otherwise get the entry page
if (!$pageId) {
  $pageId = $templateUtils->getEntryPage();

  // A redirection is required if the entry page url contains a parameter with a string followed by a number
  if ($pageId && !is_numeric($pageId)) {
    // Add the template model if any
    $templateModelId = $templateUtils->getTemplateModelFromUrl($REQUEST_URI);
    if ($templateModelId) {
      $pageId = LibUtils::addUrlParameter($pageId, 'templateModelId', $templateModelId);
    }

    $str = LibHtml::urlRedirect($pageId);
    printContent($str);
    return;
  } 
}

// Check if the requested page is secured
if ($pageId) {
  if ($templateUtils->isSecuredPage($pageId)) {
    $userUtils->checkValidUserLogin();
  }
}

if ($pageId == 'SYSTEM_PAGE_NEWSPUBLICATION') {

  require_once($gNewsPath . "newsPublication/display.php");

} else if ($pageId == 'SYSTEM_PAGE_NEWSPUBLICATION_LIST') {

  require_once($gNewsPath . "newsPublication/display_list.php");

} else if ($pageId == 'SYSTEM_PAGE_NEWSPAPER') {

  require_once($gNewsPath . "newsPaper/display.php");

} else if ($pageId == 'SYSTEM_PAGE_FORM') {

  require_once($gFormPath . "display.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_LESSON') {

  require_once($gElearningPath . "lesson/display_lesson.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_EXERCISE') {

  require_once($gElearningPath . "exercise/display_exercise.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_RESULT') {

  require_once($gElearningPath . "result/display.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_ASSIGNMENTS') {

  require_once($gElearningPath . "subscription/display_participant_assignments.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_SUBSCRIPTIONS') {

  require_once($gElearningPath . "subscription/display_participant_subscriptions.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_PARTICIPANTS') {

  require_once($gElearningPath . "subscription/display_teacher_subscriptions.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_LIST_EXERCISES') {

  require_once($gElearningPath . "exercise/display_exercises.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_LIST_LESSONS') {

  require_once($gElearningPath . "lesson/display_lessons.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_LIST_TEACHERS') {

  require_once($gElearningPath . "teacher/display_teachers.php");

} else if ($pageId == 'SYSTEM_PAGE_ELEARNING_TEACHER_CORNER') {

  require_once($gElearningPath . "teacher/corner/course/list.php");

} else if ($pageId == 'SYSTEM_PAGE_SHOP_ITEM') {

  require_once($gShopPath . "display.php");

} else if ($pageId == 'SYSTEM_PAGE_DOCUMENT_LIST') {

  $documentCategoryId = LibEnv::getEnvHttpGET("documentCategoryId");

  if (!$documentCategoryId) {
    $documentCategoryId = '0';
  }

  require_once($gDocumentPath . "/display.php");

} else if ($pageId == 'SYSTEM_PAGE_PHOTO_ALBUM_LIST') {

  require_once($gPhotoPath . "/display_list.php");

} else if ($pageId == 'SYSTEM_PAGE_PHOTO_LIST') {

  $photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");

  if (!$photoAlbumId) {
    $photoAlbumId = '0';
  }

  require_once($gPhotoPath . "/display_album.php");

} else if ($pageId == 'SYSTEM_PAGE_PHOTO_CYCLE') {

  require_once($gPhotoPath . "/display_cycle.php");

} else if ($pageId == 'SYSTEM_PAGE_PHOTO_SEARCH') {

  require_once($gPhotoPath . "search.php");

} else if ($pageId == 'SYSTEM_PAGE_LINK_LIST') {

  $linkCategoryId = LibEnv::getEnvHttpGET("linkCategoryId");

  if (!$linkCategoryId) {
    $linkCategoryId = '0';
  }

  require_once($gLinkPath . "/display.php");

} else if ($pageId == 'SYSTEM_PAGE_LINK_CYCLE') {

  require_once($gLinkPath . "/display_cycle.php");

} else if ($pageId == 'SYSTEM_PAGE_PEOPLE_LIST') {

  $peopleCategoryId = LibEnv::getEnvHttpGET("peopleCategoryId");

  if (!$peopleCategoryId) {
    $peopleCategoryId = '0';
  }

  require_once($gPeoplePath . "/display.php");

} else if ($pageId == 'SYSTEM_PAGE_CLIENT_LIST') {

  require_once($gClientPath . "display.php");

} else if ($pageId == 'SYSTEM_PAGE_CLIENT_CYCLE') {

  require_once($gClientPath . "display_cycle.php");

} else if ($pageId == 'SYSTEM_PAGE_GUESTBOOK_LIST') {

  require_once($gGuestbookPath . "display.php");

} else if ($pageId == 'SYSTEM_PAGE_GUESTBOOK_POST') {

  require_once($gGuestbookPath . "post.php");

} else if ($pageId == 'SYSTEM_PAGE_CONTACT_POST') {

  require_once($gContactPath . "post.php");

} else if ($pageId == 'SYSTEM_PAGE_INVITER') {

  require_once($gInviterPath . "invite.php");

} else if ($pageId == 'SYSTEM_PAGE_SHOP_CATEGORY_LIST') {

  require_once($gShopPath . "display_list.php");

} else if ($pageId == 'SYSTEM_PAGE_SHOP_ORDER_LIST') {

  require_once($gShopPath . "order/display_list.php");

} else if ($pageId == 'SYSTEM_PAGE_SHOP_CART') {

  require_once($gShopPath . "item/displayCart.php");

} else if ($pageId == 'SYSTEM_PAGE_SHOP_SEARCH') {

  require_once($gShopPath . "search.php");

} else if ($pageId == 'SYSTEM_PAGE_SHOP_SELECTION') {

  require_once($gShopPath . "item/selection.php");

} else if ($pageId == 'SYSTEM_PAGE_USER_PROFILE') {

  require_once($gUserPath . "editProfile.php");

} else if ($pageId == 'SYSTEM_PAGE_USER_LOGIN') {

  require_once($gUserPath . "login.php");

} else if ($pageId == 'SYSTEM_PAGE_USER_LOGOUT') {

  require_once($gUserPath . "logout.php");

} else if ($pageId == 'SYSTEM_PAGE_USER_GET_PASSWORD') {

  require_once($gUserPath . "getPassword.php");

} else if ($pageId == 'SYSTEM_PAGE_USER_CHANGE_PASSWORD') {

  require_once($gUserPath . "changePassword.php");

} else if ($pageId == 'SYSTEM_PAGE_USER_REGISTER') {

  require_once($gUserPath . "register.php");

} else if ($pageId == 'SYSTEM_PAGE_USER_UNSUBSCRIBE') {

  require_once($gUserPath . "unsubscribe.php");

} else if ($pageId == 'SYSTEM_PAGE_TERMS_OF_SERVICE') {

  require_once($gUserPath . "termsofservice.php");

} else if ($pageId == 'SYSTEM_PAGE_MAIL_REGISTER') {

  require_once($gMailPath . "address/subscribe.php");

} else if ($pageId == 'SYSTEM_PAGE_SMS_REGISTER') {

  require_once($gSmsPath . "number/subscribe.php");

} else if ($pageId == 'SYSTEM_PAGE_SEARCH') {

  require_once($gUtilsPath . "google_search.php");

} else if ($pageId == 'SYSTEM_PAGE_ENTRY_PAGE') {

  require_once($gDynpagePath . "display_entry.php");

} else if ($pageId == 'SYSTEM_PAGE_POST_LOGIN_PAGE') {

  require_once($gDynpagePath . "display_post_login.php");

} else if (is_numeric($pageId)) {

  require_once($gDynpagePath . "display.php");

} else {

  // If no page is to be displayed then display an empty one
  require_once($gDynpagePath . "display.php");

}

?>
