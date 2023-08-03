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

// Strings for settings page.
// Connection settings.
$string['heading_connection_settings'] = 'Connection Settings';
$string['dbhost'] = 'Host';
$string['dbport'] = 'Port';
$string['dbname'] = 'Name';
$string['dbpass'] = 'Pass';
$string['dbuser'] = 'User';

// Import settings.
$string['heading_import_settings'] = 'Import Settings';
$string['max_import_age'] = 'Maximum age of courses that can be imported';
$string['subcategories'] = 'Allow placement in subcategories';
$string['defaultcategory'] = 'Default category';
$string['remote_creation'] = 'Remote course creation';
$string['remote_creation_desc'] = 'Allow everyone to request courses in the name of a teacher, who than has to confirm';

