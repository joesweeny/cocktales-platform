<?php

namespace Cocktales\Framework\Uuid;

use Ramsey\Uuid\Uuid as BaseUuid;

class Uuid implements \JsonSerializable
{
    /**
     * @var BaseUuid
     */
    private $inner;
    /**
     * @param $string
     */
    public function __construct($string)
    {
        $this->inner = BaseUuid::fromString($string);
    }
    /**
     * @return Uuid
     */
    public static function generate(): Uuid
    {
        return new Uuid(BaseUuid::uuid4()->toString());
    }

    /**
     * Get a 16-bit binary representation of this UUID.
     *
     * @return string
     */
    public function toBinary()
    {
        return $this->inner->getBytes();
    }

    /**
     * Get the hexidecimal representation of this UUID. This will be UUID less and hyphens.
     *
     * @return string
     */
    public function toHex(): string
    {
        return $this->inner->getHex();
    }

    /**
     * @param $bits
     * @return Uuid
     */
    public static function createFromBinary($bits)
    {
        return new Uuid(BaseUuid::fromBytes($bits));
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->inner->__toString();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }
}