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

require_once(__DIR__ . '/../../config.php');

require_login();
$username = required_param('username', PARAM_ALPHANUMEXT);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/sap/request_remote.php', ['username' => $username]);

$requestform = new \local_sap\request_remote_form($PAGE->url);
if ($requestform->is_cancelled()) {
    redirect(new moodle_url('/local/sap/request_overview.php'));
} else if ($data = $requestform->get_data()) {
    // TODO send mails.
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('list_of_courses_for_teacher', 'local_sap', $username));
$requestform->display();
echo $OUTPUT->footer();

