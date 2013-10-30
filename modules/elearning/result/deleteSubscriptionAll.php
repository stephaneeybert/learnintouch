<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");

  if ($elearningResults = $elearningResultUtils->selectBySubscriptionId($elearningSubscriptionId)) {
    foreach($elearningResults as $elearningResult) {
      $elearningResultId = $elearningResult->getId();
      $elearningResultUtils->deleteResult($elearningResultId);
      }
    }

  $str = LibHtml::urlRedirect("$gElearningUrl/result/admin.php");
  printContent($str);
  return;

  } else {

  $elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");

  // Delete all the results of a subscription

  // Get the course details
  $name = '';
  $description = '';
  $firstname = '';
  $lastname = '';
  $email = '';
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $courseId = $elearningSubscription->getCourseId();
    $userId = $elearningSubscription->getUserId();

    if ($elearningCourse = $elearningCourseUtils->selectById($courseId)) {
      $name = $elearningCourse->getName();
      $description = $elearningCourse->getDescription();
      }

    if ($user = $userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $email = $user->getEmail();
      }
    }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/result/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "$name - $description");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $firstname . ' ' . $lastname);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $email);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningSubscriptionId', $elearningSubscriptionId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
