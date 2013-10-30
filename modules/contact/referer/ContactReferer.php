<?php

class ContactReferer {

  var $id;
  var $listOrder;
  var $description;

  function ContactReferer($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function getListOrder() {
    return($this->listOrder);
    }

  function getDescription() {
    return($this->description);
    }

  function setId($id) {
    $this->id = $id;
    }

  function setListOrder($listOrder) {
    $this->listOrder = $listOrder;
    }

  function setDescription($description) {
    $this->description= $description;
    }

  }

?>
