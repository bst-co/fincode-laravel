<?php

namespace Fincode\Laravel\Test\Feature\Eloquent;

use Fincode\Laravel\Enums\FinHistoryType;
use Fincode\Laravel\Models\FinCard;
use Fincode\Laravel\Test\TestCase;

class FinCardHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../../../database/migrations');
    }

    public function test_updating_card_appends_history(): void
    {
        $card = FinCard::factory()->create([
            'holder_name' => 'BEFORE NAME',
        ]);

        $this->assertSame(1, $card->histories()->count());

        $insertHistory = $card->histories()->firstOrFail();

        $this->assertSame(FinHistoryType::INSERT, $insertHistory->type);

        $this->assertTrue($card->update([
            'holder_name' => 'AFTER NAME',
        ]));
        $this->assertSame('AFTER NAME', $card->fresh()->holder_name);
        $this->assertSame(2, $card->histories()->count());

        $updates = $card->histories()
            ->where('type', FinHistoryType::UPDATE->value)
            ->get();

        $this->assertCount(1, $updates);

        $history = $updates->first();

        $this->assertSame(FinHistoryType::UPDATE, $history->type);
        $this->assertSame($card->getKey(), $history->source_id);
        $this->assertSame($card->getMorphClass(), $history->source_type);
        $this->assertSame(
            ['holder_name' => 'AFTER NAME'],
            $history->difference,
        );
        $this->assertTrue(
            $card->histories()->whereKey($insertHistory->getKey())->exists(),
        );
    }
}
