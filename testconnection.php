<?php

require_once(__DIR__ . '/../../config.php');

require_admin();

$controller = new \local_sap\sapdb_controller();

echo json_encode($controller->get_teachers_course_list());

