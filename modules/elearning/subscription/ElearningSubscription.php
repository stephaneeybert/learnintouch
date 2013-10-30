<?php

class ElearningSubscription {

  var $id;
  var $userId;
  var $teacherId;
  var $sessionId;
  var $courseId;
  var $classId;
  var $subscriptionDate;
  var $subscriptionClose;
  var $watchLive;
  var $lastExerciseId;
  var $lastExercisePageId;
  var $lastActive;
  var $whiteboard;

  function ElearningSubscription($id = '') {
  }

  function getId() {
    return ($this->id);
  }

  function getUserId() {
    return ($this->userId);
  }

  function getTeacherId() {
    return ($this->teacherId);
  }

  function getSessionId() {
    return ($this->sessionId);
  }

  function getCourseId() {
    return ($this->courseId);
  }

  function getClassId() {
    return ($this->classId);
  }

  function getSubscriptionDate() {
    return ($this->subscriptionDate);
  }

  function getSubscriptionClose() {
    return ($this->subscriptionClose);
  }

  function getWatchLive() {
    return ($this->watchLive);
  }

  function getLastExerciseId() {
    return ($this->lastExerciseId);
  }

  function getLastExercisePageId() {
    return ($this->lastExercisePageId);
  }

  function getLastActive() {
    return ($this->lastActive);
  }

  function getWhiteboard() {
    return ($this->whiteboard);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setUserId($userId) {
    $this->userId = $userId;
  }

  function setTeacherId($teacherId) {
    $this->teacherId = $teacherId;
  }

  function setSessionId($sessionId) {
    $this->sessionId = $sessionId;
  }

  function setCourseId($courseId) {
    $this->courseId = $courseId;
  }

  function setClassId($classId) {
    $this->classId = $classId;
  }

  function setSubscriptionDate($subscriptionDate) {
    $this->subscriptionDate = $subscriptionDate;
  }

  function setSubscriptionClose($subscriptionClose) {
    $this->subscriptionClose = $subscriptionClose;
  }

  function setWatchLive($watchLive) {
    $this->watchLive = $watchLive;
  }

  function setLastExerciseId($lastExerciseId) {
    $this->lastExerciseId = $lastExerciseId;
  }

  function setLastExercisePageId($lastExercisePageId) {
    $this->lastExercisePageId = $lastExercisePageId;
  }

  function setLastActive($lastActive) {
    $this->lastActive = $lastActive;
  }

  function setWhiteboard($whiteboard) {
    $this->whiteboard = $whiteboard;
  }

}
?>
