<?PHP

require_once("website.php");

// Check that the importer is registered
$importCertificate = LibEnv::getEnvHttpGET("importCertificate");
$domainName = LibEnv::getEnvHttpGET("domainName");
$permissionKey = LibEnv::getEnvHttpGET("permissionKey");
$logImport = LibEnv::getEnvHttpGET("logImport");

$domainName = urldecode($domainName);

if ($contentImportUtils->isValidCertificate($importCertificate)) {
  $permission = $contentImportUtils->getImportPermission($domainName, $permissionKey);
  if ($contentImportUtils->importIsGranted($permission)) {
    $listIndex = LibEnv::getEnvHttpGET("listIndex");
    $listStep = LibEnv::getEnvHttpGET("listStep");
    // Log the course import in the history
    // Do not log an import when exposing the course for display only
    if ($logImport) {
      $contentImportUtils->logImport($domainName, 'All exercises', '', '');
    }
    $str = $elearningImportUtils->exposeAllExercisesREST($listIndex, $listStep);

    print($str);
  } else {
    $contentImportUtils->illegalImportAttemptAlert($domainName);

    print($permission);
  }
}

?>
