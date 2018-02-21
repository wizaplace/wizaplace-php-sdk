<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class ProductImageUpload
{
    /** @var string */
    private $name;

    /** @var string */
    private $mimeType;

    /** @var string */
    private $data;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function setBase64Data(string $base64Data): self
    {
        $this->data = $base64Data;

        return $this;
    }

    /**
     * @internal
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        foreach ($metadata->getReflectionClass()->getProperties() as $prop) {
            $metadata->addPropertyConstraint($prop->getName(), new Constraints\NotNull());
        }
    }

    /**
     * @internal
     * @return array
     */
    public function toArray(): array
    {
        return [
            'image_name' => $this->name,
            'image_type' => $this->mimeType,
            'image_data' => $this->data,
        ];
    }

    public static function createFromPSR7UploadedFile(UploadedFileInterface $uploadedFile): self
    {
        $object = new self();
        if ($uploadedFile->getClientFilename() !== null) {
            $object->setName($uploadedFile->getClientFilename());
        }

        if ($uploadedFile->getClientMediaType() !== null) {
            $object->setMimeType($uploadedFile->getClientMediaType());
        }

        $object->setBase64Data(base64_encode($uploadedFile->getStream()->getContents()));

        return $object;
    }

    public static function createFromSymfonyUploadedFile(UploadedFile $uploadedFile): self
    {
        $object = new self();
        if ($uploadedFile->getClientOriginalName() !== null) {
            $object->setName($uploadedFile->getClientOriginalName());
        }

        if ($uploadedFile->getClientMimeType() !== null) {
            $object->setMimeType($uploadedFile->getClientMimeType());
        }

        $object->setBase64Data(base64_encode(file_get_contents($uploadedFile->getRealPath())));

        return $object;
    }
}
