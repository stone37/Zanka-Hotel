<?php

namespace App\Api\Serializer;

use App\Entity\Settings;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

class SettingsNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'AppSettingsNormalizerAlreadyCalled';

    public function __construct(private StorageInterface $storage)
    {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return !isset($context[self::ALREADY_CALLED]) && $data instanceof Settings;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $object->setFileUrl($this->storage->resolveUri($object, 'file'));

        $context[self::ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }
}