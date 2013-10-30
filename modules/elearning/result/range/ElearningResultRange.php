<?php

class ElearningResultRange {

  var $id;
  var $upperRange;
  var $grade;

  function ElearningResultRange($id = '', $upperRange = '', $grade = '') {
    $this->upperRange = $upperRange;
    $this->grade = $grade;
  }

  function getId() {
    return($this->id);
  }

  function getUpperRange() {
    return($this->upperRange);
  }

  function getGrade() {
    return($this->grade) ;
  }

  function setId($id) {
    $this->id = $id;
  }

  function setUpperRange($upperRange) {
    $this->upperRange = $upperRange;
  }

  function setGrade($grade) {
    $this->grade = $grade;
  }

}

?>
