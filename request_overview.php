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

use \local_sap\request_form;

require_once(__DIR__ . '/../../config.php');

require_login();
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/sap/request_overview.php');

$requestform = new request_form();
if ($fromform = $requestform->get_data()) {
    $newurl = $PAGE->url;
    var_dump($fromform);
    switch ($fromform->request_option) {
        case request_form::OPTION_SAP_COURSE_TEACHER:
            $newurl = new \moodle_url('/local/sap/request_course.php', ['courseid' => $fromform->courses]);
            break;
        case request_form::OPTION_SAP_COURSE_AUTHORIZED:
            $newurl = new \moodle_url('/local/sap/request_remote.php',
                    ['username' => $fromform->username_group['create_for_username']]);
            break;
        case request_form::OPTION_SAP_COURSE_NONE:
            // TODO.
            break;
    }
    redirect($newurl);
}
echo $OUTPUT->header();
echo $OUTPUT->heading('');
$requestform->display();
echo $OUTPUT->footer();

