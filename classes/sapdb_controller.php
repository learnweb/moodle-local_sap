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

use stdClass;

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
    function get_teachers_pid($username) {
        // TO CHECK: use get_record instead of get_record_sql.
        $teacherrecord = $this->db->get_record_sql("SELECT sapid FROM " . SAP_PERSONAL_LOGIN
            . " WHERE login= '" . strtoupper($username) . "'");
        if (!empty($teacherrecord)) {
            return $teacherrecord->sapid;
        }
        return null;
    }

    /**
     * This Function gives all courses for a teacherID within the defined time.
     * @param $pid int The Id of the teacher
     * @return array
     * @throws \dml_exception
     */
    function get_veranstids_by_teacher($pid) {
        // TODO: get an unique objectid (by using distinct or group by perhaps).
        $courses = $this->db->get_records_sql("SELECT DISTINCT (objid), * FROM " . SAP_VER_PO . " WHERE sapid =" . $pid . " AND (CURRENT_DATE - CAST(begda_o AS date)) < " .
            get_config('local_sap', 'max_import_age') . " ORDER BY peryr, perid");
        return $courses;
    }

    /**
     * Gets one course by its veranstid.
     * @param $veranstid int
     * @return mixed
     */
    function get_course_by_veranstid($veranstid) {
        $result = get_courses_by_veranstids(array($veranstid
        ));
        return $result[$veranstid];
    }

    /**
     * Gets multiple courses by an array of veranstid.
     * @param $veranstids array
     * @return array of stdClasses
     * @throws \dml_exception
     */
    function get_courses_by_veranstids($veranstids) {
        if (empty($veranstids)) {
            return array();
        }
        $veranstids_string = implode(',', $veranstids);

        $courses = $this->db->get_records_sql("
            SELECT v.objid, v.stext, d.peryr, d.perid, d.category, v.tabnr, v.tabseqnr, v.tline
            FROM " . SAP_VERANST . " as v JOIN " . SAP_VERANST_DETAILS . " as d on v.objid = d.objid
            WHERE v.objid in (" . $veranstids_string . ") 
                 AND " . "(CURRENT_DATE - CAST(v.begda AS date)) < " . get_config('local_sap', 'max_import_age') .
            " ORDER BY v.begda,v.tline;");

        foreach ($courses as $course) {
            $result = new stdClass();
            $result->veranstid = $course->objid;
            $result->peryr = $course->peryr;
            $result->perid = $course->perid;
            $result->semester = $course->peryr . $course->perid[-1];
            if($course->perid[-1] === "1"){
                $semester = "SoSe";
            } else if($course->perid[-1] === "2") {
                $semester = "WiSe";
            }
            $result->semestertxt = $semester . " " . $course->peryr;
            $result->veranstaltungsart = $course->category;
            // TODO klvl title
            $result->titel = $course->stext; //get_klvl_title($course->objid, $course->peryr, $course->perid, SAP_VERANST);
            //$result->urlveranst = $course->urlveranst; TODO
            // might override object with same objid.
            $result_list[$course->objid] = $result;
        }

        return $result_list;
    }

    /**
     * Appends @uni-muenster to the username
     * @param $username
     * @return string
     */
    function username_to_mail($username) {
        return $username . "@uni-muenster.de";
    }


    /**
     * TODO.
     * @return string
     */
    function gen_url() {
        return "";
    }

    function get_teachers_course_list($username, $longinfo = false) {
        $courselist = array();
        $pid = $this->get_teachers_pid($username);
        if (empty($pid)) {
            return $courselist;
        }
        $veranst = $this->get_veranstids_by_teacher($pid);
        foreach ($veranst as $course) {
            $result = new stdClass();
            $url = $this->gen_url($course);
            $result->veranstid = $course->objid;
            $result->info = $course->stext; //get_klvl_title($course->objid, $course->peryr, $course->perid, SAP_VERANST) . " (" . ($course->perid == 1? "SoSe " : "WiSe ") . $course->peryr . ",<a target='_blank' href=" . $url . "> Link - " . $course->objid . "</a>" . ")";
            // TODO URL und Optional - beschreibung, früher shorttext oder so.
            $courselist[$course->short] = $result;
        }
        return $courselist;
    }
}
