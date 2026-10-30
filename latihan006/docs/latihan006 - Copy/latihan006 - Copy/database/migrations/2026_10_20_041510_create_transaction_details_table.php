<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {$table->id();
            // Mengubah id_transaction menjadi transaction_id sesuai konvensi Laravel
            $table->foreignId('transaction_id')
            ->constrained('transactions')
            ->cascadeOnDelete();

            // Jika ada tabel `items', gunakan relasi berikut:
            $table->foreignId('item_id')
            ->constrained('items')
            ->cascadeOnDelete();

            $table->decimal('price', 12, 2);$table->integer('quantity');
            $table->decimal('subtotal', 12, 2);$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
