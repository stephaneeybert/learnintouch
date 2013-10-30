<?php

class ElearningCourseInfo {

  var $id;
  var $headline;
  var $information;
  var $listOrder;
  var $elearningCourseId;

  function ElearningCourseInfo($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getHeadline() {
    return($this->headline);
  }

  function getInformation() {
    return($this->information);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getElearningCourseId() {
    return($this->elearningCourseId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setHeadline($headline) {
    $this->headline = $headline;
  }

  function setInformation($information) {
    $this->information = $information;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setElearningCourseId($elearningCourseId) {
    $this->elearningCourseId = $elearningCourseId;
  }

}

?>
