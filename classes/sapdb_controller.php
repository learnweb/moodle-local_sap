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

define("SAP_GRUPPE",            "ovv_e_title");
define("SAP_GRUPPE_V",          "ovv_e_klvl");
define("SAP_V_GRUPPE",          "ovv_klvl_e");
define("SAP_GRUPPE_P",          "ovv_e_p");
define("SAP_VERANST",           "ovv_klvl_title");
define("SAP_VERANST_DETAILS",   "ovv_klvl_periods");
define("SAP_VERANST_KOMMENTAR", "ovv_klvl_comment");
define("SAP_PERSONAL",          "ovv_lehrende");
define("SAP_PERSONAL_LOGIN",    "ovv_lehr_email");
define("SAP_VER_PO",      	"ovv_klvl_po");

class sapdb_controller {

    /**
     * @var \moodle_database|null
     */
    private $db;

    /**
     * Creates the moodle database parameter db.
     */
    function __construct() {
        $this->db = \local_sap\sapdb::get();
    }

    /**
     * get_teachers_pid returns the pid (personen-id) connected to a specific username.
     *
     * @param $username string the teachers username
     * @return $sapid the teachers sapid (personen-id)
     */
    function get_teachers_pid_sap($username) {
        // TO CHECK: use get_record instead of get_record_sql.
        $teacherrecord = $this->db->get_record_sql("SELECT sapid FROM " . SAP_PERSONAL_LOGIN
            . " WHERE login= '" . strtoupper($username) . "'");
        if (!empty($teacherrecord)) {
            return $teacherrecord->sapid;
        }
        return null;
    }

    function get_veranstid_by_teacher_sap($pid) {
        $courses = $this->db->get_records_sql("SELECT * FROM " . SAP_VER_PO . " WHERE sapid =" . $pid . "and (CURRENT_DATE - CAST(begda_o AS date)) < " .
            get_config('local_sap', 'max_import_age') . " order by peryr, perid");

        return $courses;
    }
}
