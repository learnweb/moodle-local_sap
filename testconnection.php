<?php

require_once(__DIR__ . '/../../config.php');

require_admin();

$controller = new \local_sap\sapdb_controller();

echo json_encode($controller->get_courses_by_veranstids_sap(array(10043553, 10019096)));

