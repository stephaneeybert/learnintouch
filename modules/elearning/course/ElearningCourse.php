<?php

class ElearningCourse {

  var $id;
  var $name;
  var $description;
  var $image;
  var $instantCorrection;
  var $instantCongratulation;
  var $instantSolution;
  var $importable;
  var $locked;
  var $secured;
  var $freeSamples;
  var $autoSubscription;
  var $autoUnsubscription;
  var $interruptTimedOutExercise;
  var $resetExerciseAnswers;
  var $exerciseOnlyOnce;
  var $exerciseAnyOrder;
  var $saveResultOption;
  var $shuffleQuestions;
  var $shuffleAnswers;
  var $matterId;
  var $userId;

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

  function getImage() {
    return($this->image) ;
  }

  function getInstantCorrection() {
    return($this->instantCorrection);
  }

  function getInstantCongratulation() {
    return($this->instantCongratulation);
  }

  function getInstantSolution() {
    return($this->instantSolution);
  }

  function getImportable() {
    return($this->importable);
  }

  function getLocked() {
    return($this->locked);
  }

  function getSecured() {
    return($this->secured);
  }

  function getFreeSamples() {
    return($this->freeSamples);
  }

  function getAutoSubscription() {
    return($this->autoSubscription);
  }

  function getAutoUnsubscription() {
    return($this->autoUnsubscription);
  }

  function getInterruptTimedOutExercise() {
    return($this->interruptTimedOutExercise);
  }

  function getResetExerciseAnswers() {
    return($this->resetExerciseAnswers);
  }

  function getExerciseOnlyOnce() {
    return($this->exerciseOnlyOnce);
  }

  function getExerciseAnyOrder() {
    return($this->exerciseAnyOrder);
  }

  function getSaveResultOption() {
    return($this->saveResultOption);
  }

  function getShuffleQuestions() {
    return($this->shuffleQuestions);
  }

  function getShuffleAnswers() {
    return($this->shuffleAnswers);
  }

  function getMatterId() {
    return($this->matterId);
  }

  function getUserId() {
    return($this->userId);
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

  function setImage($image) {
    $this->image = $image;
  }

  function setInstantCorrection($instantCorrection) {
    $this->instantCorrection = $instantCorrection;
  }

  function setInstantCongratulation($instantCongratulation) {
    $this->instantCongratulation = $instantCongratulation;
  }

  function setInstantSolution($instantSolution) {
    $this->instantSolution = $instantSolution;
  }

  function setImportable($importable) {
    $this->importable = $importable;
  }

  function setLocked($locked) {
    $this->locked = $locked;
  }

  function setSecured($secured) {
    $this->secured = $secured;
  }

  function setFreeSamples($freeSamples) {
    $this->freeSamples = $freeSamples;
  }

  function setAutoSubscription($autoSubscription) {
    $this->autoSubscription = $autoSubscription;
  }

  function setAutoUnsubscription($autoUnsubscription) {
    $this->autoUnsubscription = $autoUnsubscription;
  }

  function setInterruptTimedOutExercise($interruptTimedOutExercise) {
    $this->interruptTimedOutExercise = $interruptTimedOutExercise;
  }

  function setResetExerciseAnswers($resetExerciseAnswers) {
    $this->resetExerciseAnswers = $resetExerciseAnswers;
  }

  function setExerciseOnlyOnce($exerciseOnlyOnce) {
    $this->exerciseOnlyOnce = $exerciseOnlyOnce;
  }

  function setExerciseAnyOrder($exerciseAnyOrder) {
    $this->exerciseAnyOrder = $exerciseAnyOrder;
  }

  function setSaveResultOption($saveResultOption) {
    $this->saveResultOption = $saveResultOption;
  }

  function setShuffleQuestions($shuffleQuestions) {
    $this->shuffleQuestions = $shuffleQuestions;
  }

  function setShuffleAnswers($shuffleAnswers) {
    $this->shuffleAnswers = $shuffleAnswers;
  }

  function setMatterId($matterId) {
    $this->matterId = $matterId;
  }

  function setUserId($userId) {
    $this->userId = $userId;
  }

}

?>
