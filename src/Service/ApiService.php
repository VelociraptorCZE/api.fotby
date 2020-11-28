<?php
declare(strict_types=1);

namespace App\Service;

use App\Config;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ApiService
{
    protected array $requiredPayloadKeys = [];
    protected string $defaultErrorMessage = '';

    protected function validatePayload(array $payload): void
    {
        $payloadKeys = [...$this->requiredPayloadKeys, 'apiKey'];

        $hasPayloadAllKeys = count(array_filter(
            array_map(fn (string $key) => isset($payload[$key]), $payloadKeys)
        )) === count($payloadKeys);

        $isPayloadValid = $hasPayloadAllKeys && $payload['apiKey'] === Config::SECRET_API_KEY;

        if (!$isPayloadValid) {
            throw new BadRequestException($this->defaultErrorMessage);
        }
    }
}