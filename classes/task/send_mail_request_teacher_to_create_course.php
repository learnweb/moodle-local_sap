<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The ad hoc task for sending a email to the teacher of a course. The course is requested from a different user
 * and the teacher is asked for permission.
 *
 * @package    local_sap
 * @copyright  2018 Nina Herrmann, 2023 WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_sap\task;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/lib.php');

/**
 * The ad hoc task for sending a email to the teacher of a course. The course is requested from a different user
 * and the teacher is asked for permission.
 *
 * @package    local_sap
 * @copyright  2018 Nina Herrmann, 2023 WWU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_mail_request_teacher_to_create_course extends \core\task\adhoc_task {
    /**
     * Execute the ad-hoc task.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function execute() {
        global $CFG;
        /** @var \stdClass $data */
        $data = $this->get_custom_data();

        // In case no recipient can be found the task is aborted and deleted.
        if (empty($data->user)) {
            return;
        }
        $user = $data->user;

        $params = new \stdClass();
        $params->requesturl = (new \moodle_url('/local/sap/request_respond.php',
                ['answer' => 1, 'requestid' => $data->requestid]))->out(false);
        $params->userurl = (new \moodle_url('/user/view.php', ['id' => $data->requesterid]))->out(false);
        $requestuser = \core_user::get_user($data->requesterid);
        // Expected params of $data->params are:
        // A) a-> (string) firstname,
        // B) userurl-> (string) url to the user profile page of the requesting user,
        // C) c-> the (string) coursename, and
        // D) requesturl-> the (moodle_url)link for managing the request.
        $content = get_string('remote_request_email_content', 'local_sap', $params);

        $wassent = email_to_user($user, $requestuser,
            get_string('remote_request_email_title', 'local_sap'), $content);

        if (!$wassent) {
            throw new \coding_exception("Could not send course request to {$data->user->username}.");
        }
    }
}
