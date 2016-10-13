<?php
namespace iRESTful\DSLs\Domain\Projects\Types\Exceptions;

final class TypeException extends \Exception {
    const CODE = 1;
    public function __construct($message, \Exception $parentException = null) {
        parent::__construct($message, self::CODE, $parentException);
    }
}
