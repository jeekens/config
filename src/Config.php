<?php declare(strict_types=1);

namespace Jeekens\Config;


use Jeekens\Basics\Spl\Arrayable;
use Jeekens\Config\Exception\WriteInhibitException;
use function strval;

/**
 * Class Config
 *
 * @package Jeekens\Config
 */
class Config implements \ArrayAccess, \Countable, ConfigInterface
{

    /**
     * @var array
     */
    protected $configure = [];

    /**
     * @var bool
     */
    protected $readOnly = false;

    /**
     * Config constructor.
     *
     * @param array|null $arrayConfig
     * @param bool $readOnly
     *
     * @throws WriteInhibitException
     */
    public function __construct(?array $arrayConfig = null, bool $readOnly = false)
    {
        foreach ($arrayConfig as $key => $value) {
            $this->offsetSet($key, $value);
        }

        if ($readOnly) {
            $this->readOnly();
        }
    }

    /**
     * @param mixed $index
     *
     * @return bool
     */
    public function offsetExists($index): bool
    {
        return $this->exists($index);
    }

    /**
     * @param $index
     * @param null $defaultValue
     *
     * @return mixed
     */
    public function get($index, $defaultValue = null)
    {
        $index = strval($index);

        if ($this->exists($index)) {
            return $this->configure[$index];
        }

        return $defaultValue;
    }


    public function readOnly()
    {
        $this->readOnly = true;
    }


    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * @param $index
     * @param mixed $value
     *
     * @throws WriteInhibitException
     */
    public function set($index, $value)
    {
        if ($this->isReadOnly()) {
            throw new WriteInhibitException('Current configuration prohibits writing.');
        }

        $index = strval($index);

        if (is_array($value)) {
            $this->configure[$index] = new self($value);
        } else {
            $this->configure[$index] = $value;
        }
    }

    /**
     * @param $index
     * @param mixed $value
     *
     * @throws WriteInhibitException
     */
    public function ifSet($index, $value)
    {
        $index = strval($index);

        if (! $this->exists($index)) {
            $this->set($index, $value);
        }
    }

    /**
     * @param mixed $index
     *
     * @return mixed
     */
    public function offsetGet($index)
    {
        return $this->get($index);
    }

    /**
     * @param mixed $index
     * @param mixed $value
     *
     * @throws WriteInhibitException
     */
    public function offsetSet($index, $value)
    {
        $this->set($index, $value);
    }

    /**
     * @param mixed $index
     */
    public function offsetUnset($index)
    {
        $index = strval($index);

        unset($this->configure[$index]);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $arrayConfig = [];
        foreach ($this->configure as $key => $value) {
            if (is_object($value)) {
                if ($value instanceof Arrayable) {
                    $arrayConfig[$key] = $value->toArray();
                } else {
                    $arrayConfig[$key] = $value;
                }
            } else {
                $arrayConfig[$key] = $value;
            }
        }

        return $arrayConfig;
    }

    /**
     * @param $index
     *
     * @return bool|mixed
     */
    public function exists($index)
    {
        $index = strval($index);

        return isset($this->configure[$index]);
    }

    /**
     * @param $index
     *
     * @return mixed
     */
    public function __get($index)
    {
        return $this->get($index);
    }

    /**
     * @param $index
     * @param $value
     *
     * @throws WriteInhibitException
     */
    public function __set($index, $value)
    {
        $this->set($index, $value);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->configure);
    }
}