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
 * Class for communicating between sapdb and output files.
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sap;

use stdClass;

define("SAP_GRUPPE",            "ovv_e_title");
define("SAP_GRUPPE_V",          "ovv_e_klvl");
define("SAP_V_GRUPPE",          "ovv_klvl_e");
define("SAP_GRUPPE_P",          "ovv_e_p");
define("SAP_VERANST",           "ovv_klvl_title");
define("SAP_VERANST_DETAILS",   "ovv_klvl_periods");
define("SAP_VERANST_KOMMENTAR", "ovv_klvl_comment");
define("SAP_PERSONAL",          "ovv_lehrende");
define("SAP_PERSONAL_LOGIN",    "ovv_lehr_email");
define("SAP_VER_PO",          "ovv_klvl_po");
/**
 * Class for communicating between sapdb and output files.
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sapdb_controller {

    /**
     * @var \moodle_database|null
     */
    private ?\moodle_database $db;

    /**
     * Creates the moodle database parameter db.
     */
    public function __construct() {
        $this->db = \local_sap\sapdb::get();
    }

    /**
     * get_teachers_pid returns the pid (personen-id) connected to a specific username.
     *
     * @param string $username the teachers username
     * @return int $sapid the teachers sapid (personen-id)
     * @throws \dml_exception
     */
    private function get_teachers_pid(string $username) {
        // TO CHECK: use get_record instead of get_record_sql.
        $params = array('username' => $username);
        $teacherrecord = $this->db->get_record_sql("SELECT sapid FROM " . SAP_PERSONAL_LOGIN
            . " WHERE login= ':username'", $username);
        if (!empty($teacherrecord)) {
            return $teacherrecord->sapid;
        }
        return null;
    }


    /**
     * This Function gives all courses for a teacherID within the defined time.
     *
     * @param int $pid
     * @return array
     * @throws \dml_exception
     */
    private function get_veranstids_by_teacher(int $pid): array {
        // TODO: get an unique objectid (by using distinct or group by perhaps).
        $params = array('maximportage' => get_config('local_sap', 'max_import_age'), 'pid' => $pid);
        return $this->db->get_records_sql("SELECT DISTINCT (objid), * FROM " . SAP_VER_PO .
                                            " WHERE sapid = :pid AND (CURRENT_DATE - CAST(begda_o AS date)) < :maximportage
                                             ORDER BY peryr, perid", $params);
    }

    /**
     * Gets one course by its veranstid.
     * @param int $veranstid
     * @return mixed
     */
    public function get_course_by_veranstid(int $veranstid): mixed {
        $result = get_courses_by_veranstids(array($veranstid));
        return $result[$veranstid];
    }

    /**
     * Gets multiple courses by an array of veranstid.
     * @param array $veranstids
     * @return array of stdClasses
     * @throws \dml_exception
     */
    public function get_courses_by_veranstids(array $veranstids): array {
        if (empty($veranstids)) {
            return array();
        }
        $veranstidsstring = implode(',', $veranstids);
        $params = array('veranstidsstring' => $veranstidsstring,
                    'maximportage' => get_config('local_sap', 'max_import_age') );
        $courses = $this->db->get_records_sql("
            SELECT v.objid, v.stext, d.peryr, d.perid, d.category, v.tabnr, v.tabseqnr, v.tline
            FROM " . SAP_VERANST . " as v JOIN " . SAP_VERANST_DETAILS . " as d on v.objid = d.objid
            WHERE v.objid in (:veranstidsstring)
             AND (CURRENT_DATE - CAST(v.begda AS date)) < :maximportage
             ORDER BY v.begda,v.tline;", $params);
        $resultlist = array();
        foreach ($courses as $course) {
            $result = new stdClass();
            $result->veranstid = $course->objid;
            $result->peryr = $course->peryr;
            $result->perid = $course->perid;
            $result->semester = $course->peryr . $course->perid[-1];
            if ($course->perid[-1] === "1") {
                $semester = "SoSe";
            } else if ($course->perid[-1] === "2") {
                $semester = "WiSe";
            }
            $result->semestertxt = $semester . " " . $course->peryr;
            $result->veranstaltungsart = $course->category;
            // TODO klvl title - check.
            $result->titel = $this->get_klvl_title($course->objid, $course->peryr, $course->perid, SAP_VERANST);
            // TODO $result->urlveranst = $course->urlveranst;.
            // Might override object with same objid.
            $resultlist[$course->objid] = $result;
        }

        return $resultlist;
    }

    /**
     * Appends @uni-muenster to the username
     *
     * @param string $username
     * @return string
     */
    public function username_to_mail(string $username): string {
        return $username . "@uni-muenster.de";
    }


    /**
     * TODO.
     * @return string
     */
    private function gen_url(): string {
        return "";
    }

    /**
     * Get all courses of a teacher.
     * @param string $username
     * @param bool $longinfo
     * @return array
     * @throws \dml_exception
     */
    public function get_teachers_course_list(string $username, bool $longinfo = false): array {
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
            // TODO Check if works.
            $result->info = $this->get_klvl_title($course->objid, $course->peryr, $course->perid, SAP_VERANST) .
                " (" . ($course->perid == 1 ? "SoSe " : "WiSe ") . $course->peryr . ",
                <a target='_blank' href=" . $url . "> Link - " . $course->objid . "</a>" . ")";
            // TODO URL und Optional - beschreibung, früher shorttext oder so.
            $courselist[$course->short] = $result;
        }
        return $courselist;
    }

    /**
     * returns true if a idnumber/veranstid is assigned to a specific teacher
     *
     * @param int $veranstid idnumber/veranstid
     * @param string $username the teachers username
     * @return bool course of teacher?
     * @throws \dml_exception
     */
    public function is_course_of_teacher(int $veranstid, string $username): bool {
        $courses = $this->get_teachers_course_list($username, false, true);
        foreach ($courses as $course) {
            if ($course->veranstid == $veranstid) {
                return true;
            }
        }
        return false;
    }

    /**
     * get_teachers_of_course returns the teacher objects of a course sorted by their relevance
     *
     * @param int $veranstid idnumber/veranstid
     * @return array $sortedresult sorted array of teacher objects
     * @throws \dml_exception
     */
    private function get_teachers_of_course(int $veranstid): array {
        $params = ['veranstid' => $veranstid];

        $teacherids = $this->db->get_records_sql(
            "SELECT DISTINCT sapid FROM " . SAP_VER_PO . " WHERE objid = :veranstid", $params);
        $pidstring = "";
        $pids = array();
        foreach ($teacherids as $teacher) {
            $pidstring .= (empty($pidstring) ? "" : ",") . $teacher->sapid;
            $pids[] = $teacher->sapid;
        }

        if (empty($pids)) {
            return array();
        }
        // Get personal info.
        $result = array();
        $params = array('pidstring' => $pidstring);
        $teachersinfo = $this->db->get_records_sql("SELECT p.vorname, p.nachname, l.login, p.sapid
                                                         FROM " . SAP_PERSONAL . " as p JOIN " .
                                                        SAP_PERSONAL_LOGIN. " as l on p.sapid = l.sapid
                                                         WHERE p.sapid IN ( :pidstring)", $params);

        foreach ($teachersinfo as $teacherinfo) {
            $result[$teacherinfo->sapid] = $teacherinfo;
        }
        // Sort by relevance.
        $sortedresult = array();
        foreach ($pids as $pid) {
            $sortedresult[] = $result[$pid];
        }
        return $sortedresult;
    }

    /**
     * Returns the default fullname according to a given veranstid.*
     * @param stdClass $sapcourse idnumber/veranstid
     * @return String
     */
    public function get_default_fullname(stdClass $sapcourse): string {
        $personen = "";
        foreach (get_teachers_of_course_sap($sapcourse->veranstid) as $person) {
            $personen .= ", " . trim($person->vorname) . " " . trim($person->nachname);
        }
        return (($sapcourse->titel) . " " . trim($sapcourse->semestertxt) . $personen);
    }

    /**
     * Returns the default shortname according to a given SAP course.
     *
     * @param stdClass $sapcourse
     * @param bool $long
     * @return String
     * @throws \dml_exception
     */
    public function get_default_shortname(stdClass $sapcourse, bool $long = false): string {
        global $DB;
        $i = "";
        foreach (explode(" ", $sapcourse->titel) as $word) {
            $i .= strtoupper($word[0]) . (($long && !empty($word[1])) ? $word[1] : "");
        }
        $name = utf8_encode($i . "-" . substr($sapcourse->semester, 0, 4) .
                            "_" . substr($sapcourse->semester, -1));
        if (!$long && $DB->record_exists('course', array('shortname' => $name))) {
            return $this->get_default_shortname($sapcourse, true);
        }
        return $name;
    }

    /**
     * Returns the default summary according to a given SAP course.
     *
     * @param stdClass $sapcourse
     * @return string $summary
     * @throws \dml_exception
     */
    public function get_default_summary(stdClass $sapcourse): string {
        $summary = '<p>' . $this->get_klvl_title($sapcourse->veranstid, $sapcourse->peryr, $sapcourse->perid,
                    SAP_VERANST_KOMMENTAR) . '</p>';
        return $summary . '<p><a href="' . $this->gen_url($sapcourse) . '">Kurs in SAP</a></p>';
    }

    /**
     * Returns the default startdate according to a given SAP course
     *
     * @param stdClass $sapcourse
     * @return false|int $startdate
     */
    public function get_default_startdate(stdClass $sapcourse): bool|int {
        $semester = $sapcourse->semester . '';
        $year = substr($semester, 0, 4);
        $month = (substr($semester, -1) == "1") ? 4 : 10;
        return mktime(0, 0, 0, $month, 1, $year);
    }

    /**
     * Calculate the start date of the semester.
     * @param string $peryr
     * @param string $perid
     * @return string
     */
    private function semester_begda(string $peryr, string $perid): string {
        if ($perid == "001") {
            return "$peryr"."-04-01";
        } else {
            return "$peryr"."-10-01";
        }
    }

    /**
     * Calculate the end date of the semester.
     * @param string $peryr
     * @param string $perid
     * @return string
     */
    private function semester_endda(string $peryr, string $perid): string {
        if ($perid == "001") {
            return "$peryr"."-09-30";
        } else {
            return ($peryr + 1) . "-03-31";
        }
    }

    /**
     * Generates the Title to a given SAP course.
     *
     * @param int $kid
     * @param string $peryr
     * @param string $perid
     * @param string $table
     * @return string|null
     * @throws \dml_exception
     */
    private function get_klvl_title(int $kid, string $peryr, string $perid, string $table): ?string {
        $params = array('kid' => $kid, 'peryr' => $peryr, 'perid' => $perid);
        $records = $this->db->get_records_sql("SELECT tabnr, tline, begda, endda
                                     FROM " . $table . "
                                     WHERE objid = :kid AND peryr = :peryr AND perid = :perid ORDER BY tabnr, tabseqnr",
                                    $params);
        $title = "";
        $lines = array();
        $tabnrs = array();
        foreach ($records as $record) {
            $lines[] = $record;
            $tn = $record->tabnr;
            if (!in_array($tn, $tabnrs)) {
                $tabnrs[] = $tn;
            }
            $title .= $record->tline." ";
        }
        if (count($tabnrs) == 1) {
            return trim($title);
        }
        $sembegda = $this->semester_begda($peryr, $perid);
        $semendda = $this->semester_endda($peryr, $perid);
        $title = "";
        foreach ($lines as $line) {
            if ($line["begda"] <= $sembegda && $semendda <= $line["endda"]) {
                $title .= $line["tline"]." ";
            }
        }
        if ($title == "") {
            return null;
        }
        return trim($title);
    }
}
