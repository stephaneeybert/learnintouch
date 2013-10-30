<?php

class TemplateContainerDao extends Dao {

  var $tableName;

  function TemplateContainerDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
template_model_id int unsigned not null,
index (template_model_id), foreign key (template_model_id) references template_model(id),
row_nb int unsigned not null,
cell_nb int unsigned not null,
template_property_set_id int unsigned not null,
index (template_property_set_id), foreign key (template_property_set_id) references template_property_set(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($templateModelId, $row, $cell, $templatePropertySetId) {
    $templatePropertySetId = LibString::emptyToNULL($templatePropertySetId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$templateModelId', '$row', '$cell', $templatePropertySetId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $templateModelId, $row, $cell, $templatePropertySetId) {
    $templatePropertySetId = LibString::emptyToNULL($templatePropertySetId);
    $sqlStatement = "UPDATE $this->tableName SET template_model_id = '$templateModelId', row_nb = '$row', cell_nb = '$cell', template_property_set_id = $templatePropertySetId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByTemplateModelId($templateModelId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_model_id = '$templateModelId' ORDER BY row_nb, cell_nb";
    return($this->querySelect($sqlStatement));
  }

  function selectNbCellsByRow($templateModelId) {
    $sqlStatement = "SELECT row_nb rowNb, (MAX(cell_nb)+1) nbCells FROM $this->tableName WHERE template_model_id = '$templateModelId' GROUP BY row_nb";
    return($this->querySelect($sqlStatement));
  }

  function selectByModelIdAndRow($templateModelId, $row) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_model_id = '$templateModelId' AND row_nb = '$row' ORDER BY cell_nb";
    return($this->querySelect($sqlStatement));
  }

  function selectByModelIdAndRowAndCell($templateModelId, $row, $cell) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_model_id = '$templateModelId' AND row_nb = '$row' AND cell_nb = '$cell' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextCell($templateModelId, $row, $cell) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_model_id = '$templateModelId' AND row_nb = '$row' AND cell_nb > '$cell' ORDER BY cell_nb LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousCell($templateModelId, $row, $cell) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE template_model_id = '$templateModelId' AND row_nb = '$row' AND cell_nb < '$cell' ORDER BY cell_nb DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

}

?>
