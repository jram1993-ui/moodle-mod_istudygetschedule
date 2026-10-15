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

namespace mod_istudygetschedule\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Mobile output class for istudygetschedule
 *
 * @package    mod_istudygetschedule
 * @copyright  2024 Jesus Ramirez <jram1993@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class mobile {

    /**
     * Returns the certificate course view for the mobile app.
     * @param  array $args Arguments from tool_mobile_get_content WS
     *
     * @return array HTML, javascript and otherdata
     */

    public static function mobile_view_activity($args) {
        global $OUTPUT, $USER, $DB, $CFG;

        $args = (object) $args;

		$cmid = $args->cmid;
        $courseid = $args->courseid;
        $cm = get_coursemodule_from_id('istudygetschedule', $cmid);

        // Capabilities check.
        require_login($courseid, true, $cm);

		// Get the course data and the student schedule
		$submission = [
			'id' => $USER->id,
			'schoolterm' => get_config('mod_istudy','schoolterm'),
			];
		$my_schedule = \mod_istudygetschedule\student_info::get_mobile_schedule($submission);

		$data = [
            'courseid' => $courseid,
			'myschedule' => $my_schedule
        ];

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('mod_istudygetschedule/mobile_view_page', $data),
                ],
            ],
            'javascript' => '',
            'otherdata' => '',
            'files' => ''
        ];
    }
}