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
    $searchPattern = LibEnv::getEnvHttpGET("searchPattern");
    $searchPattern = urldecode($searchPattern);
    // Log the course import in the history
    // Do not log an import when exposing the course for display only
    if ($logImport) {
      $contentImportUtils->logImport($domainName, $searchPattern, '', '');
    }
    $str = $elearningImportUtils->exposeSearchedContentREST($searchPattern);

    print($str);
  } else {
    $contentImportUtils->illegalImportAttemptAlert($domainName);

    print($permission);
  }
}

?>
