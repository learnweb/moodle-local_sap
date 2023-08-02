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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
class request_form extends moodleform {
    public $mform;
    public $option;
    public $courseid;
    public $courses;
    function definition() {
        global $USER, $CFG, $DB;

        $this->mform = $this->_form;
        $this->mform->addElement('advcheckbox', 'sap_course_teacher', get_string('sap_course_teacher', 'local_sap'));
        // Checkbox for sap_teacher.
        $this->show_courses();

        $this->mform->addElement('advcheckbox', 'sap_course_authorized', get_string('sap_course_authorized', 'local_sap'));


        $this->mform->addElement('advcheckbox', 'sap_course_none', get_string('sap_course_none', 'local_sap'));

        $this->mform->addElement('submit', 'submitbutton', get_string('submitbutton', 'local_sap'),);


    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $count = 0;
        $count += $this->get_submitted_data('sap_course_teacher');
        $count += $this->get_submitted_data('sap_course_authorized');
        $count += $this->get_submitted_data('sap_course_none');
        var_dump($this->get_submitted_data());
        if ( $count != 1) {
            $errors['shortname'] = get_string('mustselectone', 'local_sap');
        }

        return $errors;
    }

    function get_option() {
        if ($this->get_submitted_data('sap_course_teacher') == 1) {
            $this->option = 0;
        } else if($this->get_submitted_data('sap_course_authorized') == 1) {
            $this->option=1;
        } else if ($this->get_submitted_data('sap_course_none') == 1) {
            $this->option = 2;
        }
        return $this->option;
    }

    function get_courseid() {
        foreach ($this->courses as $course) {

        }
        return $this->courseid;
}
    function show_courses() {
        $this->courses = array();
        $this->courses[] = $this->mform->createElement('radio', 'courses', '', 'course1: einführung in die informatik',0);

        $this->courses[] = $this->mform->createElement('radio', 'courses', '', 'course2: einführung in die moodletechnik',0);

        $this->courses[] = $this->mform->createElement('radio', 'courses', '', 'course3: einführung in die mensapreise',0);

        $this->mform->addGroup($this->courses, 'sap_courses', '', '<br/>', false);

        $this->mform->hideif('sap_courses', 'sap_course_teacher');
    }
}
