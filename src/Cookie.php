<?php

namespace Woody\Http\Message;

/**
 * Class Cookie
 *
 * @package Woody\Http\Message
 */
class Cookie
{

    /**
     * @var array
     */
    protected $values;

    /**
     * @var int|null
     */
    protected $expires;

    /**
     * @var string|null
     */
    protected $path;

    /**
     * @var string|null
     */
    protected $domain;

    /**
     * @var bool|null
     */
    protected $secure;

    /**
     * @var bool|null
     */
    protected $httpOnly;

    /**
     * @var string|null
     */
    protected $sameSite;

    /**
     * Cookie constructor.
     *
     * @param array $values
     * @param int|null $expires
     * @param string|null $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param bool|null $httpOnly
     * @param string|null $sameSite
     */
    public function __construct(
        array $values = [],
        int $expires = null,
        string $path = null,
        string $domain = null,
        bool $secure = null,
        bool $httpOnly = null,
        string $sameSite = null
    ) {
        $this->values = $values;
        $this->expires = $expires;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->sameSite = $sameSite;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return int|null
     */
    public function getExpires(): ?int
    {
        return $this->expires;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @return bool|null
     */
    public function getSecure(): ?bool
    {
        return $this->secure;
    }

    /**
     * @return bool|null
     */
    public function getHttpOnly(): ?bool
    {
        return $this->httpOnly;
    }

    /**
     * @return string|null
     */
    public function getSameSite(): ?string
    {
        return $this->sameSite;
    }
}
