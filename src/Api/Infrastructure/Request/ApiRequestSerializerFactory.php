<?php

declare(strict_types=1);

namespace App\Api\Infrastructure\Request;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Фабрика сериалайзера для объектов API
 */
final class ApiRequestSerializerFactory
{
    public static function create(): SerializerInterface
    {
        return new Serializer(
            [
                new ArrayDenormalizer(),
                self::createPropertyNormalizer(),
                new DateTimeNormalizer([
                    DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s.uO',
                    DateTimeNormalizer::TIMEZONE_KEY => new \DateTimeZone('GMT')
                ]),
            ],
            [
                new JsonEncoder()
            ]
        );
    }

    private static function createPropertyNormalizer(): PropertyNormalizer
    {
        return new PropertyNormalizer(
            new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())),
            null, // name converter
            self::createPropertyInfo(),
            null, // class discriminator resolver
            null, // object resolver
            []    // default context
        );
    }

    private static function createPropertyInfo(): PropertyInfoExtractor
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();

        /** @var array<PropertyListExtractorInterface> $listExtractors */
        $listExtractors = [$reflectionExtractor, $phpDocExtractor];

        $typeExtractors = [$phpDocExtractor, $reflectionExtractor];

        $descriptionExtractors = [$phpDocExtractor];

        $accessExtractors = [$reflectionExtractor];

        $propertyInitializableExtractors = [$reflectionExtractor];

        return new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            $descriptionExtractors,
            $accessExtractors,
            $propertyInitializableExtractors
        );
    }
}
