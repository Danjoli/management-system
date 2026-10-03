<?php

declare(strict_types=1);

namespace App\Core;

final readonly class Response
{
    /** @param array<string, string> $headers */
    public function __construct(public string $content = '', public int $status = 200, public array $headers = [])
    {
    }

    public static function redirect(string $location, int $status = 302): self
    {
        return new self('', $status, ['Location' => $location]);
    }

    /** @param array<string, mixed> $data */
    public static function json(array $data, int $status = 200): self
    {
        return new self((string) json_encode($data, JSON_THROW_ON_ERROR), $status, ['Content-Type' => 'application/json']);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        echo $this->content;
    }
}
