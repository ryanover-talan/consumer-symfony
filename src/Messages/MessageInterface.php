<?php

namespace App\Messages;

interface MessageInterface
{
    /**
     * @param string $type
     * @return bool
     */
    public function checkType(string $type): bool;

    /**
     * @return bool
     */
    public function isValid(): bool;
}