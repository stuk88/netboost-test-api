<?php

namespace App\Services;

class UrlExtractorService
{

    public static function extractUrls($domain, $html)
    {
        // Create an array to store the extracted URLs
        $urls = [];

        // Initialize a DOMDocument object and load the HTML content
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);

        // Get all anchor tags in the HTML
        $links = $dom->getElementsByTagName('a');

        // Loop through each anchor tag and extract the href attribute value
        foreach ($links as $link) {
            $url = $link->getAttribute('href');

            // Exclude empty URLs and URLs starting with 'mailto:'
            if (empty($url) || str_contains($url, 'mailto:') || str_contains($url, 'tel:') ) 
                continue;

            // Convert relative URLs to absolute URLs
            if (strpos($url, 'http') !== 0 && strpos($url, $domain) !== 0) {
                $url = UrlExtractorService::getAbsoluteUrl($domain, $url);
            }

            // remove hash from url
            if (strpos($url, '#')) {
                $url = substr($url, 0, strpos($url, '#'));
            }

            // Add the extracted URL to the array
            $urls[$url] = $url; // Add url only if its not in the list already.
        }

        // Return the array of extracted URLs
        return $urls;
    }

    private static function getAbsoluteUrl($domain, $relativeUrl)
    {
        // Append the relative URL to the base URL
        return $domain . '/' . ltrim($relativeUrl, '/');
    }

    public static function extractDomain($url)
    {
        // Extract the host/domain from the URL using parse_url
        $parsedUrl = parse_url($url);

        // Combine the scheme, host, and port (if present)
        $domain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        if (isset($parsedUrl['port'])) {
            $domain .= ':' . $parsedUrl['port'];
        }

        return $domain;
    }
}
