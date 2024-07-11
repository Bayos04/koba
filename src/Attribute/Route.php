<?php

namespace App\Attribute;

use \Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
class Route
{
    /**
     * @param string $path Route url
     * @param string $method Http method to reach the endpoint. Possible value "GET"|"POST"|"PUT"|"PATCH"
     * @param array $guard Array of roles that can reach the endpoint. Possible values "NONE"|"USER"|"ADMIN"
     */
    public function __construct(private readonly string $path, private readonly string $method = "GET", private readonly array $guard = ["NONE"])
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getGuard() : array
	{
        return $this->guard;
    }
}
