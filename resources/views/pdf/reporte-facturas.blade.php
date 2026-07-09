<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0EA5E9; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .total { font-weight: bold; background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>REPORTE DE FACTURACIÓN - {{ $mes }}/{{ $año }}</h1>
    
    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>CI/RUC</th>
                <th>Subtotal</th>
                <th>IVA</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturas as $f)
            <tr>
                <td>{{ $f->numero_factura }}</td>
                <td>{{ $f->created_at->format('d/m/Y') }}</td>
                <td>{{ $f->cliente_nombre }}</td>
                <td>{{ $f->cliente_identificacion }}</td>
                <td style="text-align: right;">${{ number_format($f->subtotal_15, 2) }}</td>
                <td style="text-align: right;">${{ number_format($f->iva_15, 2) }}</td>
                <td style="text-align: right;">${{ number_format($f->total, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="4">TOTALES</td>
                <td style="text-align: right;">${{ number_format($facturas->sum('subtotal_15'), 2) }}</td>
                <td style="text-align: right;">${{ number_format($totalIva, 2) }}</td>
                <td style="text-align: right;">${{ number_format($totalFacturado, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <p style="margin-top: 20px;">Total Facturas: {{ $facturas->count() }}</p>
</body>
</html>