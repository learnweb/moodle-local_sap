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
 * Class for connecting to SAP.
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_sap;

/**
 * Class for connecting to SAP.
 *
 * @package     local_sap
 * @copyright   2023 Uni Münster
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pg_lite {

    public $connection = null;

    public function connect() {
        $config = "host='" . get_config('local_sap', 'dbhost') . "' port ='" .
            get_config('local_sap', 'dbport') . "' user='" . get_config('local_sap', 'dbuser') .
            "' password='" . get_config('local_sap', 'dbpass') . "' dbname='" .
            get_config('local_sap', 'dbname') . "'";
        ob_start();
        $this->connection = pg_connect($config, PGSQL_CONNECT_FORCE_NEW);
        $dberr = ob_get_contents();
        ob_end_clean();
        echo $dberr;
        return ((pg_connection_status($this->connection) === false) || (pg_connection_status($this->connection) === PGSQL_CONNECTION_BAD))?$dberr:true;
    }

    public function dispose() {
        if ($this->connection) {
            pg_close($this->connection);
            $this->connection = null;
        }
    }

}