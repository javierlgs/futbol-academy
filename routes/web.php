<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntrenamientoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\CampeonatoController;
use App\Http\Controllers\PsicologiaController;
use App\Http\Contracts\EvaluacionPsicologicaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\IvaTarifaController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RadarAlertasController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\PadreController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return redirect()->route('login');
});

// Ruta para el manifiesto PWA
Route::get('/manifest.json', function() {
    return response()->json([
        'name' => setting('academy_name', 'Academia Fútbol'),
        'short_name' => Str::limit(setting('academy_name', 'FútbolApp'), 12),
        'description' => 'Gestión de Academia de Fútbol',
        'start_url' => '/dashboard',
        'display' => 'standalone',
        'background_color' => setting('primary_color', '#0EA5E9'),
        'theme_color' => setting('primary_color', '#0EA5E9'),
        'orientation' => 'portrait',
        'icons' => [
            [
                'src' => setting('pwa_icon_192')? '/storage/'. setting('pwa_icon_192') : '/icons/default-192.png',
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ],
            [
                'src' => setting('pwa_icon_512')? '/storage/'. setting('pwa_icon_512') : '/icons/default-512.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable'
            ]
        ]
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); 
    
    // Ruta para el dashboard del padre
    Route::get('/padre/dashboard', [PadreController::class, 'index'])->name('padre.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/entrenamientos', [EntrenamientoController::class, 'index'])->name('entrenamientos.index');
    Route::get('/entrenamientos/create', [EntrenamientoController::class, 'create'])->name('entrenamientos.create');
    Route::post('/entrenamientos', [EntrenamientoController::class, 'store'])->name('entrenamientos.store');
    
    // Rutas solicitadas
    Route::get('entrenamientos/{entrenamiento}/asistencia', [EntrenamientoController::class, 'asistencia'])
        ->name('entrenamientos.asistencia');
    Route::post('entrenamientos/{entrenamiento}/asistencia', [EntrenamientoController::class, 'guardarAsistencia'])
        ->name('entrenamientos.guardarAsistencia');
    Route::get('entrenamientos/{entrenamiento}/evaluar', [EntrenamientoController::class, 'evaluar'])
        ->name('entrenamientos.evaluar');
    Route::post('entrenamientos/{entrenamiento}/evaluar', [EntrenamientoController::class, 'guardarEvaluacion'])
        ->name('entrenamientos.guardarEvaluacion');
        
    // Nueva ruta añadida para subir fotos
    Route::post('/entrenamientos/{entrenamiento}/subir-fotos', [EntrenamientoController::class, 'subirFotos'])->name('entrenamientos.subir_fotos');
    
    // Ruta para alineación de partidos
    Route::post('/partidos/{partido}/alineacion', [PartidoController::class, 'storeAlineacion'])
        ->name('partidos.alineacion.store');

    // Rutas del Radar de Alertas y Estación Infantil
    Route::get('/radar-alertas', [RadarAlertasController::class, 'index'])->name('radar.index');
    Route::post('/autoevaluacion/store', [RadarAlertasController::class, 'storeAutoevaluacion'])->name('autoevaluacion.store');
    
    // Rutas exclusivas para el Tótem/Estación de Autoevaluación de los niños
    Route::get('/entrenamientos/{entrenamiento}/autoevaluacion', [RadarAlertasController::class, 'estacionAutoevaluacion'])->name('radar.estacion');
    Route::post('/entrenamientos/{entrenamiento}/autoevaluacion', [RadarAlertasController::class, 'guardarDesdeEstacion'])->name('radar.estacion.guardar');

    // Rutas completas para la gestión de Tests Físicos y Motores
    Route::get('/entrenamientos/{entrenamiento}/tests', [RadarAlertasController::class, 'formularioTests'])->name('radar.tests');
    Route::post('/entrenamientos/{entrenamiento}/tests', [RadarAlertasController::class, 'guardarTests'])->name('radar.tests.guardar');

    // 🏆 Módulo de Torneos
    Route::get('torneos/{torneo}/fixture', [TorneoController::class, 'fixture'])->name('torneos.fixture');
    Route::resource('torneos', TorneoController::class);
    Route::get('/torneos/{torneo}/partidos', [TorneoController::class, 'verFixture'])->name('torneos.partidos');    
    
    // Rutas agregadas manualmente para control
    Route::put('/torneos/{torneo}', [TorneoController::class, 'update'])->name('torneos.update');
    Route::delete('/torneos/{torneo}', [TorneoController::class, 'destroy'])->name('torneos.destroy');
    
    // Ruta gestión de alineaciones
    Route::get('/partidos/{partido}/alineaciones', [PartidoController::class, 'indexAlineaciones'])->name('partidos.alineaciones');

    // 📅 Gestión de Asignaciones y Horarios (Solo Admin)
    Route::get('asignaciones', [AsignacionController::class, 'index'])->name('asignaciones.index');
    Route::post('asignaciones', [AsignacionController::class, 'store'])->name('asignaciones.store');
    Route::put('/asignaciones/{id}', [AsignacionController::class, 'update'])->name('asignaciones.update');
    Route::delete('/asignaciones/{id}', [AsignacionController::class, 'destroy'])->name('asignaciones.destroy');

    Route::resource('categorias', CategoriaController::class);
    Route::resource('jugadores', JugadorController::class);
    Route::resource('campeonatos', CampeonatoController::class);
    
    // Nueva ruta de Pagos
    Route::resource('pagos', PagoController::class);

    // Nuevas rutas
    Route::get('/psicologia', [PsicologiaController::class, 'index'])->name('psicologia.index');
    Route::get('/jugadores/{jugador}/test-psicologico', [PsicologiaController::class, 'create'])->name('psicologia.create');
    Route::post('/jugadores/{jugador}/test-psicologico', [PsicologiaController::class, 'store'])->name('psicologia.store');

    // Módulo 1: Psicología Clínica (Ruta específica de Evaluaciones)
    Route::get('/jugadores/{jugador}/psicologico', [EvaluacionPsicologicaController::class, 'index'])->name('jugadores.psicologico.index');
    Route::post('/jugadores/{jugador}/psicologico', [EvaluacionPsicologicaController::class, 'store'])->name('jugadores.psicologico.store');

    // Corrección para Ziggy: agregada ruta reportes.index
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    
    Route::get('/reporte/jugador/{jugador}/pdf', [ReporteController::class, 'jugadorPDF'])->name('reporte.jugador.pdf');
    Route::get('/reporte/jugador/{jugador}/whatsapp', [ReporteController::class, 'whatsappReporte'])->name('reporte.jugador.whatsapp');
    Route::get('/factura/{factura}/pdf', [ReporteController::class, 'facturaPDF'])->name('factura.pdf');

    // Rutas de Facturación
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/facturas/reporte/sri', [FacturaController::class, 'reporte'])->name('facturas.reporte');
    Route::get('/reportes/facturas', [FacturaController::class, 'reporteMensual'])->name('reportes.facturas');
    Route::get('/jugadores/{jugador}/facturar', [FacturaController::class, 'create'])->name('facturas.create');
    Route::post('/jugadores/{jugador}/facturar', [FacturaController::class, 'store'])->name('facturas.store');
    Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show');
    Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'pdf'])->name('facturas.pdf');
    Route::post('/facturas/{factura}/anular', [FacturaController::class, 'anular'])->name('facturas.anular');
    
    Route::middleware('role:superadmin')->post('/facturas/{factura}/reenviar', [FacturaController::class, 'reenviarSRI'])->name('facturas.reenviar');

    // Rutas Galería
    Route::get('/entrenamientos/{entrenamiento}/galeria', [GaleriaController::class, 'index'])->name('galeria.index');
    Route::post('/entrenamientos/{entrenamiento}/galeria', [GaleriaController::class, 'store'])->name('galeria.store');
    Route::get('/mi-galeria', [GaleriaController::class, 'galeriaPadre'])->name('galeria.padre');

    // Rutas Configuración
    Route::get('/configuracion', [SettingController::class, 'index'])->name('settings.index');
    
    // IVA Tarifas
    Route::resource('iva-tarifas', IvaTarifaController::class)->only(['index', 'store', 'update', 'destroy']);

    // Rutas Gastos y Proveedores
    Route::resource('gastos', GastoController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');

    // Rutas Caja
    Route::get('caja', [CajaController::class, 'index'])->name('caja.index');
    Route::post('caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
    Route::post('caja/movimiento', [CajaController::class, 'store'])->name('caja.store');
    Route::post('caja/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');

    // Panel de Gestión de Usuarios y Personal
    Route::get('/admin/usuarios', [AdminController::class, 'usuariosIndex'])->name('admin.usuarios.index');
    Route::post('/admin/usuarios', [AdminController::class, 'usuariosStore'])->name('admin.usuarios.store');
    Route::patch('/admin/usuarios/{user}', [AdminController::class, 'usuarioUpdate'])->name('admin.usuarios.update');

    Route::post('/torneos/{torneo}/guardar-partido', [App\Http\Controllers\TorneoController::class, 'guardarPartido'])->name('torneos.guardarPartido');
    Route::post('/partidos/{partido}/notificar-whatsapp', [PartidoController::class, 'notificarWhatsApp'])->name('partidos.notificar');
    
    // Ruta guardado de acta
    Route::post('/partidos/{partido}/guardar-acta', [PartidoController::class, 'guardarActa'])->name('partidos.guardarActa');
    
    // Ruta guardado convocatoria
    Route::post('/partidos/{partido}/guardar-convocatoria', [TorneoController::class, 'guardarConvocatoria'])->name('partidos.guardarConvocatoria');
    
    // Nueva ruta para el guardado de tácticas
    Route::post('/partidos/{partido}/guardar-tacticas', [PartidoController::class, 'guardarTacticas'])->name('partidos.guardarTacticas');
    
    Route::get('/partidos/{partido}/detalles', [PartidoController::class, 'mostrarPartido'])->name('partidos.detalles');

    // Panel de Gestión de Sedes Operativas
    Route::get('/admin/sedes', [AdminController::class, 'sedesIndex'])->name('admin.sedes.index');
    Route::post('/admin/sedes', [AdminController::class, 'sedesStore'])->name('admin.sedes.store');

    Route::middleware('role:superadmin')->group(function () {
        Route::post('/configuracion', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/configuracion/bancos', [SettingController::class, 'updateBancos'])->name('settings.bancos');
    });
});

require __DIR__.'/auth.php';