<?php
use CRM_Osepireports_ExtensionUtil as E;

class CRM_Osepireports_Form_Report_OsepiTags extends CRM_Report_Form {
  public function __construct() {
    $this->_columns = [
      'osepi_tags' => [
        'fields' => $this->getReportFields(),
        'filters' => $this->getReportFilters(),
      ],
    ];

    parent::__construct();
  }

  public function preProcess() {
    $this->assign('reportTitle', 'OSEPI Tags');
    parent::preProcess();
  }

  public function from() {
    $this->_from = '
      FROM
        civicrm_tag t
    ';
  }

  public function where() {
    $this->_where = '
      WHERE
        t.parent_id IS NULL
    ';
  }

  public function orderBy() {
    $this->_orderBy = '
      ORDER BY
        t.name
    ';
  }

  public function alterDisplay(&$rows) {
    $newRows = [];

    foreach ($rows as $rowNum => $row) {
      $this->addCurrentTagAndChildTags($newRows, $row['osepi_tags_tag_id'], 0);
      //$rows[$rowNum]['osepi_tags_tag_name'] = '<span style="background-color: ' . $row['osepi_tags_tag_color'] . '">&nbsp;' . $row['osepi_tags_tag_name'] . '&nbsp;</span>';
    }

    $rows = $newRows;
  }

  private function addCurrentTagAndChildTags(&$rows, $tagId, $indentationLevel) {
    $tag = $this->getTag($tagId);
    $this->addTagToRows($rows, $tag, $indentationLevel);
    $indentationLevel++;

    $childrenDao = $this->getTagChildren($tagId);
    while ($childrenDao->fetch()) {
      $this->addCurrentTagAndChildTags($rows, $childrenDao->id, $indentationLevel);
    }
  }

  private function getTag($tagId) {
    $sql = "select id, name, color from civicrm_tag where id = $tagId";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    return $dao;
  }

  private function addTagToRows(&$rows, $tag, $indentationLevel) {
    $rows[]['osepi_tags_tag_name'] = $this->formatTag($tag, $indentationLevel);
  }

  private function formatTag($tag, $indentationLevel) {
    $marginLeft = ($indentationLevel * 20) . 'px';
    $style = "margin-left: $marginLeft;padding: 0 5px 0 5px;";
    if ($tag->color) {
      $style .= 'background-color:' . $tag->color . ";";
    }
    $span = '<span style="' . $style . '">' . $tag->name . '</span>';
    $link = CRM_Utils_System::url('civicrm/contact/search', 'reset=1&osepi_tag_id=' . $tag->id);
    $a = '<a href="' . $link . '">' . $span . '</a>';

    return $a;
  }

  private function getTagChildren($tagId) {
    $sql = "select id from civicrm_tag where parent_id = $tagId order by name";
    return CRM_Core_DAO::executeQuery($sql);
  }

  private function getReportFields() {
    $fields = [
      'tag_name' => [
        'title' => 'Tag',
        'required' => TRUE,
        'dbAlias' => 't.name',
      ],
      'tag_id' => [
        'title' => 'Tag ID',
        'dbAlias' => 't.id',
        'required' => TRUE,
        'no_display' => TRUE,
      ],
      'tag_color' => [
        'title' => 'Tag Color',
        'dbAlias' => 't.color',
        'required' => TRUE,
        'no_display' => TRUE,
      ],
    ];

    return $fields;
  }

  private function getReportFilters() {
    return [];
  }



}
