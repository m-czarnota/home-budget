<?php

declare(strict_types=1);

namespace App\Tests\Common;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

readonly class HomeBudgetKernelBrowser
{
    public function __construct(
        private KernelBrowser $browser,
    ) {
    }

    public function json(string $method, string $url, mixed $data = null, array $headers = []): void
    {
        $this->browser->request($method, $url, server: $headers, content: $this->mapContent($data));
    }

    public function getLastResponseContent(): string|false
    {
        return $this->browser->getResponse()->getContent();
    }

    public function getLastResponseCode(): int
    {
        return $this->browser->getResponse()->getStatusCode();
    }

    public function getLastResponseContentAsArray(): array
    {
        return json_decode($this->getLastResponseContent(), true);
    }

    private function mapContent(mixed $data): string|false|null
    {
        $content = null;

        if (is_array($data) && !empty($data)) {
            $content = json_encode($data);
        }

        if (is_string($data) && mb_strlen($data) > 0) {
            $content = $data;
        }

        return $content;
    }
}
