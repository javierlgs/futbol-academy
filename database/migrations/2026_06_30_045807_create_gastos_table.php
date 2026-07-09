<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('gasto_categorias');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores');
            $table->string('numero_factura')->nullable();
            $table->text('descripcion');
            $table->date('fecha');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('forma_pago', ['EFECTIVO', 'TRANSFERENCIA', 'TARJETA', 'CHEQUE']);
            $table->string('comprobante')->nullable(); // path del archivo
            $table->boolean('tiene_iva')->default(true);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('gastos');
    }
};