<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('jugadores')->onDelete('cascade');
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('cascade');
            
            // SRI Ecuador
            $table->string('numero_factura')->unique(); // 001-001-000000001
            $table->string('clave_acceso', 49)->unique(); // 49 dígitos
            $table->string('numero_autorizacion')->nullable();
            $table->datetime('fecha_autorizacion')->nullable();
            $table->enum('ambiente', ['1', '2'])->default('1'); // 1=Pruebas, 2=Producción
            $table->enum('estado_sri', ['PENDIENTE', 'AUTORIZADA', 'RECHAZADA'])->default('PENDIENTE');
            
            // Datos representante - quien recibe la factura
            $table->string('cliente_nombres');
            $table->string('cliente_apellidos');
            $table->string('cliente_identificacion'); // cédula/RUC
            $table->enum('cliente_tipo_identificacion', ['04', '05', '06', '07'])->default('05'); // 05=Cédula
            $table->string('cliente_direccion');
            $table->string('cliente_telefono')->nullable();
            $table->string('cliente_email')->nullable();
            
            // Valores
            $table->date('fecha_emision');
            $table->decimal('subtotal_0', 10, 2)->default(0); // IVA 0%
            $table->decimal('subtotal_gravado', 10, 2)->default(0); // IVA 15%
            $table->decimal('iva_valor', 10, 2)->default(0);
            $table->decimal('iva_porcentaje_usado', 5, 2)->default(15.00);
            $table->string('iva_codigo_sri', 3)->default('2'); // Código SRI del IVA
            $table->decimal('total', 10, 2);
            
            // Control
            $table->enum('estado_pago', ['pendiente', 'pagada', 'anulada'])->default('pendiente');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta'])->nullable();
            $table->string('concepto'); // Mensualidad, Inscripción, etc
            $table->string('mes_pension')->nullable(); // 2026-06
            $table->text('observaciones_sri')->nullable();
            $table->text('notas')->nullable();
            
            // XML
            $table->longText('xml_generado')->nullable();
            $table->longText('xml_autorizado')->nullable();
            
            $table->timestamps();
        });
    }
    
    public function down(): void {
        Schema::dropIfExists('facturas');
    }
};