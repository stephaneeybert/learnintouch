<?

class ElearningImportUtils extends ContentImportUtils {

  var $languageUtils;
  var $commonUtils;
  var $propertyUtils;
  var $clockUtils;
  var $lexiconImportUtils;
  var $elearningExerciseUtils;
  var $elearningLessonUtils;
  var $elearningMatterUtils;
  var $elearningCourseUtils;
  var $elearningCourseItemUtils;
  var $elearningExercisePageUtils;
  var $elearningQuestionUtils;
  var $elearningAnswerUtils;
  var $elearningSolutionUtils;
  var $elearningLessonParagraphUtils;

  function ElearningImportUtils() {
    $this->ContentImportUtils();
  }

  // Store the last imported course id
  function storeLastImportedCourseId($elearningCourseId) {
    $this->propertyUtils->store(ELEARNING_IMPORT_LAST_COURSE_ID, $elearningCourseId);
  }

  // Retrieve the last imported course id
  function retrieveLastImportedCourseId() {
    $elearningCourseId = $this->propertyUtils->retrieve(ELEARNING_IMPORT_LAST_COURSE_ID);

    return($elearningCourseId);
  }

  // Get in an xml string the list of courses that can be imported
  function exposeCourseListXML($contentImportId) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
      $permissionKey = $contentImport->getPermissionKey();

      $importCertificate = $this->renderImportCertificate();

      $url = $domainName . "/engine/modules/elearning/import/exposeCourseListREST.php?importCertificate=$importCertificate&domainName=$gHomeUrl&permissionKey=$permissionKey";

