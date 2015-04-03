<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$currentContentImportId = LibEnv::getEnvHttpPOST("currentContentImportId");
$elearningMatterId = LibEnv::getEnvHttpPOST("elearningMatterId");
$elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
$other = LibEnv::getEnvHttpPOST("other");

if (!$currentContentImportId) {
  $currentContentImportId = LibSession::getSessionValue(CONTENT_IMPORT_SESSION_CURRENT);
} else {
  LibSession::putSessionValue(CONTENT_IMPORT_SESSION_CURRENT, $currentContentImportId);
}

if (!$elearningMatterId) {
  $elearningMatterId = LibSession::getSessionValue(ELEARNING_SESSION_MATTER);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_MATTER, $elearningMatterId);
}

if (!$elearningCourseId) {
  $elearningCourseId = LibSession::getSessionValue(ELEARNING_SESSION_COURSE);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_COURSE, $elearningCourseId);
}

if (!$other) {
  $other = LibSession::getSessionValue(ELEARNING_SESSION_IMPORT_OTHER);
} else {
  LibSession::putSessionValue(ELEARNING_SESSION_IMPORT_OTHER, $other);
}

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(CONTENT_IMPORT_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(CONTENT_IMPORT_SESSION_SEARCH_PATTERN, $searchPattern);
}
$searchPattern = LibString::cleanString($searchPattern);

$contentImports = $contentImportUtils->selectExporting();
$domainNameList = Array('-1' => '');
foreach ($contentImports as $contentImport) {
  $contentImportId = $contentImport->getId();
  $domainName = $contentImport->getDomainName();
  $domainNameList[$contentImportId] = $domainName;
}
$strDomainName = LibHtml::getSelectList("currentContentImportId", $domainNameList, $currentContentImportId, true);
$labelDomain = $popupUtils->getTipPopup($mlText[5], $mlText[9], 300, 300);
$strDomainName = "<form action='$PHP_SELF' method='post'>" . "<b>$labelDomain</b> " . $strDomainName . "</form>";

