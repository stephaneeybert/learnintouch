<?PHP

require_once("website.php");

$str = "\n<div class='elearning_teacher_list'>";

$str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

if ($elearningTeachers = $elearningTeacherUtils->selectAll()) {
  foreach ($elearningTeachers as $elearningTeacher) {
    $userId = $elearningTeacher->getUserId();
    $firstname = '';
    $lastname = '';
    $email = '';
    if ($user = $elearningTeacherUtils->userUtils->selectById($userId)) {
      $firstname = $user->getFirstname();
      $lastname = $user->getLastname();
      $email = $elearningTeacherUtils->renderEmail($user->getEmail());
    }
    $str .= "<tr valign='top'>";
    $str .= "<td>";
    $str .= "<div class='elearning_teacher_name'>$firstname $lastname</div>";
    $str .= "</td><td>";
    $str .= "<div class='elearning_teacher_email'>$email</div>";
    $str .= "</td>";
    $str .= "</tr>";
  }
}

$str .= "</table>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);

$elearningTemplateModelId = $elearningExerciseUtils->getTemplateModel();
if ($elearningTemplateModelId > 0) {
  $templateModelId = $elearningTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
