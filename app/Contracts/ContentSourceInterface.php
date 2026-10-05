<?php

namespace App\Contracts;

interface ContentSourceInterface
{
    public function synchronize(): void;
}
