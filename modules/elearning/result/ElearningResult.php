<?php

class ElearningResult {

  var $id;
  var $elearningExerciseId;
  var $subscriptionId;
  var $exerciseDate;
  var $exerciseElapsedTime;
  var $firstname;
  var $lastname;
  var $message;
  var $comment;
  var $hideComment;
  var $email;
  var $nbReadingQuestions;
  var $nbWritingQuestions;
  var $nbListeningQuestions;
  var $nbCorrectReadingAnswers;
  var $nbIncorrectReadingAnswers;
  var $nbCorrectWritingAnswers;
  var $nbIncorrectWritingAnswers;
  var $nbCorrectListeningAnswers;
  var $nbIncorrectListeningAnswers;
  var $nbReadingPoints;
  var $nbWritingPoints;
  var $nbListeningPoints;
  var $nbNotAnswered;
  var $nbIncorrectAnswers;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getElearningExerciseId() {
    return($this->elearningExerciseId);
  }

  function getSubscriptionId() {
    return($this->subscriptionId);
  }

  function getExerciseDate() {
    return($this->exerciseDate);
  }

  function getExerciseElapsedTime() {
    return($this->exerciseElapsedTime);
  }

  function getFirstname() {
    return($this->firstname) ;
  }

  function getLastname() {
    return($this->lastname);
  }

  function getMessage() {
    return($this->message);
  }

  function getComment() {
    return($this->comment);
  }

  function getHideComment() {
    return($this->hideComment);
  }

  function getEmail() {
    return($this->email);
  }

  function getNbReadingQuestions() {
    return($this->nbReadingQuestions);
  }

  function getNbCorrectReadingAnswers() {
    return($this->nbCorrectReadingAnswers);
  }

  function getNbIncorrectReadingAnswers() {
    return($this->nbIncorrectReadingAnswers);
  }

  function getNbReadingPoints() {
    return($this->nbReadingPoints);
  }

  function getNbWritingQuestions() {
    return($this->nbWritingQuestions);
  }

  function getNbCorrectWritingAnswers() {
    return($this->nbCorrectWritingAnswers);
  }

  function getNbIncorrectWritingAnswers() {
    return($this->nbIncorrectWritingAnswers);
  }

  function getNbWritingPoints() {
    return($this->nbWritingPoints);
  }

  function getNbListeningQuestions() {
    return($this->nbListeningQuestions);
  }

  function getNbCorrectListeningAnswers() {
    return($this->nbCorrectListeningAnswers);
  }

  function getNbIncorrectListeningAnswers() {
    return($this->nbIncorrectListeningAnswers);
  }

  function getNbListeningPoints() {
    return($this->nbListeningPoints);
  }

  function getNbNotAnswered() {
    return($this->nbNotAnswered);
  }

  function getNbIncorrectAnswers() {
    return($this->nbIncorrectAnswers);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setElearningExerciseId($elearningExerciseId) {
    $this->elearningExerciseId = $elearningExerciseId;
  }

  function setSubscriptionId($subscriptionId) {
    $this->subscriptionId = $subscriptionId;
  }

  function setExerciseDate($exerciseDate) {
    $this->exerciseDate = $exerciseDate;
  }

  function setExerciseElapsedTime($exerciseElapsedTime) {
    $this->exerciseElapsedTime = $exerciseElapsedTime;
  }

  function setFirstname($firstname) {
    $this->firstname = $firstname;
  }

  function setLastname($lastname) {
    $this->lastname = $lastname;
  }

  function setMessage($message) {
    $this->message = $message;
  }

  function setComment($comment) {
    $this->comment = $comment;
  }

  function setHideComment($hideComment) {
    $this->hideComment = $hideComment;
  }

  function setEmail($email) {
    $this->email = $email;
  }

  function setNbReadingQuestions($nbReadingQuestions) {
    $this->nbReadingQuestions = $nbReadingQuestions;
  }

  function setNbCorrectReadingAnswers($nbCorrectReadingAnswers) {
    $this->nbCorrectReadingAnswers = $nbCorrectReadingAnswers;
  }

  function setNbIncorrectReadingAnswers($nbIncorrectReadingAnswers) {
    $this->nbIncorrectReadingAnswers = $nbIncorrectReadingAnswers;
  }

  function setNbReadingPoints($nbReadingPoints) {
    $this->nbReadingPoints = $nbReadingPoints;
  }

  function setNbWritingQuestions($nbWritingQuestions) {
    $this->nbWritingQuestions = $nbWritingQuestions;
  }

  function setNbCorrectWritingAnswers($nbCorrectWritingAnswers) {
    $this->nbCorrectWritingAnswers = $nbCorrectWritingAnswers;
  }

  function setNbIncorrectWritingAnswers($nbIncorrectWritingAnswers) {
    $this->nbIncorrectWritingAnswers = $nbIncorrectWritingAnswers;
  }

  function setNbWritingPoints($nbWritingPoints) {
    $this->nbWritingPoints = $nbWritingPoints;
  }

  function setNbListeningQuestions($nbListeningQuestions) {
    $this->nbListeningQuestions = $nbListeningQuestions;
  }

  function setNbCorrectListeningAnswers($nbCorrectListeningAnswers) {
    $this->nbCorrectListeningAnswers = $nbCorrectListeningAnswers;
  }

  function setNbIncorrectListeningAnswers($nbIncorrectListeningAnswers) {
    $this->nbIncorrectListeningAnswers = $nbIncorrectListeningAnswers;
  }

  function setNbListeningPoints($nbListeningPoints) {
    $this->nbListeningPoints = $nbListeningPoints;
  }

  function setNbNotAnswered($nbNotAnswered) {
    $this->nbNotAnswered = $nbNotAnswered;
  }

  function setNbIncorrectAnswers($nbIncorrectAnswers) {
    $this->nbIncorrectAnswers = $nbIncorrectAnswers;
  }

}

?>
