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
 * Class for connecting to SAP with Moodle API.
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sap;

/**
 * Class for connecting to SAP with Moodle API.
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sapdb {

    /**
     * @var \moodle_database
     */
    private static $instance;

    /**
     * Get a moodledatabase based connection to sap db.
     *
     * @return \moodle_database
     */
    public static function get() {
        if (!self::$instance) {
            self::$instance = \moodle_database::get_driver_instance('pgsql', 'native', true);
            self::$instance->connect(
                get_config('local_sap', 'dbhost'),
                get_config('local_sap', 'dbuser'),
                get_config('local_sap', 'dbpass'),
                get_config('local_sap', 'dbname'),
                false
            );
        }
        return self::$instance;
    }

    /**
     * Destroy a connection to a SAP DB connection.
     * @return void
     */
    public static function destroy() {
        self::$instance->close();
    }

}
