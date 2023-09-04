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
class request_form extends \moodleform {
    public $option;
    public $courseid;
    public $courses;
    const OPTION_SAP_COURSE_TEACHER = 1;
    const OPTION_SAP_COURSE_AUTHORIZED = 2;
    const OPTION_SAP_COURSE_NONE = 3;
    protected function definition() {

        $mform = $this->_form;
        $mform->addElement('radio', 'request_option', '',
                                get_string('sap_course_teacher', 'local_sap'), self::OPTION_SAP_COURSE_TEACHER);
        // Checkbox for sap_teacher.
        $this->show_courses();
        $mform->addElement('radio', 'request_option', '',
                                get_string('sap_course_authorized', 'local_sap'), self::OPTION_SAP_COURSE_AUTHORIZED);

        $mform->addGroup([
                $mform->createElement('html', get_string('create_for_username', 'local_sap')),
                $mform->createElement('text', 'create_for_username', '', ''),
        ], 'username_group');
        $mform->setType('username_group[create_for_username]', PARAM_ALPHANUMEXT);
        $mform->hideIf('username_group', 'request_option', 'neq', self::OPTION_SAP_COURSE_AUTHORIZED);

        $mform->addElement('radio', 'request_option', '',
                                get_string('sap_course_none', 'local_sap'), self::OPTION_SAP_COURSE_NONE);
        $mform->setDefault('request_option', self::OPTION_SAP_COURSE_TEACHER);

        $mform->addGroup([
                $mform->createElement('static', 'nocoursestext', '', get_string('info_nocourses', 'local_sap'))
        ], 'nocoursestextgroup', '');
        $mform->hideIf('nocoursestextgroup', 'request_option', 'neq', self::OPTION_SAP_COURSE_NONE);

        $mform->addElement('submit', 'submitbutton',
                get_string('submitbutton', 'local_sap'));
    }

    private function show_courses() {
        $mform = $this->_form;

        $this->courses = array();
        $this->courses[] = $mform->createElement('radio', 'courses', '', 'course1: einführung in die informatik', 31);
        $this->courses[] = $mform->createElement('radio', 'courses', '', 'course2: einführung in die moodletechnik', 45);
        $this->courses[] = $mform->createElement('radio', 'courses', '', 'course3: einführung in die mensapreise', 46);

        $mform->addGroup($this->courses, 'sap_courses', '', '<br/>', false);

        $mform->hideif('sap_courses', 'request_option', 'neq', self::OPTION_SAP_COURSE_TEACHER);
    }
}
