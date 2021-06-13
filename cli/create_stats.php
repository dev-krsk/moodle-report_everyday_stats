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

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php'); // global moodle config file.
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/clilib.php');


set_debugging(DEBUG_DEVELOPER, true);

$taskdisabled = \core\task\manager::get_scheduled_task('report_every_day\task\create_stats');

cli_problem('[REPORT EVERYDAY STATS] The create stats cron has been deprecated. Please use the scheduled task instead.');

if (!$taskdisabled->get_disabled()) {
    cli_error('[REPORT EVERYDAY STATS] The scheduled task create_stats is enabled, the cron execution has been aborted.');
}

mtrace("[REPORT EVERYDAY STATS] Task 'Create stats' started");

\report_everyday_stats\lib::create_stats();

mtrace("[REPORT EVERYDAY STATS] Task 'Create stats' finished");
