<?php

class ElearningSessionCourse {

  var $id;
  var $elearningSessionId;
  var $elearningCourseId;

  function ElearningSessionCourse($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getElearningSessionId() {
    return($this->elearningSessionId);
    }

  function getElearningCourseId() {
    return($this->elearningCourseId);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setElearningSessionId($elearningSessionId) {
    $this->elearningSessionId = $elearningSessionId;
    }

  function setElearningCourseId($elearningCourseId) {
    $this->elearningCourseId = $elearningCourseId;
    }

  }

?>
