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
 * Theme settings helper - prepares data for templates.
 *
 * @package    theme_elby
 * @copyright  2025 REB Rwanda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_elby\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Theme settings helper class.
 *
 * Retrieves theme settings and formats them for use in Mustache templates.
 */
class themesettings {

    /** @var \theme_config The theme configuration object. */
    protected $theme;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->theme = \theme_config::load('elby');
    }

    /**
     * Get hero section data.
     *
     * @return array Hero section template data.
     */
    public function hero_section(): array {
        $settings = $this->theme->settings;

        if (empty($settings->heroenabled)) {
            return ['hashero' => false];
        }

        // Get carousel settings.
        $slidecount = max(1, min(10, (int)($settings->heroslidecount ?? 1)));
        $autorotate = !empty($settings->heroautorotate);
        $interval = (int)($settings->herointerval ?? 5000);

        // Build slides array - include ALL slides up to the configured count.
        $slides = [];
        for ($i = 1; $i <= $slidecount; $i++) {
            $heading = $settings->{"heroslide{$i}heading"} ?? '';
            $subheading = $settings->{"heroslide{$i}subheading"} ?? '';
            $mainimage = $this->theme->setting_file_url("heroslide{$i}mainimage", "heroslide{$i}mainimage");
            $secondaryimage = $this->theme->setting_file_url("heroslide{$i}secondaryimage", "heroslide{$i}secondaryimage");
            $ctabutton = $settings->{"heroslide{$i}ctabutton"} ?? '';
            $ctaurl = $settings->{"heroslide{$i}ctaurl"} ?? '';

            // Use defaults for slide 1 if not set.
            if ($i === 1 && empty($heading)) {
                $heading = "LET'S DESIGN YOUR FUTURE!";
            }
            if ($i === 1 && empty($subheading)) {
                $subheading = 'Our platform inspires a love of learning, encouraging creative thinking across all subjects.';
            }

            $slides[] = [
                'index' => $i - 1,
                'slidenum' => $i,
                'active' => ($i === 1),
                'heading' => $heading,
                'subheading' => format_text($subheading, FORMAT_HTML),
                'ctabutton' => $ctabutton,
                'ctaurl' => !empty($ctaurl) ? $ctaurl : '#',
                'hascta' => !empty($ctabutton),
                'mainimage' => $mainimage,
                'hasmainimage' => !empty($mainimage),
                'secondaryimage' => $secondaryimage,
                'hassecondaryimage' => !empty($secondaryimage),
            ];
        }

        // Ensure at least one slide.
        if (empty($slides)) {
            $slides[] = [
                'index' => 0,
                'slidenum' => 1,
                'active' => true,
                'heading' => "LET'S DESIGN YOUR FUTURE!",
                'subheading' => 'Our platform inspires a love of learning, encouraging creative thinking across all subjects.',
                'ctabutton' => 'Explore Academics',
                'ctaurl' => '/course/',
                'hascta' => true,
                'mainimage' => '',
                'hasmainimage' => false,
                'secondaryimage' => '',
                'hassecondaryimage' => false,
            ];
        }

        $hasmultipleslides = count($slides) > 1;

        return [
            'hashero' => true,
            'heroslides' => $slides,
            'heroslidecount' => count($slides),
            'hasmultipleslides' => $hasmultipleslides,
            'heroautorotate' => $autorotate,
            'herointerval' => $interval,
            // Keep legacy single-slide variables for backwards compatibility.
            'heroheading' => $slides[0]['heading'] ?? '',
            'herosubheading' => $slides[0]['subheading'] ?? '',
            'heroctabutton' => $slides[0]['ctabutton'] ?? '',
            'heroctaurl' => $slides[0]['ctaurl'] ?? '#',
            'hascta' => $slides[0]['hascta'] ?? false,
            'heroimage' => $slides[0]['mainimage'] ?? '',
            'hasheroimage' => $slides[0]['hasmainimage'] ?? false,
            'herosecondaryimage' => $slides[0]['secondaryimage'] ?? '',
            'hasherosecondaryimage' => $slides[0]['hassecondaryimage'] ?? false,
        ];
    }

    /**
     * Get marketing blocks data.
     *
     * @return array Marketing blocks template data.
     */
    public function marketing_blocks(): array {
        $settings = $this->theme->settings;

        if (empty($settings->marketingenabled)) {
            return ['hasmarketing' => false];
        }

        $blocks = [];
        $count = (int)($settings->marketingcount ?? 3);

        for ($i = 1; $i <= $count; $i++) {
            $title = $settings->{"marketing{$i}title"} ?? '';
            if (!empty($title)) {
                $blocks[] = [
                    'icon' => $settings->{"marketing{$i}icon"} ?? 'fa-star',
                    'title' => $title,
                    'content' => format_text($settings->{"marketing{$i}content"} ?? '', FORMAT_HTML),
                    'buttontext' => $settings->{"marketing{$i}buttontext"} ?? '',
                    'buttonurl' => $settings->{"marketing{$i}buttonurl"} ?? '#',
                    'hasbutton' => !empty($settings->{"marketing{$i}buttontext"}),
                ];
            }
        }

        return [
            'hasmarketing' => !empty($blocks),
            'marketingtitle' => $settings->marketingtitle ?? '',
            'marketingsubtitle' => $settings->marketingsubtitle ?? '',
            'marketingblocks' => $blocks,
        ];
    }

    /**
     * Get statistics section data.
     *
     * @return array Statistics template data.
     */
    public function statistics(): array {
        $settings = $this->theme->settings;

        if (empty($settings->statsenabled)) {
            return ['hasstats' => false];
        }

        $stats = [];
        for ($i = 1; $i <= 4; $i++) {
            $value = $settings->{"stat{$i}value"} ?? '';
            if (!empty($value)) {
                $stats[] = [
                    'value' => $value,
                    'suffix' => $settings->{"stat{$i}suffix"} ?? '',
                    'label' => $settings->{"stat{$i}label"} ?? '',
                    'icon' => $settings->{"stat{$i}icon"} ?? 'fa-star',
                ];
            }
        }

        return [
            'hasstats' => !empty($stats),
            'statstitle' => $settings->statstitle ?? '',
            'statssubtitle' => $settings->statssubtitle ?? '',
            'stats' => $stats,
        ];
    }

    /**
     * Get testimonials section data.
     *
     * @return array Testimonials template data.
     */
    public function testimonials(): array {
        $settings = $this->theme->settings;

        if (empty($settings->testimonialsenabled)) {
            return ['hastestimonials' => false];
        }

        $testimonials = [];
        $count = (int)($settings->testimonialcount ?? 3);

        for ($i = 1; $i <= $count; $i++) {
            $name = $settings->{"testimonial{$i}name"} ?? '';
            if (!empty($name)) {
                $image = $this->theme->setting_file_url("testimonial{$i}image", "testimonial{$i}image");
                $testimonials[] = [
                    'image' => $image,
                    'hasimage' => !empty($image),
                    'name' => $name,
                    'role' => $settings->{"testimonial{$i}role"} ?? '',
                    'quote' => format_text($settings->{"testimonial{$i}quote"} ?? '', FORMAT_HTML),
                ];
            }
        }

        return [
            'hastestimonials' => !empty($testimonials),
            'testimonialstitle' => $settings->testimonialstitle ?? '',
            'testimonialssubtitle' => $settings->testimonialssubtitle ?? '',
            'testimonials' => $testimonials,
        ];
    }

    /**
     * Get footer data.
     *
     * @return array Footer template data.
     */
    public function footer(): array {
        $settings = $this->theme->settings;

        $copyright = str_replace(
            '{year}',
            date('Y'),
            $settings->copyrighttext ?? '© {year} All Rights Reserved.'
        );

        $sociallinks = [];
        $socialicons = [
            'facebook' => 'fa-facebook-f',
            'twitter' => 'fa-x-twitter',
            'instagram' => 'fa-instagram',
            'linkedin' => 'fa-linkedin-in',
            'youtube' => 'fa-youtube',
        ];

        foreach ($socialicons as $network => $icon) {
            $url = $settings->{$network . 'url'} ?? '';
            if (!empty($url)) {
                $sociallinks[] = [
                    'url' => $url,
                    'icon' => $icon,
                    'name' => ucfirst($network),
                ];
            }
        }

        return [
            'footercontent' => format_text($settings->footercontent ?? '', FORMAT_HTML),
            'hasfootercontent' => !empty(trim(strip_tags($settings->footercontent ?? ''))),
            'copyrighttext' => $copyright,
            'sociallinks' => $sociallinks,
            'hassocialmedia' => !empty($sociallinks),
            'footercolumn1title' => $settings->footercolumn1title ?? '',
            'footercolumn1content' => format_text($settings->footercolumn1content ?? '', FORMAT_HTML),
            'footercolumn2title' => $settings->footercolumn2title ?? '',
            'footercolumn2content' => format_text($settings->footercolumn2content ?? '', FORMAT_HTML),
            'footercolumn3title' => $settings->footercolumn3title ?? '',
            'footercolumn3content' => format_text($settings->footercolumn3content ?? '', FORMAT_HTML),
        ];
    }

    /**
     * Get campus life section data.
     *
     * @return array Campus life template data.
     */
    public function campus_life(): array {
        global $SITE;
        $settings = $this->theme->settings;

        if (empty($settings->campuslifeenabled)) {
            return ['hascampuslife' => false];
        }

        $title = str_replace('{sitename}', $SITE->shortname, $settings->campuslifetitle ?? 'Campus life');

        $categories = [];
        for ($i = 1; $i <= 4; $i++) {
            $label = $settings->{"campuslife{$i}label"} ?? '';
            $image = $this->theme->setting_file_url("campuslife{$i}image", "campuslife{$i}image");
            if (!empty($label) && !empty($image)) {
                $categories[] = [
                    'label' => $label,
                    'image' => $image,
                    'active' => ($i === 1),
                    'index' => $i,
                ];
            }
        }

        return [
            'hascampuslife' => !empty($categories),
            'campuslifetitle' => $title,
            'campuslifecategories' => $categories,
        ];
    }

    /**
     * Get announcements section data.
     *
     * @return array Announcements template data.
     */
    public function announcements(): array {
        $settings = $this->theme->settings;

        if (empty($settings->announcementsenabled)) {
            return ['hasannouncements' => false];
        }

        $content = format_text($settings->announcementscontent ?? '', FORMAT_HTML);

        return [
            'hasannouncements' => !empty(trim(strip_tags($content))),
            'announcementstitle' => $settings->announcementstitle ?? '',
            'announcementscontent' => $content,
            'announcementslinktext' => $settings->announcementslinktext ?? 'View all',
            'announcementslinkurl' => $settings->announcementslinkurl ?? '#',
            'hasannouncementslink' => !empty($settings->announcementslinkurl),
        ];
    }

    /**
     * Get events section data.
     *
     * @return array Events template data.
     */
    public function events(): array {
        $settings = $this->theme->settings;

        if (empty($settings->eventsenabled)) {
            return ['hasevents' => false];
        }

        $events = [];
        for ($i = 1; $i <= 3; $i++) {
            $title = $settings->{"event{$i}title"} ?? '';
            $date = $settings->{"event{$i}date"} ?? '';
            if (!empty($title)) {
                $image = $this->theme->setting_file_url("event{$i}image", "event{$i}image");

                // Parse date for display (day number and month).
                $day = '';
                $month = '';
                if (!empty($date)) {
                    $timestamp = strtotime($date);
                    if ($timestamp) {
                        $day = date('d', $timestamp);
                        $month = date('M', $timestamp);
                    }
                }

                $events[] = [
                    'title' => $title,
                    'date' => $date,
                    'day' => $day,
                    'month' => $month,
                    'hasdate' => !empty($day),
                    'description' => format_text($settings->{"event{$i}description"} ?? '', FORMAT_HTML),
                    'url' => $settings->{"event{$i}url"} ?? '#',
                    'image' => $image,
                    'hasimage' => !empty($image),
                ];
            }
        }

        return [
            'hasevents' => !empty($events),
            'eventstitle' => $settings->eventstitle ?? 'Upcoming events',
            'events' => $events,
        ];
    }

    /**
     * Get feature section data.
     *
     * @return array Feature section template data.
     */
    public function feature_section(): array {
        global $SITE;
        $settings = $this->theme->settings;

        if (empty($settings->featuresectionenabled)) {
            return ['hasfeaturesection' => false];
        }

        $title = str_replace('{sitename}', $SITE->shortname, $settings->featuresectiontitle ?? '');
        $image = $this->theme->setting_file_url('featuresectionimage', 'featuresectionimage');

        return [
            'hasfeaturesection' => true,
            'featuresectiontitle' => $title,
            'featuresectioncontent' => format_text($settings->featuresectioncontent ?? '', FORMAT_HTML),
            'featuresectionimage' => $image,
            'hasfeaturesectionimage' => !empty($image),
            'featuresectionbuttontext' => $settings->featuresectionbuttontext ?? 'Explore More',
            'featuresectionbuttonurl' => $settings->featuresectionbuttonurl ?? '#',
            'hasfeaturesectionbutton' => !empty($settings->featuresectionbuttontext),
        ];
    }

    /**
     * Get login page data.
     *
     * @return array Login page template data.
     */
    public function login_page(): array {
        $settings = $this->theme->settings;

        $loginbg = $this->theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');

        return [
            'loginboxposition' => $settings->loginboxposition ?? 'center',
            'logintext' => format_text($settings->logintext ?? '', FORMAT_HTML),
            'haslogintext' => !empty(trim(strip_tags($settings->logintext ?? ''))),
            'loginbackgroundimage' => $loginbg,
            'hasloginbackground' => !empty($loginbg),
            'showsplitlayout' => ($settings->loginboxposition ?? 'center') !== 'center',
        ];
    }

    /**
     * Get all frontpage section data combined.
     *
     * @return array All frontpage data for template.
     */
    public function get_frontpage_data(): array {
        return array_merge(
            $this->hero_section(),
            $this->marketing_blocks(),
            $this->announcements(),
            $this->campus_life(),
            $this->feature_section(),
            $this->testimonials(),
            $this->statistics(),
            $this->events(),
            $this->footer()
        );
    }
}
