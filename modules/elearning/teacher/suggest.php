<?PHP

require_once("website.php");

LibHtml::preventCaching();

$typedInString = LibEnv::getEnvHttpGET("term");

if (!$typedInString) {
  return;
}

$typedInString = utf8_decode($typedInString);

// The name is stored in the database in a html encoded format
$typedInString = LibString::cleanString($typedInString);

$responseText = '[';

if ($elearningTeachers = $elearningTeacherUtils->selectLikePattern($typedInString)) {
  foreach ($elearningTeachers as $elearningTeacher) {
    $elearningTeacherId = $elearningTeacher->getId();
    if ($elearningTeachers = $elearningTeacherUtils->selectLikePattern($typedInString)) {
      $userId = $elearningTeacher->getUserId();
      if ($user = $userUtils->selectById($userId)) {
        $userName = $user->getFirstname() . ' ' . $user->getLastname();
        $userName = LibString::decodeHtmlspecialchars($userName);
        $userName = LibString::escapeDoubleQuotes($userName);
        $responseText .= " {\"id\": \"$elearningTeacherId\", \"label\": \"$userName\", \"value\": \"$userName\"},";
      }
    }
  }
}

$responseText .= ']';
$responseText = str_replace(',]', ']', $responseText);

print($responseText);

?>
