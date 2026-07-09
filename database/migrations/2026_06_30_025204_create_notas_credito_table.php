<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notas_credito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->onDelete('cascade');
            $table->string('numero_nota')->unique()->comment('001-001-000000001');
            $table->string('clave_acceso', 49)->unique();
            $table->string('numero_autorizacion', 49)->nullable();
            $table->dateTime('fecha_autorizacion')->nullable();
            $table->string('estado_sri')->default('PENDIENTE');
            $table->string('motivo')->comment('01=Devolución, 02=Anulación, 03=Descuento, 04=Otros');
            $table->text('detalle_motivo');
            $table->decimal('valor_modificacion', 10, 2);
            $table->text('xml_generado')->nullable();
            $table->text('xml_autorizado')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('notas_credito');
    }
};