<?php

namespace Church\Exceptions;

class MemberNotFoundException extends ChurchException
{
    public function __construct(string $id)
    {
        parent::__construct("Membro não encontrado: {$id}");
    }
}
