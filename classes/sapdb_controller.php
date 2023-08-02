<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.


/**
 * Class for communicating between sapdb and output files. *
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sap;

/**
 * Class for communicating between sapdb and output files.
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define("SAP_GRUPPE",            "public.ovv_e_title");
define("SAP_GRUPPE_V",          "public.ovv_e_klvl");
define("SAP_V_GRUPPE",          "public.ovv_klvl_e");
define("SAP_GRUPPE_P",          "public.ovv_e_p");
define("SAP_VERANST",           "public.ovv_klvl_title");
define("SAP_VERANST_DETAILS",   "public.ovv_klvl_periods");
define("SAP_VERANST_KOMMENTAR", "public.ovv_klvl_comment");
define("SAP_PERSONAL",          "ovv_lehrende");
define("SAP_PERSONAL_LOGIN",    "public.ovv_lehr_email");
define("SAP_VER_PO",      	"public.ovv_klvl_po");

class sapdb_controller {

    public function test_connection () {

        $db = \local_sap\sapdb::get();

        // return $db->get_records_sql("SELECT * FROM " . SAP_PERSONAL . " LIMIT 50", []);
        return $db->get_records(SAP_PERSONAL,null,"", "vorname, nachname" ,0, 50);

    }

}
