<?php


namespace Yetione\DTO\Support;


use Exception;

trait MagicSetter
{

    /**
     * @param $name
     * @param $value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if (!property_exists($this, $name)) {
            throw new Exception(sprintf('Access to undefined property "%s"', $name));
        }
        $this->$name = $value;
    }
}