<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DomainException extends \Exception
{
    public function __construct(
        protected string $internalCode,
        string $message,
        private ?array $data = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            $previous ? $previous->getCode() : 0,
            $previous
        );
    }

    public function getInternalCode(): string
    {
        return $this->internalCode;
    }

    public function getData(): ?array
    {
        return $this->data;
    }
}
