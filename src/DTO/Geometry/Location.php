<?php


namespace DTO\Geometry;


use DTO\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;


class Location implements DTOInterface
{
    /**
     * @var float
     * @Assert\Type(type="float")
     */
    protected $latitude;

    /**
     * @var float
     * @Assert\Type(type="float")
     */
    protected $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

}