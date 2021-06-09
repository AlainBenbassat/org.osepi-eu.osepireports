<?php
use CRM_Osepireports_ExtensionUtil as E;

class CRM_Osepireports_Form_Report_OsepiEvents extends CRM_Report_Form {
  public function __construct() {
    $this->_columns = [
      'osepi_events' => [
        'fields' => $this->getReportFields(),
        'filters' => $this->getReportFilters(),
      ],
    ];

    parent::__construct();
  }

  public function preProcess() {
    $this->assign('reportTitle', 'OSEPI Events');
    parent::preProcess();
  }

  public function from() {
    $this->_from = '
      FROM
        civicrm_event e
      LEFT OUTER JOIN
        civicrm_participant p on e.id = p.event_id and p.status_id in (1, 2)
      LEFT OUTER JOIN
        civicrm_contact c on c.id = p.contact_id and c.is_deleted = 0
    ';
  }

  public function where() {
    $minDate = date('Y-m-d', time() - (86400 * 365)); // today minus 1 year

    $this->_where = "
      WHERE
        e.is_template = 0
      AND
        e.start_date >= '$minDate'
    ";
  }

  public function groupBy() {
    $this->_groupBy = "
      GROUP BY
        e.id,
        DATE_FORMAT(e.start_date,'%Y-%m-%d'),
        e.title
    ";
  }

  public function orderBy() {
    $this->_orderBy = '
      ORDER BY
        e.start_date desc
    ';
  }

  public function alterDisplay(&$rows) {
    foreach ($rows as $rowNum => $row) {
      $rows[$rowNum]['osepi_events_event_name'] = $this->addLinkToEvent($row['osepi_events_event_id'], $row['osepi_events_event_name']);
      $rows[$rowNum]['osepi_events_participant_count'] = $this->addLinkToParticipants($row['osepi_events_event_id'], $row['osepi_events_participant_count']);
    }
  }

  private function addLinkToEvent($eventId, $eventTitle) {
    $url = CRM_Utils_System::url('civicrm/event/manage/settings', 'reset=1&action=update&id=' . $eventId);
    $a = '<a href="' . $url . '">' . $eventTitle . '</a>';
    return $a;
  }

  private function addLinkToParticipants($eventId, $participantCount) {
    $url = CRM_Utils_System::url('civicrm/event/search', 'reset=1&force=1&status=true&event=' . $eventId);
    $a = '<a href="' . $url . '">' . $participantCount . '</a>';
    return $a;
  }

  private function getReportFields() {
    $fields = [
      'event_id' => [
        'title' => 'Event ID',
        'dbAlias' => 'e.id',
        'required' => TRUE,
        'no_display' => TRUE,
      ],
      'event_start_date' => [
        'title' => 'Date',
        'dbAlias' => "DATE_FORMAT(e.start_date, '%Y-%m-%d')",
        'required' => TRUE,
      ],
      'event_name' => [
        'title' => 'Event',
        'dbAlias' => 'e.title',
        'required' => TRUE,
      ],
      'participant_count' => [
        'title' => 'Participants',
        'dbAlias' => 'count(c.id)',
        'required' => TRUE,
      ],
    ];

    return $fields;
  }

  private function getReportFilters() {
    $filters = [];

    return $filters;
  }
}
