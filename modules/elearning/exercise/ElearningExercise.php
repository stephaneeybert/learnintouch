<?php

class ElearningExercise {

  var $id;
  var $name;
  var $description;
  var $instructions;
  var $introduction;
  var $hideIntroduction;
  var $image;
  var $audio;
  var $autostart;
  var $publicAccess;
  var $maxDuration;
  var $releaseDate;
  var $secured;
  var $skipExerciseIntroduction;
  var $socialConnect;
  var $hideSolutions;
  var $hideProgressionBar;
  var $hidePageTabs;
  var $disableNextPageTabs;
  var $numberPageTabs;
  var $hideKeyboard;
  var $contactPage;
  var $categoryId;
  var $webpageId;
  var $levelId;
  var $subjectId;
  var $scoringId;
  var $garbage;
  var $locked;

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

  function getIntroduction() {
    return($this->introduction);
  }

  function getHideIntroduction() {
    return($this->hideIntroduction);
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

  function getPublicAccess() {
    return($this->publicAccess);
  }

  function getMaxDuration() {
    return($this->maxDuration);
  }

  function getReleaseDate() {
    return($this->releaseDate);
  }

  function getSecured() {
    return($this->secured);
  }

  function getSkipExerciseIntroduction() {
    return($this->skipExerciseIntroduction);
  }

  function getSocialConnect() {
    return($this->socialConnect);
  }

  function getHideSolutions() {
    return($this->hideSolutions);
  }

  function getHideProgressionBar() {
    return($this->hideProgressionBar);
  }

  function getHidePageTabs() {
    return($this->hidePageTabs);
  }

  function getDisableNextPageTabs() {
    return($this->disableNextPageTabs);
  }

  function getNumberPageTabs() {
    return($this->numberPageTabs);
  }

  function getHideKeyboard() {
    return($this->hideKeyboard);
  }

  function getContactPage() {
    return($this->contactPage);
  }

  function getCategoryId() {
    return($this->categoryId);
  }

  function getWebpageId() {
    return($this->webpageId);
  }

  function getLevelId() {
    return($this->levelId);
  }

  function getSubjectId() {
    return($this->subjectId);
  }

  function getScoringId() {
    return($this->scoringId);
  }

  function getGarbage() {
    return($this->garbage);
  }

  function getLocked() {
    return($this->locked);
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

  function setIntroduction($introduction) {
    $this->introduction = $introduction;
  }

  function setHideIntroduction($hideIntroduction) {
    $this->hideIntroduction = $hideIntroduction;
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

  function setPublicAccess($publicAccess) {
    $this->publicAccess = $publicAccess;
  }

  function setMaxDuration($maxDuration) {
    $this->maxDuration = $maxDuration;
  }

  function setReleaseDate($releaseDate) {
    $this->releaseDate = $releaseDate;
  }

  function setSecured($secured) {
    $this->secured = $secured;
  }

  function setSkipExerciseIntroduction($skipExerciseIntroduction) {
    $this->skipExerciseIntroduction = $skipExerciseIntroduction;
  }

  function setSocialConnect($socialConnect) {
    $this->socialConnect = $socialConnect;
  }

  function setHideSolutions($hideSolutions) {
    $this->hideSolutions = $hideSolutions;
  }

  function setHideProgressionBar($hideProgressionBar) {
    $this->hideProgressionBar = $hideProgressionBar;
  }

  function setHidePageTabs($hidePageTabs) {
    $this->hidePageTabs = $hidePageTabs;
  }

  function setDisableNextPageTabs($disableNextPageTabs) {
    $this->disableNextPageTabs = $disableNextPageTabs;
  }

  function setNumberPageTabs($numberPageTabs) {
    $this->numberPageTabs = $numberPageTabs;
  }

  function setHideKeyboard($hideKeyboard) {
    $this->hideKeyboard = $hideKeyboard;
  }

  function setContactPage($contactPage) {
    $this->contactPage = $contactPage;
  }

  function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }

  function setWebpageId($webpageId) {
    $this->webpageId = $webpageId;
  }

  function setLevelId($levelId) {
    $this->levelId = $levelId;
  }

  function setSubjectId($subjectId) {
    $this->subjectId = $subjectId;
  }

  function setScoringId($scoringId) {
    $this->scoringId = $scoringId;
  }

  function setGarbage($garbage) {
    $this->garbage = $garbage;
  }

  function setLocked($locked) {
    $this->locked = $locked;
  }

}

?>
