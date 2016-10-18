<?php
namespace iRESTful\DSLs\Domain\Projects\Controllers\HttpRequests\Commands\Exceptions;

final class CommandException extends \Exception {
    const CODE = 1;
    public function __construct($message, \Exception $parentException = null) {
        parent::__construct($message, self::CODE, $parentException);
    }
}