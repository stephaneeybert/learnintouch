<?PHP

require_once("website.php");

$adminUtils->checkForStaffLogin();

if ($elearningCourses = $elearningCourseUtils->selectAll()) {
  foreach ($elearningCourses as $elearningCourse) {
    $elearningCourseItemUtils->resetListOrder($elearningCourse->getId());
  }
}

if ($elearningExercises = $elearningExerciseUtils->selectAll()) {
  foreach ($elearningExercises as $elearningExercise) {
    $elearningExercisePageUtils->resetListOrder($elearningExercise->getId());
  }
}

if ($elearningExercisePages = $elearningExercisePageUtils->selectAll()) {
  foreach ($elearningExercisePages as $elearningExercisePage) {
    $elearningQuestionUtils->resetListOrder($elearningExercisePage->getId());
  }
}

if ($elearningQuestions = $elearningQuestionUtils->selectAll()) {
  foreach ($elearningQuestions as $elearningQuestion) {
    $elearningAnswerUtils->resetListOrder($elearningQuestion->getId());
  }
}

print("<br>Done!");

?>
