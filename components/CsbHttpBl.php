<?php

/**
 *
 * http://www.projecthoneypot.org
 * @see http://www.projecthoneypot.org/httpbl_api.php
 * User: radzserg
 * Date: 3/26/12
 */
class CsbHttpBl
{

    private $_accessKey;
    private $_cache;

    static $_instance;

    const VISITOR_SEARCH_ENGINE = 0;
    const VISITOR_SUSPICIOUS = 1;
    const VISITOR_HARVESTER = 2;
    const VISITOR_COMMENT_SPAMMER = 4;

    /**
     * @return CsbHttpBl
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    protected function __construct()
    {
        $this->_accessKey = Yii::app()->params['csb']['httpBlAccessKey'];
        if (!$this->_accessKey) {
            throw new App_Exception_System("Access key for honeypot project api is not set");
        }
    }

    /**
     * Return full httpBl info
     * @param $ip
     * @return array|bool array('daysSinceLastActivity', 'threatScore', 'visitorType' )
     */
    public function getHttpBlInfo($ip)
    {
        $result = $this->_httpBlInfo($ip);
        if (!$result) {
            return false;
        }
        $octets = explode('.', $result);
        return array(
            'daysSinceLastActivity' => $octets[1],
            'threatScore' => $octets[2],
            'visitorType' => $this->_decodeVisitorType($octets[3]),
        );
    }

    /**
     * Return visitor type
     * @param $ip
     * @return bool
     */
    public function getVisitorType($ip)
    {
        $result = $this->_httpBlInfo($ip);
        if (!$result) {
            return false;
        }
        $octets = explode('.', $result);
        $visitorType = $octets[3];

        return (int)$visitorType;
    }

    /**
     * Executes call to httpBl Api
     * @param $ip
     * @return string
     */
    private function _httpBlInfo($ip)
    {
        if (isset($this->_cache[$ip])) {
            return $this->_cache[$ip];
        }

        // revert ip
        $ipPieces = explode('.', $ip);
        $ipPieces = array_reverse($ipPieces);
        $ip = implode('.', $ipPieces);
        $request = "{$this->_accessKey}.{$ip}.dnsbl.httpbl.org";

        $result = gethostbyname($request);

        if ($request == $result) {
            // no info
            $this->_cache[$ip] = false;
            return false;
        }
        $this->_cache[$ip] = $result;

        $octets = explode('.', $result);
        if (127 != $octets[0]) {
            App_Log::errorLog("Can't get ip info for request {$request} result {$result}", CLogger::LEVEL_ERROR);
            return false;
        }

        return $result;
    }

    /**
     * Decode visitor type
     * @param $visitorType
     * @return string
     */
    private function _decodeVisitorType($visitorType)
    {
        switch ($visitorType) {
            case self::VISITOR_COMMENT_SPAMMER:
                return 'Comment spammer';
            case self::VISITOR_HARVESTER:
                return 'Harvester';
            case self::VISITOR_SEARCH_ENGINE:
                return 'Search engine';
            case self::VISITOR_SUSPICIOUS:
                return 'Suspicious';
            default:
                return 'Undefined';
        }
    }


}