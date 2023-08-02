<?php

require_once(__DIR__ . '/../../config.php');

require_admin();

$controller = new \local_sap\sapdb_controller();

echo json_encode($controller->test_connection());

