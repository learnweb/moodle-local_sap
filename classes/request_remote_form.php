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

namespace local_sap;
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
class request_remote_form extends \moodleform {
    public $mform;

    function definition() {
        $this->mform = $this->_form;

        $courses = [
                $this->mform->createElement('radio', 'courses', '', 'course1: einführung in die informatik', 31),
                $this->mform->createElement('radio', 'courses', '', 'course2: einführung in die moodletechnik', 45),
                $this->mform->createElement('radio', 'courses', '', 'course3: einführung in die mensapreise', 46)
        ];

        $this->mform->addGroup($courses, 'sap_courses', '', '<br/>', false);

        $this->add_action_buttons(true, get_string('submit'));
    }

}
