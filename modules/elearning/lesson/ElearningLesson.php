<?php

class ElearningLesson {

  var $id;
  var $name;
  var $description;
  var $instructions;
  var $image;
  var $audio;
  var $introduction;
  var $secured;
  var $publicAccess;
  var $releaseDate;
  var $garbage;
  var $locked;
  var $lessonModelId;
  var $categoryId;
  var $levelId;
  var $subjectId;

  function __construct($id = '') {
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

  function getImage() {
    return($this->image) ;
  }

  function getAudio() {
    return($this->audio);
  }

  function getIntroduction() {
    return($this->introduction);
  }

  function getSecured() {
    return($this->secured);
  }

  function getPublicAccess() {
    return($this->publicAccess);
  }

  function getReleaseDate() {
    return($this->releaseDate);
  }

  function getGarbage() {
    return($this->garbage);
  }

  function getLocked() {
    return($this->locked);
  }

  function getLessonModelId() {
    return($this->lessonModelId);
  }

  function getLevelId() {
    return($this->levelId);
  }

  function getCategoryId() {
    return($this->categoryId);
  }

  function getSubjectId() {
    return($this->subjectId);
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
    $this->instructions = $instructions;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setAudio($audio) {
    $this->audio = $audio;
  }

  function setIntroduction($introduction) {
    $this->introduction = $introduction;
  }

  function setSecured($secured) {
    $this->secured = $secured;
  }

  function setPublicAccess($publicAccess) {
    $this->publicAccess = $publicAccess;
  }

  function setReleaseDate($releaseDate) {
    $this->releaseDate = $releaseDate;
  }

  function setGarbage($garbage) {
    $this->garbage = $garbage;
  }

  function setLocked($locked) {
    $this->locked = $locked;
  }

  function setLessonModelId($lessonModelId) {
    $this->lessonModelId = $lessonModelId;
  }

  function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }

  function setLevelId($levelId) {
    $this->levelId = $levelId;
  }

  function setSubjectId($subjectId) {
    $this->subjectId = $subjectId;
  }

}

?>
