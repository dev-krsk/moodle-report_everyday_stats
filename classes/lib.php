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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * @package report_everyday_stats
 * @copyright 2021, Yuriy Yurinskiy <moodle@krsk.dev>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_everyday_stats;

use core_date;

defined('MOODLE_INTERNAL') || die();

class lib
{
    /**
     * @throws \dml_exception
     * @throws \Exception
     */
    public static function create_stats()
    {
        global $DB;

        $yesterday = new \DateTime("yesterday", core_date::get_server_timezone_object());

        $dBeg = $yesterday->getTimestamp();
        $dEnd = $yesterday->modify('+1day - 1 second')->getTimestamp();

        mtrace(userdate($dBeg));
        mtrace(userdate($dEnd));

        $cntStudent = sql_query::cnt_logged_in(sql_query::ROLE_STUDENT, $dBeg, $dEnd);
        $cntTeacher = sql_query::cnt_logged_in(sql_query::ROLE_TEACHER, $dBeg, $dEnd);
        $cntAssistant = sql_query::cnt_logged_in(sql_query::ROLE_ASSISTANT, $dBeg, $dEnd);

        mtrace(sprintf("[REPORT EVERYDAY STATS] Период  с %s по %s", userdate($dBeg), userdate($dEnd)));
        mtrace("[REPORT EVERYDAY STATS] Количество уникаальных входов студентов: $cntStudent");
        mtrace("[REPORT EVERYDAY STATS] Количество уникаальных входов преподователей: $cntTeacher");
        mtrace("[REPORT EVERYDAY STATS] Количество уникаальных входов ассистентов: $cntAssistant");

        $oldStat = $DB->get_record('report_everyday_stats', ['period' => $dBeg]);

        if (!$oldStat) {
            $stat = new \stdClass();
            $stat->period = $dBeg;
            $stat->cnt_student = $cntStudent;
            $stat->cnt_teacher = $cntTeacher;
            $stat->cnt_assistant = $cntAssistant;

            $DB->insert_record('report_everyday_stats', $stat);

            mtrace("[REPORT EVERYDAY STATS] Статистика за прошлый день сохранена в базе.");
        } else {
            $oldStat->cnt_student = $cntStudent;
            $oldStat->cnt_teacher = $cntTeacher;
            $oldStat->cnt_assistant = $cntAssistant;

            $DB->update_record('report_everyday_stats', $oldStat);

            mtrace("[REPORT EVERYDAY STATS] Статистика за прошлый день обновлена в базе.");
        }
    }
}