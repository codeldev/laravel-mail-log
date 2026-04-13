<?php

declare(strict_types=1);

namespace CodelDev\LaravelMailLog\Database\Factories;

use CodelDev\LaravelMailLog\Models\LaravelMailLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LaravelMailLog> */
class LaravelMailLogFactory extends Factory
{
    /** @var class-string<LaravelMailLog> */
    protected $model = LaravelMailLog::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'from'        => fake()->safeEmail(),
            'to'          => $this->fakeEmails(),
            'cc'          => fake()->boolean(30) ? $this->fakeEmails(1, 2) : null,
            'bcc'         => fake()->boolean(20) ? $this->fakeEmails(1, 2) : null,
            'subject'     => fake()->sentence(),
            'body'        => fake()->paragraphs(3, true),
            'headers'     => 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=utf-8',
            'attachments' => null,
        ];
    }

    public function withAttachments(int $count = 1): static
    {
        return $this->state([
            'attachments' => collect(range(1, $count))
                ->map(fn (): string => fake()->word() . '.' . fake()->randomElement(['pdf', 'jpg', 'png', 'docx']))
                ->all(),
        ]);
    }

    /**
     * @return array<int, string>
     *
     * @noinspection PhpSameParameterValueInspection
     */
    private function fakeEmails(int $min = 1, int $max = 3): array
    {
        return collect(range(1, fake()->numberBetween($min, $max)))
            ->map(fn () => fake()->safeEmail())
            ->all();
    }
}
