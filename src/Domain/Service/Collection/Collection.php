<?php

declare(strict_types=1);

namespace App\Domain\Service\Collection;

use Traversable;

/** @template T */
abstract class Collection implements \IteratorAggregate, \Countable
{
    protected array $elements = [];

    /**
     * @param array<T> $elements
     * @throws \InvalidArgumentException
     */
    public function __construct(...$elements)
    {
        $this->validate(...$elements);
        $this->elements = $elements;
    }

    /**
     * @param array<T> $elements
     * @throws \InvalidArgumentException
     */
    private function validate(...$elements): void
    {
        foreach ($elements as $element) {
            if (!\is_a($element, $this->getType())) {
                $class = \is_array($element) ? 'array' : \get_class($element);
                throw new \InvalidArgumentException(
                    $class . " provided to a collection of {$this->getType()}"
                );
            }
        }
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @param T $element
     * @throws \InvalidArgumentException
     */
    public function add($element): void
    {
        $this->validate($element);
        $this->elements[] = $element;
    }

    public function map(\Closure $closure): array
    {
        return \array_map($closure, $this->elements);
    }

    public function first(): mixed
    {
        return $this->elements[0] ?? null;
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    /** @return class-string<T> */
    abstract protected function getType(): string;
}
