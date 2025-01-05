<?php

declare(strict_types=1);

namespace PlanB\DS\Map\Traits;

/**
 * @template Key of string|int
 * @template Value
 */
trait MapMutableTrait
{
    /**
     * @use MapTrait<Key, Value>
     */
    use MapTrait;

    /**
     * @param Value[] $input
     */
    public function putAll(iterable $input): static
    {
        $data = $this->sanitize($input);
        $this->data = [
            ...$this->data,
            ...$data,
        ];

        return $this;
    }

    /**
     * @param Key $key
     * @param Value $value
     */
    public function put(mixed $key, mixed $value): static
    {
        return $this->putAll([
            $key => $value,
        ]);
    }

    /**
     * @param Key $key
     */
    public function remove(mixed $key): static
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * @param Value $value
     */
    public function removeValue(mixed $value): static
    {
        $key = $this->find($value);
        if (null === $key) {
            return $this;
        }

        return $this->remove($key);
    }
}
