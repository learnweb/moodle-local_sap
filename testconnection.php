<?php

require_once(__DIR__ . '/../../config.php');

require_admin();

define("SAP_GRUPPE",            "public.ovv_e_title");
define("SAP_GRUPPE_V",          "public.ovv_e_klvl");
define("SAP_V_GRUPPE",          "public.ovv_klvl_e");
define("SAP_GRUPPE_P",          "public.ovv_e_p");
define("SAP_VERANST",           "public.ovv_klvl_title");
define("SAP_VERANST_DETAILS",   "public.ovv_klvl_periods");
define("SAP_VERANST_KOMMENTAR", "public.ovv_klvl_comment");
define("SAP_PERSONAL",          "public.ovv_lehrende");
define("SAP_PERSONAL_LOGIN",    "public.ovv_lehr_email");
define("SAP_VER_PO",      	"public.ovv_klvl_po");

$db = \local_sap\sapdb::get();

$result = $db->get_records_sql("SELECT * FROM " . SAP_PERSONAL . " LIMIT 50", []);

echo json_encode($result);

