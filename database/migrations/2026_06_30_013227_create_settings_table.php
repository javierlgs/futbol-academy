<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            // DATOS EMPRESA
            ['key' => 'razon_social', 'value' => ''],
            ['key' => 'nombre_comercial', 'value' => 'Academia Fútbol Elite'],
            ['key' => 'ruc', 'value' => ''],
            ['key' => 'direccion_matriz', 'value' => ''],
            ['key' => 'telefono', 'value' => ''],
            ['key' => 'email_facturacion', 'value' => ''],
            ['key' => 'obligado_contabilidad', 'value' => 'NO'],
            ['key' => 'contribuyente_especial', 'value' => ''],
            ['key' => 'regimen', 'value' => 'RIMPE'],

            // BRANDING
            ['key' => 'academy_logo', 'value' => null],
            ['key' => 'pwa_icon_192', 'value' => null],
            ['key' => 'pwa_icon_512', 'value' => null],
            ['key' => 'primary_color', 'value' => '#0EA5E9'],

            // FACTURACIÓN ELECTRÓNICA SRI
            ['key' => 'facturacion_electronica_activa', 'value' => '0'],
            ['key' => 'ambiente_sri', 'value' => '1'], // 1=Pruebas, 2=Producción
            ['key' => 'codigo_establecimiento', 'value' => '001'],
            ['key' => 'codigo_punto_emision', 'value' => '001'],
            ['key' => 'secuencial_factura', 'value' => '1'],
            ['key' => 'iva_porcentaje', 'value' => '15'],
            ['key' => 'firma_electronica_path', 'value' => null],
            ['key' => 'firma_electronica_clave', 'value' => null],

        ]);
    }
    public function down(): void {
        Schema::dropIfExists('settings');
    }
};