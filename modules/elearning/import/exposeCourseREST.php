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
    $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

    if ($elearningCourse = $elearningCourseUtils->selectById($elearningCourseId)) {
      $name = $elearningCourse->getName();

      // Log the course import in the history
      // Do not log an import when exposing the course for display only
      if ($logImport) {
        $contentImportUtils->logImport($domainName, $name, '', '');
      }

      $str = $elearningImportUtils->exposeCourseREST($elearningCourseId);

      print($str);
    }
  } else {
    $contentImportUtils->illegalImportAttemptAlert($domainName);

    print($permission);
  }
}

?>
