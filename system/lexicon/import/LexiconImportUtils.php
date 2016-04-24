<?

class LexiconImportUtils extends ContentImportUtils {

  var $lexiconEntryUtils;

  function LexiconImportUtils() {
    $this->ContentImportUtils();
  }

  // Get in an xml string the content of a lexicon entry
  function exposeLexiconEntryAsXML($contentImportId, $lexiconEntryId) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($lexiconEntryId) {
      if ($contentImport = $this->selectById($contentImportId)) {
        $domainName = $contentImport->getDomainName();
        $permissionKey = $contentImport->getPermissionKey();

        $importCertificate = $this->renderImportCertificate();

        $url = $domainName . "/engine/system/lexicon/import/exposeLexiconEntryREST.php?importCertificate=$importCertificate&domainName=$gHomeUrl&permissionKey=$permissionKey&lexiconEntryId=$lexiconEntryId";

        $xmlResponse = LibFile::curlGetFileContent($url);
      }
    }

    return($xmlResponse);
  }

  // Expose a lexicon entry through a REST web service
  function exposeLexiconEntryREST($lexiconEntryId) {
    global $gHomeUrl;

    $xmlResponse = '';

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlRootNode = $xmlDocument->createElement(LEXICON_XML);
    $xmlRootNode->setAttribute("homeUrl", $gHomeUrl);
    $xmlDocument->appendChild($xmlRootNode);
    $xmlDocument->preserveWhiteSpace = false;
    $xmlDocument->formatOutput = true;

    $this->createNodeFromLexiconEntry($lexiconEntryId, $xmlDocument, $xmlRootNode);

    $xmlResponse = $xmlDocument->saveXML();

    return($xmlResponse);
  }

  // Create some xml nodes for some content that contains some lexicon entries
  function createNodesFromContent($content, $xmlDocument, $parentNode) {
    if ($lexiconEntryIds = $this->lexiconEntryUtils->getIdsFromContent($content)) {
      $this->createNodesFromLexiconEntries($lexiconEntryIds, $xmlDocument, $parentNode);
    }
  }

  // Create some xml nodes for some lexicon entries
  function createNodesFromLexiconEntries($lexiconEntryIds, $xmlDocument, $parentNode) {
    foreach ($lexiconEntryIds as $couple) {
      list($lexiconEntryId, $lexiconEntryDomId) = $couple;
      $this->createNodeFromLexiconEntry($lexiconEntryId, $xmlDocument, $parentNode);
    }
  }

  // Create an xml node for a lexicon entry
  function createNodeFromLexiconEntry($lexiconEntryId, $xmlDocument, $parentNode) {
    if ($lexiconEntry = $this->lexiconEntryUtils->selectById($lexiconEntryId)) {
      $lexiconEntryId = $lexiconEntry->getId();
      $name = $lexiconEntry->getName();
      $explanation = $lexiconEntry->getExplanation();
      $image = $lexiconEntry->getImage();

      $lexiconEntryId = utf8_encode($lexiconEntryId);
      $name = utf8_encode($name);
      $explanation = utf8_encode($explanation);
      $image = utf8_encode($image);

      $xmlLexiconEntryNode = $xmlDocument->createElement(LEXICON_XML_ENTRY);
      $xmlLexiconEntryNode->setAttribute("id", $lexiconEntryId);
      $xmlLexiconEntryNode->setAttribute("name", $name);
      $xmlLexiconEntryNode->setAttribute("explanation", $explanation);
      $xmlLexiconEntryNode->setAttribute("image", $image);
      $parentNode->appendChild($xmlLexiconEntryNode);
    }
  }

  // Create a lexicon entry from an xml node
  function createLexiconEntriesFromNode($parentNode, $sourceHomeUrl, $content) {
    $lexiconEntryNodes = $parentNode->getElementsByTagName(LEXICON_XML_ENTRY);
    foreach ($lexiconEntryNodes as $lexiconEntryNode) {
      $content = $this->createLexiconEntryFromNode($lexiconEntryNode, $sourceHomeUrl, $content);
    }

    return($content);
  }

  // Create a lexicon entry from an xml node
  function createLexiconEntryFromNode($lexiconEntryNode, $sourceHomeUrl, $content) {
    $nodeName = $lexiconEntryNode->tagName;
    if ($nodeName == LEXICON_XML_ENTRY) {
      $lexiconEntryId = $lexiconEntryNode->getAttribute("id");
      $name = $lexiconEntryNode->getAttribute("name");
      $explanation = $lexiconEntryNode->getAttribute("explanation");
      $image = $lexiconEntryNode->getAttribute("image");

      // Create the lexicon entry
      $lexiconEntry = new LexiconEntry();
      $lexiconEntry->setName($name);
      $lexiconEntry->setExplanation($explanation);
      $lexiconEntry->setImage($image);
      $this->lexiconEntryUtils->insert($lexiconEntry);
      $lastInsertLexiconEntryId = $this->lexiconEntryUtils->getLastInsertId();

      // Copy the image if any
      if ($image) {
        $filename = $sourceHomeUrl . "/account/data/lexicon/image/$image";
        LibFile::curlGetFileContent($filename, $this->lexiconEntryUtils->imageFilePath . $image);
      }
    }

    // Replace the foreign website lexicon entry id by the newly inserted one
    $content = $this->lexiconEntryUtils->replaceDomId($lexiconEntryId, $lastInsertLexiconEntryId, $content);

    return($content);
  }

  // Import a lexicon entry exposed by a REST web service
  function importLexiconEntryREST($xmlResponse, $lexiconEntryId = '') {
    if (!strstr($xmlResponse, LEXICON_XML)) {
      return(false);
    }

    $lastInsertLexiconEntryId = '';

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(LEXICON_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $lexiconEntryNodes = $xmlRootNode->getElementsByTagName(LEXICON_XML_ENTRY);
    foreach ($lexiconEntryNodes as $lexiconEntryNode) {
      $nodeName = $lexiconEntryNode->tagName;
      if ($nodeName == LEXICON_XML_ENTRY) {
        $lastInsertLexiconEntryId = $this->createLexiconEntryFromNode($lexiconEntryNode, $sourceHomeUrl);
      }
    }

    return($lastInsertLexiconEntryId);
  }

}

?>
