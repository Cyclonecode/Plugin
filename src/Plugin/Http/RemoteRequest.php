<?php

namespace Cyclonecode\Plugin\Http;

class RemoteRequest extends AbstractRequest
{
    /**
     * Performs a remote request.
     * @param string $url
     * @param array $args
     * @return false|string
     * @throws \Exception
     */
    public function execute($url, array $args = array())
    {
        $defaults = array(
            'method' => self::VERB_GET,
            'timeout' => self::CURL_TIMEOUT,
        );
        $args = array_merge($defaults, $args);
        $response = wp_safe_remote_request($url, $args);
        if (!is_wp_error($response) && ($response['response']['code'] == 200 || $response['response']['code'] == 201)) {
            $result = json_encode(wp_remote_retrieve_body($response));
        } else {
            throw new \Exception($response['response']['message'], $response['response']['code']);
        }
        return $result;
    }
}
