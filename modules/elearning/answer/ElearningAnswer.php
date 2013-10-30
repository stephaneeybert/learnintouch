<?php

class ElearningAnswer {

  var $id;
  var $answer;
  var $explanation;
  var $image;
  var $audio;
  var $elearningQuestion;
  var $listOrder;

  function ElearningAnswer($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getAnswer() {
    return($this->answer);
  }

  function getExplanation() {
    return($this->explanation);
  }

  function getImage() {
    return($this->image);
  }

  function getAudio() {
    return($this->audio);
  }

  function getElearningQuestion() {
    return($this->elearningQuestion);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setAnswer($answer) {
    $this->answer = $answer;
  }

  function setExplanation($explanation) {
    $this->explanation = $explanation;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setAudio($audio) {
    $this->audio = $audio;
  }

  function setElearningQuestion($elearningQuestion) {
    $this->elearningQuestion = $elearningQuestion;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
