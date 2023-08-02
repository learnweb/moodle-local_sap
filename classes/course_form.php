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
class course_form extends \moodleform {
    protected $sapid;
    protected $sap_course;

    function definition() {
        global $USER, $CFG, $DB;

        $mform    =& $this->_form;

        $this->sapid = $this->_customdata['sapid'];
       // $this->sap_course = get_course_by_veranstid_sap($this->sapid);

        $mform->addElement('header','general', get_string('general', 'form'));

        $mform->addElement('hidden', 'sapid', null);
        $mform->setType('sapid', PARAM_INT);
        $mform->setConstant('sapid', $this->sapid);
        $mform->addElement('hidden', 'answer', null);
        $mform->setType('answer', PARAM_INT);
        $mform->setConstant('answer', 1);

        $mform->addElement('text','fullname', get_string('fullnamecourse'),'maxlength="254" size="80"');
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);
       // $mform->setDefault('fullname', get_default_fullname_sap($this->sap_course));


        $mform->addElement('text', 'shortname', get_string('shortnamecourse'), 'maxlength="100" size="30"');
        $mform->addHelpButton('shortname', 'shortnamecourse');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);
        //$mform->setDefault('shortname', get_default_shortname_sap($this->sap_course));

        //$mform->addElement('html', '<i>'.get_string('shortnamehint', 'local_sap', shortname_hint($this->sap_course)).'</i>');

        $mform->addElement('text','idnumber', get_string('idnumbercourse'),'maxlength="100"  size="10"');
        $mform->addHelpButton('idnumber', 'idnumbercourse');
        $mform->setType('idnumber', PARAM_RAW);
        $mform->hardFreeze('idnumber');
        $mform->setConstant('idnumber', $this->sapid);

        $mform->addElement('textarea', 'summary', null);
        $mform->setType('summary', PARAM_RAW);
        //$mform->setDefault('summary', get_default_summary_sap($this->sap_course));
        $mform->addElement('date_selector', 'startdate', get_string('startdate'));
        $mform->addHelpButton('startdate', 'startdate');
        //$mform->setDefault('startdate', get_default_startdate_sap($this->sap_course));

        $mform->addElement('header','enrol', get_string('config_enrol', 'local_sap'));
        $mform->setExpanded('enrol');

        $mform->addElement('passwordunmask','enrolment_key', get_string('config_enrolment_key','local_sap'),'maxlength="100"  size="10"');
        $mform->setType('enrolment_key', PARAM_RAW);
        $mform->addHelpButton('enrolment_key', 'config_enrolment_key','local_sap');
        $mform->disabledIf('enrolment_key', 'selfenrolment', 'neq', 1);

        $mform->addElement('header','categoryheader', get_string('config_category', 'local_sap'));

        $displaylist = \core_course_category::make_categories_list('moodle/course:request');
        $mform->addElement('autocomplete', 'category', get_string('coursecategory'), $displaylist);
        $mform->addRule('category', null, 'required', null, 'client');
        $mform->setDefault('category', "");
        $mform->addHelpButton('category', 'coursecategory');

        $mform->addElement('textarea','category_wish', get_string('config_category_wish','local_sap'),'');
        $mform->addHelpButton('category_wish', 'config_category_wish','local_sap');
        $mform->setType('enrolment_key', PARAM_RAW);

        $mform->addElement('header','semesterheader', get_string('config_course_semester', 'local_sap'));

        $semesterfieldname = 'semester';
        if ($field = $DB->get_record('customfield_field', array('shortname' => $semesterfieldname, 'type' => 'semester'))) {
            $fieldcontroller = \core_customfield\field_controller::create($field->id);
            $datacontroller = \core_customfield\data_controller::create(0, null, $fieldcontroller);
            $datacontroller->instance_form_definition($mform);

            //$mform->setDefault('customfield_' . $shortname, 27); // TODO
            // TODO default <=> lsf data!
        }

        $this->add_action_buttons();

    }

    //function definition_after_data() {
    //}


    /// perform some extra moodle validation
    function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);
        if ($foundcourses = $DB->get_records('course', array('shortname'=>$data['shortname']))) {
            if (!empty($data['id'])) {
                unset($foundcourses[$data['id']]);
            }
            if (!empty($foundcourses)) {
                foreach ($foundcourses as $foundcourse) {
                    $foundcoursenames[] = $foundcourse->fullname;
                }
                $foundcoursenamestring = implode(',', $foundcoursenames);
                $errors['shortname']= get_string('shortnametaken', '', $foundcoursenamestring);
            }
        }

        //if (!is_shortname_valid($this->lsf_course, $data['shortname'])) {
          //  $errors['shortname']= get_string('shortnameinvalid', 'local_sap', shortname_hint($this->lsf_course));
        //}

        /*$categories = get_courses_categories($this->veranstid, false);
        if (empty($data['category']) || !isset($categories[$data['category']])) {
            $errors['category']= get_string('categoryinvalid', 'local_sap');
        }*/

        return $errors;
    }
}