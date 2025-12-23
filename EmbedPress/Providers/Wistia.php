<?php

namespace EmbedPress\Providers;

use Embera\Provider\ProviderAdapter;
use Embera\Provider\ProviderInterface;
use Embera\Url;

(defined('ABSPATH') && defined('EMBEDPRESS_IS_LOADED')) or die("No direct script access allowed.");

/**
 * Entity responsible to support Wistia embeds using API.
 *
 * @package     EmbedPress
 * @subpackage  EmbedPress/Providers
 * @author      EmbedPress <help@embedpress.com>
 * @copyright   Copyright (C) 2023 WPDeveloper. All rights reserved.
 * @license     GPLv3 or later
 * @since       1.0.0
 */
class Wistia extends ProviderAdapter implements ProviderInterface
{
    /** inline {@inheritdoc} */
    protected $shouldSendRequest = false;

    /** inline {@inheritdoc} */
    protected $endpoint = 'https://fast.wistia.com/oembed.json';

    /** inline {@inheritdoc} */
    protected static $hosts = [
        '*.wistia.com',
        'wistia.com'
    ];
    

    /** inline {@inheritdoc} */
    protected $allowedParams = [
        'maxwidth',
        'maxheight',
        'wstarttime',
        'wautoplay',
        'scheme',
        'captions',
        'playbutton',
        'smallplaybutton',
        'playbar',
        'resumable',
        'wistiafocus',
        'volumecontrol',
        'volume',
        'rewind',
        'wfullscreen',
    ];


    /** inline {@inheritdoc} */
    protected $httpsSupport = true;

    public function getAllowedParams(){
        return $this->allowedParams;
    }

    /** inline {@inheritdoc} */
    protected $responsiveSupport = true;

    public function __construct($url, array $config = [])
    {
        parent::__construct($url, $config);
        add_filter('embedpress_render_dynamic_content', [$this, 'fakeDynamicResponse'], 10, 2);
    }

    /**
     * Validate if the URL is a valid Wistia URL
     *
     * @param Url $url
     * @return bool
     */
    public function validateUrl(Url $url)
    {
        return (bool) (
            preg_match('~wistia\.com/embed/(iframe|playlists)/([^/]+)~i', (string) $url) ||
            preg_match('~wistia\.com/medias/([^/]+)~i', (string) $url)
        );
    }

    /**
     * Normalize the URL
     *
     * @param Url $url
     * @return Url
     */
    public function normalizeUrl(Url $url)
    {
        $url->convertToHttps();
        $url->removeQueryString();
        $url->removeLastSlash();

        return $url;
    }

    /**
     * Get the Video ID from the URL
     *
     * @param string $url
     * @return string|false
     */
    public function getVideoIDFromURL($url = null)
    {
        if (empty($url)) {
            $url = $this->getUrl();
        }

        // https://fast.wistia.com/embed/medias/xf1edjzn92.jsonp
        // https://ostraining-1.wistia.com/medias/xf1edjzn92
        preg_match('#\/medias\\\?\/([a-z0-9]+)\.?#i', $url, $matches);

        $id = false;
        if (isset($matches[1])) {
            $id = $matches[1];
        }

        return $id;
    }

    /**
     * Get static response using Wistia oEmbed API
     *
     * @return array
     */
    public function fakeDynamicResponse()
    {
        $videoId = $this->getVideoIDFromURL();

        if (!$videoId) {
            return [];
        }

        // Build the oEmbed API URL
        $apiUrl = $this->endpoint . '?url=' . urlencode($this->getUrl());

        // Add maxwidth and maxheight if available
        $params = $this->getParams();

        // Temporary debug - remove after testing
        error_log('Wistia Params: ' . print_r($params, true));
        error_log('Wistia Config: ' . print_r($this->config, true));
        if (!empty($params['maxwidth'])) {
            $apiUrl .= '&maxwidth=' . intval($params['maxwidth']);
        }
        if (!empty($params['maxheight'])) {
            $apiUrl .= '&maxheight=' . intval($params['maxheight']);
        }


        // Check transient cache
        $transient_key = 'ep_wistia_' . md5($apiUrl);
        $cached_data = get_transient($transient_key);

        if ($cached_data !== false) {
            // Apply enhancements to cached data
            return $this->modifyResponse($cached_data);
        }

        // Fetch data from Wistia oEmbed API
        $response = wp_remote_get($apiUrl, ['timeout' => 30]);

        if (is_wp_error($response)) {
            return $this->getFallbackResponse($videoId, $params);
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data) || !isset($data['html'])) {
            return $this->getFallbackResponse($videoId, $params);
        }

