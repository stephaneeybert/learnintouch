<?php

class StatisticsVisit {

  var $id;
  var $visitDateTime;
  var $visitorHostAddress;
  var $visitorBrowser;
  var $visitorReferer;

  function StatisticsVisit($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getVisitDateTime() {
    return($this->visitDateTime);
  }

  function getVisitorHostAddress() {
    return($this->visitorHostAddress);
  }

  function getVisitorBrowser() {
    return($this->visitorBrowser);
  }

  function getVisitorReferer() {
    return($this->visitorReferer);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setVisitDateTime($visitDateTime) {
    $this->visitDateTime = $visitDateTime;
  }

  function setVisitorHostAddress($visitorHostAddress) {
    $this->visitorHostAddress = $visitorHostAddress;
  }

  function setVisitorBrowser($visitorBrowser) {
    $this->visitorBrowser = $visitorBrowser;
  }

  function setVisitorReferer($visitorReferer) {
    $this->visitorReferer = $visitorReferer;
  }

}

?>
