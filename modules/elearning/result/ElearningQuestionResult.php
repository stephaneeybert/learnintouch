<?php

class ElearningQuestionResult {

  var $id;
  var $elearningResult;
  var $elearningQuestion;
  var $elearningAnswerId;
  var $elearningAnswerText;
  var $elearningAnswerOrder;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getElearningResult() {
    return($this->elearningResult);
  }

  function getElearningQuestion() {
    return($this->elearningQuestion);
  }

  function getElearningAnswerId() {
    return($this->elearningAnswerId);
  }

  function getElearningAnswerText() {
    return($this->elearningAnswerText);
  }

  function getElearningAnswerOrder() {
    return($this->elearningAnswerOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setElearningResult($elearningResult) {
    $this->elearningResult = $elearningResult;
  }

  function setElearningQuestion($elearningQuestion) {
    $this->elearningQuestion = $elearningQuestion;
  }

  function setElearningAnswerId($elearningAnswerId) {
    $this->elearningAnswerId = $elearningAnswerId;
  }

  function setElearningAnswerText($elearningAnswerText) {
    $this->elearningAnswerText = $elearningAnswerText;
  }

  function setElearningAnswerOrder($elearningAnswerOrder) {
    $this->elearningAnswerOrder = $elearningAnswerOrder;
  }

}

?>
