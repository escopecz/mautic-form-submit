<?php

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
     *
     * @var array
     */
    protected $store = [];

    /**
     * Get cookie with FILTER_SANITIZE_STRING
     *
     * @param  string $key
     *
     * @return string|null
     */
    public function get($key)
    {
        if (isset($this->store[$key])) {
            return filter_var($this->store[$key], FILTER_SANITIZE_STRING);
        }

        return filter_input(INPUT_COOKIE, $key, FILTER_SANITIZE_STRING);
    }

    /**
     * Get cookie with FILTER_SANITIZE_NUMBER_INT
     *
     * @param  string $key
     *
     * @return int|null
     */
    public function getInt($key)
    {
        if (isset($this->store[$key])) {
            return (int) filter_var($this->store[$key], FILTER_SANITIZE_NUMBER_INT);
        }

        return (int) filter_input(INPUT_COOKIE, $key, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Set a cookie value
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     */
    public function set($key, $value)
    {
        $this->store[$key] = $value;

        return setcookie($key, $value);
    }

    /**
     * Unset the key from the cookie
     *
     * @param string $key
     *
     * @return Cookie
     */
    public function clear($key)
    {
        setcookie($key, '', time() - 3600);
        unset($_COOKIE[$key]);
        unset($this->store[$key]);

        return $this;
    }

    /**
     * Returns $_COOKIE
     *
     * @return array
     */
    public function getSuperGlobalCookie()
    {
        return $_COOKIE;
    }

    /**
     * Return all cookies as array merged with current state
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->getSuperGlobalCookie(), $this->store);
    }

    /**
     * Creates unique cookie file in system tmp dir and returns absolute path to it.
     *
     * @return string|false
     */
    public function createCookieFile()
    {
        return tempnam(sys_get_temp_dir(), 'mauticcookie');
    }
}
