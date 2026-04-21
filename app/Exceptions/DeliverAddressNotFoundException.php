<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * DeliveryAddress not found.
 */
class DeliverAddressNotFoundException extends \Exception
{
    /** The error message */
    protected $message = 'Delivery address not found.';

    /** The error code */
    protected $code = 4040;
}
