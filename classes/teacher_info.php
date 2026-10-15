<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_istudygetschedule;

/**
 * Class teacher_info
 *
 * @package    mod_istudygetschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class teacher_info {

    /**
     * Gets the advisor ($myteacher) room number.
     *
     * @param [type] $myteacher
     * @return void
     */
    public static function get_advisor_room($myteacher) {
        global $DB;
        // Separate the first name and last name that are joined with a dash (-).
        $teacher_lastname = strstr($myteacher, '-', true);
        $myteacher_temp = strstr($myteacher, '-');
        $teacher_firstname = substr($myteacher_temp, 1);

        $this_teacher = new \stdClass();
        $sql = "SELECT * FROM {istudyteachers} WHERE lastname = '" .  $teacher_lastname . "' AND firstname = '" .  $teacher_firstname . "'";
        $params = null;
        $this_teacher = $DB->get_record_sql($sql, $params);

        return $this_teacher->room;
    }
}