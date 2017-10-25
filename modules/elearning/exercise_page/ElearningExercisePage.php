<?php

class ElearningExercisePage {

  var $id;
  var $name;
  var $description;
  var $instructions;
  var $text;
  var $hideText;
  var $textMaxHeight;
  var $image;
  var $audio;
  var $autostart;
  var $video;
  var $videoUrl;
  var $questionType;
  var $hintPlacement;
  var $elearningExerciseId;
  var $listOrder;

  function ElearningExercisePage($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getDescription() {
    return($this->description);
  }

  function getInstructions() {
    return($this->instructions);
  }

  function getText() {
    return($this->text);
  }

  function getHideText() {
    return($this->hideText);
  }

  function getTextMaxHeight() {
    return($this->textMaxHeight);
  }

  function getImage() {
    return($this->image) ;
  }

  function getAudio() {
    return($this->audio);
  }

  function getAutostart() {
    return($this->autostart);
  }

  function getVideo() {
    return($this->video);
  }

  function getVideoUrl() {
    return($this->videoUrl);
  }

  function getQuestionType() {
    return($this->questionType);
  }

  function getHintPlacement() {
    return($this->hintPlacement);
  }

  function getElearningExerciseId() {
    return($this->elearningExerciseId);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setDescription($description) {
    $this->description = $description;
  }

  function setInstructions($instructions) {
    $this->instructions= $instructions;
  }

  function setText($text) {
    $this->text = $text;
  }

  function setHideText($hideText) {
    $this->hideText = $hideText;
  }

  function setTextMaxHeight($textMaxHeight) {
    $this->textMaxHeight = $textMaxHeight;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setAudio($audio) {
    $this->audio = $audio;
  }

  function setAutostart($autostart) {
    $this->autostart = $autostart;
  }

  function setVideo($video) {
    $this->video = $video;
  }

  function setVideoUrl($videoUrl) {
    $this->videoUrl = $videoUrl;
  }

  function setQuestionType($questionType) {
    $this->questionType = $questionType;
  }

  function setHintPlacement($hintPlacement) {
    $this->hintPlacement = $hintPlacement;
  }

  function setElearningExerciseId($elearningExerciseId) {
    $this->elearningExerciseId = $elearningExerciseId;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
