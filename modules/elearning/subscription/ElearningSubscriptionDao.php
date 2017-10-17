<?php

class ElearningSubscriptionDao extends Dao {

  var $tableName;

  function ElearningSubscriptionDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
user_account_id int unsigned not null,
index (user_account_id), foreign key (user_account_id) references user(id),
teacher_id int unsigned,
index (teacher_id), foreign key (teacher_id) references elearning_teacher(id),
session_id int unsigned,
index (session_id), foreign key (session_id) references elearning_session(id),
course_id int unsigned,
index (course_id), foreign key (course_id) references elearning_course(id),
class_id int unsigned,
index (class_id), foreign key (class_id) references elearning_class(id),
subscription_date datetime,
subscription_close datetime,
watch_live boolean not null,
last_exercise_id int unsigned,
last_exercise_page_id int unsigned,
last_active datetime,
whiteboard text,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($userId, $teacherId, $sessionId, $courseId, $classId, $subscriptionDate, $subscriptionClose, $watchLive, $lastExerciseId, $lastExercisePageId, $lastActive, $whiteboard) {
    $sessionId = LibString::emptyToNULL($sessionId);
    $courseId = LibString::emptyToNULL($courseId);
    $teacherId = LibString::emptyToNULL($teacherId);
    $classId = LibString::emptyToNULL($classId);
    $subscriptionClose = LibString::emptyToNULL($subscriptionClose);
    $subscriptionClose = LibString::addSingleQuotesIfNotNULL($subscriptionClose);
    $lastExerciseId = LibString::emptyToNULL($lastExerciseId);
    $lastExercisePageId = LibString::emptyToNULL($lastExercisePageId);
    $lastActive = LibString::emptyToNULL($lastActive);
    $lastActive = LibString::addSingleQuotesIfNotNULL($lastActive);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$userId', $teacherId, $sessionId, $courseId, $classId, '$subscriptionDate', $subscriptionClose, '$watchLive', '$lastExerciseId', '$lastExercisePageId', $lastActive, '$whiteboard')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $userId, $teacherId, $sessionId, $courseId, $classId, $subscriptionDate, $subscriptionClose, $watchLive, $lastExerciseId, $lastExercisePageId, $lastActive, $whiteboard) {
    $sessionId = LibString::emptyToNULL($sessionId);
    $courseId = LibString::emptyToNULL($courseId);
    $teacherId = LibString::emptyToNULL($teacherId);
    $classId = LibString::emptyToNULL($classId);
    $subscriptionClose = LibString::emptyToNULL($subscriptionClose);
    $subscriptionClose = LibString::addSingleQuotesIfNotNULL($subscriptionClose);
    $lastExerciseId = LibString::emptyToNULL($lastExerciseId);
    $lastExercisePageId = LibString::emptyToNULL($lastExercisePageId);
    $lastActive = LibString::emptyToNULL($lastActive);
    $lastActive = LibString::addSingleQuotesIfNotNULL($lastActive);
    $sqlStatement = "UPDATE $this->tableName SET user_account_id = '$userId', teacher_id = $teacherId, session_id = $sessionId, course_id = $courseId, class_id = $classId, subscription_date = '$subscriptionDate', subscription_close = $subscriptionClose, watch_live = '$watchLive', last_exercise_id = '$lastExerciseId', last_exercise_page_id = '$lastExercisePageId', last_active = $lastActive, whiteboard = '$whiteboard' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  // Count the number of rows of the last select statement
  // ignoring the LIMIT keyword if any
  // The SQL_CALC_FOUND_ROWS clause tells MySQL to calculate how many rows there would be
  // in the result set, disregarding any LIMIT clause with the number of rows later
  // retrieved using the SELECT FOUND_ROWS() statement
  function countFoundRows() {
    $sqlStatement = "SELECT FOUND_ROWS() as count";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByTeacherId($teacherId) {
    $sqlStatement = "SELECT s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND (s.teacher_id = '$teacherId' OR (s.teacher_id IS NULL AND '$teacherId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionId($sessionId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.session_id = '$sessionId' ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByNoSessionId($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.session_id IS NULL ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByUserIdAndCourseId($userId, $courseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId' AND course_id = '$courseId' ORDER BY subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByUserIdAndCourseIdAndSessionId($userId, $courseId, $sessionId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId' AND course_id = '$courseId' AND session_id = '$sessionId' ORDER BY subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByCourseId($courseId) {
    $sqlStatement = "SELECT s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.course_id = '$courseId' ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByClassId($classId) {
    $sqlStatement = "SELECT s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND (s.class_id = '$classId' OR (s.class_id IS NULL AND '$classId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionIdAndCourseId($sessionId, $courseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionIdAndCourseIdAndClassId($sessionId, $courseId, $classId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' AND (s.class_id = '$classId' OR (s.class_id IS NULL AND '$classId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByUserId($userId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId' ORDER BY subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByUserIdAndTeacherId($userId, $teacherId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId' AND teacher_id = '$teacherId' ORDER BY subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByUserIdAndSubscriptionId($userId, $elearningSubscriptionId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE user_account_id = '$userId' AND id = '$elearningSubscriptionId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionIdAndCourseAndTeacherId($sessionId, $courseId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' AND (s.teacher_id = '$teacherId' OR (s.teacher_id IS NULL AND '$teacherId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByCourseIdAndTeacherId($courseId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.course_id = '$courseId' AND (s.teacher_id = '$teacherId' OR (s.teacher_id IS NULL AND '$teacherId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionIdAndCourseAndClassIdAndTeacherId($sessionId, $courseId, $classId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' AND (s.class_id = '$classId' OR (s.class_id IS NULL AND '$classId' < '1')) AND (s.teacher_id = '$teacherId' OR (s.teacher_id IS NULL AND '$teacherId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByCourseIdAndClassIdAndTeacherId($courseId, $classId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.course_id = '$courseId' AND (s.class_id = '$classId' OR (s.class_id IS NULL AND '$classId' < '1')) AND (s.teacher_id = '$teacherId' OR (s.teacher_id IS NULL AND '$teacherId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionIdAndTeacherId($sessionId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.session_id = '$sessionId' AND (s.teacher_id = '$teacherId' OR (s.teacher_id IS NULL AND '$teacherId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionIdAndClassIdAndTeacherId($sessionId, $classId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND s.session_id = '$sessionId' AND (s.class_id = '$classId' OR (s.class_id IS NULL AND '$classId' < '1')) AND (s.teacher_id = '$teacherId' OR (s.teacher_id IS NULL AND '$teacherId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByClassIdAndTeacherId($classId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND (s.class_id = '$classId' OR (s.class_id IS NULL AND '$classId' < '1')) AND (s.teacher_id = '$teacherId' OR (s.teacher_id IS NULL AND '$teacherId' < '1')) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionIdAndClassId($sessionId, $classId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s WHERE s.session_id = '$sessionId' AND (s.class_id = '$classId' OR (s.class_id IS NULL AND '$classId' < '1')) ORDER BY s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $OR_CLAUSE = "";
    if (strstr($searchPattern, ' ')) {
      $bits = explode(' ', $searchPattern);
      foreach ($bits as $bit) {
        if (strlen($bit) > 1) {
          if ($OR_CLAUSE) {
            $OR_CLAUSE .= "OR ";
          }
          $OR_CLAUSE .= "lower(u.email) LIKE lower('%$bit%') OR lower(u.firstname) LIKE lower('%$bit%') OR lower(u.lastname) LIKE lower('%$bit%')";
        }
      }
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND ($OR_CLAUSE) ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectLikePatternDistinctUsers($searchPattern) {
    $OR_CLAUSE = "";
    if (strstr($searchPattern, ' ')) {
      $bits = explode(' ', $searchPattern);
      foreach ($bits as $bit) {
        if (strlen($bit) > 1) {
          if ($OR_CLAUSE) {
            $OR_CLAUSE .= "OR ";
          }
          $OR_CLAUSE .= "lower(u.email) LIKE lower('%$bit%') OR lower(u.firstname) LIKE lower('%$bit%') OR lower(u.lastname) LIKE lower('%$bit%')";
        }
      }
    }
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS s.* FROM $this->tableName s, " . DB_TABLE_USER . " u WHERE u.id = s.user_account_id AND ($OR_CLAUSE) GROUP BY u.id ORDER BY u.firstname, u.lastname, u.email, s.subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectUserSubscriptions($userId) {
    $sqlStatement = "SELECT su.* FROM $this->tableName su LEFT JOIN " . DB_TABLE_ELEARNING_SESSION . " se ON su.session_id = se.id WHERE su.user_account_id = '$userId' ORDER BY se.opening_date DESC, su.subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectOpenedUserSubscriptionsWithCourse($userId, $systemDate) {
    $sqlStatement = "SELECT su.* FROM $this->tableName su LEFT JOIN " . DB_TABLE_ELEARNING_SESSION . " se ON su.session_id = se.id WHERE su.course_id IS NOT NULL AND su.user_account_id = '$userId' AND ((se.closed != '1' AND DATE(se.opening_date) <= '$systemDate' AND (se.closing_date IS NULL OR (se.closing_date IS NOT NULL AND DATE(se.closing_date) >= '$systemDate')) AND DATE(su.subscription_date) <= '$systemDate' AND (su.subscription_close IS NULL OR DATE(su.subscription_close) >= '$systemDate')) OR su.session_id IS NULL) ORDER BY se.opening_date DESC, su.subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectOpenedUserSubscriptions($userId, $systemDate) {
    $sqlStatement = "SELECT su.* FROM $this->tableName su LEFT JOIN " . DB_TABLE_ELEARNING_SESSION . " se ON su.session_id = se.id WHERE su.user_account_id = '$userId' AND ((su.session_id = se.id AND se.closed != '1' AND DATE(se.opening_date) <= '$systemDate' AND (se.closing_date IS NULL OR (se.closing_date IS NOT NULL AND DATE(se.closing_date) >= '$systemDate')) AND DATE(su.subscription_date) <= '$systemDate' AND (su.subscription_close IS NULL OR DATE(su.subscription_close) >= '$systemDate')) OR su.session_id IS NULL) ORDER BY se.opening_date DESC, su.subscription_date DESC";
    return($this->querySelect($sqlStatement));
  }

  function countOpenedSubscriptions($systemDate) {
    $sqlStatement = "SELECT count(su.id) as count FROM $this->tableName su LEFT JOIN " . DB_TABLE_ELEARNING_SESSION . " se ON su.session_id = se.id WHERE (se.closed != '1' AND DATE(se.opening_date) <= '$systemDate' AND (se.closing_date IS NULL OR (se.closing_date IS NOT NULL AND DATE(se.closing_date) >= '$systemDate')) AND DATE(su.subscription_date) <= '$systemDate' AND (su.subscription_close IS NULL OR DATE(su.subscription_close) >= '$systemDate')) OR su.session_id IS NULL";
    return($this->querySelect($sqlStatement));
  }

}

?>
