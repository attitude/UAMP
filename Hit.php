<?php

/**
 * Google Analytics Universal Analytics Measument Protocol v1
 */

namespace attitude\UAMPv1;

/**
 * Google Analytics Universal Analytics Measument Protocol Hit Class
 *
 * @author  Martin Adamko <@martin_adamko>
 * @license MIT
 * @version v0.1.0
 *
 * @link    https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide
 * @link    https://developers.google.com/analytics/devguides/collection/protocol/v1/reference
 * @link    https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters
 *
 * @todo    Calculate `Queue Time` offset (useful for batch processing)
 * @todo    Apply max width to text fields
 *
 */
class Hit
{
    // Class Settings //////////////////////////////////////////////////////////

    /**
     * @var array   Parameter required for all hits
     *
     */
    private $ALLREQUIRED = array('tid', 'cid', 't');

    /**
     * @var array   Parameters required for certain hit types
     *
     */
    private $TYPEREQUIRED = array(
        'transaction' => array('ti'),
        'item'        => array('ti', 'in'),
        'social'      => array('sn', 'sa', 'st')
    );

    /**
     * @var array   Index of implemented parts
     *
     */
    private $GROUPS = array(
        'general',
        'visitor',
        'session',
        'traffic_sources',
        'system',
        'hit',
        'content_information',
        'app_tracking',
        'event_tracking',
        'e_commerce',
        'social_interactions',
        'timing',
        'exceptions',
        'custom_dimensions_metrics'
    );

    /**
     * @var string  User Agent string associated with current hit
     *
     */
    private $user_agent = null;

    /**
     * @var string  Microtimestamp when the hit occured
     *
     */
    private $hit_microtime = null;

    /**
     * @var array   General parameters group
     *
     */
    private $general = array(
        // Tracking ID / Web Property ID
        'tid' => null,

        // Anonymize IP
        'aip' => null,

        // Queue Time
        'qt' => null,

        // Cache Buster
        'z' => null
    );

    /**
     * @var array   Visitor parameters group
     *
     */
    private $visitor = array(
        // Client ID
        'cid' => null
    );

    /**
     * @var array   Session parameters group
     *
     */
    private $session = array(
        // Session Control
        'sc' => null
    );

    /**
     * @var array   Traffic sources parameters group
     *
     */
    private $traffic_sources = array(
        // Document Referrer
        'dr' => null,

        // Campaign Name
        'cn' => null,

        // Campaign Source
        'cs' => null,

        // Campaign Medium
        'cm' => null,

        // Campaign Keyword
        'ck' => null,

        // Campaign Content
        'cc' => null,

        // Campaign ID
        'ci' => null,

        // Google AdWords ID
        'gclid' => null,

        // Google Display Ads ID
        'dclid' => null,

        // Google Display Ads ID
        'dclid' => null
    );

    /**
     * @var array   System parameters group
     *
     */
    private $system = array(
        // Screen Resolution
        'sr' => null,

        // Viewport size
        'vp' => null,

        // Document Encoding
        'de' => null,

        // Screen Colors
        'sd' => null,

        // User Language
        'ul' => null,

        // Java Enabled
        'je' => null,

        // Flash Version
        'fl' => null
    );

    /**
     * @var array   Hit parameters group
     *
     */
    private $hit = array(
        // Hit type
        't' => null,

        // Non-Interaction Hit
        'ni' => null
    );

    /**
     * @var array   Content information parameters group
     *
     */
    private $content_information = array(
        // Document location URL
        'dl' => null,

        // Document Host Name
        'dh' => null,

        // Document Path
        'dp' => null,

        // Document Title
        'dt' => null,

        // Content Description
        'cd' => null
    );

    /**
     * @var array   App tracking parameters group
     *
     */
    private $app_tracking = array(
        // Application Name
        'an' => null,

        // Application Version
        'av' => null,
    );

    /**
     * @var array   Event tracking parameters group
     *
     */
    private $event_tracking = array(
        // Event Category
        'ec' => null,

        // Event Action
        'ea' => null,

        // Event Label
        'el' => null,

        // Event Value
        'ev' => null
    );

