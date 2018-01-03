<?php

namespace futuretek\shared;

/**
 * Class UrlBuilder
 *
 * @package futuretek\shared
 * @author  Lukas Cerny <lukas.cerny@futuretek.cz>
 * @license https://www.apache.org/licenses/LICENSE-2.0.html Apache-2.0
 * @link    http://www.futuretek.cz
 */
class UrlBuilder
{
    /** @var array */
    private $_urlParts;

    /**
     * UrlBuilder constructor.
     *
     * @param string $url URL to manipulate
     */
    public function __construct($url)
    {
        $this->_urlParts = parse_url($url);
        if (!array_key_exists('query', $this->_urlParts)) {
            $this->_urlParts['query'] = null;
        }
    }

    /**
     * Return complete URL
     *
     * @return string
     */
    public function returnUrl()
    {
        $return = '';

        $return .= empty($this->_urlParts['scheme']) ? '' : $this->_urlParts['scheme'] . '://';

        $return .= empty($this->_urlParts['user']) ? '' : $this->_urlParts['user'];
        $return .= empty($this->_urlParts['pass']) || empty($this->_urlParts['user']) ? '' : ':' . $this->_urlParts['pass'];

        $return .= empty($this->_urlParts['user']) ? '' : '@';

        $return .= empty($this->_urlParts['host']) ? '' : $this->_urlParts['host'];
        $return .= empty($this->_urlParts['port']) ? '' : ':' . $this->_urlParts['port'];

        $return .= empty($this->_urlParts['path']) ? '' : '/' . ltrim($this->_urlParts['path'], '/');

        $return .= empty($this->_urlParts['query']) ? '' : '?' . $this->_urlParts['query'];

        $return .= empty($this->_urlParts['fragment']) ? '' : '#' . $this->_urlParts['fragment'];

        return $return;
    }

    /**
     * Change URL path part
     *
     * @param string $path URL path part
     * @return $this
     */
    public function changePath($path)
    {
        $this->_urlParts['path'] = $path;

        return $this;
    }

    /**
     * Edit existing query parameter
     *
     * @param string $name Parameter name
     * @param string $value New value
     * @return $this
     */
    public function editQuery($name, $value)
    {
        $parts = explode('&', $this->_urlParts['query']);
        $return = [];
        foreach ($parts as $p) {
            $paramData = explode('=', $p);
            if ($paramData[0] === $name) {
                $paramData[1] = $value;
            }
            $return[] = implode('=', $paramData);
        }

        $this->_urlParts['query'] = implode('&', $return);

        return $this;
    }

    /**
     * Add new query parameter
     *
     * @param string $name Parameter name
     * @param string $value Parameter value
     * @return $this
     */
    public function addQuery($name, $value)
    {
        $part = $name . '=' . $value;

        $this->_urlParts['query'] .= empty($this->_urlParts['query']) ? $part : '&' . $part;

        return $this;
    }

    /**
     * Check if parameter is present in query
     *
     * @param string $name Parameter name
     * @return bool
     */
    public function checkQuery($name)
    {
        $parts = explode('&', $this->_urlParts['query']);

        foreach ($parts as $p) {
            $paramData = explode('=', $p);
            if ($paramData[0] === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set value of existing query parameter or create new one
     *
     * @param string $name Parameter name
     * @param string $value Parameter value
     * @return $this
     */
    public function setQueryParam($name, $value)
    {
        if ($this->checkQuery($name)) {
            $this->editQuery($name, $value);
        } else {
            $this->addQuery($name, $value);
        }

        return $this;
    }
}