<?php

namespace Rumur\WordPress\Scheduling;

class Pinger
{
    /**
     * Tries to ping the passed URL.
     *
     * @param string $url
     *
     * @uses \WP_Http
     * @uses \is_wp_error
     * @uses \wp_remote_get
     * @uses \wp_remote_retrieve_response_code
     *
     * @return bool     Returns `true` if got a response with a successful status.
     */
    public function ping(string $url): bool
    {
        $code = \wp_remote_retrieve_response_code(
            $response = \wp_remote_get($url)
        );

        if (\is_wp_error($response)) {
            error_log(
                static::class . " failed reach out the url `{$url}`, reason: " . $response->get_error_message()
            );
        }

        return $code >= \WP_Http::OK && $code < \WP_Http::MULTIPLE_CHOICES;
    }
}
