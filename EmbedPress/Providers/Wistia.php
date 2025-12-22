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
        '*.wistia.com', 'wistia.com'
    ];
    
    /** inline {@inheritdoc} */
    protected $allowedParams = ['maxwidth', 'maxheight'];
    
    /** inline {@inheritdoc} */
    protected $httpsSupport = true;
    
    /** inline {@inheritdoc} */
    protected $responsiveSupport = true;
    
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
    public function getStaticResponse()
    {
        $videoId = $this->getVideoIDFromURL();
        
        if (!$videoId) {
            return [];
        }
        
        // Build the oEmbed API URL
        $apiUrl = $this->endpoint . '?url=' . urlencode($this->getUrl());
        
        // Add maxwidth and maxheight if available
        $params = $this->getParams();
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
            return $cached_data;
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
        
        // Cache for 1 hour
        set_transient($transient_key, $result, HOUR_IN_SECONDS);

        return $result;
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

