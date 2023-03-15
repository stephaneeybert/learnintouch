<?php

class ElearningQuestion {

  var $id;
  var $question;
  var $explanation;
  var $image;
  var $audio;
  var $hint;
  var $points;
  var $answerNbWords;
  var $listOrder;
  var $elearningExercisePageId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getQuestion() {
    return($this->question);
  }

  function getExplanation() {
    return($this->explanation);
  }

  function getElearningExercisePage() {
    return($this->elearningExercisePageId);
  }

  function getImage() {
    return($this->image) ;
  }

  function getAudio() {
    return($this->audio);
  }

  function getHint() {
    return($this->hint);
  }

  function getPoints() {
    return($this->points);
  }

  function getAnswerNbWords() {
    return($this->answerNbWords);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setQuestion($question) {
    $this->question = $question;
  }

  function setExplanation($explanation) {
    $this->explanation = $explanation;
  }

  function setElearningExercisePage($elearningExercisePageId) {
    $this->elearningExercisePageId = $elearningExercisePageId;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setAudio($audio) {
    $this->audio = $audio;
  }

  function setHint($hint) {
    $this->hint = $hint;
  }

  function setPoints($points) {
    $this->points = $points;
  }

  function setAnswerNbWords($answerNbWords) {
    $this->answerNbWords = $answerNbWords;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
