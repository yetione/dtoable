<?php


namespace Yetione\DTO\Support;


use Exception;

trait MagicGetter
{

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception(sprintf('Access to undefined property "%s"', $name));
    }
}