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
 * Class student_info
 *
 * @package    mod_istudygetschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class student_info {

    /**
     * Get the student weekly class schedule.
     *
     * @param [type] $submission
     * @return void
     */
    public static function get_schedule($submission) {

        $student_id = $submission['id'];
        $school_term = $submission['schoolterm'];

        // Get the student information
        $student_info = student_info::get_singlestudent($student_id, $school_term);

        //Print the student schedule on a new tab using HTML
        $student_schedule = student_info::get_studentcardlayout($student_info);

        return $student_schedule;
    }

    /**
     * Get the student schedule for the Moodle mobile app.
     *
     * @param [type] $submission
     * @return void
     */
    public static function get_mobile_schedule($submission) {

        $student_id = $submission['id'];
        $school_term = $submission['schoolterm'];
        $student_info = student_info::get_mobile_singlestudent($student_id, $school_term);

        return $student_info;
    }

    /**
     * Get single student record for the mobile app.
     *
     * @param [int] $student_id
     * @param [test] $school_term
     * @return student information
     */
     public static function get_mobile_singlestudent($student_id, $school_term) {
        global $DB;

        $week_day[1] = 'monday';
        $week_day[2] = 'tuesday';
        $week_day[3] = 'wednesday';
        $week_day[4] = 'thursday';
        $week_day[5] = 'friday';
        $number_of_sessions = get_config('mod_istudy', 'number_of_sessions');

        // Student Info
        $student_info['id'] = $student_id;
        $student_temp = $DB->get_record('user', ['id' => $student_id] , '*');
        $student_info['lastname'] = $student_temp->lastname;
        $student_info['firstname'] = $student_temp->firstname;
        $student_info['schoolid'] = utils::get_field_data($student_id, utils::get_fieldid('schoolid'));
        $student_info['term'] = $school_term;
        $student_info['gradelevel'] = utils::get_gradelevel(utils::get_field_data($student_id, utils::get_fieldid('graduationyear')));
        $my_advisor = utils::get_field_data($student_id, utils::get_fieldid('advisor'));
        $student_info['advisor'] = $my_advisor;

        $mystudent['term'] = ' Student Schedule - ' . $student_info['term'] . ' ' . utils::get_schoolyear($school_term);
        $mystudent['name'] = $student_info['lastname'] . ', ' . $student_info['firstname'] . ' (ID# ' . $student_info['schoolid'] . ') - ' . $student_info['gradelevel'] . 'th grade';
        $mystudent['advisor'] = 'Advisor: ' . utils::put_comma_in_name($student_info['advisor']);

        // Student courses
        $summer = "";
        if ($school_term == 'Summer') {
            $summer = 'summer_'; }

                // Monday-Friday courses
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= $number_of_sessions; $j++) {
                $student_info[$week_day[$i] . 'session' . $j . 'time'] = get_config('mod_istudy', $summer . $week_day[$i] . 'session' . $j);
                $student_info[$week_day[$i] . 'session' . $j . 'course'] =  utils::get_field_data($student_id, utils::get_fieldid($school_term . $week_day[$i] . 'session' . $j));
                $mycourse_info = utils::get_mycourse($student_info[$week_day[$i] . 'session' . $j . 'course']);
                $student_info[$week_day[$i] . 'session' . $j . 'credits'] = utils::get_field_data($student_id, utils::get_fieldid($school_term . $week_day[$i] . 'creditssession' . $j));

                $mystudent[$week_day[$i] . 'time' . $j] = 'Period '. $j . ': ' . $student_info[$week_day[$i] . 'session' . $j . 'time'];

                if ($student_info[$week_day[$i] . 'session' . $j . 'course'] <> 'None') {
                    $mystudent[$week_day[$i] . 'course' . $j] = 'Course: ' . $mycourse_info->subject . ' (' . $student_info[$week_day[$i] . 'session' . $j . 'credits'] . ' credits)';
                    $mystudent[$week_day[$i] . 'teacher' . $j] = 'Teacher: '. utils::put_comma_in_name($mycourse_info->teacher) . ' - Room: ' . $mycourse_info->room;
                }
                else {
                    $mystudent[$week_day[$i] . 'course' . $j] = 'None';
                    $mystudent[$week_day[$i] . 'teacher' . $j] = '';
                }
            }
        }
        return $mystudent;
    }

    /**
     * Get a single student information.
     *
     * @param [type] $student_id
     * @param [type] $school_term
     * @return student information
     */
    public static function get_singlestudent($student_id, $school_term) {
        global $DB;

        $week_day[1] = 'monday';
        $week_day[2] = 'tuesday';
        $week_day[3] = 'wednesday';
        $week_day[4] = 'thursday';
        $week_day[5] = 'friday';
        $number_of_sessions = get_config('mod_istudy', 'number_of_sessions');

        // Student Info
        $student_info = new \stdClass();
        $student_info->id = $student_id;
        $student_temp = $DB->get_record('user', ['id' => $student_id] , '*');
        $student_info->lastname = $student_temp->lastname;
        $student_info->firstname = $student_temp->firstname;
        $student_info->schoolid = utils::get_field_data($student_id, utils::get_fieldid('schoolid'));
        $student_info->term = $school_term;
        $student_info->gradelevel = utils::get_gradelevel(utils::get_field_data($student_id, utils::get_fieldid('graduationyear')));
        $my_advisor = utils::get_field_data($student_id, utils::get_fieldid('advisor'));
        $student_info->advisor = $my_advisor;

        // Student courses
        $summer = "";
        if ($school_term == 'Summer') {
            $summer = 'summer_'; }

        // Monday-Friday courses {strtolower($week_day[$i]).'session1course'}
        for ($i = 1; $i <= 5; $i++) {
            for ($j = 1; $j <= $number_of_sessions; $j++) {
                $student_info->{$week_day[$i] . 'session' . $j . 'time'} = get_config('mod_istudy', $summer . $week_day[$i] . 'session' . $j);
            $student_info->{$week_day[$i] . 'session' . $j . 'course'} =  utils::get_field_data($student_id, utils::get_fieldid($school_term . $week_day[$i] . 'session' . $j));
                $student_info->{$week_day[$i] . 'session' . $j . 'credits'} = utils::get_field_data($student_id, utils::get_fieldid($school_term . $week_day[$i] . 'creditssession' . $j));
            }
        }
        return $student_info;
    }

    /**
     * Create the student card for his or her weekly schedule.
     *
     * @param [object] $student_info
     * @return student schedule html file
     */
    public static function get_studentcardlayout($student_info) {

        $week_day[1] = 'Monday';
        $week_day[2] = 'Tuesday';
        $week_day[3] = 'Wednesday';
        $week_day[4] = 'Thursday';
        $week_day[5] = 'Friday';
        $number_of_sessions = get_config('mod_istudy', 'number_of_sessions');

        $student_schedule = '<link rel="stylesheet" type="text/css" href="styles.css"><html><p>' .
                            get_config('mod_istudy', 'schoolname') . '<br>';

        $student_schedule = $student_schedule . get_config('mod_istudy', 'schooladdress') . '<br>';
        $student_schedule = $student_schedule . get_config('mod_istudy', 'schoolcity') . ', '
                                            . get_config('mod_istudy', 'schoolstate') . ' '
                                            . get_config('mod_istudy', 'schoolzip') . '</p>';
        $student_schedule = $student_schedule . '<h1>Student Schedule - '. $student_info->term . ' ' . $student_info->year . '</h1>';
        $student_schedule = $student_schedule .
            '<table class="table1"><tr><col width="35%"><col width="20%"><col width="10%"><col width="35%"><b><u><th>Student Name</th><th>ID #</th><th>Grade</th><th>Advisor</th></u></b></tr>' .
            '<tr><td>' . $student_info->lastname . ', ' . $student_info->firstname . '</td><td>' . $student_info->schoolid .
            '</td><td>' . $student_info->gradelevel . '</td><td>' . utils::put_comma_in_name($student_info->advisor) . '</td></tr></table><hr>';
        for ($i = 1; $i <= 5; $i++) {
            $student_schedule = $student_schedule . '<h4><center>' . $week_day[$i] . '</center></h4>';
            $classes_found = 0;
            for ($this_session = 1; $this_session <= $number_of_sessions; $this_session++) {
                $mysessions[$this_session] = $student_info->{strtolower($week_day[$i]).'session' . $this_session . 'course'};
                if ($mysessions[$this_session] == 'None') {
                    $classes_found = $classes_found + 1;
                }
            }
            if ($classes_found == $number_of_sessions) {
                $student_schedule = $student_schedule . '<h2>None</h2>';
            }
            else {
                $student_schedule = $student_schedule .
                    '<table class="table2">' .
                    '<tr><col width="3%"><col width="22%"><col width="30%"><col width="5%"><col width="25%"><col width="15%">' .
                    '<b><th><center>Pd</center></th><th><center>Time</center></th><th><center>Course</center></th><th><center>Credits</center></th><th><center>Teacher</center></th><th><center>Room #</center></th></b></tr>';
                for ($count = 1; $count <= $number_of_sessions; $count++) {
                    $mysession = $mysessions[$count];
                    if ($mysession <> 'None'){
                        $mycourse = new \stdClass();
                        $mycourse = utils::get_mycourse($mysession);
                        $mycourse->time = $student_info -> {strtolower($week_day[$i]) . 'session' . $count . 'time'};
                        $mycourse->credits = $student_info -> {strtolower($week_day[$i]) . 'session' . $count . 'credits'};
                        $student_schedule = $student_schedule .
                            '<tr><td><center>' . $count . '</center></td><td><center>' . $mycourse->time . '</center></td><td><center>' . $mycourse->subject . '</center></td><td><center>' .
                            $mycourse->credits . '</center></td><td><center>' . utils::put_comma_in_name($mycourse->teacher) . '</center></td><td><center>' . $mycourse->room . '</center></td></tr>';
                    }
                }
                $student_schedule = $student_schedule . '</table><hr>';
            }
        }
        $student_schedule = $student_schedule . '</html>';
        return $student_schedule;
    }
}