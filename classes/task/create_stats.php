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
 * @copyright  2021, Yuriy Yurinskiy <moodle@krsk.dev>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_everyday_stats\task;

defined('MOODLE_INTERNAL') || die();

/**
 * @copyright  2021, Yuriy Yurinskiy <moodle@krsk.dev>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_stats extends \core\task\scheduled_task
{
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name()
    {
        return get_string('task_create_stats', 'report_everyday_stats');
    }

    /**
     * Execute the task.
     */
    public function execute()
    {
        global $DB;

        mtrace("Task 'Create stats' started");

        \report_everyday_stats\lib::create_stats();

        mtrace("Task 'Create stats' finished");
    }
}
