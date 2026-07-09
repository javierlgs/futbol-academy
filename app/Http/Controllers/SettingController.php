<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Banco;
use App\Models\IvaTarifa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SettingController extends Controller {

    public function index() {
        return Inertia::render('Settings/Index', [
            'settings' => Setting::all()->pluck('value', 'key'),
            'bancos' => Banco::orderBy('principal', 'desc')->get(),
            'iva_tarifas' => IvaTarifa::where('activo', true)->orderBy('porcentaje')->get()
        ]);
    }

    public function update(Request $request) {
        $rules = [
            // EMPRESA
            'razon_social' => 'required|string|max:300',
            'nombre_comercial' => 'required|string|max:300',
            'ruc' => 'required|string|size:13',
            'direccion_matriz' => 'required|string|max:300',
            'telefono' => 'required|string|max:20',
            'email_facturacion' => 'required|email',
            'obligado_contabilidad' => 'required|in:SI,NO',
            'contribuyente_especial' => 'nullable|string|max:20',
            'regimen' => 'required|in:RIMPE_EMPRENDEDOR,RIMPE_NEGOCIO_POPULAR,GENERAL',

            // BRANDING
            'primary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'logo' => 'nullable|image|mimes:png,jpg,svg|max:2048',

            // FACTURACIÓN SRI
            'facturacion_electronica_activa' => 'required|boolean',
            'codigo_establecimiento' => 'required|string|size:3|regex:/^[0-9]{3}$/',
            'codigo_punto_emision' => 'required|string|size:3|regex:/^[0-9]{3}$/',
            'secuencial_factura' => 'required|integer|min:1',
            'firma_electronica' => 'nullable|file|mimes:p12,pfx|max:1024',
            'firma_electronica_clave' => 'nullable|string|max:255',
            'iva_tarifa_default_id' => 'required|exists:iva_tarifas,id',
        ];

        // SOLO SUPERADMIN PUEDE CAMBIAR AMBIENTE
        if (auth()->user()->hasRole('superadmin')) {
            $rules['ambiente_sri'] = 'required|in:1,2';
        }

        $data = $request->validate($rules);

        // Cambiar tarifa por defecto
        IvaTarifa::query()->update(['por_defecto' => false]);
        IvaTarifa::find($data['iva_tarifa_default_id'])->update(['por_defecto' => true]);

        // Guardar todos los settings
        foreach ($data as $key => $value) {
            if ($key !== 'iva_tarifa_default_id' && !in_array($key, ['logo', 'firma_electronica'])) {
                Setting::set($key, $value);
            }
        }

        // Logo + iconos PWA
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('branding', 'public');
            Setting::set('academy_logo', $logoPath);
            $this->generatePWAIcons($request->file('logo'));
        }

        // Firma electrónica.p12
        if ($request->hasFile('firma_electronica')) {
            $firmaPath = $request->file('firma_electronica')->store('firmas', 'local');
            Setting::set('firma_electronica_path', $firmaPath);
            if (isset($data['firma_electronica_clave'])) {
                Setting::set('firma_electronica_clave', $data['firma_electronica_clave']);
            }
        }

        return back()->with('success', 'Settings actualizados');
    }

    public function updateBancos(Request $request) {
        $data = $request->validate([
            'bancos' => 'required|array|min:1',
            'bancos.*.id' => 'nullable|integer|exists:bancos,id',
            'bancos.*.nombre' => 'required|string|max:100',
            'bancos.*.tipo' => 'required|in:Ahorros,Corriente',
            'bancos.*.numero_cuenta' => 'required|string|max:20',
            'bancos.*.titular' => 'required|string|max:200',
            'bancos.*.activo' => 'required|boolean',
            'bancos.*.principal' => 'required|boolean',
        ]);

        // Solo puede haber 1 principal
        $principales = collect($data['bancos'])->where('principal', true)->count();
        if ($principales !== 1) {
            return back()->withErrors(['bancos' => 'Debe marcar exactamente 1 banco como principal']);
        }

        Banco::truncate();
        foreach ($data['bancos'] as $banco) {
            Banco::create($banco);
        }

        return back()->with('success', 'Bancos actualizados');
    }

    private function generatePWAIcons($file) {
        $manager = new ImageManager(new Driver());
        
        // Icon 192x192
        $icon192 = $manager->read($file)->cover(192, 192)->toPng();
        Storage::disk('public')->put('branding/icon-192.png', (string) $icon192);
        Setting::set('pwa_icon_192', 'branding/icon-192.png');

        // Icon 512x512
        $icon512 = $manager->read($file)->cover(512, 512)->toPng();
        Storage::disk('public')->put('branding/icon-512.png', (string) $icon512);
        Setting::set('pwa_icon_512', 'branding/icon-512.png');
    }
}