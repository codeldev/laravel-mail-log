<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use CodelDev\LaravelMailLog\Listeners\LaravelLogMailSent;
use CodelDev\LaravelMailLog\Models\LaravelMailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

it('logs an email when MessageSending event is fired', function (): void
{
    event(new MessageSending(createMessage()));

    expect(LaravelMailLog::count())
        ->toBe(1);

    $log = LaravelMailLog::first();

    expect($log->from)
        ->toBe('sender@example.com')
        ->and($log->to)
        ->toBe(['recipient@example.com'])
        ->and($log->subject)
        ->toBe('Test Subject')
        ->and($log->body)
        ->toContain('Test body');
});

it('logs multiple recipients', function (): void
{
    event(new MessageSending(createMessage(to: $emails = [
        'one@example.com',
        'two@example.com',
    ])));

    $log = LaravelMailLog::first();

    expect($log->to)
        ->toBe($emails);
});

it('logs cc and bcc addresses', function (): void
{
    event(new MessageSending(createMessage(
        cc: $cc   = ['cc@example.com'],
        bcc: $bcc = ['bcc@example.com'],
    )));

    $log = LaravelMailLog::first();

    expect($log->cc)
        ->toBe($cc)
        ->and($log->bcc)
        ->toBe($bcc);
});

it('stores null for empty cc and bcc', function (): void
{
    event(new MessageSending(createMessage()));

    $log = LaravelMailLog::first();

    expect($log->cc)
        ->toBeNull()
        ->and($log->bcc)
        ->toBeNull();
});

it('logs email headers', function (): void
{
    event(new MessageSending(createMessage()));

    expect(LaravelMailLog::first()->headers)
        ->toContain('From: sender@example.com')
        ->toContain('To: recipient@example.com');
});

it('stores null attachments when there are none', function (): void
{
    event(new MessageSending(createMessage()));

    expect(LaravelMailLog::first()->attachments)
        ->toBeNull();
});

it('logs attachments', function (): void
{
    $message = createMessage();
    $message->attach('file content', $file = 'report.pdf', 'application/pdf');

    event(new MessageSending($message));

    expect(LaravelMailLog::first()->attachments)
        ->toBe([$file]);
});

it('does not prevent mail from being sent when logging fails', function (): void
{
    config()
        ->set('mail-log.table', 'nonexistent_table');

    event(new MessageSending(createMessage()));

    expect(true)
        ->toBeTrue();
});

it('registers the listener for MessageSending', function (): void
{
    Event::fake([MessageSending::class]);

    Event::assertListening(
        MessageSending::class,
        LaravelLogMailSent::class,
    );
});

function createMessage(
    ?string $from = 'sender@example.com',
    array $to = ['recipient@example.com'],
    array $cc = [],
    array $bcc = [],
    string $subject = 'Test Subject',
    string $body = '<p>Test body</p>',
): Email {
    $email = new Email;

    if ($from !== null)
    {
        $email->from(new Address($from));
    }

    $email->to(...array_map(fn (string $addr) => new Address($addr), $to));

    if ($cc !== [])
    {
        $email->cc(...array_map(fn (string $addr) => new Address($addr), $cc));
    }

    if ($bcc !== [])
    {
        $email->bcc(...array_map(fn (string $addr) => new Address($addr), $bcc));
    }

    $email->subject($subject);
    $email->html($body);

    return $email;
}
