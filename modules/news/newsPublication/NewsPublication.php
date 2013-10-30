<?php

// A news publication is a journal or a magazine or newsletter.
// It has many editions in the form of newspapers.
// A newspaper is one edition of a news publication.

class NewsPublication {

  var $id;
  var $name;
  var $description;
  var $nbColumns;
  var $slideDown;
  var $align;
  var $withArchive;
  var $withOthers;
  var $withByHeading;
  var $hideHeading;
  var $autoArchive;
  var $autoDelete;
  var $secured;

  function NewsPublication($id = '') {
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

  function getNbColumns() {
    return($this->nbColumns);
  }

  function getSlideDown() {
    return($this->slideDown);
  }

  function getAlign() {
    return($this->align);
  }

  function getWithArchive() {
    return($this->withArchive);
  }

  function getWithOthers() {
    return($this->withOthers);
  }

  function getWithByHeading() {
    return($this->withByHeading);
  }

  function getHideHeading() {
    return($this->hideHeading);
  }

  function getAutoArchive() {
    return($this->autoArchive);
  }

  function getAutoDelete() {
    return($this->autoDelete);
  }

  function getSecured() {
    return($this->secured);
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

  function setNbColumns($nbColumns) {
    $this->nbColumns = $nbColumns;
  }

  function setSlideDown($slideDown) {
    $this->slideDown = $slideDown;
  }

  function setAlign($align) {
    $this->align = $align;
  }

  function setWithArchive($withArchive) {
    $this->withArchive = $withArchive;
  }

  function setWithOthers($withOthers) {
    $this->withOthers = $withOthers;
  }

  function setWithByHeading($withByHeading) {
    $this->withByHeading = $withByHeading;
  }

  function setHideHeading($hideHeading) {
    $this->hideHeading = $hideHeading;
  }

  function setAutoArchive($autoArchive) {
    $this->autoArchive = $autoArchive;
  }

  function setAutoDelete($autoDelete) {
    $this->autoDelete = $autoDelete;
  }

  function setSecured($secured) {
    $this->secured = $secured;
  }

}

?>
