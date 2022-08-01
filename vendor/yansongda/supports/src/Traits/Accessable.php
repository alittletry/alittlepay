<?php

declare(strict_types=1);

namespace Yansongda\Supports\Traits;

trait Accessable
{
    /**
     * __get.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * __set.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * get.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param null $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key = null, $default = null)
    {
        if (is_null($key)) {
            return method_exists($this, 'toArray') ? $this->toArray() : $default;
        }

        $method = 'get';
        foreach (explode('_', $key) as $item) {
            $method .= ucfirst($item);
        }

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return $default;
    }

    /**
     * set.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $method = 'set';
        foreach (explode('_', $key) as $item) {
            $method .= ucfirst($item);
        }

        if (method_exists($this, $method)) {
            return $this->{$method}($value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return !is_null($this->get($offset));
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
    }
}
