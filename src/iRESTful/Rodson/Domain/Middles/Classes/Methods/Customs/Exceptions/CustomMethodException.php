<?php
namespace iRESTful\Rodson\Domain\Middles\Classes\Methods\Customs\Exceptions;

final class CustomMethodException extends \Exception {
    const CODE = 1;
    public function __construct($message, \Exception $parentException = null) {
        parent::__construct($message, self::CODE, $parentException);
    }
}
