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

class sql_query
{
    const ROLE_TEACHER = 3;
    const ROLE_ASSISTANT = 4;
    const ROLE_STUDENT = 5;

    /**
     * Возвращает количество успешных попыток авторизации между датами
     *
     * @param int $role
     * @param int $dBeg
     * @param int $dEnd
     * @return int
     * @throws \dml_exception
     * @throws \Exception
     */
    public static function cnt_logged_in(int $role, int $dBeg, int $dEnd) {
        global $DB;

        if (!in_array($role, [self::ROLE_STUDENT, self::ROLE_ASSISTANT, self::ROLE_TEACHER])) {
            throw new \Exception('Неизвестная роль');
        }

        $sql = 'SELECT count(distinct u.id)
FROM {user} u
         LEFT JOIN {role_assignments} a ON a.userid = u.id
         LEFT JOIN {context} b ON a.contextid = b.id
         LEFT JOIN {course} c ON b.instanceid = c.id
WHERE u.username <> \'guest\' -- отсееваем учетку гостя
  AND u.suspended = 0       -- отсееваем заблокированных пользователей
  AND a.roleid = :role          -- 3 - преподаватель, 4 - ассистент, 5 - студент
  AND b.contextlevel = 50   -- 50 - курсы, 40 - категории
  AND c.id is not null
  AND EXISTS( SELECT 1
           FROM {logstore_standard_log} l
           WHERE l.userid = u.id
           AND l.timecreated BETWEEN :dbeg AND :dend -- ограничение по датам
           AND l.contextlevel = 10   
           AND l.component = \'core\'
           AND l.action = \'loggedin\'
           AND l.target = \'user\' )';
        $params = [
            'role' => $role,
            'dbeg' => $dBeg,
            'dend' => $dEnd,
        ];

        $result = $DB->count_records_sql($sql, $params);

        if (is_numeric($result)) {
            return $result;
        }

        return 0;
    }
}