<?php


namespace yetione\DTO\Geometry;


use yetione\DTO\DTOInterface;

class PointsCollection implements DTOInterface
{

    /**
     * @var Location[]
     */
    private $point;

    /**
     * PointsCollection constructor.
     * @param Location[] $point
     */
    public function __construct(array $point)
    {
        $this->point = $point;
    }

    /**
     * @return Location[]
     */
    public function getPoint()
    {
        return $this->point;
    }


}