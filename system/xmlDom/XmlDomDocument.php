<?php

class XmlDomDocument {

  var $id;
  var $rootNode;

  function XmlDomDocument($id = '') {
    }

  function getId() {
    return($this->id);
    }

  function setId($id) {
    $this->id = $id;
    }

  // The utilities
  function appendChild(& $xmlChildNode) {
    $this->rootNode =& $xmlChildNode;
    }

  function dumpFile($filename) {
    $str = "<?xml version=\"1.0\"?>";

    $str .= $this->rootNode->dump();

    LibFile::writeString($filename, $str);
    }

  }

?>
