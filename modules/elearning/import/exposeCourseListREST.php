<?PHP

require_once("website.php");

// Check that the importer is registered
$importCertificate = LibEnv::getEnvHttpGET("importCertificate");
$domainName = LibEnv::getEnvHttpGET("domainName");
$permissionKey = LibEnv::getEnvHttpGET("permissionKey");

$domainName = urldecode($domainName);

if ($contentImportUtils->isValidCertificate($importCertificate)) {
  $permission = $contentImportUtils->getImportPermission($domainName, $permissionKey);
  if ($contentImportUtils->importIsGranted($permission)) {
    $str = $elearningImportUtils->exposeCourseListREST();

    print($str);
  } else {
    $contentImportUtils->illegalImportAttemptAlert($domainName);

    print($permission);
  }
}

?>
