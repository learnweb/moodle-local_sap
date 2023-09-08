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

use local_sap\course_form;
require_once(__DIR__ . '/../../config.php');
global $DB, $PAGE, $OUTPUT, $USER;
$courseid = optional_param('courseid', null, PARAM_TEXT);

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new \moodle_url('/local/sap/request_course.php', array('courseid' => $courseid)));

if ($DB->record_exists('course', ['idnumber' => $courseid])) {
    throw new coding_exception('Course already exists!');
}

$sapcontroller = \local_sap\sapdb_controller::get();
$sapcourse = $sapcontroller->get_course_by_veranstid($courseid);

if (!$sapcourse) {
    throw new coding_exception('Course does not exist in SAP');
}

if (!\local_sap\sapdb_controller::get()->is_course_of_teacher($sapcourse, $USER->username)) {
    $request = $DB->get_record('local_sap_courserequests', ['sapid' => $courseid]);
    if (!$request || $request->requestuserid != $USER->id || $request->state != 3) {
        throw new coding_exception('You have no permission to request this course!');
    }
}

$courseform = new course_form(null, array('sapcourse' => $sapcourse));

echo $OUTPUT->header();
echo $OUTPUT->heading('');
$courseform->display();
echo $OUTPUT->footer();
