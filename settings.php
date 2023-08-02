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
 * Plugin administration pages are defined here.
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster 
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_sap_settings', new lang_string('pluginname', 'local_sap'));

    // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
    if ($ADMIN->fulltree) {

        $settings->add(new admin_setting_heading('heading_connection',
            get_string('heading_connection_settings', 'local_sap'), ''));

        $settings->add(new admin_setting_configtext('local_sap/dbhost',
            get_string('dbhost', 'local_sap'),  '',
            '', PARAM_TEXT));
        $settings->add(new admin_setting_configtext('local_sap/dbport',
            get_string('dbport', 'local_sap'), '',
            '', PARAM_INT));
        $settings->add(new admin_setting_configtext('local_sap/dbuser',
            get_string('dbuser', 'local_sap'), '',
            '', PARAM_TEXT));
        $settings->add(new admin_setting_configpasswordunmask('local_sap/dbpass',
            get_string('dbpass', 'local_sap'), '',
            '', PARAM_RAW));
        $settings->add(new admin_setting_configtext('local_sap/dbname',
            get_string('dbname', 'local_sap'), '',
            '', PARAM_TEXT));

        $settings->add(new admin_setting_heading('heading_import',
            get_string('heading_import_settings', 'local_sap'), ''));

        $settings->add(new admin_setting_configtext('local_sap/max_import_age',
            get_string('max_import_age', 'local_sap'), '',
            365, PARAM_INT));

        // JN: Removed setting for teacher and student role id.

        $settings->add(new admin_setting_configcheckbox('local_sap/subcategories',
            get_string('subcategories', 'local_sap'), '',
            0));

        $displaylist = core_course_category::make_categories_list();
        $settings->add(new admin_setting_configselect('local_sap/defaultcategory',
            get_string('defaultcategory', 'local_sap'), '',
            1, $displaylist));

        $settings->add(new admin_setting_configcheckbox('local_sap/remote_creation',
            get_string('remote_creation', 'local_sap'),
            get_string('remote_creation_desc', 'local_sap'), 1));

    }

    $ADMIN->add('localplugins', $settings);
}
