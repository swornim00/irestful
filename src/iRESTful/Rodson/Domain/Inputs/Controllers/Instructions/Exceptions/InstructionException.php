<?php
namespace iRESTful\Rodson\Domain\Inputs\Controllers\Instructions\Exceptions;

final class InstructionException extends \Exception {
    const CODE = 1;
    public function __construct($message, \Exception $parentException = null) {
        parent::__construct($message, self::CODE, $parentException);
    }
}
