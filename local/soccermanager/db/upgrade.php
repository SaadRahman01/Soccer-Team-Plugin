<?php
/**
 * Upgrade script for the soccer manager plugin
 *
 * @package    local_soccermanager
 * @copyright  2025 Saad Rahman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_soccermanager_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025061100) {
        // Initial installation - no upgrades needed yet
        upgrade_plugin_savepoint(true, 2025061100, 'local', 'soccermanager');
    }

    return true;
}
