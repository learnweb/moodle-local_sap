<?php

require_once(__DIR__ . '/../../config.php');

require_admin();

$controller = new \local_sap\sapdb_controller();

echo json_encode($controller->get_veranstid_by_teacher_sap(40002669));