$listNbItems = 0;
$allImportable = '';
$strImportMessage = '';
$strImportOffer = '';
$courses = array();
$courseItems = array();
$exercises = array();
$lessons = array();
$matters = array();
if ($currentContentImportId > 0) {
  $xmlResponse = $elearningImportUtils->exposeCourseListXML($currentContentImportId);
  if ($contentImportUtils->importerIsDenied($xmlResponse)) {
    $strImportMessage = $contentImportUtils->renderImportDeniedWarning($currentContentImportId);
  } else if ($contentImportUtils->importerIsPending($xmlResponse, $currentContentImportId)) {
    $strImportMessage = $contentImportUtils->renderImportPendingWarning($currentContentImportId);
  } else if ($contentImportUtils->importerHasAnUnknownPermissionKey($xmlResponse)) {
    $strImportMessage = $contentImportUtils->renderImportUnknownPermissionKey($currentContentImportId);
  } else if ($contentImportUtils->importerIsUnknown($xmlResponse)) {
    $strImportMessage = $contentImportUtils->renderImportUnknownWarning($currentContentImportId);
    $strImportOffer = $contentImportUtils->renderImportOffer($currentContentImportId);
  } else {
    $allImportable = $elearningImportUtils->getAllImportable($xmlResponse);
    $matters = $elearningImportUtils->getMatterListREST($xmlResponse);
    if (strlen($searchPattern) > 3) {
      $xmlResponseSearchedContent = $elearningImportUtils->exposeSearchedContentAsXML($currentContentImportId, $searchPattern, false);
      $courses = $elearningImportUtils->getCourseListREST($xmlResponseSearchedContent);
      $lessons = $elearningImportUtils->getLessonListREST($xmlResponseSearchedContent);
      $exercises = $elearningImportUtils->getExerciseListREST($xmlResponseSearchedContent);
    } else if ($elearningMatterId > 0) {
      $courses = $elearningImportUtils->getCourseListREST($xmlResponse, $elearningMatterId);
    } else if ($other == ELEARNING_IMPORT_OTHER_EXERCISE) {
      $xmlResponseOther = $elearningImportUtils->exposeAllExercisesAsXML($currentContentImportId, false, $listIndex, $listStep);
      $exercises = $elearningImportUtils->getExerciseListREST($xmlResponseOther);
      $listNbItems = $elearningImportUtils->getListNbItemsREST($xmlResponseOther);
    } else if ($other == ELEARNING_IMPORT_OTHER_LESSON) {
      $xmlResponseOther = $elearningImportUtils->exposeAllLessonsAsXML($currentContentImportId, false, $listIndex, $listStep);
      $lessons = $elearningImportUtils->getLessonListREST($xmlResponseOther);
      $listNbItems = $elearningImportUtils->getListNbItemsREST($xmlResponseOther);
    }
    if (count($courses) > 0 && $elearningCourseId > 0) {
      $xmlResponseCourse = $elearningImportUtils->exposeCourseAsXML($currentContentImportId, $elearningCourseId, false);
      $courseContent = $elearningImportUtils->getCourseContentREST($currentContentImportId, $xmlResponseCourse);
      if ($courseContent) {
        list($unusedElearningCourseId, $name, $description, $image, $courseItems) = $courseContent;
      }
    }
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/admin.php");
$help = $popupUtils->getHelpPopup($mlText[10], 300, 200);
$panelUtils->setHelp($help);

// Save the current url as the parent url of child pages for an easier navigation
LibSession::putSessionValue(UTILS_SESSION_PARENT_URL, "$gElearningUrl/import/admin.php");

$strCommand = "<a href='$gContentImportUrl/exporters/admin.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[3]'></a>"
. " <a href='$gContentImportUrl/importers/admin.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageExport' title='$mlText[6]'></a>";

if ($currentContentImportId > 0 && $allImportable) {
  $label = $popupUtils->getTipPopup($mlText[14], $mlText[15], 300, 300);
  $strSearch = "<form action='$PHP_SELF' method='post'>"
  . "<div style='white-space:nowrap;'><b>$label</b>"
  . "<input type='text' id='searchPattern' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> " . $panelUtils->getTinyOk() . "</div>"
  . $commonUtils->ajaxAutocomplete("$gElearningUrl/import/suggest_search.php?contentImportId=$currentContentImportId", "searchPattern", "searchPattern")
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . "<input type='hidden' name='elearningMatterId' value='-1'> "
  . "<input type='hidden' name='elearningCourseId' value='-1'> "
  . "<input type='hidden' name='allExercises' value='-1'> "
  . "</form>";
} else {
  $strSearch = '';
}

$panelUtils->addLine($panelUtils->addCell($strDomainName, "n"), $strSearch, $panelUtils->addCell($strCommand, "nr"));

$panelUtils->openForm($PHP_SELF, "edit");
if (count($matters) > 0 && !$searchPattern) {
  $matterList = Array('-1' => '');
  foreach ($matters as $matter) {
    list($elearningMatterId, $name, $description) = $matter;
    $matterList[$elearningMatterId] = $name;
  }
  $labelMatter = $popupUtils->getTipPopup($mlText[7], $mlText[16], 300, 300);
  $strMatter = "<b>$labelMatter</b> " . LibHtml::getSelectList("elearningMatterId", $matterList, $elearningMatterId, true);

  $otherList = array(
    '0' => '',
    ELEARNING_IMPORT_OTHER_EXERCISE => $mlText[18],
    ELEARNING_IMPORT_OTHER_LESSON => $mlText[19],
  );
  $labelOther = $popupUtils->getTipPopup($mlText[21], $mlText[22], 300, 300);
  $strOther = "<b>$labelOther</b> " . LibHtml::getSelectList("other", $otherList, $other, true);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($strMatter, "n"), $panelUtils->addCell($strOther, "nl"), '');
}

if (count($courses) > 0 && !$searchPattern) {
  $courseList = Array('-1' => '');
  foreach ($courses as $course) {
    list($elearningCourseId, $name, $description) = $course;
    $courseList[$elearningCourseId] = $name;
  }
  $labelCourse = $popupUtils->getTipPopup($mlText[13], $mlText[17], 300, 300);
  $strCourse = "<b>$labelCourse</b> " . LibHtml::getSelectList("elearningCourseId", $courseList, $elearningCourseId, true);

  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($strCourse, "n"));
}
$panelUtils->closeForm();

