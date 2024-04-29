<?php

namespace App\DTO\Messages;

use Nyholm\Psr7\UploadedFile;

readonly final class CreateMessageDTO
{
    public function __construct(
        public string $message,
        public string $area,
        public ?UploadedFile $file = null,
        public ?int $lastId = null,
        public ?string $token = null,
    ) {}

    public static function fromRequest(array $request, string $token): self
    {
        return new self(
            message: $request['message'],
            area: $request['area'],
            file: $request['file'] ?? null,
            lastId: $request['lastId'] ?? null,
            token: $token,
        );
    }
}
