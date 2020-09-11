<?php


namespace Yetione\DTO;


use Yetione\DTO\Exception\SerializerException;

class DTO
{
    protected static Serializer $instance;

    /**
     * @param DTOInterface $oDto
     * @param array $aContext
     * @return string|null
     */
    public static function serialize(DTOInterface $oDto, array $aContext=[]): ?string
    {
        try {
            return self::getInstance()->serialize($oDto, $aContext);
        } catch (SerializerException $e) {
            return null;
        }
    }
    /**
     * @param DTOInterface $oDTO
     * @param string|null $sFormat
     * @param array $aContext
     * @return array|null
     */
    public static function toArray(DTOInterface $oDTO, ?string $sFormat=null, array $aContext=[]): ?array
    {
        try {
            return self::getInstance()->toArray($oDTO, $sFormat, $aContext);
        } catch (SerializerException $e) {
            return null;
        }
    }

    /**
     * @param string $sData
     * @param string $sClassName
     * @param array $aContext
     * @return DTOInterface|null
     */
    public static function deserialize(string $sData, string $sClassName, array $aContext=[]): ?DTOInterface
    {
        try {
            return self::getInstance()->deserialize($sData, $sClassName, $aContext);
        } catch (SerializerException $e) {
            return null;
        }
    }

    /**
     * @param array $aData
     * @param string $sClassName
     * @param string|null $sFormat
     * @param array $aContext
     * @return DTOInterface|null
     */
    public static function fromArray(array $aData, string $sClassName, ?string $sFormat = null, array $aContext=[]): ?DTOInterface
    {
        try {
            return self::getInstance()->fromArray($aData, $sClassName, $sFormat, $aContext);
        } catch (SerializerException $e) {
            return null;
        }
    }

    protected static function getInstance(): Serializer
    {
        if (!isset(self::$instance)) {
            self::$instance = new Serializer();
        }
        return self::$instance;
    }


}