    /**
     * @var array   E-commerce parameters group
     *
     */
    private $e_commerce = array(
        // Transaction ID
        'ti' => null,

        // Transaction Affiliation
        'ta' => null,

        // Transaction Revenue
        'tr' => null,

        // Transaction Shipping
        'ts' => null,

        // Transaction Tax
        'tt' => null,

        // Item Name
        'in' => null,

        // Item Price
        'ip' => null,

        // Item Quantity
        'iq' => null,

        // Item Code
        'ic' => null,

        // Item Category
        'iv' => null,

        // Currency Code
        'cu' => null
    );

    /**
     * @var array   Social interactions parameters group
     *
     */
    private $social_interactions = array(
        // Social Network
        'sn' => null,

        // Social Action
        'sa' => null,

        // Social Action Target
        'st' => null
    );

    /**
     * @var array   Timing parameters group
     *
     */
    private $timing = array(
        // User timing category
        'utc' => null,

        // User timing variable name
        'utv' => null,

        // User timing time
        'utt' => null,

        // User timing label
        'utl' => null,

        // Page Load Time
        'plt' => null,

        // DNS Time
        'dns' => null,

        // Page Download Time
        'pdt' => null,

        // Redirect Response Time
        'rrt' => null,

        // TCP Connect Time
        'tcp' => null,

        // Server Response Time
        'srt' => null
    );

    /**
     * @var array   Exceptions parameters group
     *
     */
    private $exceptions = array(
        // Exception Description
        'exd' => null,

        // Is Exception Fatal?
        'exf' => null
    );

    /**
     * @var array   Custom dimensions/metrics parameters group
     *
     */
    private $custom_dimensions_metrics = array(
        // Custom Dimension
        // Custom Metric
    );

    /**
     * Constructs a hit
     *
     * @param   array   Array of arguments
     * @param   string  Sets User Agent string (optional)
     * @param   string  Set hit microtimestamp (optional)
     * @returns object  Returns `$this`
     *
     */
    public function __construct(array $args=array(), $user_agent = null, $hit_microtime = null)
    {
        if (empty($user_agent)) {
            $user_agent =& $_SERVER['HTTP_USER_AGENT'];
        }

        if (empty($hit_microtime)) {
            $hit_microtime = microtime();
        }

        $this->user_agent    =& $user_agent;
        $this->hit_microtime =  $hit_microtime;

        foreach ($args as $k => $v) {
            $this->__set($k, $v);
        }

        return $this;
    }

