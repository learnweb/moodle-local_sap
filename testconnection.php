<?php

require_once(dirname(__FILE__) . '/../../config.php');

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

$pgthing = new \local_sap\pg_lite();
$pgthing->connect();

$result = pg_query($pgthing->connection,
    "SELECT p.vorname, p.nachname, l.login, p.sapid FROM " . SAP_PERSONAL . " as p JOIN " .
    SAP_PERSONAL_LOGIN. " as l on p.sapid = l.sapid LIMIT 50");

echo json_encode(pg_fetch_all($result));

$pgthing->dispose();
