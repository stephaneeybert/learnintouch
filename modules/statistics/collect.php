<?PHP

if (!$adminUtils->getSessionLogin() && $REQUEST_URI != '/admin.php' && $REQUEST_URI != '/engine/system/admin/login.php') {
  $statisticsVisitUtils->logVisit();
}

?>
