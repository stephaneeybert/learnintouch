<?php

class ContentImport {

  var $id;
  var $domainName;
  var $isImporting;
  var $isExporting;
  var $permissionKey;
  var $permissionStatus;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getDomainName() {
    return($this->domainName);
  }

  function getIsImporting() {
    return($this->isImporting);
  }

  function getIsExporting() {
    return($this->isExporting);
  }

  function getPermissionKey() {
    return($this->permissionKey);
  }

  function getPermissionStatus() {
    return($this->permissionStatus);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setDomainName($domainName) {
    $this->domainName = $domainName;
  }

  function setIsImporting($isImporting) {
    $this->isImporting = $isImporting;
  }

  function setIsExporting($isExporting) {
    $this->isExporting = $isExporting;
  }

  function setPermissionKey($permissionKey) {
    $this->permissionKey = $permissionKey;
  }

  function setPermissionStatus($permissionStatus) {
    $this->permissionStatus = $permissionStatus;
  }

}

?>
