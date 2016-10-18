<?php
namespace iRESTful\TestInstructions\Domain\CustomMethods\Exceptions;

final class CustomMethodException extemds \Exception {
    const CODE = 1;
    public function __construct($message, \Exception $parentException = null) {
        parent::__construct($message, self::CODE, $parentException);
    }
}