<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_can_be_created_updated_and_deleted(): void
    {
        $this->postJson('/items', [
            'name' => 'Pensil',
            'description' => 'Pensil 2B',
            'price' => 2500,
            'stock' => 10,
        ])->assertOk()->assertJson(['status' => true]);

        $this->assertDatabaseHas('items', [
            'name' => 'Pensil',
            'stock' => 10,
        ]);

        $itemId = (int) \DB::table('items')->where('name', 'Pensil')->value('id');

        $this->putJson('/items', [
            'id' => $itemId,
            'name' => 'Pensil HB',
            'description' => 'Pensil HB',
            'price' => 3000,
            'stock' => 8,
        ])->assertOk()->assertJson(['status' => true]);

        $this->assertDatabaseHas('items', [
            'id' => $itemId,
            'name' => 'Pensil HB',
            'stock' => 8,
        ]);

        $this->deleteJson('/items', ['id' => $itemId])
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertDatabaseMissing('items', ['id' => $itemId]);
    }
}
