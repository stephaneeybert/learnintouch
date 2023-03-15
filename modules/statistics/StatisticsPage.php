<?php

// The web site visit statistics per page.

class StatisticsPage {

  var $id;
  var $page;
  var $hits;
  var $month;
  var $year;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getPage() {
    return($this->page);
  }

  function getHits() {
    return($this->hits);
  }

  function getMonth() {
    return($this->month);
  }

  function getYear() {
    return($this->year);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setPage($page) {
    $this->page = $page;
  }

  function setHits($hits) {
    $this->hits = $hits;
  }

  function setMonth($month) {
    $this->month = $month;
  }

  function setYear($year) {
    $this->year = $year;
  }

}

?>
