<?php

/**
 * Google Analytics Universal Analytics Measument Protocol v1
 */

namespace attitude\UAMPv1;

/**
 * Google Analytics Universal Analytics Measument Protocol Connection
 *
 * @author  Martin Adamko <@martin_adamko>
 * @license MIT
 * @version v0.1.0
 *
 * @link    https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide
 * @link    https://developers.google.com/analytics/devguides/collection/protocol/v1/reference
 * @link    https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters
 *
 */
class Connection
{
    /**
     * Collect url
     *
     */
    const COLLECT_URL      = 'http://www.google-analytics.com/collect';

    /**
     * Protocol Version
     *
     * The Protocol version. The current value is '1'. This will only change
     * when there are changes made that are not backwards compatible.
     *
     * Required for all hit types.
     *
     * @example value: `1`
     * @example usage: `v=1`
     *
     * @link    https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters#v
     * @var     int
     *
     */
    const PROTOCOL_VERSION = 1;

    /**
     * Returns Universally Unique IDentifier
     *
     * See https://gist.github.com/dahnielson/508447
     *
     * @param   void
     * @returns string  32 bit hexadecimal hash
     *
     */
    public static function getUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

        // 32 bits for "time_low"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function post(UAMPv1_Hit $hit)
    {
        try {
            $ch = curl_init(self::COLLECT_URL);

            $payload_data = $hit->build();

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_data);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2000);

            curl_setopt($ch, CURLOPT_USERAGENT, $hit->getUserAgent());

            $response = curl_exec($ch);

            $info = curl_getinfo($ch);

            curl_close($ch);

            if ($info['http_code'] < 200 || $info['http_code'] >= 300) {
                trigger_error('POST of hit failed with HTTP code `'.$info['http_code'].'`', E_USER_WARNING);
            }
//             print_r($info);
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
        }
    }
}
