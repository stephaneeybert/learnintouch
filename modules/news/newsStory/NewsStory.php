<?php

class NewsStory {

  var $id;
  var $headline;
  var $excerpt;
  var $audio;
  var $audioUrl;
  var $link;
  var $releaseDate;
  var $archive;
  var $eventStartDate;
  var $eventEndDate;
  var $newsEditor;
  var $newsPaper;
  var $newsHeading;
  var $listOrder;

  function NewsStory($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getHeadline() {
    return($this->headline);
  }

  function getExcerpt() {
    return($this->excerpt);
  }

  function getAudio() {
    return($this->audio);
  }

  function getAudioUrl() {
    return($this->audioUrl);
  }

  function getLink() {
    return($this->link);
  }

  function getReleaseDate() {
    return($this->releaseDate);
  }

  function getArchive() {
    return($this->archive);
  }

  function getEventStartDate() {
    return($this->eventStartDate);
  }

  function getEventEndDate() {
    return($this->eventEndDate);
  }

  function getNewsEditor() {
    return($this->newsEditor);
  }

  function getNewsPaper() {
    return($this->newsPaper);
  }

  function getNewsHeading() {
    return($this->newsHeading);
  }

  function getListOrder() {
    return($this->listOrder);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setHeadline($headline) {
    $this->headline = $headline;
  }

  function setExcerpt($excerpt) {
    $this->excerpt = $excerpt;
  }

  function setAudio($audio) {
    $this->audio = $audio;
  }

  function setAudioUrl($audioUrl) {
    $this->audioUrl = $audioUrl;
  }

  function setLink($link) {
    $this->link = $link;
  }

  function setReleaseDate($releaseDate) {
    $this->releaseDate = $releaseDate;
  }

  function setArchive($archive) {
    $this->archive = $archive;
  }

  function setEventStartDate($eventStartDate) {
    $this->eventStartDate = $eventStartDate;
  }

  function setEventEndDate($eventEndDate) {
    $this->eventEndDate = $eventEndDate;
  }

  function setNewsEditor($newsEditor) {
    $this->newsEditor = $newsEditor;
  }

  function setNewsPaper($newsPaper) {
    $this->newsPaper = $newsPaper;
  }

  function setNewsHeading($newsHeading) {
    $this->newsHeading = $newsHeading;
  }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
  }

}

?>
