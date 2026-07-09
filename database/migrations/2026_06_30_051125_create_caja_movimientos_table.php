<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('caja_movimientos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->enum('tipo', ['APERTURA', 'INGRESO', 'EGRESO', 'CIERRE']);
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->decimal('saldo', 10, 2); // saldo después del movimiento
            $table->enum('metodo', ['EFECTIVO', 'TRANSFERENCIA', 'TARJETA', 'CHEQUE'])->default('EFECTIVO');
            $table->text('observacion')->nullable();
            $table->foreignId('factura_id')->nullable()->constrained('facturas');
            $table->foreignId('gasto_id')->nullable()->constrained('gastos');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            
            $table->index(['fecha', 'tipo']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('caja_movimientos');
    }
};