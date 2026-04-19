<?php

namespace Church\Contracts;

interface WhatsAppSenderInterface
{
    /**
     * Envia uma mensagem de texto para o número informado.
     *
     * @param  string $phone   Número no formato E.164 sem "+" (ex: 5511999999999)
     * @param  string $message Texto da mensagem
     * @return bool            true em caso de sucesso
     */
    public function send(string $phone, string $message): bool;
}
