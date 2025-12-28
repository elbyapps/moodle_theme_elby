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

/**
 * Custom renderer for theme_elby.
 *
 * @package    theme_elby
 * @copyright  2025 REB Rwanda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_elby\output;

defined('MOODLE_INTERNAL') || die;

use moodle_url;
use theme_boost\output\core_renderer as boost_core_renderer;

/**
 * Custom renderer - overrides core output methods.
 */
class core_renderer extends boost_core_renderer {

    /**
     * Return favicon URL from theme settings or default.
     *
     * @return string The favicon URL.
     */
    public function favicon() {
        $favicon = $this->page->theme->setting_file_url('favicon', 'favicon');
        if (!empty($favicon)) {
            return $favicon;
        }
        return parent::favicon();
    }

    /**
     * Return compact logo URL.
     *
     * @param int|null $maxwidth Maximum width.
     * @param int|null $maxheight Maximum height.
     * @return moodle_url|null The logo URL or null.
     */
    public function get_compact_logo_url($maxwidth = null, $maxheight = null) {
        $logo = $this->page->theme->setting_file_url('logocompact', 'logocompact');
        if (!empty($logo)) {
            // setting_file_url returns full URL, extract path only for moodle_url
            $path = parse_url($logo, PHP_URL_PATH);
            if ($path) {
                return new moodle_url($path);
            }
        }
        // Fall back to main logo if compact not set.
        $mainlogo = $this->page->theme->setting_file_url('logo', 'logo');
        if (!empty($mainlogo)) {
            $path = parse_url($mainlogo, PHP_URL_PATH);
            if ($path) {
                return new moodle_url($path);
            }
        }
        return parent::get_compact_logo_url($maxwidth, $maxheight);
    }

    /**
     * Return full logo URL.
     *
     * @param int|null $maxwidth Maximum width.
     * @param int|null $maxheight Maximum height.
     * @return moodle_url|null The logo URL or null.
     */
    public function get_logo_url($maxwidth = null, $maxheight = null) {
        $logo = $this->page->theme->setting_file_url('logo', 'logo');
        if (!empty($logo)) {
            // setting_file_url returns full URL, extract path only for moodle_url
            $path = parse_url($logo, PHP_URL_PATH);
            if ($path) {
                return new moodle_url($path);
            }
        }
        return parent::get_logo_url($maxwidth, $maxheight);
    }
}
