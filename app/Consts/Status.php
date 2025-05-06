<?php

namespace App\Consts;

class Status {
    const PENDING = 0;
    const PROCESSING = 1;
    const FAILED = 2;
    const COMPLETED = 3;

    public static function all() {
        $reflector = new \ReflectionClass(self::class);
        return array_flip($reflector->getConstants());
    }
}
