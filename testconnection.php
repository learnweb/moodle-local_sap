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
 * Testing the output of the sap plugin dependend on url parameter
 *
 * @package     local_sap
 * @category    upgrade
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');

require_admin();

$context = context_system::instance();

$teachername = optional_param('teachername', false, PARAM_TEXT);
var_dump($teachername);
$pagetitle = get_string('testconnection', 'local_sap');
$url = new \moodle_url("/local/sap/testconnection.php");

global $OUTPUT, $PAGE;

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->set_pagelayout('admin');

$OUTPUT->heading($pagetitle);
$OUTPUT->header();
if ($teachername) {
    $controller = new \local_sap\sapdb_controller();

    echo html_writer::tag('h2', 'Get teachers course list of ' . $teachername);
    $courses = $controller->get_teachers_course_list($teachername);
    echo html_writer::tag('h2', ' teachers mail ' . $controller->username_to_mail($teachername));
    echo json_encode($courses);
    echo html_writer::tag('h2', 'Is course with id 2 Course of teacher: ' . $teachername);
    echo json_encode($controller->is_course_of_teacher('2', $teachername));
    if (!empty($courses)) {
        echo html_writer::tag('h2', 'Is course with id' . array_key_first($courses) . ' Course of teacher: ' . $teachername);
        echo ($controller->is_course_of_teacher($courses[array_key_first($courses)]->veranstid, $teachername));
        echo html_writer::tag('h2', 'Course with id' . array_key_first($courses));
        $course = $controller->get_course_by_veranstid($courses[array_key_first($courses)]->veranstid);
        echo json_encode($course);
        echo html_writer::tag('h2', 'Courses with id' . array_key_first($courses) . ' and ' . array_key_last($courses));
        echo json_encode($controller->get_courses_by_veranstids(array($courses[array_key_first($courses)]->veranstid,
            $courses[array_key_last($courses)]->veranstid)));
        echo html_writer::tag('h2', 'Fullname, shortname of course: ' . array_key_first($courses));
        echo json_encode($controller->get_default_fullname($course));
        echo json_encode($controller->get_default_shortname($course, $long = false));
        echo html_writer::tag('h2', 'Summary default startdate of course: ' . array_key_first($courses));
        echo json_encode($controller->get_default_summary($course));
        echo json_encode($controller->get_default_startdate($course));
    }
}

$OUTPUT->footer();
