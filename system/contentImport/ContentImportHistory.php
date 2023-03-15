<?php

class ContentImportHistory {

  var $id;
  var $domainName;
  var $course;
  var $importDateTime;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getDomainName() {
    return($this->domainName);
  }

  function getCourse() {
    return($this->course);
  }

  function getLesson() {
    return($this->lesson);
  }

  function getExercise() {
    return($this->exercise);
  }

  function getImportDateTime() {
    return($this->importDateTime);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setDomainName($domainName) {
    $this->domainName = $domainName;
  }

  function setCourse($course) {
    $this->course = $course;
  }

  function setLesson($lesson) {
    $this->lesson = $lesson;
  }

  function setExercise($exercise) {
    $this->exercise = $exercise;
  }

  function setImportDateTime($importDateTime) {
    $this->importDateTime = $importDateTime;
  }

}

?>