$listStep = $preferenceUtils->getValue("ELEARNING_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
    $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if (count($contentImports) == 0) {
  $panelUtils->addLine($panelUtils->addCell($mlText[8], "wn"));
} else if ($currentContentImportId == -1) {
  $panelUtils->addLine($panelUtils->addCell($mlText[9], "wn"));
} else if ($strImportMessage) {
  $panelUtils->addLine($panelUtils->addCell($strImportMessage, "wn"));
  $panelUtils->addLine($panelUtils->addCell($strImportOffer, "nb"));
} else if ($searchPattern && strlen($searchPattern) <= 3) {
  $panelUtils->addLine($panelUtils->addCell($mlText[20], "wn"));
}

$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

if ($elearningCourseId > 0 && is_array($courseItems)) {
  $panelUtils->addLine($panelUtils->addCell($mlText[11], "nb"), $panelUtils->addCell($mlText[2], "nb"), '');
  $panelUtils->addLine();
  $panelUtils->openList();
  foreach ($courseItems as $courseItem) {
    list($type, $id, $name, $description, $image) = $courseItem;
    if ($type == ELEARNING_XML_EXERCISE && $id && $name) {
      $url = "$gElearningUrl/import/importExercise.php?contentImportId=$currentContentImportId&elearningExerciseId=$id";

      $strCommand = "<a href='$url' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[12]'></a>";

      $strName = "<img border='0' src='$gCommonImagesUrl/$gImageExercise' title='$mlText[18]' style='vertical-align:middle;'> " . $name;
      $panelUtils->addLine($strName, $description, $panelUtils->addCell($strCommand, "nr"));
    } else if ($type == ELEARNING_XML_LESSON && $id && $name) {
      $url = "$gElearningUrl/import/importLesson.php?contentImportId=$currentContentImportId&elearningLessonId=$id";

      $strCommand = "<a href='$url' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[12]'></a>";

      $strName = "<img border='0' src='$gCommonImagesUrl/$gImageLesson' title='$mlText[19]' style='vertical-align:middle;'> " . $name;
      $panelUtils->addLine($strName, $description, $panelUtils->addCell($strCommand, "nr"));
    }
  }
  $panelUtils->closeList();
} else {
  if (count($courses) > 0) {
    $panelUtils->addLine($panelUtils->addCell($mlText[1], "nb"), $panelUtils->addCell($mlText[2], "nb"), '');
    $panelUtils->addLine();
    $panelUtils->openList();
    foreach ($courses as $course) {
      list($elearningCourseId, $name, $description) = $course;
      if ($elearningCourseId && $name) {
        $url = "$gElearningUrl/import/importCourse.php?contentImportId=$currentContentImportId&elearningCourseId=$elearningCourseId";

        $strCommand = "<a href='$url' $gJSNoStatus>"
          . "<img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[4]'></a>";

        $panelUtils->addLine($name, $description, $panelUtils->addCell($strCommand, "nr"));
      }
    }
    $panelUtils->closeList();
  }
  if (count($lessons) > 0) {
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[19], "nb"), $panelUtils->addCell($mlText[2], "nb"), '');
    $panelUtils->addLine();
    $panelUtils->openList();
    foreach ($lessons as $lesson) {
      list($elearningLessonId, $name, $description) = $lesson;
      $url = "$gElearningUrl/import/importLesson.php?contentImportId=$currentContentImportId&elearningLessonId=$elearningLessonId";
      $strCommand = "<a href='$url' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[12]'></a>";

      $strName = "<img border='0' src='$gCommonImagesUrl/$gImageLesson' title='$mlText[19]' style='vertical-align:middle;'> " . $name;
      $panelUtils->addLine($strName, $description, $panelUtils->addCell($strCommand, "nr"));
    }
    $panelUtils->closeList();
  }
  if (count($exercises) > 0) {
    $panelUtils->addLine();
    $panelUtils->addLine($panelUtils->addCell($mlText[18], "nb"), $panelUtils->addCell($mlText[2], "nb"), '');
    $panelUtils->addLine();
    $panelUtils->openList();
    foreach ($exercises as $exercise) {
      list($elearningExerciseId, $name, $description) = $exercise;
      $url = "$gElearningUrl/import/importExercise.php?contentImportId=$currentContentImportId&elearningExerciseId=$elearningExerciseId";
      $strCommand = "<a href='$url' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageImport' title='$mlText[12]'></a>";

      $strName = "<img border='0' src='$gCommonImagesUrl/$gImageExercise' title='$mlText[18]' style='vertical-align:middle;'> " . $name;
      $panelUtils->addLine($strName, $description, $panelUtils->addCell($strCommand, "nr"));
    }
    $panelUtils->closeList();
  }
}

$str = $panelUtils->render();

printAdminPage($str);

?>
