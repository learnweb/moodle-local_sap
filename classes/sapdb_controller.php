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

use local_sap\local\entity\sap_course;
use stdClass;

/**
 * Class for communicating between sapdb and output files.
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sapdb_controller {

    const SAP_GRUPPE = "ovv_e_title";
    const SAP_GRUPPE_V = "ovv_e_klvl";
    const SAP_V_GRUPPE = "ovv_klvl_e";
    const SAP_GRUPPE_P = "ovv_e_p";
    const SAP_VERANST_TITLE = "ovv_klvl_title";
    const SAP_VERANST_DETAILS = "ovv_klvl_periods";
    const SAP_VERANST_KOMMENTAR = "ovv_klvl_comment";
    const SAP_PERSONAL = "ovv_lehrende";
    const SAP_PERSONAL_LOGIN = "ovv_lehr_email";
    const SAP_VER_PO = "ovv_klvl_po";

    /**
     * @var ?sapdb_controller
     */
    private static $instance;

    public static function get(): sapdb_controller {
        if (!self::$instance) {
            self::$instance = new sapdb_controller();
        }
        return self::$instance;
    }

    /**
     * @var \moodle_database|null
     */
    private ?\moodle_database $db;

    /**
     * Creates the moodle database parameter db.
     */
    private function __construct() {
        $this->db = \local_sap\sapdb::get();
    }

    /**
     * get_teachers_pid returns the pid (personen-id) connected to a specific username.
     *
     * @param string $username the teachers username
     * @return int $sapid the teachers sapid (personen-id)
     */
    public function get_teachers_pid(string $username) {
        $teacherrecord = $this->db->get_record(self::SAP_PERSONAL_LOGIN, ['login' => strtoupper($username)], 'sapid');

        if ($teacherrecord) {
            return $teacherrecord->sapid;
        } else {
            return null;
        }
    }

    /**
     * Gets one course by its veranstid.
     *
     * @param string $veranstid
     * @return sap_course|null
     */
    public function get_course_by_veranstid(string $veranstid): ?sap_course {
        return $this->get_courses_by_veranstids([$veranstid])[$veranstid] ?? null;
    }

    /**
     * Gets multiple courses by an array of veranstid.
     *
     * @param string[] $veranstids
     * @return sap_course[]
     */
    public function get_courses_by_veranstids(array $veranstids): array {
        global $DB;
        if (empty($veranstids)) {
            return array();
        }
        list($insql, $params) = $DB->get_in_or_equal($veranstids, SQL_PARAMS_NAMED);
        $params['maximportage'] = get_config('local_sap', 'max_import_age');

        $courses = $this->db->get_records_sql("
            SELECT DISTINCT ON (d.short, d.peryr, d.perid) d.short || '-' || d.peryr || '-' || d.perid as id,
                    d.objid, d.short, d.peryr, d.perid, d.category, d.categoryt
            FROM " . self::SAP_VERANST_DETAILS . " as d
            WHERE (d.short || '-' || d.peryr || '-' || d.perid) $insql
             AND (CURRENT_DATE - CAST(d.begda AS date)) < :maximportage
             ORDER BY d.short, d.peryr, d.perid, d.begda;", $params);

        $resultlist = array();
        foreach ($courses as $course) {
            $result = new sap_course(
                    $course->objid,
                    $course->short,
                    $course->peryr,
                    $course->perid
            );
            $resultlist[$result->id] = $result;
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
     *
     * @return string
     */
    private function gen_url(): string {
        return "";
    }

    /**
     * Get all courses of a teacher.
     *
     * @param string $username
     * @return sap_course[]
     */
    public function get_teachers_course_list(string $username): array {
        $courselist = array();
        $pid = $this->get_teachers_pid($username);

        if (empty($pid)) {
            return $courselist;
        }

        $veranst = $this->db->get_records_sql("SELECT DISTINCT ON(objid, peryr, perid) * FROM " . self::SAP_VER_PO .
                " WHERE sapid = :pid AND (CURRENT_DATE - CAST(begda_o AS date)) < :maximportage
                                             ORDER BY objid, peryr, perid", [
                'maximportage' => get_config('local_sap', 'max_import_age'),
                'pid' => $pid
        ]);

        foreach ($veranst as $course) {
            $result = new sap_course($course->objid, $course->short, $course->peryr, $course->perid);
            $courselist[$result->id] = $result;
        }
        return $courselist;
    }

    /**
     * returns true if a sap course is assigned to a specific teacher
     *
     * @param sap_course $course sap course
     * @param string $username the teachers username
     * @return bool course of teacher?
     */
    public function is_course_of_teacher(sap_course $course, string $username): bool {
        $pid = $this->get_teachers_pid($username);
        return $this->db->record_exists(self::SAP_VER_PO,
                ['objid' => $course->objid, 'peryr' => $course->peryr, 'perid' => $course->perid, 'sapid' => $pid]
        );
    }

    /**
     * get_teachers_of_course returns the teacher objects of a course sorted by their relevance
     *
     * @param sap_course $course sap course
     * @return array $sortedresult sorted array of teacher objects
     */
    private function get_teachers_of_course(sap_course $course): array {
        // TODO Try to sort by relevance.
        return $this->db->get_records_sql("
            SELECT DISTINCT ON (p.sapid) p.sapid, p.vorname, p.nachname, l.login FROM " . self::SAP_PERSONAL . " p
            JOIN " . self::SAP_PERSONAL_LOGIN . " l ON p.sapid = l.sapid
            WHERE p.sapid IN (
                SELECT DISTINCT sapid FROM " . self::SAP_VER_PO . "
                WHERE short = :short AND peryr = :peryr AND perid = :perid
            )
            ORDER BY p.sapid
       ", ['short' => $course->short, 'peryr' => $course->peryr, 'perid' => $course->perid]);
    }

    /**
     * Returns the default fullname according to a given sap course
     *
     * @param sap_course $sapcourse sap course
     * @return string
     */
    public function get_default_fullname(sap_course $sapcourse): string {
        $personen = "";
        foreach ($this->get_teachers_of_course($sapcourse) as $person) {
            $personen .= ", " . trim($person->vorname) . " " . trim($person->nachname);
        }
        return $sapcourse->get_title() . " " . $sapcourse->get_semester_text() . $personen;
    }

    /**
     * Returns the default shortname for a given SAP course.
     *
     * @param sap_course $sapcourse
     * @return string
     */
    public function get_default_shortname(sap_course $sapcourse): string {
        global $DB;
        // Using \p{L} for all unicode letters including ümläütṡ.
        preg_match_all('/[\p{L}\d]+/u', $sapcourse->get_title(), $matches);

        $end = "-" . $sapcourse->peryr . "_" . (int) $sapcourse->perid;

        $shortname = '';

        for ($longwords = 0; $longwords <= count($matches[0]); $longwords++) {
            $shortname = '';
            for ($i = 0; $i < count($matches[0]); $i++) {
                $shortname .= mb_convert_case(mb_substr($matches[0][$i], 0, $longwords > $i ? 2 : 1), MB_CASE_TITLE);
            }

            $shortname .= $end;

            if (!$DB->record_exists('course', ['shortname' => $shortname])) {
                return $shortname;
            }
        }

        return $shortname;
    }

    /**
     * Returns the default summary according to a given SAP course.
     *
     * @param sap_course $sapcourse
     * @return string $summary
     */
    public function get_default_summary(sap_course $sapcourse): string {
        return '<p>' . $sapcourse->get_desc() . '</p><p><a href="' .
                $this->gen_url($sapcourse) . '">Kurs in SAP</a></p>';
    }

    /**
     * Returns the default startdate according to a given SAP course
     *
     * @param sap_course $sapcourse
     * @return false|int
     */
    public function get_default_startdate(sap_course $sapcourse): bool|int {
        return mktime(0, 0, 0, $sapcourse->perid === '001' ? 4 : 10, 1, $sapcourse->peryr);
    }

    /**
     * Calculate the start date of the semester.
     *
     * @param string $peryr
     * @param string $perid
     * @return string
     */
    private function semester_begda(string $peryr, string $perid): string {
        if ($perid == "001") {
            return "$peryr" . "-04-01";
        } else {
            return "$peryr" . "-10-01";
        }
    }

    /**
     * Calculate the end date of the semester.
     *
     * @param string $peryr
     * @param string $perid
     * @return string
     */
    private function semester_endda(string $peryr, string $perid): string {
        if ($perid == "001") {
            return "$peryr" . "-09-30";
        } else {
            return ($peryr + 1) . "-03-31";
        }
    }

    /**
     * Generates the Title to a given SAP course.
     *
     * @param int $objid
     * @param string $peryr
     * @param string $perid
     * @param string $table
     * @return string
     */
    public function get_klvl_title(int $objid, string $peryr, string $perid, string $table): string {
        if (!in_array($table, [self::SAP_VERANST_TITLE, self::SAP_VERANST_KOMMENTAR])) {
            throw new \coding_exception('$table has to be ' . self::SAP_VERANST_TITLE . ' or ' . self::SAP_VERANST_KOMMENTAR);
        }
        $str = $this->db->get_field_sql("SELECT string_agg(t.tline, '') FROM ( " .
                "   SELECT DISTINCT ON (tabseqnr) tabseqnr, tline " .
                "   FROM $table " .
                "   WHERE objid = :objid AND peryr = :peryr AND perid = :perid " .
                "   ORDER BY tabseqnr " .
                ") t", ['objid' => $objid, 'peryr' => $peryr, 'perid' => $perid]);
        return $str ? trim($str) : '';
    }
}
