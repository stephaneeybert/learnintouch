<?php

class NewsPaperDao extends Dao {

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
title varchar(255) not null,
image varchar(255),
header text,
footer text,
release_date datetime,
archive_date datetime,
not_published boolean not null,
news_publication_id int unsigned,
index (news_publication_id), foreign key (news_publication_id) references news_publication(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($title, $image, $header, $footer, $releaseDate, $archive, $notPublished, $newsPublicationId) {
    $newsPublicationId = LibString::emptyToNULL($newsPublicationId);
    $releaseDate = LibString::emptyToNULL($releaseDate);
    $releaseDate = LibString::addSingleQuotesIfNotNULL($releaseDate);
    $archive = LibString::emptyToNULL($archive);
    $archive = LibString::addSingleQuotesIfNotNULL($archive);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$title', '$image', '$header', '$footer', $releaseDate, $archive, '$notPublished', $newsPublicationId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $title, $image, $header, $footer, $releaseDate, $archive, $notPublished, $newsPublicationId) {
    $newsPublicationId = LibString::emptyToNULL($newsPublicationId);
    $releaseDate = LibString::emptyToNULL($releaseDate);
    $releaseDate = LibString::addSingleQuotesIfNotNULL($releaseDate);
    $archive = LibString::emptyToNULL($archive);
    $archive = LibString::addSingleQuotesIfNotNULL($archive);
    $sqlStatement = "UPDATE $this->tableName SET title = '$title', image = '$image', header = '$header', footer = '$footer', release_date = $releaseDate, archive_date = $archive, not_published = '$notPublished', news_publication_id = $newsPublicationId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll() {
    $sqlStatement = "SELECT * FROM $this->tableName ORDER BY release_date DESC, title";
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

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByTitle($title) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE title = '$title' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPublicationId($newsPublicationId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1') ORDER BY release_date DESC, title";
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
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(title) LIKE lower('%$searchPattern%') OR lower(header) LIKE lower('%$searchPattern%') OR lower(footer) LIKE lower('%$searchPattern%') ORDER BY release_date DESC, title";
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

  function selectLikePatternAndPublished($searchPattern, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (lower(title) LIKE lower('%$searchPattern%') OR lower(header) LIKE lower('%$searchPattern%') OR lower(footer) LIKE lower('%$searchPattern%')) AND not_published != '1' AND ((release_date IS NOT NULL AND archive_date IS NOT NULL AND DATE(release_date) <= '$systemDate' AND DATE(archive_date) >= '$systemDate') OR (release_date IS NOT NULL AND archive_date IS NULL AND DATE(release_date) <= '$systemDate') OR (release_date IS NULL AND archive_date IS NOT NULL AND DATE(archive_date) >= '$systemDate')) ORDER BY release_date DESC, title";
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

  function selectLikePatternInNewsPaperAndNewsPublication($searchPattern, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS npa.* FROM $this->tableName npa LEFT JOIN " . DB_TABLE_NEWS_PUBLICATION . " npu ON npa.news_publication_id = npu.id WHERE (lower(npa.title) LIKE lower('%$searchPattern%') OR lower(npa.header) LIKE lower('%$searchPattern%') OR lower(npa.footer) LIKE lower('%$searchPattern%') OR lower(npu.name) LIKE lower('%$searchPattern%')) AND npa.not_published != '1' AND ((npa.release_date IS NOT NULL AND npa.archive_date IS NOT NULL AND DATE(npa.release_date) <= '$systemDate' AND DATE(npa.archive_date) >= '$systemDate') OR (npa.release_date IS NOT NULL AND npa.archive_date IS NULL AND DATE(npa.release_date) <= '$systemDate') OR (npa.release_date IS NULL AND npa.archive_date IS NOT NULL AND DATE(npa.archive_date) >= '$systemDate')) ORDER BY npa.release_date DESC, npa.title";
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

  function selectByNewsPublicationAndPublished($newsPublicationId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1')) AND not_published != '1' AND ((release_date IS NOT NULL AND archive_date IS NOT NULL AND DATE(release_date) <= '$systemDate' AND DATE(archive_date) >= '$systemDate') OR (release_date IS NOT NULL AND archive_date IS NULL AND DATE(release_date) <= '$systemDate') OR (release_date IS NULL AND archive_date IS NOT NULL AND DATE(archive_date) >= '$systemDate')) ORDER BY release_date DESC, title";
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

  function selectByPublished($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE not_published != '1' AND ((release_date IS NOT NULL AND archive_date IS NOT NULL AND DATE(release_date) <= '$systemDate' AND DATE(archive_date) >= '$systemDate') OR (release_date IS NOT NULL AND archive_date IS NULL AND DATE(release_date) <= '$systemDate') OR (release_date IS NULL AND archive_date IS NOT NULL AND DATE(archive_date) >= '$systemDate')) ORDER BY release_date DESC, title";
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

  function selectByDeferred($newsPublicationId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1')) AND not_published != '1' AND release_date IS NOT NULL AND release_date > '$systemDate' ORDER BY release_date DESC, title";
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

  function selectByNewsPublicationIdAndPublish($newsPublicationId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1')) AND not_published != '1' ORDER BY release_date DESC, title";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPublicationIdAndNotPublish($newsPublicationId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1')) AND not_published = '1' ORDER BY release_date DESC, title";
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

  function selectByPatternAndNewsPublicationId($searchPattern, $newsPublicationId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (lower(title) LIKE lower('%$searchPattern%') OR lower(header) LIKE lower('%$searchPattern%') OR lower(footer) LIKE lower('%$searchPattern%')) AND (news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1')) ORDER BY release_date DESC, title";
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

  function selectRecentReleases($newsPublicationId, $systemDate) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1')) AND not_published != '1' AND release_date IS NOT NULL AND DATE(release_date) <= '$systemDate' ORDER BY release_date DESC, title LIMIT 50";
    return($this->querySelect($sqlStatement));
  }

  function archiveByReleaseDate($newsPublicationId, $sinceDate, $systemDate) {
    $sqlStatement = "UPDATE $this->tableName SET archive_date = '$systemDate' WHERE (news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1')) AND release_date IS NOT NULL AND archive_date IS NULL AND DATE(release_date) <= '$sinceDate'";
    return($this->querySelect($sqlStatement));
  }

  function selectByArchived($newsPublicationId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1')) AND not_published != '1' AND archive_date IS NOT NULL AND DATE(archive_date) < '$systemDate' ORDER BY release_date DESC, title";
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

  function selectByPublicationAndArchiveDate($newsPublicationId, $sinceDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (news_publication_id = '$newsPublicationId' OR (news_publication_id IS NULL AND '$newsPublicationId' < '1')) AND archive_date IS NOT NULL AND DATE(archive_date) < '$sinceDate' ORDER BY release_date DESC, title";
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
