<?php

class ElearningLessonDao extends Dao {

  var $tableName;

  function __construct($dataSource, $tableName) {
    parent::__construct($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(100) not null,
unique(name),
description text,
instructions text,
image varchar(255),
audio varchar(255),
introduction text,
secured boolean not null,
public_access boolean not null,
release_date datetime not null,
garbage boolean not null,
locked boolean not null,
lesson_model_id int unsigned,
index (lesson_model_id), foreign key (lesson_model_id) references elearning_lesson_model(id),
category_id int unsigned,
index (category_id), foreign key (category_id) references elearning_category(id),
level_id int unsigned,
index (level_id), foreign key (level_id) references elearning_level(id),
subject_id int unsigned,
index (subject_id), foreign key (subject_id) references elearning_subject(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $instructions, $image, $audio, $introduction, $secured, $publicAccess, $releaseDate, $garbage, $locked, $lessonModelId, $categoryId, $levelId, $subjectId) {
    $lessonModelId = LibString::emptyToNULL($lessonModelId);
    $categoryId = LibString::emptyToNULL($categoryId);
    $levelId = LibString::emptyToNULL($levelId);
    $subjectId = LibString::emptyToNULL($subjectId);
    $releaseDate = LibString::emptyToNULL($releaseDate);
    $releaseDate = LibString::addSingleQuotesIfNotNULL($releaseDate);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$instructions', '$image', '$audio', '$introduction', '$secured', '$publicAccess', $releaseDate, '$garbage', '$locked', $lessonModelId, $categoryId, $levelId, $subjectId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $instructions, $image, $audio, $introduction, $secured, $publicAccess, $releaseDate, $garbage, $locked, $lessonModelId, $categoryId, $levelId, $subjectId) {
    $lessonModelId = LibString::emptyToNULL($lessonModelId);
    $categoryId = LibString::emptyToNULL($categoryId);
    $levelId = LibString::emptyToNULL($levelId);
    $subjectId = LibString::emptyToNULL($subjectId);
    $releaseDate = LibString::emptyToNULL($releaseDate);
    $releaseDate = LibString::addSingleQuotesIfNotNULL($releaseDate);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', instructions = '$instructions', image = '$image', audio = '$audio', introduction = '$introduction', secured = '$secured', public_access = '$publicAccess', release_date = $releaseDate, garbage = '$garbage', locked = '$locked', lesson_model_id = $lessonModelId, category_id = $categoryId, level_id = $levelId, subject_id = $subjectId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY name";
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

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
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

  function selectByLessonModelId($lessonModelId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE lesson_model_id = '$lessonModelId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByCourseIdAndReleaseDate($elearningCourseId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS el.* FROM $this->tableName el LEFT JOIN " . DB_TABLE_ELEARNING_COURSE_ITEM . " eci ON el.id = eci.elearning_lesson_id WHERE el.garbage != '1' AND eci.elearning_course_id = '$elearningCourseId' AND (('$sinceDate' >= '$systemDate' AND el.release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND el.release_date <= '$systemDate' AND el.release_date >= '$sinceDate')) ORDER BY eci.list_order, el.name";
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

  function selectByCourseId($elearningCourseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS el.* FROM $this->tableName el LEFT JOIN " . DB_TABLE_ELEARNING_COURSE_ITEM . " eci ON el.id = eci.elearning_lesson_id WHERE el.garbage != '1' AND eci.elearning_course_id = '$elearningCourseId' ORDER BY eci.list_order, el.name";
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
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (lower(name) LIKE lower('%$searchPattern%') OR id = '$searchPattern') ORDER BY name";
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

  function selectLikePatternInLessonAndCourse($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS DISTINCT el.* FROM $this->tableName el LEFT JOIN " . DB_TABLE_ELEARNING_COURSE_ITEM . " eci ON (el.id = eci.elearning_lesson_id) LEFT JOIN " . DB_TABLE_ELEARNING_COURSE . " ec ON (eci.elearning_course_id = ec.id) WHERE el.garbage != '1' AND (lower(el.name) LIKE lower('%$searchPattern%') OR lower(ec.name) LIKE lower('%$searchPattern%')) ORDER BY el.name";
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

  function selectByReleaseDate($sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectByCategoryIdAndReleaseDate($categoryId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectByCategoryId($categoryId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) ORDER BY name";
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

  function selectByLevelIdAndReleaseDate($levelId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (level_id = '$levelId' OR (level_id IS NULL AND '$levelId' < '1')) AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectByLevelId($levelId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (level_id = '$levelId' OR (level_id IS NULL AND '$levelId' < '1')) ORDER BY name";
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

  function selectNonGarbage($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' ORDER BY name";
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

  function selectGarbage($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage = '1' ORDER BY name";
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

  function selectBySubjectIdAndReleaseDate($subjectId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (subject_id = '$subjectId' OR (subject_id IS NULL AND '$subjectId' < '1')) AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectBySubjectId($subjectId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (subject_id = '$subjectId' OR (subject_id IS NULL AND '$subjectId' < '1')) ORDER BY name";
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

  function selectByCategoryIdAndLevelIdAndSubjectId($categoryId, $levelId, $subjectId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND (level_id = '$levelId' OR (level_id IS NULL AND '$levelId' < '1')) AND (subject_id = '$subjectId' OR (subject_id IS NULL AND '$subjectId' < '1')) ORDER BY name";
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

  function selectByCategoryIdAndLevelIdAndSubjectIdAndReleaseDate($categoryId, $levelId, $subjectId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND (level_id = '$levelId' OR (level_id IS NULL AND '$levelId' < '1')) AND (subject_id = '$subjectId' OR (subject_id IS NULL AND '$subjectId' < '1')) AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectByCategoryIdAndLevelIdAndReleaseDate($categoryId, $levelId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND (level_id = '$levelId' OR (level_id IS NULL AND '$levelId' < '1')) AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectByCategoryIdAndLevelId($categoryId, $levelId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND (level_id = '$levelId' OR (level_id IS NULL AND '$levelId' < '1')) ORDER BY name";
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

  function selectByCategoryIdAndSubjectIdAndReleaseDate($categoryId, $subjectId, $sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND (subject_id = '$subjectId' OR (subject_id IS NULL AND '$subjectId' < '1')) AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectByCategoryIdAndSubjectId($categoryId, $subjectId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (category_id = '$categoryId' OR (category_id IS NULL AND '$categoryId' < '1')) AND (subject_id = '$subjectId' OR (subject_id IS NULL AND '$subjectId' < '1')) ORDER BY name";
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

  function selectByLevelIdAndSubjectIdAndReleaseDate($levelId, $subjectId, $sinceDate, $systemDate, $listIndex, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (level_id = '$levelId' OR (level_id IS NULL AND '$levelId' < '1')) AND (subject_id = '$subjectId' OR (subject_id IS NULL AND '$subjectId' < '1')) AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectByLevelIdAndSubjectId($levelId, $subjectId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND (level_id = '$levelId' OR (level_id IS NULL AND '$levelId' < '1')) AND (subject_id = '$subjectId' OR (subject_id IS NULL AND '$subjectId' < '1')) ORDER BY name";
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

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function selectIntroductionLikeImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE introduction LIKE '%$image%'";
    return($this->querySelect($sqlStatement));
  }

  function selectByAudio($audio) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE audio = '$audio'";
    return($this->querySelect($sqlStatement));
  }

  function selectPublicAccessAndReleaseDate($sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND public_access = '1' AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectPublicAccess($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND public_access = '1' ORDER BY name";
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

  function selectProtectedAndReleaseDate($sinceDate, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND public_access != '1' AND (('$sinceDate' >= '$systemDate' AND release_date > '$sinceDate') OR ('$sinceDate' < '$systemDate' AND release_date <= '$systemDate' AND release_date >= '$sinceDate')) ORDER BY name";
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

  function selectProtected($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE garbage != '1' AND public_access != '1' ORDER BY name";
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

}

?>
