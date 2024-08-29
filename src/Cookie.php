<?php

declare(strict_types=1);

namespace Escopecz\MauticFormSubmit;

/**
 * $_COOKIE object representation
 */
class Cookie
{
    /**
     * Store for values stored in this PHP runtime
     * because when cookie is set with setCookie
     * it's accessible on the next script run only,
     * not at the same run.
     */
    protected array $store = [];

    /**
     * Get cookie with FILTER_SANITIZE_STRING
     */
    public function get(string $key): string|false|null
    {
        if (isset($this->store[$key])) {
            return filter_var($this->store[$key], FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return filter_input(INPUT_COOKIE, $key, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * Get cookie with FILTER_SANITIZE_NUMBER_INT
     */
    public function getInt(string $key): int
    {
        if (isset($this->store[$key])) {
            return (int) filter_var($this->store[$key], FILTER_SANITIZE_NUMBER_INT);
        }

        return (int) filter_input(INPUT_COOKIE, $key, FILTER_SANITIZE_NUMBER_INT);
    }

    public function set(string $key, mixed $value): bool
    {
        $this->store[$key] = $value;

        return setcookie($key, (string) $value);
    }

    public function clear(string $key): static
    {
        setcookie($key, '', ['expires' => time() - 3600]);
        unset($_COOKIE[$key]);
        unset($this->store[$key]);

        return $this;
    }

    public function getSuperGlobalCookie(): array
    {
        return $_COOKIE;
    }

    /**
     * Return all cookies as array merged with current state
     */
    public function toArray(): array
    {
        return array_merge($this->getSuperGlobalCookie(), $this->store);
    }

    /**
     * Creates unique cookie file in system tmp dir and returns absolute path to it.
     */
    public function createCookieFile(): string|false
    {
        return tempnam(sys_get_temp_dir(), 'mauticcookie');
    }
}