        // Prepare the response
        $result = [
            'type' => $data['type'] ?? 'video',
            'provider_name' => $data['provider_name'] ?? 'Wistia',
            'provider_url' => $data['provider_url'] ?? 'https://wistia.com',
            'title' => $data['title'] ?? '',
            'html' => $data['html'] ?? '',
            'width' => $data['width'] ?? 600,
            'height' => $data['height'] ?? 450,
        ];

        // Cache for 1 hour (cache the raw response, not the modified one)
        set_transient($transient_key, $result, HOUR_IN_SECONDS);

        // Apply enhancements
        return $this->modifyResponse($result);
    }

    /**
     * Modify the response to apply Wistia-specific enhancements
     * This handles all customizations from Gutenberg, Elementor, and Shortcode
     *
     * @param array $response
     * @return array
     */
    public function modifyResponse(array $response = [])
    {
        if (empty($response['html'])) {
            return $response;
        }

        $params = $this->getParams();
        
        $videoId = $this->getVideoIDFromURL();

        if (!$videoId) {
            return $response;
        }

        // Get plugin settings
        $options = $this->getWistiaSettings();

        // Build embed options
        $embedOptions = new \stdClass;
        $embedOptions->videoFoam = false;

        // Fullscreen button
        if (isset($params['wfullscreen'])) {
            $embedOptions->fullscreenButton = (bool) $params['wfullscreen'];
        } elseif (isset($options['display_fullscreen_button'])) {
            $embedOptions->fullscreenButton = (bool) $options['display_fullscreen_button'];
        }

        // Playbar
        if (isset($params['playbar'])) {
            $embedOptions->playbar = (bool) $params['playbar'];
        } elseif (isset($options['display_playbar'])) {
            $embedOptions->playbar = (bool) $options['display_playbar'];
        }

        // Small play button
        if (isset($params['smallplaybutton'])) {
            $embedOptions->smallPlayButton = (bool) $params['smallplaybutton'];
        } elseif (isset($options['small_play_button'])) {
            $embedOptions->smallPlayButton = (bool) $options['small_play_button'];
        }

        // Autoplay
        if (isset($params['wautoplay'])) {
            $embedOptions->autoPlay = (bool) $params['wautoplay'];
        } elseif (isset($options['autoplay'])) {
            $embedOptions->autoPlay = (bool) $options['autoplay'];
        }

        // Start time
        if (isset($params['wstarttime']) && !empty($params['wstarttime'])) {
            $embedOptions->time = (int) $params['wstarttime'];
        } elseif (!empty($options['start_time'])) {
            $embedOptions->time = (int) $options['start_time'];
        }

        // Player color/scheme
        if (isset($params['scheme']) && !empty($params['scheme'])) {
            $embedOptions->playerColor = $params['scheme'];
        } elseif (isset($options['player_color']) && !empty($options['player_color'])) {
            $embedOptions->playerColor = $options['player_color'];
        }

        // Build plugins
        $pluginsBaseURL = plugins_url('assets/js/wistia/min', dirname(__DIR__) . '/embedpress-Wistia.php');
        $pluginList = [];

        // Resumable plugin
        $isResumableEnabled = false;
        if (isset($params['resumable'])) {
            $isResumableEnabled = (bool) $params['resumable'];
        } elseif (isset($options['plugin_resumable'])) {
            $isResumableEnabled = (bool) $options['plugin_resumable'];
        }

        if ($isResumableEnabled) {
            $pluginList['resumable'] = [
                'src' => $pluginsBaseURL . '/resumable.min.js',
                'async' => false
            ];
        }

        // Fix for autoplay and resumable working together
        if (isset($embedOptions->autoPlay) && $embedOptions->autoPlay && $isResumableEnabled) {
            $pluginList['fixautoplayresumable'] = [
                'src' => $pluginsBaseURL . '/fixautoplayresumable.min.js'
            ];
        }

        // Focus plugin
        $isFocusEnabled = false;
        if (isset($params['wistiafocus'])) {
            $isFocusEnabled = (bool) $params['wistiafocus'];
        } elseif (isset($options['plugin_focus'])) {
            $isFocusEnabled = (bool) $options['plugin_focus'];
        }

        if ($isFocusEnabled) {
            $pluginList['dimthelights'] = [
                'src' => $pluginsBaseURL . '/dimthelights.min.js',
                'autoDim' => $isFocusEnabled
            ];
            $embedOptions->focus = $isFocusEnabled;
        }

        // Rewind plugin
        $isRewindEnabled = false;
        if (isset($params['rewind'])) {
            $isRewindEnabled = (bool) $params['rewind'];
        } elseif (isset($options['plugin_rewind'])) {
            $isRewindEnabled = (bool) $options['plugin_rewind'];
        }

        if ($isRewindEnabled) {
            $embedOptions->rewindTime = isset($options['plugin_rewind_time']) ? (int) $options['plugin_rewind_time'] : 10;
            $pluginList['rewind'] = [
                'src' => $pluginsBaseURL . '/rewind.min.js'
            ];
        }

        $embedOptions->plugin = $pluginList;
        $embedOptionsJson = json_encode($embedOptions);

        // Get short video ID (first 3 characters)
        $shortVideoId = substr($videoId, 0, 3);

        // Build the custom HTML
        $class = [
            'wistia_embed',
            'wistia_async_' . $videoId
        ];

        $width = $response['width'] ?? 600;
        $height = $response['height'] ?? 450;

        $attribs = [
            sprintf('id="wistia_%s"', $videoId),
            sprintf('class="%s"', join(' ', $class)),
            sprintf('style="width:%spx; height:%spx;"', $width, $height)
        ];

        // Labels for resumable plugin
        $labels = [
            'watch_from_beginning' => __('Watch from the beginning', 'embedpress'),
            'skip_to_where_you_left_off' => __('Skip to where you left off', 'embedpress'),
            'you_have_watched_it_before' => __(
                'It looks like you\'ve watched<br />part of this video before!',
                'embedpress'
            ),
        ];
        $labelsJson = json_encode($labels);

        // Generate unique ID for wrapper
        $uid = substr(md5($this->getUrl() . time()), 0, 10);

        // Build the final HTML
        $html = "<div class=\"embedpress-wrapper ose-wistia ose-uid-{$uid} responsive\">";
        $html .= '<script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>';
        $html .= "<script>window.pp_embed_wistia_labels = {$labelsJson};</script>\n";
        $html .= "<script>window._wq = window._wq || []; _wq.push({\"{$shortVideoId}\": {$embedOptionsJson}});</script>\n";
        $html .= '<div ' . join(' ', $attribs) . "></div>\n";
        $html .= '</div>';

        $response['html'] = $html;

        return $response;
    }

    /**
     * Get Wistia settings from WordPress options
     *
     * @return array
     */
    protected function getWistiaSettings()
    {
        $schema = [
            'autoplay' => false,
            'display_fullscreen_button' => true,
            'display_playbar' => true,
            'small_play_button' => true,
            'start_time' => '',
            'player_color' => '',
            'plugin_resumable' => false,
            'plugin_focus' => false,
            'plugin_rewind' => false,
            'plugin_rewind_time' => 10,
        ];

        $options = get_option(EMBEDPRESS_PLG_NAME . ':wistia', []);

        return wp_parse_args($options, $schema);
    }

    /**
     * Get fallback response when API fails
     *
     * @param string $videoId
     * @param array $params
     * @return array
     */
    protected function getFallbackResponse($videoId, $params)
    {
        $width = !empty($params['maxwidth']) ? intval($params['maxwidth']) : 600;
        $height = !empty($params['maxheight']) ? intval($params['maxheight']) : 450;

        // Create a basic iframe embed as fallback
        $embedUrl = 'https://fast.wistia.net/embed/iframe/' . $videoId;

        $attr = [];
        $attr[] = 'src="' . esc_url($embedUrl) . '"';
        $attr[] = 'width="' . esc_attr($width) . '"';
        $attr[] = 'height="' . esc_attr($height) . '"';
        $attr[] = 'frameborder="0"';
        $attr[] = 'scrolling="no"';
        $attr[] = 'class="wistia_embed"';
        $attr[] = 'name="wistia_embed"';
        $attr[] = 'allowtransparency="true"';
        $attr[] = 'allowfullscreen';

        return [
            'type' => 'video',
            'provider_name' => 'Wistia',
            'provider_url' => 'https://wistia.com',
            'title' => '',
            'html' => '<iframe ' . implode(' ', $attr) . '></iframe>',
            'width' => $width,
            'height' => $height,
        ];
    }
}