    /**
     * Setter magic method
     *
     * Sets any valid parameter a value.
     *
     * @param   string  Parameter key
     * @param   mixed   Parameter value
     * @return  object  Returns `$this` (chainable)
     *
     */
    public function __set($k, $v)
    {
        $method = 'set'.strtoupper($k);

        if (method_exists($this, $method)) {
            try {
                $this->{$method}($v);
            } catch (\Exception $e) {
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        } else {
            foreach ($this->GROUPS as &$group) {
                // Set custom dimmension and metric parameters
                if ($group === 'custom_dimensions_metrics' && preg_match('/(?:cd|cm)\d{1,3}/', $k, $devnull)) {
                    $this->{$group}[$k] = $v;

                    continue;
                }

                // Set valid parameters
                if (isset($this->{$group}) && array_key_exists($k, $this->{$group})) {
                    // Simple value assigment
                    $this->{$group}[$k] = $v;
                }
            }
        }

        return $this;
    }

    /**
     * Builds a payload string
     *
     * @param   void
     * @returns string  URL encoded payload string
     *
     */
    public function build()
    {
        $query = array();

        foreach ($this->GROUPS as &$group) {
            foreach ($this->$group as $k => &$v) {
                if ($v!==null) {
                    $query[$k] = urlencode($v);
                }
            }
        }

        foreach ($this->ALLREQUIRED as $k) {
            if (!isset($query[$k])) {
                throw new \Exception('Missing required parameter: `'.$k.'`');
            }
        }

        foreach ($this->TYPEREQUIRED as $t => $keys) {
            if ($query['t']===$t) {
                foreach ($keys as $k) {
                    if (!isset($query[$k])) {
                        throw new \Exception('Missing required parameter: `'.$k.' for type `'.$t.'`');
                    }
                }
            }
        }

        return 'v=1&'.http_build_query($query);
    }

    /**
     * Returns set User Agent string
     *
     * @param   void
     * @returns string
     *
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * Sets text value for a known parameter
     *
     * @param   string  Parameters group
     * @param   string  Parameter key
     * @param   string  Parameter value
     * @returns object  Returns `$this` (chainable)
     *
     */
    private function setText($group, $k, $v)
    {
        if (!is_string($v) || (is_string($v) && strlen(trim($v))===0)) {
            throw new \Exception('Argument must be a non-empty string');
        }

        $this->{$group}[$k] = $v;

        return $this;
    }

    /**
     * Sets boolean value for a known parameter
     *
     * @param   string  Parameters group
     * @param   string  Parameter key
     * @param   mixed   Parameter value
     * @returns object  Returns `$this` (chainable)
     *
     */
    private function setBoolean($group, $k, $v)
    {
        // Booleanize 1 or 0 integers
        if (is_int($v) && ($v===1 || $v===0)) {
            $v = !! $v;
        }

        if (!is_bool($v)) {
            throw new \Exception('Argument must be a boolean or integer 1 or 0');
        }

        $this->{$group}[$k] = $v ? 1 : 0;

        return $this;
    }

    /**
     * Sets integer value for a known parameter
     *
     * @param   string  Parameters group
     * @param   string  Parameter key
     * @param   mixed   Parameter value
     * @returns object  Returns `$this` (chainable)
     *
     */
    private function setInteger($group, $k, $v)
    {
        if (!is_int($v)) {
            // Allow numeric string
            if (is_numeric($v) && (int) $v==$v) {
                $v = (int) $v;
            } else {
                throw new \Exception('Argument must be an int');
            }
        }

        $this->{$group}[$k] = $v;

        return $this;
    }

    /**
     * Sets currency value for a known parameter
     *
     * @param   string  Parameters group
     * @param   string  Parameter key
     * @param   mixed   Parameter value
     * @returns object  Returns `$this` (chainable)
     *
     */
    private function setCurrency($group, $k, $v)
    {
        if (is_float($v) || is_numeric($v)) {
            $this->{$group}[$k] = number_format((float) $v, 2, '.', '');

            return $this;
        }

        throw new \Exception('Currency must be a valid float or a numeric string.');
    }

    // PARAMETER SETTERS ///////////////////////////////////////////////////////

    public function setTID($v) { return $this->setText('general', 'tid', $v); }
    public function setAIP($v) { return $this->setBoolean('general', 'aip', $v); }
    public function setQT($v)
    {
        if ($v >= 0) {
            $this->setInteger('general', 'qt', $v);
        }

        return $this;
    }
    public function setZ($v)   { return $this->setText('general', 'z', $v); }

    public function setCID($v) { return $this->setText('visitor', 'cid', $v); }

    public function setSC($v)
    {
        if ($v==='start' || $v==='end') {
            return $this->setText('session', 'sc', $v);
        }

        throw new \Exception('Session Control must be either `start` or `end`.');
    }

    public function setDR($v)    { return $this->setText('trafic_sources', 'dr', $v); }
    public function setCN($v)    { return $this->setText('trafic_sources', 'cn', $v); }
    public function setCS($v)    { return $this->setText('trafic_sources', 'cs', $v); }
    public function setCM($v)    { return $this->setText('trafic_sources', 'cm', $v); }
    public function setCK($v)    { return $this->setText('trafic_sources', 'ck', $v); }
    public function setCC($v)    { return $this->setText('trafic_sources', 'cc', $v); }
    public function setCI($v)    { return $this->setText('trafic_sources', 'ci', $v); }
    public function setGCLID($v) { return $this->setText('trafic_sources', 'gclid', $v); }
    public function setDCLID($v) { return $this->setText('trafic_sources', 'dclid', $v); }

    public function setSR($v) { return $this->setText('system_info', 'sr', $v); }
    public function setVP($v) { return $this->setText('system_info', 'vp', $v); }
    public function setDE($v) { return $this->setText('system_info', 'de', $v); }
    public function setSD($v) { return $this->setText('system_info', 'sd', $v); }
    public function setUL($v) { return $this->setText('system_info', 'ul', $v); }
    public function setJE($v) { return $this->setBoolean('system_info', 'je', $v); }
    public function setFL($v) { return $this->setText('system_info', 'fl', $v); }

    public function setT($v)
    {
        if (in_array($v, array('pageview', 'appview', 'event', 'transaction', 'item', 'social', 'exception', 'timing'))) {
            return $this->setText('hit', 't', $v);
        }

        throw new \Exception("Hit type must one of these values: 'pageview', 'appview', 'event', 'transaction', 'item', 'social', 'exception', 'timing'");
    }
    public function setNI($v) { return $this->setBoolean('hit', 'ni', $v); }

    public function setDL($v) { return $this->setText('content_information', 'dl', $v); }
    public function setDP($v) { return $this->setText('content_information', 'dp', $v); }
    public function setDT($v) { return $this->setText('content_information', 'dt', $v); }
    public function setCD($v) { return $this->setText('content_information', 'cd', $v); }

    public function setAN($v) { return $this->setText('app_tracking', 'an', $v); }
    public function setAV($v) { return $this->setText('app_tracking', 'av', $v); }

    public function setEC($v) { return $this->setText('event_tracking', 'ec', $v); }
    public function setEA($v) { return $this->setText('event_tracking', 'ea', $v); }
    public function setEL($v) { return $this->setText('event_tracking', 'el', $v); }
    public function setEV($v) { return $this->setInteger('event_tracking', 'ev', $v); }

    public function setTI($v) { return $this->setText('e_commerce', 'ti', $v); }
    public function setTA($v) { return $this->setText('e_commerce', 'ti', $v); }
    public function setTR($v) { return $this->setCurrency('e_commerce', 'tr', $v); }
    public function setTS($v) { return $this->setCurrency('e_commerce', 'ts', $v); }
    public function setTT($v) { return $this->setCurrency('e_commerce', 'tt', $v); }
    public function setIN($v) { return $this->setText('e_commerce', 'in', $v); }
    public function setIP($v) { return $this->setCurrency('e_commerce', 'ip', $v); }
    public function setIQ($v) { return $this->setInteger('e_commerce', 'iq', $v); }
    public function setIC($v) { return $this->setText('e_commerce', 'ic', $v); }
    public function setIV($v) { return $this->setText('e_commerce', 'iv', $v); }
    public function setCU($v) { return $this->setText('e_commerce', 'cu', $v); }

    public function setSN($v) { return $this->setText('social_interactions', 'sn', $v); }
    public function setSA($v) { return $this->setText('social_interactions', 'sa', $v); }
    public function setST($v) { return $this->setText('social_interactions', 'st', $v); }

    public function setUTC($v) { return $this->setText('timing', 'utc', $v); }
    public function setUTV($v) { return $this->setText('timing', 'utv', $v); }
    public function setUTT($v) { return $this->setInteger('timing', 'utt', $v); }
    public function setUTL($v) { return $this->setText('timing', 'utl', $v); }
    public function setPLT($v) { return $this->setInteger('timing', 'plt', $v); }
    public function setDNS($v) { return $this->setInteger('timing', 'dns', $v); }
    public function setPDT($v) { return $this->setInteger('timing', 'pdt', $v); }
    public function setRRT($v) { return $this->setInteger('timing', 'rrt', $v); }
    public function setTCP($v) { return $this->setInteger('timing', 'tcp', $v); }
    public function setSRT($v) { return $this->setInteger('timing', 'srt', $v); }

    public function setEXD($v) { return $this->setText('exceptions', 'exd', $v); }
    public function setEXF($v) { return $this->setBoolean('exceptions', 'exf', $v); }

    public function setCDn($n, $v)
    {
        if (!is_int($n)) {
            throw new \Exception('Custon Dimmention number must be an integer.');
        }

        return $this->setText('custom_dimensions_metrics', 'cd'.$n, $v);
    }
    public function setCMn($n, $v)
    {
        if (!is_int($n)) {
            throw new \Exception('Custon Dimmention number must be an integer.');
        }

        return $this->setText('custom_dimensions_metrics', 'cm'.$n, $v);
    }
}
