<?php

class ElearningSolution {

  var $id;
  var $elearningQuestion;
  var $elearningAnswer;

  function ElearningSolution($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getElearningAnswer() {
    return($this->elearningAnswer);
  }

  function getElearningQuestion() {
    return($this->elearningQuestion);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setElearningAnswer($elearningAnswer) {
    $this->elearningAnswer = $elearningAnswer;
  }

  function setElearningQuestion($elearningQuestion) {
    $this->elearningQuestion = $elearningQuestion;
  }

}

?>
