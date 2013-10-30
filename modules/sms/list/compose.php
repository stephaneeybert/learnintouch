<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_SMS);

$mlText = $languageUtils->getMlText(__FILE__);

$addUser = LibEnv::getEnvHttpPOST("addUser");
$removeUser = LibEnv::getEnvHttpPOST("removeUser");
$addSmsNumber = LibEnv::getEnvHttpPOST("addSmsNumber");
$removeSmsNumber = LibEnv::getEnvHttpPOST("removeSmsNumber");

if ($removeUser) {
  $smsListId = LibEnv::getEnvHttpPOST("smsListId");
  $listUserIds = LibEnv::getEnvHttpPOST("listUserIds");

  if ($listUserIds && $smsListId) {
    foreach ($listUserIds as $listUserId) {
      if ($smsListUser = $smsListUserUtils->selectBySmsListIdAndUserId($smsListId, $listUserId)) {
        $smsListUserId = $smsListUser->getId();
        $smsListUserUtils->delete($smsListUserId);
        }
      }
    }
  } else if ($addUser) {
  $smsListId = LibEnv::getEnvHttpPOST("smsListId");
  $userIds = LibEnv::getEnvHttpPOST("userIds");

  if ($userIds && $smsListId) {
    foreach ($userIds as $userId) {
      if (!$smsListUser = $smsListUserUtils->selectBySmsListIdAndUserId($smsListId, $userId)) {
        $smsListUser = new SmsListUser();
        $smsListUser->setUserId($userId);
        $smsListUser->setSmsListId($smsListId);
        $smsListUserUtils->insert($smsListUser);
        }
      }
    }
  } else if ($removeSmsNumber) {
  $smsListId = LibEnv::getEnvHttpPOST("smsListId");
  $listSmsNumberIds = LibEnv::getEnvHttpPOST("listSmsNumberIds");

  if ($listSmsNumberIds && $smsListId) {
    foreach ($listSmsNumberIds as $listSmsNumberId) {
      if ($smsListNumber = $smsListNumberUtils->selectBySmsListIdAndSmsNumberId($smsListId, $listSmsNumberId)) {
        $smsListNumberId = $smsListNumber->getId();
        $smsListNumberUtils->delete($smsListNumberId);
        }
      }
    }
  } else if ($addSmsNumber) {
  $smsListId = LibEnv::getEnvHttpPOST("smsListId");
  $smsNumberIds = LibEnv::getEnvHttpPOST("smsNumberIds");

  if ($smsNumberIds && $smsListId) {
    foreach ($smsNumberIds as $smsNumberId) {
      if (!$smsListUser = $smsListNumberUtils->selectBySmsListIdAndSmsNumberId($smsListId, $smsNumberId)) {
        $smsListNumber = new SmsListNumber();
        $smsListNumber->setSmsNumberId($smsNumberId);
        $smsListNumber->setSmsListId($smsListId);
        $smsListNumberUtils->insert($smsListNumber);
        }
      }
    }
  }

$smsListId = LibEnv::getEnvHttpGET("smsListId");

if (!$smsListId) {
  $smsListId = LibEnv::getEnvHttpPOST("smsListId");
  }

$name = '';
if ($smsListId) {
  if ($smsList = $smsListUtils->selectById($smsListId)) {
    $name = $smsList->getName();
    }
  }

$smsListUsers = $smsListUserUtils->selectBySmsListId($smsListId);
$smsList = Array();
foreach ($smsListUsers as $smsListUser) {
  $wId = $smsListUser->getId();
  $wSmsListId = $smsListUser->getSmsListId();
  $wUserId = $smsListUser->getUserId();
  if ($user = $userUtils->selectById($wUserId)) {
    $wFirstname = $user->getFirstname();
    $wLastname = $user->getLastname();
    $wMobilePhone = $user->getMobilePhone();
    if ($wMobilePhone) {
      if ($wFirstname && $wLastname) {
        $wName = "$wLastname $wFirstname";
        } else {
        $wName = $wMobilePhone;
        }
      $smsList[$wUserId] = "$wName";
      }
    }
  }
$strSelectListUser = LibHtml::getMultiSelectList("listUserIds", $smsList, 10);

$users = $userUtils->selectAllSmsSubscribers();
$userList = Array();
foreach ($users as $user) {
  $wId = $user->getId();
  $wFirstname = $user->getFirstname();
  $wLastname = $user->getLastname();
  $wMobilePhone = $user->getMobilePhone();
  if (!$smsListUser = $smsListUserUtils->selectBySmsListIdAndUserId($smsListId, $wId)) {
    if ($wMobilePhone) {
      if ($wFirstname && $wLastname) {
        $wName = "$wLastname $wFirstname";
        } else {
        $wName = $wMobilePhone;
        }
      $userList[$wId] = "$wName";
      }
    }
  }
