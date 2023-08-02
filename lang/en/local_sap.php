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

/**
 * Plugin strings are defined here.
 *
 * @package     local_sap
 * @category    string
 * @copyright   2023 Uni Münster 
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'SAP Course Application';
$string['sap_course_teacher'] = 'The course exists on the SAP platform and you are registered as a teacher for that course';
$string['sap_course_authorized'] = 'The course exists on the SAP platform and you are authorized to create this course on behalf of  a registered teacher';
$string['sap_course_none'] = 'None of the above apply and you are authorized to create this course in the Learnweb';
$string['submitbutton'] = 'Submit';
$string['mustselectone'] = 'Unknown parameter, please select exactly 1 option';

// Strings of the course_form.php.
$string['shortnamehint'] = 'Shortname must contain {$a} at the end';
$string['config_enrol'] = 'Enrolment';
$string['config_enrolment_key'] = 'Self Enrolment Key';
$string['config_enrolment_key_help'] = "If you only want students with knowledge of a specific password to enrol, then specify your password wish. If you want every student to be able to enrol, just leave the textbox empty.";
$string['config_category'] = 'Category';
$string['config_category_wish'] = 'Category Relocation Wish';
$string['config_category_wish_help'] = "If you have a wish to get your course moved into a more specific category, please leave a comment here containing your wish-category and path.";
$string['config_course_semester'] = 'Term';
$string['shortnameinvalid'] = 'Shortname is invalid (it must contain {$a} at the end)';
