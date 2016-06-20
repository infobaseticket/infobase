<?php
/**
 * Pushy: Pushover PHP Client
 *
 * @author    Michael Squires <sqmk@php.net>
 * @copyright Copyright (c) 2013 Michael K. Squires
 * @license   http://github.com/sqmk/Pushy/wiki/License
 */

namespace Pushy\Transport;

/**
 * Request message for HTTP client
 */
class RequestMessage
{
    /**
     * API Domain
     */
    const API_DOMAIN = 'https://api.pushover.net/1/';

    /**
     * Request method
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * Request path
     *
     * @var string
     */
    protected $path;

    /**
     * Query params
     *
     * @var array
     */
    protected $queryParams = [];

    /**
     * Post body
     *
     * @var array
     */
    protected $postBody = [];

    /**
     * Get request method
     *
     * @return string Request method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set request method
     *
     * @param string $method
     *
     * @return RequestMessage This object
     */
    public function setMethod($method)
    {
        $this->method = (string) $method;

        return $this;
    }

    /**
     * Get request path
     *
     * @return string Request path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set request path
     *
     * @param string $path
     *
     * @return RequestMessage This object
     */
    public function setPath($path)
    {
        $this->path = (string) $path;

        return $this;
    }

    /**
     * Get query params
     *
     * @return string|null URL-encoded query params
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Set query param
     *
     * @param string $paramName Param name
     * @param mixed  $value     Value
     *
     * @return self This object
     */
    public function setQueryParam($paramName, $value)
    {
        $this->queryParams[(string) $paramName] = $value;

        return $this;
    }

    /**
     * Get full request URL
     *
     * @return string Full request URL
     */
    public function getFullUrl()
    {
        $fullPath = self::API_DOMAIN . $this->getPath();

        if ($query = $this->getQueryParams()) {
            $fullPath .= '?' . http_build_query($query);
        }

        return $fullPath;
    }

    /**
     * Get JSON body
     *
     * @return string JSON
     */
    public function getPostBody()
    {
        return $this->postBody;
    }

    /**
     * Set post body field
     *
     * @param string $fieldName Field name
     * @param string $value     Value
     *
     * @return self This object
     */
    public function setPostBodyField($fieldName, $value)
    {
        $this->postBody[$fieldName] = $value;

        return $this;
    }
}
