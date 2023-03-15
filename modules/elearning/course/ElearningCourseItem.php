<?php

class ElearningCourseItem {

  var $id;
  var $elearningCourseId;
  var $elearningExerciseId;
  var $elearningLessonId;
  var $listOrder;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getElearningCourseId() {
    return($this->elearningCourseId);
  }

  function getElearningExerciseId() {
    return($this->elearningExerciseId);
  }

  function getElearningLessonId() {
    return($this->elearningLessonId);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setElearningCourseId($elearningCourseId) {
    $this->elearningCourseId = $elearningCourseId;
  }

  function setElearningExerciseId($elearningExerciseId) {
    $this->elearningExerciseId = $elearningExerciseId;
  }

  function setElearningLessonId($elearningLessonId) {
    $this->elearningLessonId = $elearningLessonId;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
