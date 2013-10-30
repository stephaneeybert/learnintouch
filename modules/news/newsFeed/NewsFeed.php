<?php

class NewsFeed {

  var $id;
  var $newsPaperId;
  var $image;
  var $maxDisplayNumber;
  var $imageAlign;
  var $imageWidth;
  var $withExcerpt;
  var $withImage;
  var $searchOptions;
  var $searchCalendar;
  var $displayUpcoming;
  var $searchTitle;
  var $searchDisplayAsPage;

  function NewsFeed($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getNewsPaperId() {
    return($this->newsPaperId);
  }

  function getImage() {
    return($this->image);
  }

  function getMaxDisplayNumber() {
    return($this->maxDisplayNumber);
  }

  function getImageAlign() {
    return($this->imageAlign);
  }

  function getImageWidth() {
    return($this->imageWidth);
  }

  function getWithExcerpt() {
    return($this->withExcerpt);
  }

  function getWithImage() {
    return($this->withImage);
  }

  function getSearchOptions() {
    return($this->searchOptions);
  }

  function getSearchCalendar() {
    return($this->searchCalendar);
  }

  function getDisplayUpcoming() {
    return($this->displayUpcoming);
  }

  function getSearchTitle() {
    return($this->searchTitle);
  }

  function getSearchDisplayAsPage() {
    return($this->searchDisplayAsPage);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setNewsPaperId($newsPaperId) {
    $this->newsPaperId = $newsPaperId;
  }

  function setImage($image) {
    $this->image = $image;
  }

  function setMaxDisplayNumber($maxDisplayNumber) {
    $this->maxDisplayNumber = $maxDisplayNumber;
  }

  function setImageAlign($imageAlign) {
    $this->imageAlign = $imageAlign;
  }

  function setImageWidth($imageWidth) {
    $this->imageWidth = $imageWidth;
  }

  function setWithExcerpt($withExcerpt) {
    $this->withExcerpt = $withExcerpt;
  }

  function setWithImage($withImage) {
    $this->withImage = $withImage;
  }

  function setSearchOptions($searchOptions) {
    $this->searchOptions = $searchOptions;
  }

  function setSearchCalendar($searchCalendar) {
    $this->searchCalendar = $searchCalendar;
  }

  function setDisplayUpcoming($displayUpcoming) {
    $this->displayUpcoming = $displayUpcoming;
  }

  function setSearchTitle($searchTitle) {
    $this->searchTitle = $searchTitle;
  }

  function setSearchDisplayAsPage($searchDisplayAsPage) {
    $this->searchDisplayAsPage = $searchDisplayAsPage;
  }

}

?>