$strSelectUser = LibHtml::getMultiSelectList("userIds", $userList, 10);

$smsListNumbers = $smsListNumberUtils->selectBySmsListId($smsListId);
$smsList = Array();
foreach ($smsListNumbers as $smsListNumber) {
  $wId = $smsListNumber->getId();
  $wSmsListId = $smsListNumber->getSmsListId();
  $wSmsNumberId = $smsListNumber->getSmsNumberId();
  if ($smsNumber = $smsNumberUtils->selectById($wSmsNumberId)) {
    $wFirstname = $smsNumber->getFirstname();
    $wLastname = $smsNumber->getLastname();
    $wMobilePhone = $smsNumber->getMobilePhone();
    if ($wMobilePhone) {
      $smsList[$wSmsNumberId] = "$wFirstname $wLastname $wMobilePhone";
      }
    }
  }
$strSelectListSmsNumber = LibHtml::getMultiSelectList("listSmsNumberIds", $smsList, 10);

$smsNumbers = $smsNumberUtils->selectSubscribers();
$smsNumberList = Array();
foreach ($smsNumbers as $smsNumber) {
  $wId = $smsNumber->getId();
  $wFirstname = $smsNumber->getFirstname();
  $wLastname = $smsNumber->getLastname();
  $wMobilePhone = $smsNumber->getMobilePhone();
  if (!$smsListNumber = $smsListNumberUtils->selectBySmsListIdAndSmsNumberId($smsListId, $wId)) {
    if ($wMobilePhone) {
      $smsNumberList[$wId] = "$wFirstname $wLastname $wMobilePhone";
      }
    }
  }
$strSelectSmsNumber = LibHtml::getMultiSelectList("smsNumberIds", $smsNumberList, 10);

$panelUtils->setHeader($mlText[0], "$gSmsUrl/list/admin.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine('', $panelUtils->addCell($mlText[6], "nbr"), $name);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('smsListId', $smsListId);
$panelUtils->closeForm();
$panelUtils->addLine();
$strCell1 = ''
  . "\n<form action='$PHP_SELF' method='post'>"
  . "\n" . $strSelectListUser;

$strCell2 = ''
  . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageRight'>"
  . "\n<input type='hidden' name='removeUser' value='1'>"
  . "\n<input type='hidden' name='smsListId' value='$smsListId'>"
  . "\n</form>"
  . "\n<form action='$PHP_SELF' method='post'>"
  . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageLeft'>";

$strCell3 = ''
  . "\n" . $strSelectUser
  . "\n<input type='hidden' name='addUser' value='1'>"
  . "\n<input type='hidden' name='smsListId' value='$smsListId'>"
  . "\n</form>";

$panelUtils->addLine($panelUtils->addCell(count($smsListUsers) . ' ' . $mlText[1], "nbr"), '', $panelUtils->addCell($mlText[2], "nb"));
$panelUtils->addLine($panelUtils->addCell($strCell1, "nr"), $panelUtils->addCell($strCell2, "nc"), $strCell3);
$panelUtils->addLine();

$strCell1 = ''
  . "\n<form action='$PHP_SELF' method='post'>"
  . "\n" . $strSelectListSmsNumber;

$strCell2 = ''
  . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageRight'>"
  . "\n<input type='hidden' name='removeSmsNumber' value='1'>"
  . "\n<input type='hidden' name='smsListId' value='$smsListId'>"
  . "\n</form>"
  . "\n<form action='$PHP_SELF' method='post'>"
  . "\n<input type='image' border='0' src='$gCommonImagesUrl/$gImageLeft'>";

$strCell3 = ''
  . "\n" . $strSelectSmsNumber
  . "\n<input type='hidden' name='addSmsNumber' value='1'>"
  . "\n<input type='hidden' name='smsListId' value='$smsListId'>"
  . "\n</form>";

$panelUtils->addLine($panelUtils->addCell(count($smsListNumbers) . ' ' . $mlText[3], "nbr"), '', $panelUtils->addCell($mlText[4], "nb"));
$panelUtils->addLine($panelUtils->addCell($strCell1, "nr"), $panelUtils->addCell($strCell2, "nc"), $strCell3);
$panelUtils->addLine();
$str = $panelUtils->render();

printAdminPage($str);

?>
