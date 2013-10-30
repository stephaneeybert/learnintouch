<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $adminId = LibEnv::getEnvHttpPOST("adminId");

  // Check that an admin is selected
  if ($admin = $adminUtils->selectById($adminId)) {
    $firstname = $admin->getFirstname();
    $lastname = $admin->getLastname();

    $str = <<<HEREDOC
<script type='text/javascript'>

// Get the form of the parent window
var form = window.opener.document.forms['edit'];

// Set the adminId field
if (form.elements['adminId']) {
  form.elements['adminId'].value = '$adminId';
  }

// Set the webpageName field in the model navigation elements
if (form.elements['adminName']) {
  form.elements['adminName'].value = '$firstname $lastname';
  }

</script>
HEREDOC;

    printMessage($str);

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
    }
  }

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");

$listAdmins = array();
if ($searchPattern) {
  $admins = $adminUtils->selectLikePattern($searchPattern);
  } else {
  $admins = $adminUtils->selectAll();
  }

// Create the admin html select list
$adminList = Array('' => '');
foreach ($admins as $admin) {
  $wAdminId = $admin->getId();
  $firstname = $admin->getFirstname();
  $lastname = $admin->getLastname();
  $adminList[$wAdminId] = "$firstname $lastname";
  }
$strSelectAdmin = LibHtml::getSelectList("adminId", $adminList);

$panelUtils->setHeader($mlText[0]);
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> " );
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectAdmin);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
