<?php

namespace Jeekens\Config;


use Jeekens\Basics\Spl\Arrayable;

interface ConfigInterface extends Arrayable
{
    /**
     * 获取配置项
     *
     * @param $index
     * @param mixed $defaultValue
     *
     * @return mixed | ConfigInterface
     */
    public function get($index, $defaultValue = null);

    /**
     * 设置配置值
     *
     * @param $index
     * @param mixed $value
     */
    public function set($index, $value);

    /**
     * 如果配置不存在则设置配置值
     *
     * @param $index
     * @param mixed $value
     */
    public function ifSet( $index, $value);

    /**
     * 判断配置是否存在
     *
     * @param $index
     *
     * @return mixed
     */
    public function exists($index);

    /**
     * 设置只读模式
     */
    public function readOnly();

    /**
     * 判断是否为只读模式
     *
     * @return bool
     */
    public function isReadOnly(): bool;
}