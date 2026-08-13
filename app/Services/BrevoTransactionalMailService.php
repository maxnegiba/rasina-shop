<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class BrevoTransactionalMailService
{
    /**
     * @param array<int, string|array{email: string, name?: string|null}> $to
     * @param array{email: string, name?: string|null}|null $replyTo
     * @param array<int, array{name: string, content: string}> $attachments
     * @param list<string> $tags
     */
    public function send(
        array $to,
        string $subject,
        ?string $htmlContent = null,
        ?string $textContent = null,
        ?array $replyTo = null,
        array $attachments = [],
        array $tags = [],
    ): string {
        $apiKey = trim((string) config('services.brevo.api_key'));
        $endpoint = trim((string) config('services.brevo.endpoint'));
        $senderEmail = trim((string) config('services.brevo.sender_email'));
        $senderName = trim((string) config('services.brevo.sender_name'));

        if ($apiKey === '') {
            throw new RuntimeException('BREVO_API_KEY is not configured.');
        }

        if ($endpoint === '' || ! filter_var($endpoint, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Brevo transactional email endpoint is invalid.');
        }

        if (! filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('BREVO_SENDER_EMAIL is not a valid email address.');
        }

        $recipients = $this->normalizeRecipients($to);

        if ($recipients === []) {
            throw new RuntimeException('Brevo email has no valid recipients.');
        }

        if ($htmlContent === null && $textContent === null) {
            throw new RuntimeException('Brevo email must contain HTML or plain text content.');
        }

        $payload = [
            'sender' => array_filter([
                'email' => $senderEmail,
                'name' => $senderName !== '' ? $senderName : null,
            ], static fn ($value): bool => $value !== null && $value !== ''),
            'to' => $recipients,
            'subject' => $subject,
        ];

        if ($htmlContent !== null) {
            $payload['htmlContent'] = $htmlContent;
        } else {
            $payload['textContent'] = $textContent;
        }

        $effectiveReplyTo = $replyTo ?? $this->configuredReplyTo();

        if ($effectiveReplyTo && filter_var($effectiveReplyTo['email'] ?? null, FILTER_VALIDATE_EMAIL)) {
            $payload['replyTo'] = array_filter([
                'email' => $effectiveReplyTo['email'],
                'name' => ($effectiveReplyTo['name'] ?? null) ?: null,
            ], static fn ($value): bool => $value !== null && $value !== '');
        }

        if ($attachments !== []) {
            $payload['attachment'] = array_values(array_map(
                static fn (array $attachment): array => [
                    'name' => $attachment['name'],
                    'content' => $attachment['content'],
                ],
                $attachments,
            ));
        }

        if ($tags !== []) {
            $payload['tags'] = array_values(array_unique(array_filter($tags)));
        }

        $response = Http::acceptJson()
            ->asJson()
            ->withHeaders(['api-key' => $apiKey])
            ->timeout(15)
            ->post($endpoint, $payload);

        if (! $response->successful()) {
            throw new RuntimeException('Brevo transactional email request failed with HTTP '.$response->status().'.');
        }

        return (string) ($response->json('messageId') ?? '');
    }

    /** @param array<int, string|array{email: string, name?: string|null}> $recipients */
    private function normalizeRecipients(array $recipients): array
    {
        $normalized = [];

        foreach ($recipients as $recipient) {
            if (is_string($recipient)) {
                $email = trim($recipient);
                $name = null;
            } else {
                $email = trim((string) ($recipient['email'] ?? ''));
                $name = trim((string) ($recipient['name'] ?? '')) ?: null;
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $normalized[$email] = array_filter([
                'email' => $email,
                'name' => $name,
            ], static fn ($value): bool => $value !== null && $value !== '');
        }

        return array_values($normalized);
    }

    /** @return array{email: string, name?: string}|null */
    private function configuredReplyTo(): ?array
    {
        $email = trim((string) config('services.brevo.reply_to_email'));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $name = trim((string) config('services.brevo.reply_to_name'));

        return array_filter([
            'email' => $email,
            'name' => $name !== '' ? $name : null,
        ], static fn ($value): bool => $value !== null && $value !== '');
    }
}
