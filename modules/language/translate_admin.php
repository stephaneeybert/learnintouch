<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_LANGUAGE);

$adminModuleUtils->checkAdminModule(MODULE_LANGUAGE_TRANSLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$toLanguageCode = LibEnv::getEnvHttpGET("toLanguageCode");

$filePaths = LibDir::getDirFileNames($gModulesPath, 'Utils.en.php');
$filePaths = array_merge($filePaths, LibDir::getDirFileNames($gModulesPath, 'Pdf.en.php'));
$filePaths = array_merge($filePaths, 
  array(
    $gModulesPath . "user/.login.en.php", 
    $gModulesPath . "user/.loginController.en.php",
    $gModulesPath . "user/.logout.en.php",
    $gModulesPath . "user/.register.en.php", 
    $gModulesPath . "user/.registerController.en.php",
    $gModulesPath . "user/.getPassword.en.php",
    $gModulesPath . "user/.changePassword.en.php",
    $gModulesPath . "user/.editProfile.en.php",
    $gModulesPath . "user/.image.en.php",
    $gModulesPath . "user/.validate_email.en.php",
    $gModulesPath . "user/.unsubscribe.en.php",
    $gModulesPath . "elearning/teacher/.register.en.php",
    $gModulesPath . "elearning/subscription/.add.en.php",
    $gModulesPath . "elearning/result/.send.en.php",
    $gModulesPath . "elearning/result/.send_comment.en.php",
    $gModulesPath . "elearning/exercise/.display_contact_page.en.php",
    $gModulesPath . "elearning/exercise/.display_exercises.en.php",
    $gModulesPath . "elearning/exercise/.send.en.php",
    $gModulesPath . "elearning/lesson/.send.en.php",
    $gModulesPath . "elearning/lesson/.display_lessons.en.php",
    $gModulesPath . "elearning/teacher/corner/course/info/.edit.en.php",
    $gModulesPath . "elearning/teacher/corner/course/info/.list.en.php",
    $gModulesPath . "elearning/teacher/corner/course/info/.delete.en.php",
    $gModulesPath . "elearning/teacher/corner/course/.edit.en.php",
    $gModulesPath . "elearning/teacher/corner/course/.list.en.php",
    $gModulesPath . "elearning/teacher/corner/course/.add_lesson.en.php",
    $gModulesPath . "elearning/teacher/corner/course/.remove_lesson.en.php",
    $gModulesPath . "elearning/teacher/corner/course/.add_exercise.en.php",
    $gModulesPath . "elearning/teacher/corner/course/.remove_exercise.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/.edit.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/.audio.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/.compose.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/.delete.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/.image.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/.instructions.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/.introduction.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/page/.content.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/page/.edit.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/page/.audio.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/page/.delete.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/page/.duplicate.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/page/.image.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/page/.instructions.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/.edit.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/.audio.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/.compose.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/.delete.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/.image.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/.instructions.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/.introduction.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/paragraph/.content.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/paragraph/.edit.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/paragraph/.audio.en.php",
    $gModulesPath . "elearning/teacher/corner/lesson/paragraph/.image.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/question/.edit.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/question/.audio.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/question/.delete.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/question/.duplicate.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/question/.image.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/answer/.edit.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/answer/.audio.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/answer/.delete.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/answer/.solution.en.php",
    $gModulesPath . "elearning/teacher/corner/exercise/answer/.image.en.php",
    $gModulesPath . "photo/.search.en.php",
    $gModulesPath . "guestbook/.post.en.php",
    $gModulesPath . "contact/.sendForm.en.php",
    $gModulesPath . "contact/.post.en.php",
    $gModulesPath . "form/.controller.en.php",
    $gModulesPath . "form/.acknowledge.en.php",
    $gModulesPath . "mail/address/.subscribe.en.php",
    $gModulesPath . "sms/number/.subscribe.en.php",
    $gModulesPath . "news/newsStory/.send.en.php",
    $gModulesPath . "news/newsPaper/.send.en.php",
    $gModulesPath . "shop/order/.confirm.en.php",
    $gModulesPath . "shop/order/.checkout.en.php",
    $gModulesPath . "shop/payment/.notify.en.php",
    $gModulesPath . "shop/payment/.cancel.en.php",
    $gModulesPath . "shop/payment/.message_complete.en.php",
    $gModulesPath . "shop/payment/.message_cancel.en.php",
    $gModulesPath . "shop/.search.en.php",
    $gSystemPath . "social/inviter/.invite.en.php",
    $gSystemPath . "social/inviter/.get_contacts.en.php",
    $gSystemPath . "social/.register.en.php",
    $gSystemPath . "utils/.google_search.en.php"
  ));

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->addLine();

foreach ($filePaths as $filePath) {
  $scriptFilePath = str_replace('/.', '/', $filePath);
  $scriptFilePath = str_replace('.en', '', $scriptFilePath);
  $displayFilePath = str_replace($gEnginePath, '', $filePath);

  $displayFilePath = "<a href='$gLanguageUrl/translate.php?filePath=$scriptFilePath&toLanguageCode=$toLanguageCode' $gJSNoStatus title='$mlText[2]'><img border='0' src='$gCommonImagesUrl/$gImageEdit' title=''> $displayFilePath</a>";

  $panelUtils->addLine($displayFilePath);
}

$str = $panelUtils->render();

printAdminPage($str);

?>
