<?php

declare(strict_types=1);

namespace Zoho\Crm\Exceptions;

use Throwable;

class AsyncBatchRequestException extends Exception
{
    /** The actual exception that was thrown */
    protected Throwable $wrappedException;

    /** The key of the failed request inside the batch */
    protected string|int $keyInBatch;

    /**
     * The constructor.
     *
     * @param \Throwable $wrappedException The actual exception that was thrown
     * @param string|int $keyInBatch The key of the failed request inside the batch
     */
    public function __construct(Throwable $wrappedException, string|int $keyInBatch)
    {
        $this->wrappedException = $wrappedException;
        $this->keyInBatch = $keyInBatch;
    }

    /**
     * Get the actual exception that was thrown by the HTTP layer.
     */
    public function getWrappedException(): Throwable
    {
        return $this->wrappedException;
    }

    /**
     * Get the key of the failed request inside the batch.
     */
    public function getKeyInBatch(): string|int
    {
        return $this->keyInBatch;
    }
}
