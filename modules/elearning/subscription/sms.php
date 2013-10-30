<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);
$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);


$elearningSubscriptionId = LibEnv::getEnvHttpGET("elearningSubscriptionId");
$elearningSessionId = LibEnv::getEnvHttpGET("elearningSessionId");
$elearningClassId = LibEnv::getEnvHttpGET("elearningClassId");

if (!$elearningSubscriptionId) {
  $elearningSubscriptionId = LibEnv::getEnvHttpPOST("elearningSubscriptionId");
  }

if (!$elearningSessionId) {
  $elearningSessionId = LibEnv::getEnvHttpPOST("elearningSessionId");
  }

if (!$elearningClassId) {
  $elearningClassId = LibEnv::getEnvHttpPOST("elearningClassId");
  }

// These variables are set for use by another included script
$strImposedSelectList = '';
$parentUrl = "$gElearningUrl/subscription/admin.php";
$strHiddenPost = "<input type='hidden' name='elearningSubscriptionId' value='$elearningSubscriptionId'><input type='hidden' name='elearningSessionId' value='$elearningSessionId'><input type='hidden' name='elearningClassId' value='$elearningClassId'>";

// Create the list of mobile phone numbers
if ($elearningSubscriptionId) {
  if ($elearningSubscription = $elearningSubscriptionUtils->selectById($elearningSubscriptionId)) {
    $userId = $elearningSubscription->getUserId();
    if ($user = $userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $mobilePhone = $user->getMobilePhone();
      $smsSubscribe = $user->getSmsSubscribe();
      $password = $user->getPassword();
      if ($mobilePhone && $smsSubscribe) {
        // Add the number to the list
        $smsRecipients = array();
        array_push($smsRecipients, array($mobilePhone, $firstname, $lastname, $password));
        $strImposedSelectList = $mlText[3] . ' ' . $firstname . ' ' . $lastname . ' ' . $mobilePhone;
        }
      }
    }
  } else if ($elearningSessionId && $elearningClassId) {
  // Get the class name
  $strClass = '';
  if ($elearningClass = $elearningClassUtils->selectById($elearningClassId)) {
    $strClass = $elearningClass->getName();
    }
  // Get the session name
  $strSession = '';
  if ($elearningSession = $elearningSessionUtils->selectById($elearningSessionId)) {
    $sessionName = $elearningSession->getName();
    $openDate = $elearningSession->getOpenDate();
    $closeDate = $elearningSession->getCloseDate();
    $openDate = $clockUtils->systemToLocalNumericDate($openDate);
    if ($clockUtils->systemDateIsSet($closeDate)) {
      $closeDate = $clockUtils->systemToLocalNumericDate($closeDate);
      } else {
      $closeDate = '';
      }
    $strSession = $sessionName . ' (' . $openDate . ' / ' . $closeDate . ')';
    }
  $strImposedSelectList = $mlText[2] . ' ' . $strClass . ' ' . $mlText[1] . ' ' . $strSession;
  $smsRecipients = array();
  $elearningSubscriptions = $elearningSubscriptionUtils->selectBySessionIdAndClassId($elearningSessionId, $elearningClassId);
  foreach ($elearningSubscriptions as $elearningSubscription) {
    $elearningSubscriptionId = $elearningSubscription->getId();
    $userId = $elearningSubscription->getUserId();
    if ($user = $userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $mobilePhone = $user->getMobilePhone();
      $password = $user->getPassword();
      if ($mobilePhone) {
        // Add the number to the list
        array_push($smsRecipients, array($mobilePhone, $firstname, $lastname, $password));
        }
      }
    }
  }

require_once($gSmsPath . "send.php");

?>
