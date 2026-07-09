<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { border-bottom: 3px solid #0EA5E9; padding-bottom: 10px; margin-bottom: 15px; }
        .logo { width: 100px; }
        .title { font-size: 24px; font-weight: bold; color: #0EA5E9; }
        .box { border: 1px solid #ccc; padding: 10px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f0f0f0; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .total-box { background: #0EA5E9; color: white; padding: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%;">
                    @if($settings['academy_logo'])
                        <img src="{{ storage_path('app/public/' . $settings['academy_logo']) }}" class="logo">
                    @endif
                    <h1 style="margin: 5px 0;">{{ $settings['razon_social'] }}</h1>
                    <p>{{ $settings['nombre_comercial'] }}</p>
                    <p>RUC: {{ $settings['ruc'] }}</p>
                    <p>{{ $settings['direccion_matriz'] }}</p>
                    <p>Tel: {{ $settings['telefono'] }}</p>
                </td>
                <td style="width: 40%; text-align: right;">
                    <div class="box">
                        <p style="font-size: 8px;">R.U.C.: {{ $settings['ruc'] }}</p>
                        <p class="title">FACTURA</p>
                        <p style="font-size: 16px; font-weight: bold;">No. {{ $factura->numero_factura }}</p>
                        <p style="font-size: 8px;">NÚMERO DE AUTORIZACIÓN</p>
                        <p style="font-size: 8px;">{{ $factura->numero_autorizacion }}</p>
                        <p style="font-size: 8px;">FECHA Y HORA DE AUTORIZACIÓN</p>
                        <p style="font-size: 8px;">{{ $factura->fecha_autorizacion? $factura->fecha_autorizacion->format('d/m/Y H:i:s') : '' }}</p>
                        <p style="font-size: 8px;">AMBIENTE: {{ $factura->ambiente == '1'? 'PRUEBAS' : 'PRODUCCIÓN' }}</p>
                        <p style="font-size: 8px;">EMISIÓN: NORMAL</p>
                        <p style="font-size: 8px;">CLAVE DE ACCESO</p>
                        <p style="font-size: 7px;">{{ $factura->clave_acceso }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="box">
        <strong>Razón Social / Nombres y Apellidos:</strong> {{ $factura->cliente_nombre }}<br>
        <strong>Identificación:</strong> {{ $factura->cliente_identificacion }}<br>
        <strong>Fecha Emisión:</strong> {{ $factura->created_at->format('d/m/Y') }}<br>
        <strong>Dirección:</strong> {{ $factura->cliente_direccion }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Cod. Principal</th>
                <th>Cant.</th>
                <th>Descripción</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Descuento</th>
                <th class="text-right">Precio Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>SERV001</td>
                <td>1.00</td>
                <td>{{ $factura->concepto }}</td>
                <td class="text-right">{{ number_format($factura->subtotal_gravado, 2) }}</td>
                <td class="text-right">0.00</td>
                <td class="text-right">{{ number_format($factura->subtotal_gravado, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top: 20px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="box">
                    <strong>Información Adicional</strong><br>
                    Email: {{ $factura->cliente_email }}<br>
                    Teléfono: {{ $factura->cliente_telefono }}
                </div>
            </td>
            <td style="width: 40%;">
                <table>
                    <tr>
                        <td>SUBTOTAL GRAVADO</td>
                        <td class="text-right">{{ number_format($factura->subtotal_gravado, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SUBTOTAL 0%</td>
                        <td class="text-right">{{ number_format($factura->subtotal_0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SUBTOTAL SIN IMPUESTOS</td>
                        <td class="text-right">{{ number_format($factura->subtotal_gravado + $factura->subtotal_0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>DESCUENTO</td>
                        <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                        <td>IVA {{ $factura->iva_porcentaje_usado }}%</td>
                        <td class="text-right">{{ number_format($factura->iva_valor, 2) }}</td>
                    </tr>
                    <tr class="total-box">
                        <td><strong>VALOR TOTAL</strong></td>
                        <td class="text-right"><strong>{{ number_format($factura->total, 2) }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="box" style="margin-top: 20px;">
        <strong>Formas de Pago:</strong><br>
        @foreach($bancos as $banco)
            {{ $banco->nombre }} - {{ $banco->tipo }} - {{ $banco->numero_cuenta }} - {{ $banco->titular }}<br>
        @endforeach
    </div>
</body>
</html>