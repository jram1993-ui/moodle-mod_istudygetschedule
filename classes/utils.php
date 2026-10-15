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
 * Class utils
 *
 * @package    mod_istudygetschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    // Break up the string and return the course info - subject, teacher, section.
    public static function get_mycourse($mycourse) {
        $course_info = new \stdClass();
        $course_info->subject = strstr($mycourse, '/', true);
        $mycourse = strstr($mycourse, "/");
        $mycourse = substr($mycourse, 1);
        $course_info->teacher = strstr($mycourse, '/', true);
        $mycourse = strstr($mycourse, "/");
        $mycourse = substr($mycourse, 1);
        $course_info->section = trim($mycourse);
        $course_info->room = teacher_info::get_advisor_room($course_info->teacher);
        return $course_info;
    }

    // **Calculate the grade level from the graduation year.
    public static function get_gradelevel($grad_year) {
        $currentmonth = date('m');
        $currentyear = date('Y');
        if ($grad_year <= $currentyear) {
            $years_left = 0;
        }
        else if ($currentmonth > 7) {
            $years_left = $grad_year - ($currentyear + 1);
        }
        else {
            $years_left = $grad_year - $currentyear;
        }
        $my_grade_level = 12 - $years_left;
        return $my_grade_level;
    }

    // Get the years for that start and end of the school year.
    public static function get_schoolyear($school_term) {
        // Find the beginning and the end of the current school year
        $month_today = strval(date("m"));
        if ($month_today >= 8) {
            $year_start = strval(date("Y"));
        }
        else {
            $year_start = strval(date("Y")) - 1;
        }
        $year_end = $year_start + 1;
        if ($school_term == 'fall' OR $school_term == 'Fall') {
            return $year_start;
        }
        else {
            return $year_end;
        }
    }

    // **Get the user_info_field id to use in searching user_info_data.
    public static function get_fieldid($item_name) {
        global $DB;
        $item = $DB->get_record('user_info_field', ['shortname' => $item_name], 'id');
        return $item->id;
    }

    // **Search user_info_data for student data using the student id and user_info_field id.
    public static function get_field_data($student_id, $itemid) {
        global $DB;
        if ($DB->record_exists('user_info_data', ['userid'=>$student_id, 'fieldid'=>$itemid])) {
            $search_results = $DB->get_record('user_info_data', ['userid'=>$student_id, 'fieldid'=>$itemid], 'data');
            return $search_results->data;
        }
        else {
            return '';
        }
    }

    // **Replace the dash in the advisor name with a comma and a space.
    public static function put_comma_in_name($myteacher) {
        $postion = strrpos($myteacher,"-");
        $name_with_dash = substr($myteacher, $postion);
        $name_with_comma = ', ' . substr($name_with_dash,1);
        $new_name = str_replace($name_with_dash,$name_with_comma,$myteacher);
        return $new_name;
    }
}