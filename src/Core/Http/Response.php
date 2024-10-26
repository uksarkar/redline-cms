<?php

namespace RedlineCms\Core\Http;

use RedlineCms\Core\Support\Session;
use RedlineCms\Core\Support\View;

class Response
{
    private const BODY_TYPE_STR = 0;
    private const BODY_TYPE_JSON = 1;
    private const BODY_TYPE_VIEW = 2;

    private mixed $body;
    private int $statusCode;
    private array $headers;
    private array $cookies = [];
    private array $sessions = [];
    private array $viewContext = [];
    private int $bodyType = 0;

    public function __construct($body = '', int $statusCode = 200, array $headers = [])
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    // Set status code
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    // Add headers
    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    // Method to set a cookie
    public function setCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httponly = false
    ) {
        $this->cookies[] = [
            'name' => $name,
            'value' => $value,
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
        ];

        return $this;
    }

    public function with(array $sessions): self
    {
        $this->sessions += $sessions;
        return $this;
    }

    public function setBodyType(int $type): self
    {
        $this->bodyType = $type;
        return $this;
    }

    public function setViewContext(array $context): self
    {
        $this->viewContext += $context;
        return $this;
    }

    // Send the response
    public function send()
    {
        // Send status code
        http_response_code($this->statusCode);

        // Send headers
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        // Set cookies
        foreach ($this->cookies as $cookie) {
            setcookie($cookie['name'], $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
        }

        foreach ($this->sessions as $key => $value) {
            Session::set($key, $value);
        }
        session_write_close();

        // Send body
        if ($this->bodyType === static::BODY_TYPE_JSON && is_array($this->body)) {
            echo json_encode($this->body);
        } else if ($this->bodyType === static::BODY_TYPE_VIEW) {
            echo View::render($this->body, $this->viewContext);
        } else {
            echo $this->body;
        }
    }

    // Create response from string
    public static function fromString(string $body, int $statusCode = 200): self
    {
        return (new self($body, $statusCode))
            ->addHeader('Content-Type', 'text/html');
    }

    // Create response from array (JSON)
    public static function fromArray(array $data, int $statusCode = 200): self
    {
        return (new self($data, $statusCode))
            ->setBodyType(static::BODY_TYPE_JSON)
            ->addHeader('Content-Type', 'application/json');
    }

    // Helper method to detect the response type and create the appropriate response object
    public static function create($response)
    {
        if ($response instanceof self) {
            // If it's already a Response object, return as-is
            return $response;
        } elseif (is_array($response)) {
            // Convert array to JSON response
            return self::fromArray($response);
        } elseif (is_string($response)) {
            // Convert string to HTML response
            return self::fromString($response);
        } elseif (is_object($response) && method_exists($response, "toResponse")) {
            return $response->toResponse();
        }

        // Default to a plain text response
        return self::fromString('Invalid Response Type', 500);
    }

    public static function json(array $data, int $statusCode = 200): self
    {
        return static::fromArray($data, $statusCode);
    }

    public static function view(string $view, array $context = [], int $statusCode = 200)
    {
        return static::fromString($view, $statusCode)
            ->setBodyType(static::BODY_TYPE_VIEW)
            ->setViewContext($context);
    }

    public static function notFound(string|null $heading = null)
    {
        return static::view("error.html", ["error" => 404, "heading" => $heading], 404);
    }

    public static function redirect(string $to, int $code = 302)
    {
        return new static('', $code, ["Location" => $to]);
    }
    public static function back(array $queries = [], int $code = 302)
    {
        $previousUrl = $_SERVER['HTTP_REFERER'] ?? '/';

        if (!empty($queries)) {
            $queryString = http_build_query($queries);
            $previousUrl .= (strpos($previousUrl, '?') === false ? '?' : '&') . $queryString;
        }

        return static::redirect($previousUrl, $code);
    }
}
