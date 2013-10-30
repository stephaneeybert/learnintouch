<?php

class RssFeedLanguage {

  var $id;
  var $language;
  var $title;
  var $url;
  var $rssFeedId;

  function RssFeedLanguage($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getLanguage() {
    return($this->language);
  }

  function getTitle() {
    return($this->title);
  }

  function getUrl() {
    return($this->url);
  }

  function getRssFeedId() {
    return($this->rssFeedId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setLanguage($language) {
    $this->language = $language;
  }

  function setTitle($title) {
    $this->title = $title;
  }

  function setUrl($url) {
    $this->url = $url;
  }

  function setRssFeedId($rssFeedId) {
    $this->rssFeedId = $rssFeedId;
  }

}

?>
