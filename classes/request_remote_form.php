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

    public function __construct(private array $courses, $action = null, $customdata = null, $method = 'post', $target = '',
            $attributes = null, $editable = true, $ajaxformdata = null) {
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);

    }

    protected function definition() {
        $mform = $this->_form;

        $radios = [];
        foreach ($this->courses as $course) {
            $radios[] = $mform->createElement('radio', 'course', '', $course->info, $course->id);
        }
        $mform->addGroup($radios, 'sap_courses', '', '<br/>', false);

        $this->add_action_buttons(true, get_string('submit'));
    }

}
