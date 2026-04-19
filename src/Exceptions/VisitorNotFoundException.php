<?php

namespace Church\Exceptions;

class VisitorNotFoundException extends ChurchException
{
    public function __construct(string $id)
    {
        parent::__construct("Visitante não encontrado: {$id}");
    }
}
