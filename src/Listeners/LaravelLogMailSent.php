<?php

declare(strict_types=1);

namespace CodelDev\LaravelMailLog\Listeners;

use CodelDev\LaravelMailLog\Enums\MailHeaderEnum;
use CodelDev\LaravelMailLog\Models\LaravelMailLog;
use Illuminate\Mail\Events\MessageSending;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\MailboxListHeader;
use Symfony\Component\Mime\Part\DataPart;
use Throwable;

final readonly class LaravelLogMailSent
{
    public function __construct(private LaravelMailLog $mailLog) {}

    public function handle(MessageSending $event): void
    {
        try
        {
            $message = $event->message;

            $this->mailLog->create([
                'from'        => $this->getSingleEmail($message, MailHeaderEnum::FROM),
                'to'          => $this->getEmailAddresses($message, MailHeaderEnum::TO),
                'cc'          => $this->getEmailAddresses($message, MailHeaderEnum::CC),
                'bcc'         => $this->getEmailAddresses($message, MailHeaderEnum::BCC),
                'subject'     => $message->getSubject(),
                'body'        => $message->getBody()->bodyToString(),
                'headers'     => $message->getHeaders()->toString(),
                'attachments' => $this->getAttachments($message),
            ]);
        }
        catch (Throwable $throwable)
        {
            report($throwable);
        }
    }

    /** @return array<int, string>|null */
    private function getEmailAddresses(Email $message, MailHeaderEnum $field): ?array
    {
        $header = $message->getHeaders()->get($field->value);

        return ($header instanceof MailboxListHeader) ? $this->collectEmails($header) : null;
    }

    /** @noinspection PhpSameParameterValueInspection */
    private function getSingleEmail(Email $message, MailHeaderEnum $field): ?string
    {
        $header = $message->getHeaders()->get($field->value);

        return ($header instanceof MailboxListHeader) ? ($header->getAddresses()[0] ?? null)?->getAddress() : null;
    }

    /** @return array<int, string> */
    private function collectEmails(MailboxListHeader $header): array
    {
        return collect($header->getAddresses())
            ->map(fn (Address $address): string => $address->getAddress())
            ->values()
            ->all();
    }

    /** @return array<int, string>|null */
    private function getAttachments(Email $message): ?array
    {
        $attachments = $message->getAttachments();

        return $attachments === [] ? null : collect($attachments)
            ->map(fn (DataPart $part): ?string => $part->getFilename())
            ->filter()
            ->values()
            ->all();
    }
}
