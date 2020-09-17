<?php


namespace Yetione\DTO\Support;


use Exception;

trait MagicGetter
{

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception(sprintf('Access to undefined property "%s"', $name));
    }
}