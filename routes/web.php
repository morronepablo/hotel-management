<?php

use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\ArqueoController;
use App\Http\Controllers\CleaningController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ConsumoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\EntradaController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\ServicioConsumoController;
use App\Http\Controllers\UserController; // Nuevo controlador para usuarios
use App\Http\Controllers\StaffController; // Nuevo controlador para personal
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('check_permission:ver-dashboard')
        ->name('dashboard');
    Route::get('/dashboard/data-by-month', [DashboardController::class, 'getDataByMonth'])->name('dashboard.data-by-month');

    // Clientes
    Route::get('/clientes', [ClienteController::class, 'index'])
        ->middleware('check_permission:ver-clientes')
        ->name('clientes.index');
    Route::get('/clientes/create', [ClienteController::class, 'create'])
        ->middleware('check_permission:crear-clientes')
        ->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])
        ->middleware('check_permission:crear-clientes')
        ->name('clientes.store');
    // Nueva ruta para AJAX
    Route::post('/clientes/store-ajax', [ClienteController::class, 'storeAjax'])
        ->middleware('check_permission:crear-clientes')
        ->name('clientes.storeAjax');
    Route::get('/clientes/{id}', [ClienteController::class, 'show'])
        ->middleware('check_permission:ver-clientes')
        ->name('clientes.show');
    Route::get('/clientes/{id}/edit', [ClienteController::class, 'edit'])
        ->middleware('check_permission:editar-clientes')
        ->name('clientes.edit');
    Route::put('/clientes/{id}', [ClienteController::class, 'update'])
        ->middleware('check_permission:editar-clientes')
        ->name('clientes.update');
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])
        ->middleware('check_permission:eliminar-clientes')
        ->name('clientes.destroy');

    // // Mantenimiento
    // Route::prefix('mantenimiento')->group(function () {
    //     Route::resource('nivel', LevelController::class)
    //         ->names('mantenimiento.nivel')
    //         ->middleware('check_permission:ver-niveles');

    //     Route::resource('tipo_habitacion', RoomTypeController::class)
    //         ->names('mantenimiento.tipo_habitacion')
    //         ->middleware('check_permission:ver-tipos-habitacion');

    //     Route::resource('habitacion', RoomController::class)
    //         ->names('mantenimiento.habitacion')
    //         ->middleware('check_permission:ver-habitaciones');
    // });











    // Mantenimiento
    Route::prefix('mantenimiento')->group(function () {
        Route::resource('nivel', LevelController::class)
            ->names('mantenimiento.nivel')
            ->middleware('check_permission:ver-niveles');

        Route::prefix('tipo_habitacion')->group(function () {
            Route::get('/', [RoomTypeController::class, 'index'])
                ->name('mantenimiento.tipo_habitacion.index')
                ->middleware('check_permission:ver-tipos-habitacion');
            Route::get('/create', [RoomTypeController::class, 'create'])
                ->name('mantenimiento.tipo_habitacion.create')
                ->middleware('check_permission:ver-tipos-habitacion');
            Route::post('/', [RoomTypeController::class, 'store'])
                ->name('mantenimiento.tipo_habitacion.store')
                ->middleware('check_permission:ver-tipos-habitacion');
            Route::get('/{tipo_habitacion}', [RoomTypeController::class, 'show'])
                ->name('mantenimiento.tipo_habitacion.show')
                ->middleware('check_permission:ver-tipos-habitacion');
            Route::get('/{tipo_habitacion}/edit', [RoomTypeController::class, 'edit'])
                ->name('mantenimiento.tipo_habitacion.edit')
                ->middleware('check_permission:ver-tipos-habitacion');
            Route::put('/{tipo_habitacion}', [RoomTypeController::class, 'update'])
                ->name('mantenimiento.tipo_habitacion.update')
                ->middleware('check_permission:ver-tipos-habitacion');
            Route::delete('/{tipo_habitacion}', [RoomTypeController::class, 'destroy'])
                ->name('mantenimiento.tipo_habitacion.destroy')
                ->middleware('check_permission:ver-tipos-habitacion');

            // Rutas para gestionar tarifas
            Route::get('/{tipo_habitacion}/tarifas', [RoomTypeController::class, 'manageTariffs'])
                ->name('mantenimiento.tipo_habitacion.tarifas')
                ->middleware('check_permission:ver-tipos-habitacion');
            Route::post('/{tipo_habitacion}/tarifas', [RoomTypeController::class, 'storeTariff'])
                ->name('mantenimiento.tipo_habitacion.tarifas.store')
                ->middleware('check_permission:ver-tipos-habitacion');
            Route::put('/tarifas/{tariff}', [RoomTypeController::class, 'updateTariff'])
                ->name('mantenimiento.tipo_habitacion.tarifas.update')
                ->middleware('check_permission:ver-tipos-habitacion');
            Route::delete('/tarifas/{tariff}', [RoomTypeController::class, 'destroyTariff'])
                ->name('mantenimiento.tipo_habitacion.tarifas.destroy')
                ->middleware('check_permission:ver-tipos-habitacion');
        });

        Route::resource('habitacion', RoomController::class)
            ->names('mantenimiento.habitacion')
            ->middleware('check_permission:ver-habitaciones');
    });









    // Reservas
    Route::get('/reservas/calendario', [ReservaController::class, 'calendario'])
        ->middleware('check_permission:ver-reservas')
        ->name('reservas.calendario');
    Route::post('reservas/cancel', [ReservaController::class, 'cancel'])
        ->middleware('check_permission:ver-reservas')
        ->name('reservas.cancel');
    Route::resource('reservas', ReservaController::class)
        ->middleware('check_permission:ver-reservas');

    // Nueva ruta para cargar meses dinámicamente
    Route::get('/load-month', [ReservaController::class, 'loadMonth'])
        ->middleware('check_permission:ver-reservas')
        ->name('reservas.loadMonth');

    // Cajas
    Route::prefix('caja')->group(function () {
        Route::prefix('arqueos')->name('caja.arqueos.')->group(function () {
            Route::get('/', [ArqueoController::class, 'index'])->name('index');
            Route::get('/create', [ArqueoController::class, 'create'])->name('create');
            Route::post('/', [ArqueoController::class, 'store'])->name('store');
            Route::get('/{id}', [ArqueoController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ArqueoController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ArqueoController::class, 'update'])->name('update');
            Route::delete('/{id}', [ArqueoController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/ingresoegreso', [ArqueoController::class, 'ingresoegreso'])->name('ingresoegreso');
            Route::post('/ingresoegreso', [ArqueoController::class, 'store_ingresos_egresos'])->name('store_ingresos_egresos');
            Route::get('/{id}/cierre', [ArqueoController::class, 'cierre'])->name('cierre');
            Route::post('/cierre', [ArqueoController::class, 'store_cierre'])->name('store_cierre');
            Route::get('/reporte/reporte', [ArqueoController::class, 'reporte'])->name('reporte');
        });

        // Ruta para Pagos
        Route::get('/pagos', [PagoController::class, 'index'])->name('caja.pagos')->middleware('can:ver-pagos');
        Route::get('/pagos/create', [PagoController::class, 'create'])->name('caja.pagos.create')->middleware('can:crear-pagos');
        Route::post('/pagos/store', [PagoController::class, 'store'])->name('caja.pagos.store')->middleware('can:crear-pagos');
        Route::post('/pagos/get-details', [PagoController::class, 'getDetails'])->name('caja.pagos.getDetails')->middleware('can:crear-pagos');
        Route::post('/pagos/pagar-consumo-servicio', [PagoController::class, 'pagarConsumoServicio'])->name('caja.pagos.pagarConsumoServicio')->middleware('can:crear-pagos');
    });

    // Roles
    Route::resource('roles', RoleController::class)
        ->middleware('check_permission:ver-roles');

    // Permisos
    Route::resource('permisos', PermissionController::class)
        ->middleware('check_permission:ver-permisos');

    // Entradas / Salidas
    Route::prefix('entradas')->group(function () {
        Route::get('/panel-control', [EntradaController::class, 'panelControl'])
            ->middleware('check_permission:ver-panel-control')
            ->name('entradas.panel-control');
        Route::get('/recepcion/{room?}', [EntradaController::class, 'recepcion'])
            ->middleware('check_permission:ver-recepcion')
            ->name('entradas.recepcion');
        Route::get('/registros', [EntradaController::class, 'registros'])
            ->middleware('check_permission:ver-registros')
            ->name('entradas.registros');
        Route::get('/renovaciones', [EntradaController::class, 'renovaciones'])
            ->middleware('check_permission:ver-renovaciones')
            ->name('entradas.renovaciones');
        Route::get('/create/{room}', [EntradaController::class, 'create'])
            ->middleware('check_permission:crear-entrada')
            ->name('entradas.create');
        Route::post('/store', [EntradaController::class, 'store'])
            ->middleware('check_permission:crear-entrada')
            ->name('entradas.store');

        // Nueva ruta para el checkout
        Route::post('/checkout', [EntradaController::class, 'checkout'])
            ->middleware('check_permission:crear-entrada')
            ->name('entradas.checkout');

        // Rutas para Salidas
        Route::get('salidas', [SalidaController::class, 'index'])->name('salidas.index');
        Route::get('salidas/{entry}', [SalidaController::class, 'show'])->name('salidas.show');
        Route::post('salidas/checkout/{entry}', [SalidaController::class, 'checkout'])->name('salidas.checkout');
        Route::get('salidas/{entry}/details', [SalidaController::class, 'getDetails'])->name('salidas.getDetails');
        Route::post('salidas/pagar-consumo-servicio', [SalidaController::class, 'pagarConsumoServicio'])->name('salidas.pagarConsumoServicio');

        // Nuevas rutas para verificar la caja antes de abrir modales
        Route::get('/check-caja-for-cleaning/{room}', [EntradaController::class, 'checkCajaForCleaning'])
            ->middleware('check_permission:ver-panel-control')
            ->name('entradas.check-caja-for-cleaning');
        Route::get('/check-caja-for-reservation/{room}', [EntradaController::class, 'checkCajaForReservation'])
            ->middleware('check_permission:ver-panel-control')
            ->name('entradas.check-caja-for-reservation');

        // Nueva ruta para generar el ticket (PDF)
        Route::get('/ticket/{entryId}', [EntradaController::class, 'ticket'])
            ->middleware('check_permission:ver-panel-control')
            ->name('entradas.ticket');

        // Nueva ruta para generar el detalle general (PDF)
        Route::get('/detalle-general/{entryId}', [EntradaController::class, 'detalleGeneral'])
            ->middleware('check_permission:ver-panel-control')
            ->name('entradas.detalle-general');

        // Rutas para limpiezas
        Route::post('/cleaning/assign/{room}', [CleaningController::class, 'assignCleaning'])
            ->middleware('check_permission:ver-panel-control')
            ->name('cleaning.assign');
        Route::get('/cleaning/finish/{room}', [CleaningController::class, 'finishCleaning'])
            ->middleware('check_permission:ver-panel-control')
            ->name('cleaning.finish');

        // Rutas para reservaciones
        Route::get('/renovaciones', [RenewalController::class, 'index'])
            ->middleware('check_permission:ver-renovaciones')
            ->name('entradas.renovaciones');
        Route::get('/renewals/create/{entryId}', [RenewalController::class, 'create'])
            ->middleware('check_permission:crear-renovacion')
            ->name('renewals.create');
        Route::post('/renewals/store', [RenewalController::class, 'store'])
            ->middleware('check_permission:crear-renovacion')
            ->name('renewals.store');
    });

    // Acceso
    Route::prefix('acceso')->group(function () {
        // Usuarios
        Route::get('/usuarios', [UserController::class, 'index'])
            ->middleware('check_permission:ver-usuarios')
            ->name('acceso.usuarios');
        Route::get('/usuarios/create', [UserController::class, 'create'])
            ->middleware('check_permission:crear-usuarios')
            ->name('acceso.usuarios.create');
        Route::post('/usuarios/create', [UserController::class, 'store'])
            ->middleware('check_permission:crear-usuarios')
            ->name('acceso.usuarios.store');
        Route::get('/usuarios/{id}', [UserController::class, 'show'])
            ->middleware('check_permission:ver-usuarios')
            ->name('acceso.usuarios.show');
        Route::get('/usuarios/{id}/edit', [UserController::class, 'edit'])
            ->middleware('check_permission:editar-usuarios')
            ->name('acceso.usuarios.edit');
        Route::put('/usuarios/{id}', [UserController::class, 'update'])
            ->middleware('check_permission:editar-usuarios')
            ->name('acceso.usuarios.update');
        Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])
            ->middleware('check_permission:eliminar-usuarios')
            ->name('acceso.usuarios.destroy');

        // Personal
        Route::get('/personal', [StaffController::class, 'index'])
            ->middleware('check_permission:ver-personal')
            ->name('acceso.personal');
        Route::get('/personal/create', [StaffController::class, 'create'])
            ->middleware('check_permission:crear-personal')
            ->name('acceso.personal.create');
        Route::post('/personal/create', [StaffController::class, 'store'])
            ->middleware('check_permission:crear-personal')
            ->name('acceso.personal.store');
        Route::get('/personal/{id}', [StaffController::class, 'show'])
            ->middleware('check_permission:ver-personal')
            ->name('acceso.personal.show');
        Route::get('/personal/{id}/edit', [StaffController::class, 'edit'])
            ->middleware('check_permission:editar-personal')
            ->name('acceso.personal.edit');
        Route::put('/personal/{id}', [StaffController::class, 'update'])
            ->middleware('check_permission:editar-personal')
            ->name('acceso.personal.update');
        Route::delete('/personal/{id}', [StaffController::class, 'destroy'])
            ->middleware('check_permission:eliminar-personal')
            ->name('acceso.personal.destroy');
    });

    // Configuración
    Route::prefix('configuracion')->group(function () {
        // Ajustes
        Route::get('/datos_hotel', [ConfiguracionController::class, 'datosHotel'])
            ->middleware('check_permission:ver-configuracion')
            ->name('configuracion.datos_hotel');
        Route::post('/datos_hotel', [ConfiguracionController::class, 'actualizarDatosHotel'])
            ->middleware('check_permission:ver-configuracion')
            ->name('configuracion.actualizar_datos_hotel');

        // Tipo Documento
        Route::get('/tipo_documento', [ConfiguracionController::class, 'tipoDocumentoIndex'])
            ->middleware('check_permission:ver-tipo-documento')
            ->name('configuracion.tipo_documento');
        Route::get('/tipo_documento/create', [ConfiguracionController::class, 'tipoDocumentoCreate'])
            ->middleware('check_permission:crear-tipo-documento')
            ->name('configuracion.tipo_documento.create');
        Route::post('/tipo_documento/create', [ConfiguracionController::class, 'tipoDocumentoStore'])
            ->middleware('check_permission:crear-tipo-documento')
            ->name('configuracion.tipo_documento.store');
        Route::get('/tipo_documento/{id}', [ConfiguracionController::class, 'tipoDocumentoShow'])
            ->middleware('check_permission:ver-tipo-documento')
            ->name('configuracion.tipo_documento.show');
        Route::get('/tipo_documento/{id}/edit', [ConfiguracionController::class, 'tipoDocumentoEdit'])
            ->middleware('check_permission:editar-tipo-documento')
            ->name('configuracion.tipo_documento.edit');
        Route::put('/tipo_documento/{id}', [ConfiguracionController::class, 'tipoDocumentoUpdate'])
            ->middleware('check_permission:editar-tipo-documento')
            ->name('configuracion.tipo_documento.update');
        Route::delete('/tipo_documento/{id}', [ConfiguracionController::class, 'tipoDocumentoDestroy'])
            ->middleware('check_permission:eliminar-tipo-documento')
            ->name('configuracion.tipo_documento.destroy');

        // Unidad Medida
        Route::get('/unidad_medida', [ConfiguracionController::class, 'unidadMedidaIndex'])
            ->middleware('check_permission:ver-unidad-medida')
            ->name('configuracion.unidad_medida');
        Route::get('/unidad_medida/create', [ConfiguracionController::class, 'unidadMedidaCreate'])
            ->middleware('check_permission:crear-unidad-medida')
            ->name('configuracion.unidad_medida.create');
        Route::post('/unidad_medida/create', [ConfiguracionController::class, 'unidadMedidaStore'])
            ->middleware('check_permission:crear-unidad-medida')
            ->name('configuracion.unidad_medida.store');
        Route::get('/unidad_medida/{id}', [ConfiguracionController::class, 'unidadMedidaShow'])
            ->middleware('check_permission:ver-unidad-medida')
            ->name('configuracion.unidad_medida.show');
        Route::get('/unidad_medida/{id}/edit', [ConfiguracionController::class, 'unidadMedidaEdit'])
            ->middleware('check_permission:editar-unidad-medida')
            ->name('configuracion.unidad_medida.edit');
        Route::put('/unidad_medida/{id}', [ConfiguracionController::class, 'unidadMedidaUpdate'])
            ->middleware('check_permission:editar-unidad-medida')
            ->name('configuracion.unidad_medida.update');
        Route::delete('/unidad_medida/{id}', [ConfiguracionController::class, 'unidadMedidaDestroy'])
            ->middleware('check_permission:eliminar-unidad-medida')
            ->name('configuracion.unidad_medida.destroy');
    });

    // Almacén
    Route::prefix('almacen')->group(function () {
        // Categorías
        Route::get('/categorias', [AlmacenController::class, 'categoriaIndex'])
            ->middleware('check_permission:ver-categorias')
            ->name('almacen.categoria');
        Route::get('/categorias/create', [AlmacenController::class, 'categoriaCreate'])
            ->middleware('check_permission:crear-categoria')
            ->name('almacen.categoria.create');
        Route::post('/categorias/create', [AlmacenController::class, 'categoriaStore'])
            ->middleware('check_permission:crear-categoria')
            ->name('almacen.categoria.store');
        Route::get('/categorias/{id}', [AlmacenController::class, 'categoriaShow'])
            ->middleware('check_permission:ver-categorias')
            ->name('almacen.categoria.show');
        Route::get('/categorias/{id}/edit', [AlmacenController::class, 'categoriaEdit'])
            ->middleware('check_permission:editar-categoria')
            ->name('almacen.categoria.edit');
        Route::put('/categorias/{id}', [AlmacenController::class, 'categoriaUpdate'])
            ->middleware('check_permission:editar-categoria')
            ->name('almacen.categoria.update');
        Route::delete('/categorias/{id}', [AlmacenController::class, 'categoriaDestroy'])
            ->middleware('check_permission:eliminar-categoria')
            ->name('almacen.categoria.destroy');

        // Productos
        Route::get('/productos', [AlmacenController::class, 'productoIndex'])
            ->middleware('check_permission:ver-productos')
            ->name('almacen.producto');
        Route::get('/productos/create', [AlmacenController::class, 'productoCreate'])
            ->middleware('check_permission:crear-producto')
            ->name('almacen.producto.create');
        Route::post('/productos/create', [AlmacenController::class, 'productoStore'])
            ->middleware('check_permission:crear-producto')
            ->name('almacen.producto.store');
        Route::get('/productos/{id}', [AlmacenController::class, 'productoShow'])
            ->middleware('check_permission:ver-productos')
            ->name('almacen.producto.show');
        Route::get('/productos/{id}/edit', [AlmacenController::class, 'productoEdit'])
            ->middleware('check_permission:editar-producto')
            ->name('almacen.producto.edit');
        Route::put('/productos/{id}', [AlmacenController::class, 'productoUpdate'])
            ->middleware('check_permission:editar-producto')
            ->name('almacen.producto.update');
        Route::delete('/productos/{id}', [AlmacenController::class, 'productoDestroy'])
            ->middleware('check_permission:eliminar-producto')
            ->name('almacen.producto.destroy');

        // Servicios
        Route::get('/servicios', [AlmacenController::class, 'servicioIndex'])
            ->middleware('check_permission:ver-servicios')
            ->name('almacen.servicio');
        Route::get('/servicios/create', [AlmacenController::class, 'servicioCreate'])
            ->middleware('check_permission:crear-servicio')
            ->name('almacen.servicio.create');
        Route::post('/servicios/create', [AlmacenController::class, 'servicioStore'])
            ->middleware('check_permission:crear-servicio')
            ->name('almacen.servicio.store');
        Route::get('/servicios/{id}', [AlmacenController::class, 'servicioShow'])
            ->middleware('check_permission:ver-servicios')
            ->name('almacen.servicio.show');
        Route::get('/servicios/{id}/edit', [AlmacenController::class, 'servicioEdit'])
            ->middleware('check_permission:editar-servicio')
            ->name('almacen.servicio.edit');
        Route::put('/servicios/{id}', [AlmacenController::class, 'servicioUpdate'])
            ->middleware('check_permission:editar-servicio')
            ->name('almacen.servicio.update');
        Route::delete('/servicios/{id}', [AlmacenController::class, 'servicioDestroy'])
            ->middleware('check_permission:eliminar-servicio')
            ->name('almacen.servicio.destroy');
    });

    // Rutas para el módulo de Consumo
    Route::prefix('consumo')->middleware(['auth'])->group(function () {
        Route::get('/', [ConsumoController::class, 'index'])->name('consumo.index');
        Route::get('/create/{entryId}', [ConsumoController::class, 'create'])->name('consumo.create');
        Route::post('/add-product/{consumoId}', [ConsumoController::class, 'addProduct'])->name('consumo.addProduct');
        Route::delete('/remove-product/{detalleId}', [ConsumoController::class, 'removeProduct'])->name('consumo.removeProduct');
        Route::post('/update-payment-status', [ConsumoController::class, 'updatePaymentStatus'])->name('consumo.updatePaymentStatus');
        Route::post('/mark-as-paid/{consumoId}', [ConsumoController::class, 'markAsPaid'])->name('consumo.markAsPaid');
        Route::post('/devolver/{detalleId}', [ConsumoController::class, 'devolver'])->name('consumo.devolver');
    });

    // Rutas para el módulo de Servicio (Cobro por habitación)
    Route::prefix('servicio-consumo')->middleware(['auth'])->group(function () {
        Route::get('/', [ServicioConsumoController::class, 'index'])->name('servicio-consumo.index');
        Route::get('/create/{entryId}', [ServicioConsumoController::class, 'create'])->name('servicio-consumo.create');
        Route::post('/add-servicio/{servicioConsumoId}', [ServicioConsumoController::class, 'addServicio'])->name('servicio-consumo.addServicio');
        Route::delete('/remove-servicio/{detalleId}', [ServicioConsumoController::class, 'removeServicio'])->name('servicio-consumo.removeServicio');
        Route::post('/update-payment-status', [ServicioConsumoController::class, 'updatePaymentStatus'])->name('servicio-consumo.updatePaymentStatus');
        Route::post('/mark-as-paid/{servicioConsumoId}', [ServicioConsumoController::class, 'markAsPaid'])->name('servicio-consumo.markAsPaid');
        Route::post('/quitar/{detalleId}', [ServicioConsumoController::class, 'quitar'])->name('servicio-consumo.quitar');
        // Cambiar esta línea de GET a POST
        Route::post('/vendido/{servicioConsumo}', [ServicioConsumoController::class, 'vendido'])->name('servicio-consumo.vendido');
    });
});