      $xmlResponse = LibFile::curlGetFileContent($url);
    }

    return($xmlResponse);
  }

  // Get in an xml string the content of a course
  function exposeCourseAsXML($contentImportId, $elearningCourseId, $logImport) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
      $permissionKey = $contentImport->getPermissionKey();

      $importCertificate = $this->renderImportCertificate();

      $url = $domainName . "/engine/modules/elearning/import/exposeCourseREST.php?importCertificate=$importCertificate&domainName=$gHomeUrl&permissionKey=$permissionKey&elearningCourseId=$elearningCourseId&logImport=$logImport";

      $xmlResponse = LibFile::curlGetFileContent($url);
    }

    return($xmlResponse);
  }

  // Get in an xml string all the exercises, including those not in a course
  function exposeAllExercisesAsXML($contentImportId, $logImport, $listIndex, $listStep) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
      $permissionKey = $contentImport->getPermissionKey();

      $importCertificate = $this->renderImportCertificate();

      $url = $domainName . "/engine/modules/elearning/import/exposeAllExercisesREST.php?importCertificate=$importCertificate&domainName=$gHomeUrl&permissionKey=$permissionKey&logImport=$logImport&listIndex=$listIndex&listStep=$listStep";

      $xmlResponse = LibFile::curlGetFileContent($url);
    }

    return($xmlResponse);
  }

  // Get in an xml string all the lessons, including those not in a course
  function exposeAllLessonsAsXML($contentImportId, $logImport, $listIndex, $listStep) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
      $permissionKey = $contentImport->getPermissionKey();

      $importCertificate = $this->renderImportCertificate();

      $url = $domainName . "/engine/modules/elearning/import/exposeAllLessonsREST.php?importCertificate=$importCertificate&domainName=$gHomeUrl&permissionKey=$permissionKey&logImport=$logImport&listIndex=$listIndex&listStep=$listStep";

      $xmlResponse = LibFile::curlGetFileContent($url);
    }

    return($xmlResponse);
  }

  // Get in an xml string the searched content, that is, courses, lessons and exercises
  function exposeSearchedContentAsXML($contentImportId, $searchPattern, $logImport) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
      $permissionKey = $contentImport->getPermissionKey();

      $importCertificate = $this->renderImportCertificate();

      $searchPattern = urlencode($searchPattern);

      $url = $domainName . "/engine/modules/elearning/import/exposeSearchedContentREST.php?importCertificate=$importCertificate&domainName=$gHomeUrl&permissionKey=$permissionKey&searchPattern=$searchPattern&logImport=$logImport";

      $xmlResponse = LibFile::curlGetFileContent($url);
    }

    return($xmlResponse);
  }

  // Expose the list of courses through a REST web service
  function exposeCourseListREST() {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($this->elearningExerciseUtils->coursesCanBeImported()) {
      $allImportable = 1;
    } else {
      $allImportable = 0;
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlRootNode = $xmlDocument->createElement(ELEARNING_XML);
    $xmlRootNode->setAttribute("homeUrl", $gHomeUrl);
    $xmlRootNode->setAttribute("allImportable", $allImportable);
    $xmlDocument->appendChild($xmlRootNode);

    if ($elearningMatters = $this->elearningMatterUtils->selectAll()) {
      foreach ($elearningMatters as $elearningMatter) {
        $elearningMatterId = $elearningMatter->getId();
        $name = $elearningMatter->getName();
        $description = $elearningMatter->getDescription();

        $name = utf8_encode($name);
        $description = utf8_encode($description);

        $xmlMatterNode = $xmlDocument->createElement(ELEARNING_XML_MATTER);
        $xmlMatterNode->setAttribute("id", $elearningMatterId);
        $xmlMatterNode->setAttribute("name", $name);
        $xmlMatterNode->setAttribute("description", $description);
        $xmlRootNode->appendChild($xmlMatterNode);
      }
    }

    if ($allImportable) {
      $elearningCourses = $this->elearningCourseUtils->selectAll();
    } else {
      $elearningCourses = $this->elearningCourseUtils->selectImportable();
    }
    if (count($elearningCourses) > 0) {
      foreach ($elearningCourses as $elearningCourse) {
        $elearningCourseId = $elearningCourse->getId();
        $name = $elearningCourse->getName();
        $description = $elearningCourse->getDescription();
        $matterId = $elearningCourse->getMatterId();

        $name = utf8_encode($name);
        $description = utf8_encode($description);

        $xmlCourseNode = $xmlDocument->createElement(ELEARNING_XML_COURSE);
        $xmlCourseNode->setAttribute("id", $elearningCourseId);
        $xmlCourseNode->setAttribute("name", $name);
        $xmlCourseNode->setAttribute("description", $description);
        $xmlCourseNode->setAttribute("matterId", $matterId);
        $xmlRootNode->appendChild($xmlCourseNode);
      }
    }

    $xmlDocument->preserveWhiteSpace = false;
    $xmlDocument->formatOutput = true;
    $xmlResponse = $xmlDocument->saveXML();

    return($xmlResponse);
  }

  // Get a list of matters exposed by a REST web service
  function getMatterListREST($xmlResponse) {
    $matters = array();

    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return($matters);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $matterNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_MATTER);
    foreach ($matterNodes as $matterNode) {
      $nodeName = $matterNode->tagName;
      if ($nodeName == ELEARNING_XML_MATTER) {
        $id = $matterNode->getAttribute("id");
        $name = $matterNode->getAttribute("name");
        $description = $matterNode->getAttribute("description");

        array_push($matters, array($id, $name, $description));
      }
    }

    return($matters);
  }

  // Check if all the content is offered for import by other websites, or if only the courses marked as such
  function getAllImportable($xmlResponse) {
    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return(0);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $allImportable = $xmlRootNode->getAttribute("allImportable");

    return($allImportable);
  }

  // Get a list of courses exposed by a REST web service
  // The matter parameter is a matter at the exporting website
  function getCourseListREST($xmlResponse, $elearningMatterId = '') {
    $courses = array();

    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return($courses);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $courseNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_COURSE);
    foreach ($courseNodes as $courseNode) {
      $nodeName = $courseNode->tagName;
      if ($nodeName == ELEARNING_XML_COURSE) {
        $matterId = $courseNode->getAttribute("matterId");

        // Only list the courses of the selected matter, if any
        if ($elearningMatterId && $matterId != $elearningMatterId) {
          continue;
        }

        $id = $courseNode->getAttribute("id");
        $name = $courseNode->getAttribute("name");
        $description = $courseNode->getAttribute("description");

        array_push($courses, array($id, $name, $description, $matterId));
      }
    }

    return($courses);
  }

  // Expose the list of lessons and exercises of a course through a REST web service
  function NOT_USED_exposeCourseItemListREST($elearningCourseId) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
      $xmlDocument = new DOMDocument("1.0", "UTF-8");
      $xmlRootNode = $xmlDocument->createElement(ELEARNING_XML);
      $xmlRootNode->setAttribute("homeUrl", $gHomeUrl);
      $xmlDocument->appendChild($xmlRootNode);

      $xmlCourseNode = $xmlDocument->createElement(ELEARNING_XML_COURSE);
      $xmlCourseNode->setAttribute("name", $name);
      $xmlCourseNode->setAttribute("description", $description);
      $xmlCourseNode->setAttribute("image", $image);
      $xmlRootNode->appendChild($xmlCourseNode);

      foreach ($elearningCourseItems as $elearningCourseItem) {
        $elearningExerciseId = $elearningCourseItem->getElearningExerciseId();
        $elearningLessonId = $elearningCourseItem->getElearningLessonId();

        if ($elearningExerciseId) {
          if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
            $name = $elearningExercise->getName();
            $description = $elearningExercise->getDescription();
            $image = $elearningExercise->getImage();
          }
        } else if ($elearningLessonId) {
          if ($elearningLesson = $this->elearningLessonUtils->selectById($elearningLessonId)) {
            $name = $elearningLesson->getName();
            $description = $elearningLesson->getDescription();
            $image = $elearningLesson->getImage();
          }
        }

        $name = utf8_encode($name);
        $description = utf8_encode($description);
        $image = utf8_encode($image);

        $xmlCourseItemNode = $xmlDocument->createElement(ELEARNING_XML_COURSE_ITEM);
        if ($elearningExerciseId) {
          $xmlCourseItemNode->setAttribute("id", $elearningExerciseId);
          $xmlCourseItemNode->setAttribute("type", ELEARNING_XML_EXERCISE);
        } else if ($elearningLessonId) {
          $xmlCourseItemNode->setAttribute("id", $elearningLessonId);
          $xmlCourseItemNode->setAttribute("type", ELEARNING_XML_LESSON);
        }

        $xmlCourseItemNode->setAttribute("name", $name);
        $xmlCourseItemNode->setAttribute("description", $description);
        $xmlCourseItemNode->setAttribute("image", $image);
        $xmlCourseNode->appendChild($xmlCourseItemNode);
      }

      $xmlDocument->preserveWhiteSpace = false;
      $xmlDocument->formatOutput = true;
      $xmlResponse = $xmlDocument->saveXML();
    }

    return($xmlResponse);
  }

  // Get a list of lessons and exercises of a course exposed by a REST web service
  function NOT_USED_getCourseItemListREST($xmlResponse) {
    $elearningCourseItems = array();

    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return($elearningCourseItems);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $elearningCourseItemNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_COURSE_ITEM);
    foreach ($elearningCourseItemNodes as $elearningCourseItemNode) {
      $nodeName = $elearningCourseItemNode->tagName;
      if ($nodeName == ELEARNING_XML_COURSE_ITEM) {
        $id = $elearningCourseItemNode->getAttribute("id");
        $type = $elearningCourseItemNode->getAttribute("type");
        $name = $elearningCourseItemNode->getAttribute("name");
        $description = $elearningCourseItemNode->getAttribute("description");

        array_push($elearningCourseItems, array($type, $id, $name, $description));
      }
    }

    return($elearningCourseItems);
  }

  // Get a list of exercises exposed by a REST web service
  function getListNbItemsREST($xmlResponse) {
    $listNbItems = 0;

    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return($elearningExercises);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $listNbItems = $xmlRootNode->getAttribute("listNbItems");

    return($listNbItems);
  }

  // Get a list of exercises exposed by a REST web service
  function getExerciseListREST($xmlResponse) {
    $elearningExercises = array();

    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return($elearningExercises);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $elearningExerciseNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_EXERCISE);
    foreach ($elearningExerciseNodes as $elearningExerciseNode) {
      $nodeName = $elearningExerciseNode->tagName;
      if ($nodeName == ELEARNING_XML_EXERCISE) {
        $id = $elearningExerciseNode->getAttribute("id");
        $name = $elearningExerciseNode->getAttribute("name");
        $description = $elearningExerciseNode->getAttribute("description");

        array_push($elearningExercises, array($id, $name, $description));
      }
    }

    return($elearningExercises);
  }

  // Get a list of lessons exposed by a REST web service
  function getLessonListREST($xmlResponse) {
    $elearningLessons = array();

    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return($elearningLessons);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $elearningLessonNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_LESSON);
    foreach ($elearningLessonNodes as $elearningLessonNode) {
      $nodeName = $elearningLessonNode->tagName;
      if ($nodeName == ELEARNING_XML_LESSON) {
        $id = $elearningLessonNode->getAttribute("id");
        $name = $elearningLessonNode->getAttribute("name");
        $description = $elearningLessonNode->getAttribute("description");

        array_push($elearningLessons, array($id, $name, $description));
      }
    }

    return($elearningLessons);
  }

  // Expose a course through a REST web service
  function exposeCourseREST($elearningCourseId) {
    global $gHomeUrl;

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlRootNode = $xmlDocument->createElement(ELEARNING_XML);
    $xmlRootNode->setAttribute("homeUrl", $gHomeUrl);
    $xmlDocument->appendChild($xmlRootNode);
    $xmlDocument->preserveWhiteSpace = false;
    $xmlDocument->formatOutput = true;

    if ($elearningCourse = $this->elearningCourseUtils->selectById($elearningCourseId)) {
      $this->createNodeFromCourse($elearningCourse, $xmlDocument, $xmlRootNode);
    }

    $xmlResponse = $xmlDocument->saveXML();

    return($xmlResponse);
  }

  // Expose all the exercises through a REST web service
  function exposeAllExercisesREST($listIndex, $listStep) {
    global $gHomeUrl;

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlRootNode = $xmlDocument->createElement(ELEARNING_XML);
    $xmlRootNode->setAttribute("homeUrl", $gHomeUrl);
    $xmlRootNode->setAttribute("listIndex", $listIndex);
    $xmlRootNode->setAttribute("listStep", $listStep);
    $xmlDocument->appendChild($xmlRootNode);
    $xmlDocument->preserveWhiteSpace = false;
    $xmlDocument->formatOutput = true;

    if ($elearningExercises = $this->elearningExerciseUtils->selectNonGarbage($listIndex, $listStep)) {
      $listNbItems = $this->elearningExerciseUtils->countFoundRows();
      $xmlRootNode->setAttribute("listNbItems", $listNbItems);
      foreach ($elearningExercises as $elearningExercise) {
        $elearningExerciseId = $elearningExercise->getId();
        $this->createNodeFromExercise($elearningExerciseId, $xmlDocument, $xmlRootNode);
      }
    }

    $xmlResponse = $xmlDocument->saveXML();

    return($xmlResponse);
  }

  // Expose all the lessons through a REST web service
  function exposeAllLessonsREST($listIndex, $listStep) {
    global $gHomeUrl;

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlRootNode = $xmlDocument->createElement(ELEARNING_XML);
    $xmlRootNode->setAttribute("homeUrl", $gHomeUrl);
    $xmlRootNode->setAttribute("listIndex", $listIndex);
    $xmlRootNode->setAttribute("listStep", $listStep);
    $xmlDocument->appendChild($xmlRootNode);
    $xmlDocument->preserveWhiteSpace = false;
    $xmlDocument->formatOutput = true;

    if ($elearningLessons = $this->elearningLessonUtils->selectNonGarbage($listIndex, $listStep)) {
      $listNbItems = $this->elearningLessonUtils->countFoundRows();
      $xmlRootNode->setAttribute("listNbItems", $listNbItems);
      foreach ($elearningLessons as $elearningLesson) {
        $elearningLessonId = $elearningLesson->getId();
        $this->createNodeFromLesson($elearningLessonId, $xmlDocument, $xmlRootNode);
      }
    }

    $xmlResponse = $xmlDocument->saveXML();

    return($xmlResponse);
  }

  // Expose searched content, thatis, courses, lessons and exercises, through a REST web service
  function exposeSearchedContentREST($searchPattern) {
    global $gHomeUrl;

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlRootNode = $xmlDocument->createElement(ELEARNING_XML);
    $xmlRootNode->setAttribute("homeUrl", $gHomeUrl);
    $xmlDocument->appendChild($xmlRootNode);
    $xmlDocument->preserveWhiteSpace = false;
    $xmlDocument->formatOutput = true;

    if ($elearningCourses = $this->elearningCourseUtils->selectLikePattern($searchPattern)) {
      foreach ($elearningCourses as $elearningCourse) {
        $this->createNodeFromCourse($elearningCourse, $xmlDocument, $xmlRootNode);
      }
    }

    if ($elearningExercises = $this->elearningExerciseUtils->selectLikePattern($searchPattern)) {
      foreach ($elearningExercises as $elearningExercise) {
        $elearningExerciseId = $elearningExercise->getId();
        $this->createNodeFromExercise($elearningExerciseId, $xmlDocument, $xmlRootNode);
      }
    }

    if ($elearningLessons = $this->elearningLessonUtils->selectLikePattern($searchPattern)) {
      foreach ($elearningLessons as $elearningLesson) {
        $elearningLessonId = $elearningLesson->getId();
        $this->createNodeFromLesson($elearningLessonId, $xmlDocument, $xmlRootNode);
      }
    }

    $xmlResponse = $xmlDocument->saveXML();

    return($xmlResponse);
  }

  // Create an xml node for a course
  function createNodeFromCourse($elearningCourse, $xmlDocument, $parentNode) {
    if ($elearningCourse) {
      $elearningCourseId = $elearningCourse->getId();
      $name = $elearningCourse->getName();
      $description = $elearningCourse->getDescription();
      $image = $elearningCourse->getImage();

      $name = utf8_encode($name);
      $description = utf8_encode($description);
      $image = utf8_encode($image);

      $xmlCourseNode = $xmlDocument->createElement(ELEARNING_XML_COURSE);
      $xmlCourseNode->setAttribute("id", $elearningCourseId);
      $xmlCourseNode->setAttribute("name", $name);
      $xmlCourseNode->setAttribute("description", $description);
      $xmlCourseNode->setAttribute("image", $image);
      $parentNode->appendChild($xmlCourseNode);

      if ($elearningCourseItems = $this->elearningCourseItemUtils->selectByCourseId($elearningCourseId)) {
        foreach ($elearningCourseItems as $elearningCourseItem) {
          $elearningExerciseId = $elearningCourseItem->getElearningExerciseId();
          $elearningLessonId = $elearningCourseItem->getElearningLessonId();

          if ($elearningExerciseId) {
            if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
              $name = $elearningExercise->getName();
              $description = $elearningExercise->getDescription();
              $image = $elearningExercise->getImage();
            }
          } else if ($elearningLessonId) {
            if ($elearningLesson = $this->elearningLessonUtils->selectById($elearningLessonId)) {
              $name = $elearningLesson->getName();
              $description = $elearningLesson->getDescription();
              $image = $elearningLesson->getImage();
            }
          }

          $name = utf8_encode($name);
          $description = utf8_encode($description);
          $image = utf8_encode($image);

          $xmlCourseItemNode = $xmlDocument->createElement(ELEARNING_XML_COURSE_ITEM);
          if ($elearningExerciseId) {
            $xmlCourseItemNode->setAttribute("id", $elearningExerciseId);
            $xmlCourseItemNode->setAttribute("type", ELEARNING_XML_EXERCISE);
          } else if ($elearningLessonId) {
            $xmlCourseItemNode->setAttribute("id", $elearningLessonId);
            $xmlCourseItemNode->setAttribute("type", ELEARNING_XML_LESSON);
          }
          $xmlCourseItemNode->setAttribute("name", $name);
          $xmlCourseItemNode->setAttribute("description", $description);
          $xmlCourseItemNode->setAttribute("image", $image);
          $xmlCourseNode->appendChild($xmlCourseItemNode);
        }
      }

      $xmlDocument->preserveWhiteSpace = false;
      $xmlDocument->formatOutput = true;
      $xmlResponse = $xmlDocument->saveXML();
    }

    return($xmlResponse);
  }

  // Import a course exposed by a REST web service
  // The matter parameter is a matter of the importing website
  // It is the matter that the course will be assign to when it is imported
  function importCourseREST($contentImportId, $xmlResponse, $elearningMatterId) {
    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return(false);
    }

    $lastInsertElearningCourseId = '';

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $courseNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_COURSE);
    foreach ($courseNodes as $courseNode) {
      $nodeName = $courseNode->tagName;
      if ($nodeName == ELEARNING_XML_COURSE) {
        $name = $courseNode->getAttribute("name");
        $description = $courseNode->getAttribute("description");
        $image = $courseNode->getAttribute("image");

        // The name must not already exist
        if ($elearningCourse = $this->elearningCourseUtils->selectByName($name)) {
          $randomNumber = LibUtils::generateUniqueId();
          $name = $name . ELEARNING_DUPLICATA . '_' . $randomNumber;
        }

        // Create the course
        $elearningCourse = new ElearningCourse();
        $elearningCourse->setName($name);
        $elearningCourse->setDescription($description);
        $elearningCourse->setImage($image);
        $elearningCourse->setMatterId($elearningMatterId);
        $this->elearningCourseUtils->insert($elearningCourse);
        $lastInsertElearningCourseId = $this->elearningCourseUtils->getLastInsertId();

        $this->storeLastImportedCourseId($lastInsertElearningCourseId);

        // Copy the image if any
        $filename = $sourceHomeUrl . "/account/data/elearning/course/image/$image";
        if ($image) {
          LibFile::curlGetFileContent($filename, $this->elearningCourseUtils->imageFilePath . $image);
        }

        $elearningCourseItemNodes = $courseNode->getElementsByTagName(ELEARNING_XML_COURSE_ITEM);
        foreach ($elearningCourseItemNodes as $elearningCourseItemNode) {
          $nodeName = $elearningCourseItemNode->tagName;
          if ($nodeName == ELEARNING_XML_COURSE_ITEM) {
            $type = $elearningCourseItemNode->getAttribute("type");
            if ($type == ELEARNING_XML_EXERCISE) {
              $elearningExerciseId = $elearningCourseItemNode->getAttribute("id");
              $courseXmlResponse = $this->exposeExerciseAsXML($contentImportId, $elearningExerciseId);
              $lastInsertElearningExerciseId = $this->importExerciseREST($courseXmlResponse, $lastInsertElearningCourseId);
            } else if ($type == ELEARNING_XML_LESSON) {
              $elearningLessonId = $elearningCourseItemNode->getAttribute("id");
              $courseXmlResponse = $this->exposeLessonAsXML($contentImportId, $elearningLessonId);
              $lastInsertElearningLessonId = $this->importLessonREST($courseXmlResponse, $lastInsertElearningCourseId);
            }
          }
        }
      }
    }

    return($lastInsertElearningCourseId);
  }

  // Get a course exposed by a REST web service
  function getCourseContentREST($contentImportId, $xmlResponse) {
    $course = array();

    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return($course);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);

    $courseNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_COURSE);
    foreach ($courseNodes as $courseNode) {
      $nodeName = $courseNode->tagName;
      if ($nodeName == ELEARNING_XML_COURSE) {
        $elearningCourseId = $courseNode->getAttribute("id");
        $courseName = $courseNode->getAttribute("name");
        $courseDescription = $courseNode->getAttribute("description");
        $courseImage = $courseNode->getAttribute("image");

        $courseItems = array();
        $elearningCourseItemNodes = $courseNode->getElementsByTagName(ELEARNING_XML_COURSE_ITEM);
        foreach ($elearningCourseItemNodes as $elearningCourseItemNode) {
          $nodeName = $elearningCourseItemNode->tagName;
          if ($nodeName == ELEARNING_XML_COURSE_ITEM) {
            $id = $elearningCourseItemNode->getAttribute("id");
            $type = $elearningCourseItemNode->getAttribute("type");
            $name = $elearningCourseItemNode->getAttribute("name");
            $description = $elearningCourseItemNode->getAttribute("description");
            $image = $elearningCourseItemNode->getAttribute("image");

            array_push($courseItems, array($type, $id, $name, $description, $image));
          }
        }

        $course = array($elearningCourseId, $courseName, $courseDescription, $courseImage, $courseItems);
      }
    }

    return($course);
  }

  // Get in an xml string the content of an exercise
  function exposeExerciseAsXML($contentImportId, $elearningExerciseId) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($elearningExerciseId) {
      if ($contentImport = $this->selectById($contentImportId)) {
        $domainName = $contentImport->getDomainName();
        $permissionKey = $contentImport->getPermissionKey();

        $importCertificate = $this->renderImportCertificate();

        $url = $domainName . "/engine/modules/elearning/import/exposeExerciseREST.php?importCertificate=$importCertificate&domainName=$gHomeUrl&permissionKey=$permissionKey&elearningExerciseId=$elearningExerciseId";

        $xmlResponse = LibFile::curlGetFileContent($url);
      }
    }

    return($xmlResponse);
  }

  // Expose an exercise through a REST web service
  function exposeExerciseREST($elearningExerciseId) {
    global $gHomeUrl;

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlRootNode = $xmlDocument->createElement(ELEARNING_XML);
    $xmlRootNode->setAttribute("homeUrl", $gHomeUrl);
    $xmlDocument->appendChild($xmlRootNode);
    $xmlDocument->preserveWhiteSpace = false;
    $xmlDocument->formatOutput = true;

    $this->createNodeFromExercise($elearningExerciseId, $xmlDocument, $xmlRootNode);

    $xmlResponse = $xmlDocument->saveXML();

    return($xmlResponse);
  }

  // Create an xml node for an exercise
  function createNodeFromExercise($elearningExerciseId, $xmlDocument, $parentNode) {
    if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
      $name = $elearningExercise->getName();
      $description = $elearningExercise->getDescription();
      $instructions = $elearningExercise->getInstructions();
      $introduction = $elearningExercise->getIntroduction();
      $hideIntroduction = $elearningExercise->getHideIntroduction();
      $image = $elearningExercise->getImage();
      $audio = $elearningExercise->getAudio();
      $autostart = $elearningExercise->getAutostart();
      $maxDuration = $elearningExercise->getMaxDuration();
      $hideKeyboard = $elearningExercise->getHideKeyboard();
      $disableNextPageTabs = $elearningExercise->getDisableNextPageTabs();
      $hidePageTabs = $elearningExercise->getHidePageTabs();
      $hideProgressionBar = $elearningExercise->getHideProgressionBar();
      $skipExerciseIntroduction = $elearningExercise->getSkipExerciseIntroduction();

      $name = utf8_encode($name);
      $description = utf8_encode($description);
      $introduction = utf8_encode($introduction);
      $instructions = utf8_encode($instructions);
      $image = utf8_encode($image);
      $audio = utf8_encode($audio);
      $maxDuration = utf8_encode($maxDuration);
      $hideKeyboard = utf8_encode($hideKeyboard);
      $disableNextPageTabs = utf8_encode($disableNextPageTabs);
      $hidePageTabs = utf8_encode($hidePageTabs);
      $hideProgressionBar = utf8_encode($hideProgressionBar);
      $hideIntroduction = utf8_encode($hideIntroduction);
      $skipExerciseIntroduction = utf8_encode($skipExerciseIntroduction);

      $leIntroduction = $introduction;
      $introduction = LibString::cleanString($introduction);

      $xmlExerciseNode = $xmlDocument->createElement(ELEARNING_XML_EXERCISE);
      $xmlExerciseNode->setAttribute("id", $elearningExerciseId);
      $xmlExerciseNode->setAttribute("name", $name);
      $xmlExerciseNode->setAttribute("description", $description);
      $xmlExerciseNode->setAttribute("image", $image);
      $xmlExerciseNode->setAttribute("audio", $audio);
      $xmlExerciseNode->setAttribute("autostart", $autostart);
      $xmlExerciseNode->setAttribute("maxDuration", $maxDuration);
      $xmlExerciseNode->setAttribute("hideKeyboard", $hideKeyboard);
      $xmlExerciseNode->setAttribute("disableNextPageTabs", $disableNextPageTabs);
      $xmlExerciseNode->setAttribute("hidePageTabs", $hidePageTabs);
      $xmlExerciseNode->setAttribute("hideProgressionBar", $hideProgressionBar);
      $xmlExerciseNode->setAttribute("skipExerciseIntroduction", $skipExerciseIntroduction);
      $xmlExerciseNode->setAttribute("hideIntroduction", $hideIntroduction);
      $parentNode->appendChild($xmlExerciseNode);

      $xmlIntroductionNode = $xmlDocument->createElement(ELEARNING_XML_EXERCISE_INTRODUCTION, $introduction);
      $xmlExerciseNode->appendChild($xmlIntroductionNode);

      if (strstr($leIntroduction, LEXICON_ENTRY_CLASS_NAME)) {
        $this->lexiconImportUtils->createNodesFromContent($leIntroduction, $xmlDocument, $xmlIntroductionNode);
      }

      // The instructions are stored in one node per language
      $languages = $this->languageUtils->getActiveLanguages();
      foreach ($languages as $language) {
        $languageCode = $language->getCode();
        $instructions = $this->languageUtils->getTextForLanguage($elearningExercise->getInstructions(), $languageCode);
        $instructions = utf8_encode($instructions);
        $instructions = LibString::cleanString($instructions);
        $xmlInstructionsNode = $xmlDocument->createElement(ELEARNING_XML_EXERCISE_INSTRUCTIONS, $instructions);
        $xmlInstructionsNode->setAttribute("language", $languageCode);
        $xmlExerciseNode->appendChild($xmlInstructionsNode);
      }

      // Export the pages of questions
      $elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
      foreach ($elearningExercisePages as $elearningExercisePage) {
        $elearningExercisePageId = $elearningExercisePage->getId();
        if ($elearningExercisePage = $this->elearningExercisePageUtils->selectById($elearningExercisePageId)) {
          $name = $elearningExercisePage->getName();
          $description = $elearningExercisePage->getDescription();
          $text = $elearningExercisePage->getText();
          $image = $elearningExercisePage->getImage();
          $audio = $elearningExercisePage->getAudio();
          $questionType = $elearningExercisePage->getQuestionType();
          $video = $elearningExercisePage->getVideo();
          $videoUrl = $elearningExercisePage->getVideoUrl();
          $hideText = $elearningExercisePage->getHideText();
          $hintPlacement = $elearningExercisePage->getHintPlacement();
          $listOrder = $elearningExercisePage->getListOrder();

          $name = utf8_encode($name);
          $description = utf8_encode($description);
          $text = utf8_encode($text);
          $image = utf8_encode($image);
          $audio = utf8_encode($audio);
          $questionType = utf8_encode($questionType);
          $video = utf8_encode($video);
          $videoUrl = utf8_encode($videoUrl);
          $hideText = utf8_encode($hideText);
          $hintPlacement = utf8_encode($hintPlacement);
          $listOrder = utf8_encode($listOrder);

          $leText = $text;
          $text = LibString::cleanString($text);

          $xmlExercisePageNode = $xmlDocument->createElement(ELEARNING_XML_EXERCISE_PAGE);
          $xmlExercisePageNode->setAttribute("name", $name);
          $xmlExercisePageNode->setAttribute("description", $description);
          $xmlExercisePageNode->setAttribute("image", $image);
          $xmlExercisePageNode->setAttribute("audio", $audio);
          $xmlExercisePageNode->setAttribute("questionType", $questionType);
          $xmlExercisePageNode->setAttribute("videoUrl", $videoUrl);
          $xmlExercisePageNode->setAttribute("video", $video);
          $xmlExercisePageNode->setAttribute("hideText", $hideText);
          $xmlExercisePageNode->setAttribute("hintPlacement", $hintPlacement);
          $xmlExercisePageNode->setAttribute("listOrder", $listOrder);
          $xmlExerciseNode->appendChild($xmlExercisePageNode);

          $xmlTextNode = $xmlDocument->createElement(ELEARNING_XML_EXERCISE_PAGE_TEXT, $text);
          $xmlExercisePageNode->appendChild($xmlTextNode);

          if (strstr($leText, LEXICON_ENTRY_CLASS_NAME)) {
            $this->lexiconImportUtils->createNodesFromContent($leText, $xmlDocument, $xmlTextNode);
          }

          // The instructions are stored in one node per language
          $languages = $this->languageUtils->getActiveLanguages();
          foreach ($languages as $language) {
            $languageCode = $language->getCode();
            $instructions = $this->languageUtils->getTextForLanguage($elearningExercisePage->getInstructions(), $languageCode);
            $instructions = utf8_encode($instructions);
            $instructions = LibString::cleanString($instructions);
            $xmlInstructionsNode = $xmlDocument->createElement(ELEARNING_XML_EXERCISE_PAGE_INSTRUCTIONS, $instructions);
            $xmlInstructionsNode->setAttribute("language", $languageCode);
            $xmlExercisePageNode->appendChild($xmlInstructionsNode);
          }

          // Export the questions
          $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);
          foreach ($elearningQuestions as $elearningQuestion) {
            $elearningQuestionId = $elearningQuestion->getId();
            if ($elearningQuestion = $this->elearningQuestionUtils->selectById($elearningQuestionId)) {
              $question = $elearningQuestion->getQuestion();
              $image = $elearningQuestion->getImage();
              $audio = $elearningQuestion->getAudio();
              $hint = $elearningQuestion->getHint();
              $points = $elearningQuestion->getPoints();
              $listOrder = $elearningQuestion->getListOrder();

              $question = utf8_encode($question);
              $image = utf8_encode($image);
              $audio = utf8_encode($audio);
              $hint = utf8_encode($hint);
              $points = utf8_encode($points);
              $listOrder = utf8_encode($listOrder);

              $xmlQuestionNode = $xmlDocument->createElement(ELEARNING_XML_QUESTION);
              $xmlQuestionNode->setAttribute("question", $question);
              $xmlQuestionNode->setAttribute("image", $image);
              $xmlQuestionNode->setAttribute("audio", $audio);
              $xmlQuestionNode->setAttribute("hint", $hint);
              $xmlQuestionNode->setAttribute("points", $points);
              $xmlQuestionNode->setAttribute("listOrder", $listOrder);
              $xmlExercisePageNode->appendChild($xmlQuestionNode);

              // The explanation is stored in one node per language
              $languages = $this->languageUtils->getActiveLanguages();
              foreach ($languages as $language) {
                $languageCode = $language->getCode();
                $explanation = $this->languageUtils->getTextForLanguage($elearningQuestion->getExplanation(), $languageCode);
                $explanation = utf8_encode($explanation);
                $explanation = LibString::cleanString($explanation);
                $xmlExplanationNode = $xmlDocument->createElement(ELEARNING_XML_QUESTION_EXPLANATION, $explanation);
                $xmlExplanationNode->setAttribute("language", $languageCode);
                $xmlQuestionNode->appendChild($xmlExplanationNode);
              }

              // Export the answers
              $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);
              foreach ($elearningAnswers as $elearningAnswer) {
                $elearningAnswerId = $elearningAnswer->getId();
                if ($elearningAnswer = $this->elearningAnswerUtils->selectById($elearningAnswerId)) {
                  $answer = $elearningAnswer->getAnswer();
                  $image = $elearningQuestion->getImage();
                  $audio = $elearningAnswer->getAudio();
                  $listOrder = $elearningAnswer->getListOrder();

                  $answer = utf8_encode($answer);
                  $image = utf8_encode($image);
                  $audio = utf8_encode($audio);
                  $listOrder = utf8_encode($listOrder);

                  $xmlAnswerNode = $xmlDocument->createElement(ELEARNING_XML_ANSWER);
                  $xmlAnswerNode->setAttribute("answer", $answer);
                  $xmlAnswerNode->setAttribute("image", $image);
                  $xmlAnswerNode->setAttribute("audio", $audio);
                  $xmlAnswerNode->setAttribute("listOrder", $listOrder);
                  $xmlQuestionNode->appendChild($xmlAnswerNode);

                  // The explanation is stored in one node per language
                  $languages = $this->languageUtils->getActiveLanguages();
                  foreach ($languages as $language) {
                    $languageCode = $language->getCode();
                    $explanation = $this->languageUtils->getTextForLanguage($elearningAnswer->getExplanation(), $languageCode);
                    $explanation = utf8_encode($explanation);
                    $explanation = LibString::cleanString($explanation);
                    $xmlExplanationNode = $xmlDocument->createElement(ELEARNING_XML_ANSWER_EXPLANATION, $explanation);
                    $xmlExplanationNode->setAttribute("language", $languageCode);
                    $xmlAnswerNode->appendChild($xmlExplanationNode);
                  }
                }
              }

              // Export the solutions
              if ($elearningSolutions = $this->elearningSolutionUtils->selectByQuestion($elearningQuestionId)) {
                foreach ($elearningSolutions as $elearningSolution) {
                  $elearningSolutionId = $elearningSolution->getId();
                  if ($elearningSolution = $this->elearningSolutionUtils->selectById($elearningSolutionId)) {
                    $solutionElearningAnswerId = $elearningSolution->getElearningAnswer();
                    if ($solutionElearningAnswer = $this->elearningAnswerUtils->selectById($solutionElearningAnswerId)) {
                      $answer = $solutionElearningAnswer->getAnswer();

                      $answer = utf8_encode($answer);

                      $xmlSolutionNode = $xmlDocument->createElement(ELEARNING_XML_SOLUTION);
                      $xmlSolutionNode->setAttribute("answer", $answer);
                      $xmlQuestionNode->appendChild($xmlSolutionNode);
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    return($xmlExerciseNode);
  }

  // Create an exercise from an xml node
  function createExerciseFromNode($elearningExerciseNode, $sourceHomeUrl, $elearningCourseId = '') {
    $name = $elearningExerciseNode->getAttribute("name");
    $description = $elearningExerciseNode->getAttribute("description");
    $maxDuration = $elearningExerciseNode->getAttribute("maxDuration");
    $hideKeyboard = $elearningExerciseNode->getAttribute("hideKeyboard");
    $disableNextPageTabs = $elearningExerciseNode->getAttribute("disableNextPageTabs");
    $hidePageTabs = $elearningExerciseNode->getAttribute("hidePageTabs");
    $hideProgressionBar = $elearningExerciseNode->getAttribute("hideProgressionBar");
    $skipExerciseIntroduction = $elearningExerciseNode->getAttribute("skipExerciseIntroduction");
    $hideIntroduction = $elearningExerciseNode->getAttribute("hideIntroduction");
    $image = $elearningExerciseNode->getAttribute("image");
    $audio = $elearningExerciseNode->getAttribute("audio");
    $autostart = $elearningExerciseNode->getAttribute("autostart");

    // The name must not already exist
    if ($elearningExercise = $this->elearningExerciseUtils->selectByName($name)) {
      $randomNumber = LibUtils::generateUniqueId();
      $name = $name . ELEARNING_DUPLICATA . '_' . $randomNumber;
    }

    $introduction = '';
    $introductionNodes = $elearningExerciseNode->getElementsByTagName(ELEARNING_XML_EXERCISE_INTRODUCTION);
    foreach ($introductionNodes as $introductionNode) {
      $nodeName = $introductionNode->tagName;
      if ($nodeName == ELEARNING_XML_EXERCISE_INTRODUCTION) {
        $introduction = $introductionNode->nodeValue;
        $introduction = LibString::decodeHtmlspecialchars($introduction);
        $introduction = LibString::stripMultipleSpaces($introduction);

        // Copy the images if any
        $images = $this->commonUtils->getContentImages($introduction);
        foreach ($images as $image) {
          if ($image) {
            $filename = $sourceHomeUrl . "/account/data/elearning/exercise/image/$image";
            LibFile::curlGetFileContent($filename, $this->elearningExerciseUtils->imageFilePath . $image);
          }
        }

        // Create the lexicon entries
        $introduction = $this->lexiconImportUtils->createLexiconEntriesFromNode($introductionNode, $sourceHomeUrl, $introduction);
      }
    }

    // Create the exercise
    $elearningExercise = new ElearningExercise();
    $elearningExercise->setName($name);
    $elearningExercise->setDescription($description);
    $elearningExercise->setMaxDuration($maxDuration);
    $elearningExercise->setHideKeyboard($hideKeyboard);
    $elearningExercise->setDisableNextPageTabs($disableNextPageTabs);
    $elearningExercise->setHidePageTabs($hidePageTabs);
    $elearningExercise->setHideProgressionBar($hideProgressionBar);
    $elearningExercise->setSkipExerciseIntroduction($skipExerciseIntroduction);
    $elearningExercise->setIntroduction($introduction);
    $elearningExercise->setHideIntroduction($hideIntroduction);
    $releaseDate = $this->clockUtils->getSystemDate();
    $elearningExercise->setReleaseDate($releaseDate);
    $elearningExercise->setImage($image);
    $elearningExercise->setAudio($audio);
    $elearningExercise->setAutostart($autostart);
    $this->elearningExerciseUtils->insert($elearningExercise);
    $lastInsertElearningExerciseId = $this->elearningExerciseUtils->getLastInsertId();

    $instructionsNodes = $elearningExerciseNode->getElementsByTagName(ELEARNING_XML_EXERCISE_INSTRUCTIONS);
    foreach ($instructionsNodes as $instructionsNode) {
      $nodeName = $instructionsNode->tagName;
      if ($nodeName == ELEARNING_XML_EXERCISE_INSTRUCTIONS) {
        $instructions = $instructionsNode->nodeValue;
        $language = $instructionsNode->getAttribute("language");

        $elearningExercise->setInstructions($this->languageUtils->setTextForLanguage($elearningExercise->getInstructions(), $language, $instructions));
      }
    }

    // Copy the image if any
    $filename = $sourceHomeUrl . "/account/data/elearning/exercise/image/$image";
    if ($image) {
      LibFile::curlGetFileContent($filename, $this->elearningExerciseUtils->imageFilePath . $image);
    }

    // Copy the audio if any
    $filename = $sourceHomeUrl . "/account/data/elearning/exercise/audio/$audio";
    if ($audio) {
      LibFile::curlGetFileContent($filename, $this->elearningExerciseUtils->audioFilePath . $audio);
    }

    // If a course is specified then attach the exercise to the course
    if ($elearningCourseId) {
      $elearningCourseItem = new ElearningCourseItem();
      $elearningCourseItem->setElearningCourseId($elearningCourseId);
      $elearningCourseItem->setElearningExerciseId($lastInsertElearningExerciseId);
      $listOrder = $this->elearningCourseItemUtils->getNextListOrder($elearningCourseId);
      $elearningCourseItem->setListOrder($listOrder);
      $this->elearningCourseItemUtils->insert($elearningCourseItem);
    }

    $elearningExercisePageNodes = $elearningExerciseNode->getElementsByTagName(ELEARNING_XML_EXERCISE_PAGE);
    foreach ($elearningExercisePageNodes as $elearningExercisePageNode) {
      $nodeName = $elearningExercisePageNode->tagName;
      if ($nodeName == ELEARNING_XML_EXERCISE_PAGE) {
        $name = $elearningExercisePageNode->getAttribute("name");
        $description = $elearningExercisePageNode->getAttribute("description");
        $hideText = $elearningExercisePageNode->getAttribute("hideText");
        $image = $elearningExercisePageNode->getAttribute("image");
        $audio = $elearningExercisePageNode->getAttribute("audio");
        $listOrder = $elearningExercisePageNode->getAttribute("listOrder");
        $questionType = $elearningExercisePageNode->getAttribute("questionType");
        $video = $elearningExercisePageNode->getAttribute("video");
        $videoUrl = $elearningExercisePageNode->getAttribute("videoUrl");
        $hideIntroduction = $elearningExercisePageNode->getAttribute("hideIntroduction");
        $hintPlacement = $elearningExercisePageNode->getAttribute("hintPlacement");

        $text = '';
        $textNodes = $elearningExercisePageNode->getElementsByTagName(ELEARNING_XML_EXERCISE_PAGE_TEXT);
        foreach ($textNodes as $textNode) {
          $nodeName = $textNode->tagName;
          if ($nodeName == ELEARNING_XML_EXERCISE_PAGE_TEXT) {
            $text = $textNode->nodeValue;

            $text = LibString::decodeHtmlspecialchars($text);
            $text = LibString::stripMultipleSpaces($text);

            // Copy the images if any
            $images = $this->commonUtils->getContentImages($text);
            foreach ($images as $image) {
              if ($image) {
                $filename = $sourceHomeUrl . "/account/data/elearning/exercise_page/image/$image";
                LibFile::curlGetFileContent($filename, $this->elearningExercisePageUtils->imageFilePath . $image);
              }
            }

            // Create the lexicon entries
            $text = $this->lexiconImportUtils->createLexiconEntriesFromNode($textNode, $sourceHomeUrl, $text);
          }
        }

        // Create the exercise page
        $elearningExercisePage = new ElearningExercisePage();
        $elearningExercisePage->setName($name);
        $elearningExercisePage->setDescription($description);
        $elearningExercisePage->setHintPlacement($hintPlacement);
        $elearningExercisePage->setText($text);
        $elearningExercisePage->setHideText($hideText);
        $elearningExercisePage->setImage($image);
        $elearningExercisePage->setAudio($audio);
        $elearningExercisePage->setQuestionType($questionType);
        $elearningExercisePage->setVideo($video);
        $elearningExercisePage->setVideoUrl($videoUrl);
        $elearningExercisePage->setListOrder($listOrder);
        $elearningExercisePage->setElearningExerciseId($lastInsertElearningExerciseId);
        $this->elearningExercisePageUtils->insert($elearningExercisePage);
        $lastInsertElearningExercisePageId = $this->elearningExercisePageUtils->getLastInsertId();

        $instructionsNodes = $elearningExercisePageNode->getElementsByTagName(ELEARNING_XML_EXERCISE_PAGE_INSTRUCTIONS);
        foreach ($instructionsNodes as $instructionsNode) {
          $nodeName = $instructionsNode->tagName;
          if ($nodeName == ELEARNING_XML_EXERCISE_PAGE_INSTRUCTIONS) {
            $instructions = $instructionsNode->nodeValue;
            $language = $instructionsNode->getAttribute("language");

            $elearningExercisePage->setInstructions($this->languageUtils->setTextForLanguage($elearningExercisePage->getInstructions(), $language, $instructions));
          }
        }

        // Copy the image if any
        $filename = $sourceHomeUrl . "/account/data/elearning/exercise_page/image/$image";
        if ($image) {
          LibFile::curlGetFileContent($filename, $this->elearningExercisePageUtils->imageFilePath . $image);
        }

        // Copy the audio if any
        $filename = $sourceHomeUrl . "/account/data/elearning/exercise_page/audio/$audio";
        if ($audio) {
          LibFile::curlGetFileContent($filename, $this->elearningExercisePageUtils->audioFilePath . $audio);
        }

        $elearningQuestionNodes = $elearningExercisePageNode->getElementsByTagName(ELEARNING_XML_QUESTION);
        foreach ($elearningQuestionNodes as $elearningQuestionNode) {
          $nodeName = $elearningQuestionNode->tagName;
          if ($nodeName == ELEARNING_XML_QUESTION) {
            $question = $elearningQuestionNode->getAttribute("question");
            $image = $elearningQuestionNode->getAttribute("image");
            $audio = $elearningQuestionNode->getAttribute("audio");
            $hint = $elearningQuestionNode->getAttribute("hint");
            $points = $elearningQuestionNode->getAttribute("points");
            $listOrder = $elearningQuestionNode->getAttribute("listOrder");

            // Create the question
            $elearningQuestion = new ElearningQuestion();
            $elearningQuestion->setQuestion($question);
            $elearningQuestion->setImage($image);
            $elearningQuestion->setAudio($audio);
            $elearningQuestion->setHint($hint);
            $elearningQuestion->setPoints($points);
            $elearningQuestion->setListOrder($listOrder);
            $elearningQuestion->setElearningExercisePage($lastInsertElearningExercisePageId);

            $questionExplanationNodes = $elearningQuestionNode->getElementsByTagName(ELEARNING_XML_QUESTION_EXPLANATION);
            foreach ($questionExplanationNodes as $questionExplanationNode) {
              $nodeName = $questionExplanationNode->tagName;
              if ($nodeName == ELEARNING_XML_QUESTION_EXPLANATION) {
                $explanation = $questionExplanationNode->nodeValue;
                $language = $questionExplanationNode->getAttribute("language");

                $elearningQuestion->setExplanation($this->languageUtils->setTextForLanguage($elearningQuestion->getExplanation(), $language, $explanation));
              }
            }

            $this->elearningQuestionUtils->insert($elearningQuestion);
            $lastInsertElearningQuestionId = $this->elearningQuestionUtils->getLastInsertId();

            // Copy the image if any
            $filename = $sourceHomeUrl . "/account/data/elearning/question/image/$image";
            if ($image) {
              LibFile::curlGetFileContent($filename, $this->elearningQuestionUtils->imageFilePath . $image);
            }

            // Copy the audio if any
            $filename = $sourceHomeUrl . "/account/data/elearning/question/audio/$audio";
            if ($audio) {
              LibFile::curlGetFileContent($filename, $this->elearningQuestionUtils->audioFilePath . $audio);
            }

            $elearningAnswerNodes = $elearningQuestionNode->getElementsByTagName(ELEARNING_XML_ANSWER);
            foreach ($elearningAnswerNodes as $elearningAnswerNode) {
              $nodeName = $elearningAnswerNode->tagName;
              if ($nodeName == ELEARNING_XML_ANSWER) {
                $answer = $elearningAnswerNode->getAttribute("answer");
                $image = $elearningAnswerNode->getAttribute("image");
                $audio = $elearningAnswerNode->getAttribute("audio");
                $listOrder = $elearningAnswerNode->getAttribute("listOrder");

                // Create the answer
                $elearningAnswer = new ElearningAnswer();
                $elearningAnswer->setAnswer($answer);
                $elearningAnswer->setImage($image);
                $elearningAnswer->setAudio($audio);
                $elearningAnswer->setListOrder($listOrder);
                $elearningAnswer->setElearningQuestion($lastInsertElearningQuestionId);

                $answerExplanationNodes = $elearningAnswerNode->getElementsByTagName(ELEARNING_XML_ANSWER_EXPLANATION);
                foreach ($answerExplanationNodes as $answerExplanationNode) {
                  $nodeName = $answerExplanationNode->tagName;
                  if ($nodeName == ELEARNING_XML_ANSWER_EXPLANATION) {
                    $explanation = $answerExplanationNode->nodeValue;
                    $language = $answerExplanationNode->getAttribute("language");

                    $elearningAnswer->setExplanation($this->languageUtils->setTextForLanguage($elearningAnswer->getExplanation(), $language, $explanation));
                  }
                }

                $this->elearningAnswerUtils->insert($elearningAnswer);
                $lastInsertElearningAnswerId = $this->elearningAnswerUtils->getLastInsertId();

                // Copy the audio if any
                $filename = $sourceHomeUrl . "/account/data/elearning/answer/audio/$audio";
                if ($audio) {
                  LibFile::curlGetFileContent($filename, $this->elearningAnswerUtils->audioFilePath . $audio);
                }
              }
            }

            $elearningSolutionNodes = $elearningQuestionNode->getElementsByTagName(ELEARNING_XML_SOLUTION);
            foreach ($elearningSolutionNodes as $elearningSolutionNode) {
              $nodeName = $elearningSolutionNode->tagName;
              if ($nodeName == ELEARNING_XML_SOLUTION) {
                $answer = $elearningSolutionNode->getAttribute("answer");
                $answer = LibString::databaseEscapeQuotes($answer);

                // Create the solution
                if ($elearningAnswer = $this->elearningAnswerUtils->selectByQuestionAndAnswer($lastInsertElearningQuestionId, $answer)) {
                  $elearningAnswerId = $elearningAnswer->getId();
                  if (!$elearningSolution = $this->elearningSolutionUtils->selectByQuestionAndAnswer($lastInsertElearningQuestionId, $elearningAnswerId)) {
                    $elearningSolution = new ElearningSolution();
                    $elearningSolution->setElearningQuestion($lastInsertElearningQuestionId);
                    $elearningSolution->setElearningAnswer($elearningAnswerId);
                    $this->elearningSolutionUtils->insert($elearningSolution);
                  }
                }
              }
            }
          }
        }
      }
    }

    return($lastInsertElearningExerciseId);
  }

  // Import an exercise exposed by a REST web service
  function importExerciseREST($xmlResponse, $elearningCourseId = '') {
    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return(false);
    }

    $lastInsertElearningExerciseId = '';

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $elearningExerciseNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_EXERCISE);
    foreach ($elearningExerciseNodes as $elearningExerciseNode) {
      $nodeName = $elearningExerciseNode->tagName;
      if ($nodeName == ELEARNING_XML_EXERCISE) {
        $lastInsertElearningExerciseId = $this->createExerciseFromNode($elearningExerciseNode, $sourceHomeUrl, $elearningCourseId);
      }
    }

    return($lastInsertElearningExerciseId);
  }

  // Get an exercise exposed by a REST web service
  function getExerciseDetailsREST($contentImportId, $xmlResponse) {
    $exercise = array();

    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return(false);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $elearningExerciseNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_EXERCISE);
    foreach ($elearningExerciseNodes as $elearningExerciseNode) {
      $nodeName = $elearningExerciseNode->tagName;
      if ($nodeName == ELEARNING_XML_EXERCISE) {
        $name = $elearningExerciseNode->getAttribute("name");
        $description = $elearningExerciseNode->getAttribute("description");
        $maxDuration = $elearningExerciseNode->getAttribute("maxDuration");
        $image = $elearningExerciseNode->getAttribute("image");
        $audio = $elearningExerciseNode->getAttribute("audio");
        $autostart = $elearningExerciseNode->getAttribute("autostart");

        $exercisePages = array();
        $elearningExercisePageNodes = $elearningExerciseNode->getElementsByTagName(ELEARNING_XML_EXERCISE_PAGE);
        foreach ($elearningExercisePageNodes as $elearningExercisePageNode) {
          $nodeName = $elearningExercisePageNode->tagName;
          if ($nodeName == ELEARNING_XML_EXERCISE_PAGE) {
            $paragraphName = $elearningExercisePageNode->getAttribute("paragraphName");
            $paragraphDescription = $elearningExercisePageNode->getAttribute("paragraphDescription");
            $paragraphImage = $elearningExercisePageNode->getAttribute("paragraphImage");
            $paragraphAudio = $elearningExercisePageNode->getAttribute("paragraphAudio");
            $listOrder = $elearningExercisePageNode->getAttribute("listOrder");
            $questionType = $elearningExercisePageNode->getAttribute("questionType");
            $video = $elearningExercisePageNode->getAttribute("video");
            $videoUrl = $elearningExercisePageNode->getAttribute("videoUrl");
            $hideText = $elearningExercisePageNode->getAttribute("hideText");
            $hintPlacement = $elearningExercisePageNode->getAttribute("hintPlacement");

            array_push($exercisePages, array($paragraphName, $paragraphDescription, $paragraphImage, $paragraphAudio, $listOrder, $questionType, $hintPlacement));
          }
        }

        $exercise = array($name, $description, $maxDuration, $image, $audio, $exercisePages);
      }
    }

    return($exercise);
  }

  // Get in an xml string the content of a lesson
  function exposeLessonAsXML($contentImportId, $elearningLessonId) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($elearningLessonId) {
      if ($contentImport = $this->selectById($contentImportId)) {
        $domainName = $contentImport->getDomainName();
        $permissionKey = $contentImport->getPermissionKey();

        $importCertificate = $this->renderImportCertificate();

        $url = $domainName . "/engine/modules/elearning/import/exposeLessonREST.php?importCertificate=$importCertificate&domainName=$gHomeUrl&permissionKey=$permissionKey&elearningLessonId=$elearningLessonId";

        $xmlResponse = LibFile::curlGetFileContent($url);
      }
    }

    return($xmlResponse);
  }

  // Expose a lesson through a REST web service
  function exposeLessonREST($elearningLessonId) {
    global $gHomeUrl;

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlRootNode = $xmlDocument->createElement(ELEARNING_XML);
    $xmlRootNode->setAttribute("homeUrl", $gHomeUrl);
    $xmlDocument->appendChild($xmlRootNode);
    $xmlDocument->preserveWhiteSpace = false;
    $xmlDocument->formatOutput = true;

    $this->createNodeFromLesson($elearningLessonId, $xmlDocument, $xmlRootNode);

    $xmlResponse = $xmlDocument->saveXML();

    return($xmlResponse);
  }

  // Create an xml node for a lesson
  function createNodeFromLesson($elearningLessonId, $xmlDocument, $parentNode) {
    if ($elearningLesson = $this->elearningLessonUtils->selectById($elearningLessonId)) {
      $name = $elearningLesson->getName();
      $description = $elearningLesson->getDescription();
      $instructions = $elearningLesson->getInstructions();
      $image = $elearningLesson->getImage();
      $audio = $elearningLesson->getAudio();
      $introduction = $elearningLesson->getIntroduction();

      $name = utf8_encode($name);
      $description = utf8_encode($description);
      $instructions = utf8_encode($instructions);
      $image = utf8_encode($image);
      $audio = utf8_encode($audio);
      $introduction = utf8_encode($introduction);

      $leIntroduction = $introduction;
      $introduction = LibString::cleanString($introduction);

      $xmlLessonNode = $xmlDocument->createElement(ELEARNING_XML_LESSON);
      $xmlLessonNode->setAttribute("id", $elearningLessonId);
      $xmlLessonNode->setAttribute("name", $name);
      $xmlLessonNode->setAttribute("description", $description);
      $xmlLessonNode->setAttribute("image", $image);
      $xmlLessonNode->setAttribute("audio", $audio);
      $parentNode->appendChild($xmlLessonNode);

      $xmlIntroductionNode = $xmlDocument->createElement(ELEARNING_XML_LESSON_INTRODUCTION, $introduction);
      $xmlLessonNode->appendChild($xmlIntroductionNode);

      if (strstr($leIntroduction, LEXICON_ENTRY_CLASS_NAME)) {
        $this->lexiconImportUtils->createNodesFromContent($leIntroduction, $xmlDocument, $xmlIntroductionNode);
      }

      // The instructions are stored in one node per language
      $languages = $this->languageUtils->getActiveLanguages();
      foreach ($languages as $language) {
        $languageCode = $language->getCode();
        $instructions = $this->languageUtils->getTextForLanguage($elearningLesson->getInstructions(), $languageCode);
        $instructions = utf8_encode($instructions);
        $instructions = LibString::cleanString($instructions);
        $xmlInstructionsNode = $xmlDocument->createElement(ELEARNING_XML_LESSON_INSTRUCTIONS, $instructions);
        $xmlInstructionsNode->setAttribute("language", $languageCode);
        $xmlLessonNode->appendChild($xmlInstructionsNode);
      }

      // Export the paragraphs
      $elearningLessonParagraphs = $this->elearningLessonParagraphUtils->selectByLessonId($elearningLessonId);
      foreach ($elearningLessonParagraphs as $elearningLessonParagraph) {
        $elearningLessonParagraphId = $elearningLessonParagraph->getId();
        if ($elearningLessonParagraph = $this->elearningLessonParagraphUtils->selectById($elearningLessonParagraphId)) {
          $headline = $elearningLessonParagraph->getHeadline();
          $body = $elearningLessonParagraph->getBody();
          $image = $elearningLessonParagraph->getImage();
          $audio = $elearningLessonParagraph->getAudio();
          $video = $elearningLessonParagraph->getVideo();
          $videoUrl = $elearningLessonParagraph->getVideoUrl();
          $listOrder = $elearningLessonParagraph->getListOrder();
          $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
          $exerciseTitle = $elearningLessonParagraph->getExerciseTitle();

          $headline = utf8_encode($headline);
          $body = utf8_encode($body);
          $image = utf8_encode($image);
          $audio = utf8_encode($audio);
          $video = utf8_encode($video);
          $videoUrl = utf8_encode($videoUrl);
          $listOrder = utf8_encode($listOrder);
          $exerciseTitle = utf8_encode($exerciseTitle);

          $leBody = $body;
          $body = LibString::cleanString($body);

          $xmlLessonParagraphNode = $xmlDocument->createElement(ELEARNING_XML_LESSON_PARAGRAPH);
          $xmlLessonParagraphNode->setAttribute("headline", $headline);
          $xmlLessonParagraphNode->setAttribute("image", $image);
          $xmlLessonParagraphNode->setAttribute("audio", $audio);
          $xmlLessonParagraphNode->setAttribute("video", $video);
          $xmlLessonParagraphNode->setAttribute("videoUrl", $videoUrl);
          $xmlLessonParagraphNode->setAttribute("listOrder", $listOrder);
          $xmlLessonParagraphNode->setAttribute("exerciseTitle", $exerciseTitle);
          $xmlLessonNode->appendChild($xmlLessonParagraphNode);

          $xmlBodyNode = $xmlDocument->createElement(ELEARNING_XML_LESSON_PARAGRAPH_BODY, $body);
          $xmlLessonParagraphNode->appendChild($xmlBodyNode);

          if (strstr($leBody, LEXICON_ENTRY_CLASS_NAME)) {
            $this->lexiconImportUtils->createNodesFromContent($leBody, $xmlDocument, $xmlBodyNode);
          }

          $this->createNodeFromExercise($elearningExerciseId, $xmlDocument, $xmlLessonParagraphNode);
        }
      }

      $xmlDocument->preserveWhiteSpace = false;
      $xmlDocument->formatOutput = true;
      $xmlResponse = $xmlDocument->saveXML();
    }

    return($xmlResponse);
  }

  // Import a lesson exposed by a REST web service
  function importLessonREST($xmlResponse, $elearningCourseId = '') {
    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return(false);
    }

    $lastInsertElearningLessonId = '';

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $elearningLessonNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_LESSON);
    foreach ($elearningLessonNodes as $elearningLessonNode) {
      $nodeName = $elearningLessonNode->tagName;
      if ($nodeName == ELEARNING_XML_LESSON) {
        $name = $elearningLessonNode->getAttribute("name");
        $description = $elearningLessonNode->getAttribute("description");
        $image = $elearningLessonNode->getAttribute("image");
        $audio = $elearningLessonNode->getAttribute("audio");

        // The name must not already exist
        if ($elearningLesson = $this->elearningLessonUtils->selectByName($name)) {
          $randomNumber = LibUtils::generateUniqueId();
          $name = $name . ELEARNING_DUPLICATA . '_' . $randomNumber;
        }

        $introduction = '';
        $introductionNodes = $elearningLessonNode->getElementsByTagName(ELEARNING_XML_LESSON_INTRODUCTION);
        foreach ($introductionNodes as $introductionNode) {
          $nodeName = $introductionNode->tagName;
          if ($nodeName == ELEARNING_XML_LESSON_INTRODUCTION) {
            $introduction = $introductionNode->nodeValue;
            $introduction = LibString::decodeHtmlspecialchars($introduction);
            $introduction = LibString::stripMultipleSpaces($introduction);

            // Copy the images if any
            $images = $this->commonUtils->getContentImages($introduction);
            foreach ($images as $image) {
              if ($image) {
                $filename = $sourceHomeUrl . "/account/data/elearning/lesson/image/$image";
                LibFile::curlGetFileContent($filename, $this->elearningLessonUtils->imageFilePath . $image);
              }
            }

            // Create the lexicon entries
            $introduction = $this->lexiconImportUtils->createLexiconEntriesFromNode($introductionNode, $sourceHomeUrl, $introduction);
          }
        }

        // Create the lesson
        $elearningLesson = new ElearningLesson();
        $elearningLesson->setName($name);
        $elearningLesson->setDescription($description);
        $elearningLesson->setIntroduction($introduction);
        $elearningLesson->setImage($image);
        $elearningLesson->setAudio($audio);
        $releaseDate = $this->clockUtils->getSystemDate();
        $elearningLesson->setReleaseDate($releaseDate);
        $this->elearningLessonUtils->insert($elearningLesson);
        $lastInsertElearningLessonId = $this->elearningLessonUtils->getLastInsertId();

        $instructionsNodes = $elearningLessonNode->getElementsByTagName(ELEARNING_XML_LESSON_INSTRUCTIONS);
        foreach ($instructionsNodes as $instructionsNode) {
          $nodeName = $instructionsNode->tagName;
          if ($nodeName == ELEARNING_XML_LESSON_INSTRUCTIONS) {
            $instructions = $instructionsNode->nodeValue;
            $language = $instructionsNode->getAttribute("language");
            $elearningLesson->setInstructions($this->languageUtils->setTextForLanguage($elearningLesson->getInstructions(), $language, $instructions));
          }
        }

        // Copy the image if any
        $filename = $sourceHomeUrl . "/account/data/elearning/lesson/image/$image";
        if ($image) {
          LibFile::curlGetFileContent($filename, $this->elearningLessonUtils->imageFilePath . $image);
        }

        // Copy the audio if any
        $filename = $sourceHomeUrl . "/account/data/elearning/lesson/audio/$audio";
        if ($audio) {
          LibFile::curlGetFileContent($filename, $this->elearningLessonUtils->audioFilePath . $audio);
        }

        // If a course is specified then attach the lesson to the course
        if ($elearningCourseId) {
          $elearningCourseItem = new ElearningCourseItem();
          $elearningCourseItem->setElearningCourseId($elearningCourseId);
          $elearningCourseItem->setElearningLessonId($lastInsertElearningLessonId);
          $listOrder = $this->elearningCourseItemUtils->getNextListOrder($elearningCourseId);
          $elearningCourseItem->setListOrder($listOrder);
          $this->elearningCourseItemUtils->insert($elearningCourseItem);
        }

        $elearningLessonParagraphNodes = $elearningLessonNode->getElementsByTagName(ELEARNING_XML_LESSON_PARAGRAPH);
        foreach ($elearningLessonParagraphNodes as $elearningLessonParagraphNode) {
          $nodeName = $elearningLessonParagraphNode->tagName;
          if ($nodeName == ELEARNING_XML_LESSON_PARAGRAPH) {
            $elearningExerciseId = '';
            $headline = $elearningLessonParagraphNode->getAttribute("headline");
            $image = $elearningLessonParagraphNode->getAttribute("image");
            $audio = $elearningLessonParagraphNode->getAttribute("audio");
            $video = $elearningLessonParagraphNode->getAttribute("video");
            $videoUrl = $elearningLessonParagraphNode->getAttribute("videoUrl");
            $listOrder = $elearningLessonParagraphNode->getAttribute("listOrder");
            $elearningExerciseNodes = $elearningLessonParagraphNode->getElementsByTagName(ELEARNING_XML_EXERCISE);
            foreach ($elearningExerciseNodes as $elearningExerciseNode) {
              $nodeName = $elearningExerciseNode->tagName;
              if ($nodeName == ELEARNING_XML_EXERCISE) {
                $elearningExerciseId = $this->createExerciseFromNode($elearningExerciseNode, $sourceHomeUrl);
              }
            }
            $exerciseTitle = $elearningLessonParagraphNode->getAttribute("exerciseTitle");

            $body = '';
            $bodyNodes = $elearningLessonParagraphNode->getElementsByTagName(ELEARNING_XML_LESSON_PARAGRAPH_BODY);
            foreach ($bodyNodes as $bodyNode) {
              $nodeName = $bodyNode->tagName;
              if ($nodeName == ELEARNING_XML_LESSON_PARAGRAPH_BODY) {
                $body = $bodyNode->nodeValue;
                $body = LibString::decodeHtmlspecialchars($body);
                $body = LibString::stripMultipleSpaces($body);

                // Copy the images if any
                $images = $this->commonUtils->getContentImages($body);
                foreach ($images as $image) {
                  if ($image) {
                    $filename = $sourceHomeUrl . "/account/data/elearning/lesson/paragraph/image/$image";
                    LibFile::curlGetFileContent($filename, $this->elearningLessonParagraphUtils->imageFilePath . $image);
                  }
                }

                // Create the lexicon entries
                $body = $this->lexiconImportUtils->createLexiconEntriesFromNode($bodyNode, $sourceHomeUrl, $body);
              }
            }

            $elearningLessonParagraph = new ElearningLessonParagraph();
            $elearningLessonParagraph->setHeadline($headline);
            $elearningLessonParagraph->setBody($body);
            $elearningLessonParagraph->setImage($image);
            $elearningLessonParagraph->setAudio($audio);
            $elearningLessonParagraph->setVideo($video);
            $elearningLessonParagraph->setVideoUrl($videoUrl);
            $elearningLessonParagraph->setListOrder($listOrder);
            $elearningLessonParagraph->setElearningExerciseId($elearningExerciseId);
            $elearningLessonParagraph->setExerciseTitle($exerciseTitle);
            $elearningLessonParagraph->setElearningLessonId($lastInsertElearningLessonId);
            $this->elearningLessonParagraphUtils->insert($elearningLessonParagraph);
            $lastInsertElearningLessonParagraphId = $this->elearningLessonParagraphUtils->getLastInsertId();

            // Copy the image if any
            $filename = $sourceHomeUrl . "/account/data/elearning/lesson/paragraph/image/$image";
            if ($image) {
              LibFile::curlGetFileContent($filename, $this->elearningLessonParagraphUtils->imageFilePath . $image);
            }

            // Copy the audio if any
            $filename = $sourceHomeUrl . "/account/data/elearning/lesson/paragraph/audio/$audio";
            if ($audio) {
              LibFile::curlGetFileContent($filename, $this->elearningLessonParagraphUtils->audioFilePath . $audio);
            }

            $elearningQuestionNodes = $elearningLessonParagraphNode->getElementsByTagName(ELEARNING_XML_QUESTION);
          }
        }
      }
    }

    return($lastInsertElearningLessonId);
  }

  // Get a lesson exposed by a REST web service
  function getLessonDetailsREST($contentImportId, $xmlResponse) {
    $lesson = array();

    if (!strstr($xmlResponse, ELEARNING_XML)) {
      return(false);
    }

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->loadXML($xmlResponse);
    $children = $xmlDocument->getElementsByTagName(ELEARNING_XML);
    $xmlRootNode = $children->item(0);
    $sourceHomeUrl = $xmlRootNode->getAttribute("homeUrl");

    $elearningLessonNodes = $xmlRootNode->getElementsByTagName(ELEARNING_XML_LESSON);
    foreach ($elearningLessonNodes as $elearningLessonNode) {
      $nodeName = $elearningLessonNode->tagName;
      if ($nodeName == ELEARNING_XML_LESSON) {
        $name = $elearningLessonNode->getAttribute("name");
        $description = $elearningLessonNode->getAttribute("description");
        $image = $elearningLessonNode->getAttribute("image");
        $audio = $elearningLessonNode->getAttribute("audio");

        $lessonParagraphs = array();
        $elearningLessonParagraphNodes = $elearningLessonNode->getElementsByTagName(ELEARNING_XML_LESSON_PARAGRAPH);
        foreach ($elearningLessonParagraphNodes as $elearningLessonParagraphNode) {
          $nodeName = $elearningLessonParagraphNode->tagName;
          if ($nodeName == ELEARNING_XML_EXERCISE_PAGE) {
            $headline = $elearningLessonParagraphNode->getAttribute("headline");
            $body = $elearningLessonParagraphNode->getAttribute("body");

            array_push($lessonParagraphs, array($headline, $body));
          }
        }

        $lesson = array($name, $description, $image, $audio, $lessonParagraphs);
      }
    }

    return($lesson);
  }

}

?>
