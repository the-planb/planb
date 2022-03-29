<?php

declare(strict_types=1);

namespace PlanB\DS\Attribute;

use Attribute;
use ReflectionClass;

#[Attribute]
final class ElementType
{
    private array $types;

    public function __construct(string ...$types)
    {
        $this->types = $types;
    }

    public static function fromClass(object|string $objectOrClassname): self
    {
        $reflection = new ReflectionClass($objectOrClassname);
        $attributes = $reflection->getAttributes(self::class);

        if (empty($attributes)) {
            return new self();
        }

        return $attributes[0]->newInstance();
    }

    /**
     * @return string[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
