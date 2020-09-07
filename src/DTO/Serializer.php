<?php


namespace yetione\DTO;


use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use OutOfBoundsException;
use yetione\DTO\Exception\ObjectInvalid;
use yetione\DTO\Exception\SerializerException;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Serializer
{
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Serializer constructor.
     * @throws SerializerException
     */
    public function __construct()
    {
        try {
            $oJsonEncoder = new JsonEncoder();
            $oClassMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
            $oMetadataAwareNameConverter = new MetadataAwareNameConverter($oClassMetadataFactory);
//            $oObjectNormalizer = new ObjectNormalizer($oClassMetadataFactory, $oMetadataAwareNameConverter, $oPropertyAccessor, $propertyInfo, new ClassDiscriminatorFromClassMetadata($oClassMetadataFactory));
            try {
                $oObjectNormalizer = new ObjectNormalizer($oClassMetadataFactory, $oMetadataAwareNameConverter, null, new PhpDocExtractor());
            } catch (LogicException | \LogicException  $e) {
                throw new SerializerException($e->getMessage(), $e->getCode(), $e);
            }
            try {
                $this->serializer = new \Symfony\Component\Serializer\Serializer([new ArrayDenormalizer(), $oObjectNormalizer], [$oJsonEncoder]);
            } catch (InvalidArgumentException $e) {
                throw new SerializerException($e->getMessage(), $e->getCode(), $e);
            }
            try {
                $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
            } catch (\Symfony\Component\Validator\Exception\LogicException | ValidatorException $e) {
                throw new SerializerException($e->getMessage(), $e->getCode(), $e);
            }
        } catch (AnnotationException $e) {
            throw new SerializerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param DTOInterface $oDto
     * @param array $aContext
     * @return string
     * @throws ObjectInvalid
     * @throws SerializerException
     */
    public function serialize(DTOInterface $oDto, array $aContext=[]): string
    {
        $oResult = $this->validator->validate($oDto);
        if ($oResult->count() === 0) {
            try {
                return $this->serializer->serialize($oDto, 'json', $aContext);
            } catch (NotEncodableValueException $e) {
                throw new SerializerException($e->getMessage(), $e->getCode(), $e);
            }
        }
        throw $this->buildObjectInvalidException($oResult);
    }

    /**
     * @param DTOInterface $oDTO
     * @param string|null $sFormat
     * @param array $aContext
     * @return array
     * @throws ObjectInvalid
     * @throws SerializerException
     */
    public function toArray(DTOInterface $oDTO, ?string $sFormat=null, array $aContext=[]): array
    {
        $oResult = $this->validator->validate($oDTO);
        if ($oResult->count() === 0) {
            try {
                return $this->serializer->normalize($oDTO, $sFormat, $aContext);
            } catch (ExceptionInterface $e) {
                throw new SerializerException($e->getMessage(), $e->getCode(), $e);
            }
        }
        throw $this->buildObjectInvalidException($oResult);
    }

    /**
     * @param string $sData
     * @param string $sClassName
     * @param array $aContext
     * @return DTOInterface
     * @throws ObjectInvalid
     * @throws SerializerException
     */
    public function deserialize(string $sData, string $sClassName, array $aContext=[]): DTOInterface
    {
        try {
            $oObject = $this->serializer->deserialize($sData, $sClassName, 'json', $aContext);
        } catch (NotEncodableValueException $e) {
            throw new SerializerException($e->getMessage(), $e->getCode(), $e);
        }
        $oResult = $this->validator->validate($oObject);
        if ($oResult->count() === 0) {
            return $oObject;
        }
        throw $this->buildObjectInvalidException($oResult);
    }

    /**
     * @param array $aData
     * @param string $sClassName
     * @param string|null $sFormat
     * @param array $aContext
     * @return DTOInterface
     * @throws ObjectInvalid
     * @throws SerializerException
     */
    public function fromArray(array $aData, string $sClassName, ?string $sFormat = null, array $aContext=[]): DTOInterface
    {
        try {
            $oObject = $this->serializer->denormalize($aData, $sClassName, $sFormat, $aContext);
        } catch (ExceptionInterface $e) {
            throw new SerializerException($e->getMessage(), $e->getCode(), $e);
        }
        /** @var ConstraintViolationList $oResult */
        $oResult = $this->validator->validate($oObject);
        if ($oResult->count() === 0) {
            return $oObject;
        }
        throw $this->buildObjectInvalidException($oResult);
    }

    /**
     * @param ConstraintViolationListInterface $oViolationList
     * @return ObjectInvalid
     */
    protected function buildObjectInvalidException(ConstraintViolationListInterface $oViolationList): ObjectInvalid
    {
        $aResult = [];
        $iCount = $oViolationList->count();
        for ($i=0;$i<$iCount;++$i) {
            if ($oViolationList->has($i)) {
                try {
                    $oViolation = $oViolationList->get($i);
                    $aResult[] = $oViolation->getPropertyPath().': '.$oViolation->getMessage();
                } catch (OutOfBoundsException $e) {
                }
            }
        }
        return new ObjectInvalid(implode(PHP_EOL, $aResult));

    }
}