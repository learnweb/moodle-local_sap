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
    public $mform;
    public $option;
    public $courseid;
    public $courses;
    const OPTION_SAP_COURSE_TEACHER = 1;
    const OPTION_SAP_COURSE_AUTHORIZED = 2;
    const OPTION_SAP_COURSE_NONE = 3;
    function definition() {
        global $USER, $CFG, $DB;

        $this->mform = $this->_form;
        $options = array();
        $this->mform->addElement('radio', 'request_option', '',
                                get_string('sap_course_teacher', 'local_sap'), self::OPTION_SAP_COURSE_TEACHER);
        // Checkbox for sap_teacher.
        $this->show_courses();

        $this->mform->addElement('radio', 'request_option', '',
                                get_string('sap_course_authorized', 'local_sap'), self::OPTION_SAP_COURSE_AUTHORIZED);


        $this->mform->addElement('radio', 'request_option', '',
                                get_string('sap_course_none', 'local_sap'), self::OPTION_SAP_COURSE_NONE);

        $this->mform->addElement('submit', 'submitbutton',
                                get_string('submitbutton', 'local_sap'));
        $this->mform->setDefault('request_option', 1);

    }

    function show_courses() {
        $this->courses = array();
        $this->courses[] = $this->mform->createElement('radio', 'courses', '', 'course1: einführung in die informatik', 31);

        $this->courses[] = $this->mform->createElement('radio', 'courses', '', 'course2: einführung in die moodletechnik',45);

        $this->courses[] = $this->mform->createElement('radio', 'courses', '', 'course3: einführung in die mensapreise',46);

        $this->mform->addGroup($this->courses, 'sap_courses', '', '<br/>', false);

        $this->mform->hideif('sap_courses', 'sap_course', 'neq', 1);
    }
}
