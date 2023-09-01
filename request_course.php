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

use \local_sap\course_form;
require_once(__DIR__ . '/../../config.php');
// Site parameter.
$courseid = required_param('courseid', PARAM_TEXT);


require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new \moodle_url('/local/sap/request_course.php', array('courseid' => $courseid)));

$courseform = new course_form(null, array('sapid' => $courseid));


echo $OUTPUT->header();
echo $OUTPUT->heading('');
$courseform->display();
echo $OUTPUT->footer();
