<?php

namespace RedlineCms\Core\Http;

use RedlineCms\Core\Support\App;

class Request
{
    protected $query;      // For query parameters ($_GET)
    protected $body;       // For form data ($_POST) or JSON body
    protected $headers;    // HTTP headers

    public function __construct()
    {
        $this->query = $_GET;
        $this->headers = $this->getHeaders();
    }

    /**
     * Get all query parameters
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * Get a specific query parameter
     */
    public function getQuery($key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function getParam($key, $default = null)
    {
        return App::getParams()[$key] ?? $default;
    }

    /**
     * Get the request body
     */
    public function body($key = null)
    {
        if (!isset($this->body)) {
            $this->body = $this->parseBody();
        }

        if ($key) {
            return $this->body[$key] ?? null;
        }

        return $this->body;
    }

    /**
     * Get a specific field from the request body
     */
    public function getBody($key, $default = null)
    {
        return $this->body($key) ?? $default;
    }

    public function setBody($key, $value)
    {
        if (isset($this->body)) {
            $this->body[$key] = $value;
        }
    }

    public function getCookie($key, $default = null)
    {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key] ?? $default;
        }

        return $default;
    }

    /**
     * Parse the body content (form-data or JSON)
     */
    protected function parseBody()
    {
        if ($this->getMethod() === 'POST') {
            if ($this->getContentType() === 'application/json') {
                return json_decode(file_get_contents('php://input'), true) ?? [];
            }

            if ($this->getContentType() === 'application/x-www-form-urlencoded' || str_contains($this->getContentType(), 'multipart/form-data')) {
                return $_POST;
            }
        }

        // Handle other methods like PUT, PATCH
        if ($this->getMethod() === 'PUT' || $this->getMethod() === 'PATCH') {
            if ($this->getContentType() === 'application/json') {
                return json_decode(file_get_contents('php://input'), true) ?? [];
            }

            // Handle form-data for PUT/PATCH if needed
            if ($this->getContentType() === 'application/x-www-form-urlencoded' || str_contains($this->getContentType(), 'multipart/form-data')) {
                parse_str(file_get_contents('php://input'), $data);
                return $data;
            }
        }

        return [];
    }

    /**
     * Get the content type of the request
     */
    public function getContentType()
    {
        return $_SERVER['CONTENT_TYPE'] ?? 'application/x-www-form-urlencoded';
    }

    /**
     * Get all HTTP headers
     */
    protected function getHeaders()
    {
        if (!function_exists('getallheaders')) {
            // For NGINX and environments where getallheaders is unavailable
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) === 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }

        return getallheaders();
    }

    /**
     * Get a specific header
     */
    public function getHeader($key)
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * Get the request method
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function currentPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}
