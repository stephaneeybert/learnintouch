<?php

class NewsPaper {

  var $id;
  var $title;
  var $image;
  var $header;
  var $footer;
  var $releaseDate;
  var $archive;
  var $notPublished;
  var $newsPublicationId;

  function NewsPaper($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getTitle() {
    return($this->title);
  }

  function getImage() {
    return($this->image);
  }

  function getHeader() {
    return($this->header);
  }

  function getFooter() {
    return($this->footer);
  }

  function getReleaseDate() {
    return($this->releaseDate);
  }

  function getArchive() {
    return($this->archive);
  }

  function getNotPublished() {
    return($this->notPublished);
  }

  function getNewsPublicationId() {
    return($this->newsPublicationId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setTitle($title) {
    $this->title = $title;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setHeader($header) {
    $this->header = $header;
  }

  function setFooter($footer) {
    $this->footer = $footer;
  }

  function setReleaseDate($releaseDate) {
    $this->releaseDate = $releaseDate;
  }

  function setArchive($archive) {
    $this->archive = $archive;
  }

  function setNotPublished($notPublished) {
    $this->notPublished = $notPublished;
  }

  function setNewsPublicationId($newsPublicationId) {
    $this->newsPublicationId = $newsPublicationId;
  }

}

?>
