<?php


namespace Yetione\DTO\Support;


trait MagicSetter
{

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            var_dump($name, $value);
            $this->$name = $value;
        }
    }
}