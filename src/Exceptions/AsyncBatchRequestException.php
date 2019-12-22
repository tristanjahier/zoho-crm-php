<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class AsyncBatchRequestException extends Exception
{
    /** @var \Exception The actual exception that was thrown */
    protected $wrappedException;

    /** @var mixed The key of the failed request inside the batch */
    protected $keyInBatch;

    /**
     * The constructor.
     *
     * @param \Exception $wrappedException The actual exception that was thrown
     * @param mixed $keyInBatch The key of the failed request inside the batch
     */
    public function __construct(Exception $wrappedException, $keyInBatch)
    {
        $this->wrappedException = $wrappedException;
        $this->keyInBatch = $keyInBatch;
    }

    /**
     * Get the actual exception that was thrown by the request sender.
     *
     * @return \Exception
     */
    public function getWrappedException(): Exception
    {
        return $this->wrappedException;
    }

    /**
     * Get the key of the failed request inside the batch.
     *
     * @return mixed
     */
    public function getKeyInBatch()
    {
        return $this->keyInBatch;
    }
}
