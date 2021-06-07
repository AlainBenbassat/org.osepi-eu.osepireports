<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
return [
  [
    'name' => 'CRM_Osepireports_Form_Report_OsepiTags',
    'entity' => 'ReportTemplate',
    'params' => [
      'version' => 3,
      'label' => 'OsepiTags',
      'description' => 'OsepiTags (org.osepi-eu.osepireports)',
      'class_name' => 'CRM_Osepireports_Form_Report_OsepiTags',
      'report_url' => 'org.osepi-eu.osepireports/osepitags',
      'component' => '',
    ],
  ],
];
