<?php

class ElearningLessonParagraph {

  var $id;
  var $headline;
  var $body;
  var $image;
  var $audio;
  var $video;
  var $videoUrl;
  var $listOrder;
  var $elearningLessonId;
  var $elearningLessonHeadingId;
  var $elearningExerciseId;
  var $exerciseTitle;

  function ElearningLessonParagraph($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getHeadline() {
    return($this->headline) ;
  }

  function getBody() {
    return($this->body) ;
  }

  function getImage() {
    return($this->image) ;
  }

  function getAudio() {
    return($this->audio);
  }

  function getVideo() {
    return($this->video);
  }

  function getVideoUrl() {
    return($this->videoUrl);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function getElearningLessonId() {
    return($this->elearningLessonId);
  }

  function getElearningLessonHeadingId() {
    return($this->elearningLessonHeadingId);
  }

  function getElearningExerciseId() {
    return($this->elearningExerciseId);
  }

  function getExerciseTitle() {
    return($this->exerciseTitle);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setHeadline($headline) {
    $this->headline = $headline;
  }

  function setBody($body) {
    $this->body = $body;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setAudio($audio) {
    $this->audio = $audio;
  }

  function setVideo($video) {
    $this->video = $video;
  }

  function setVideoUrl($videoUrl) {
    $this->videoUrl = $videoUrl;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

  function setElearningLessonId($elearningLessonId) {
    $this->elearningLessonId = $elearningLessonId;
  }

  function setElearningLessonHeadingId($elearningLessonHeadingId) {
    $this->elearningLessonHeadingId = $elearningLessonHeadingId;
  }

  function setElearningExerciseId($elearningExerciseId) {
    $this->elearningExerciseId = $elearningExerciseId;
  }

  function setExerciseTitle($exerciseTitle) {
    $this->exerciseTitle = $exerciseTitle;
  }

}

?>
