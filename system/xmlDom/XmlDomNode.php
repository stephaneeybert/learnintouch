<?php

class XmlDomNode {

  var $id;
  var $name;
  var $attributes;
  var $content;
  var $childNodes;

  function __construct($name) {
    global $gXmlDomNodeId;

    $gXmlDomNodeId++;
    $this->id = $gXmlDomNodeId;

    $this->name = $name;
    $this->attributes = array();
    $this->content = '';
    $this->childNodes = array();
  }

  function getId() {
    return($this->id);
  }

  function getName() {
    return($this->name);
  }

  function getAttributes() {
    return($this->attributes);
  }

  function getContent() {
    return($this->content);
  }

  function getChildNodes() {
    return($this->childNodes);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setName($name) {
    $this->name = $name;
  }

  function setAttributes($attributes) {
    $this->attributes = $attributes;
  }

  function setContent($content) {
    $this->content = $content;
  }

  function setChildNodes($childNodes) {
    $this->childNodes = $childNodes;
  }

  // The utilities
  function setAttribute($name, $value) {
    $this->attributes[$name] = $value;
  }

  function getAttribute($name) {
    return($this->attributes[$name]);
  }

  function appendChild($childNode) {
    array_push($this->childNodes, $childNode);
  }

  function dump() {
    $str = "\n<" . $this->name;

    foreach ($this->attributes as $name => $value) {
      $str .= " $name=\"$value\"";
    }

    $str .= ">";

    foreach ($this->childNodes as $childNode) {
      if (isset($childNode->name)) {
        $str .= $childNode->dump();
      }
    }

    $str .= "\n</" . $this->name . ">";

    return($str);
  }

}

?>
