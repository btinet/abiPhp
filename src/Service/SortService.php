<?php

namespace App\Service;

class SortService
{
    private string $method;

    public function __construct(string $attribute)
    {
        $this->method = 'get' . ucfirst($attribute);
    }

    public function __invoke($a,$b): int
    {
        if(method_exists($a,$this->method) and method_exists($b,$this->method)) {
            return $a->{$this->method}() <=> $b->{$this->method}();
        }
        return 0;
    }

    public function __toString()
    {
        return $this->method;
    }
}
