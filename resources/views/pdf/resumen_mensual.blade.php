<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe Mensual de Ventas - {{ $mes }}</title>
    <style>
        @page {
            margin: 50px 40px;
        }

        body,
        table,
        th,
        td,
        h1,
        h2 {
            font-family: "DejaVu Sans", sans-serif;
            color: #032b4c;
        }

        body {
            background-color: #ffffff;
            line-height: 1.6;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 10px;
            border-bottom: 3px solid #97d569;
        }

        header img {
            max-width: 140px;
            height: auto;
        }

        header .header-text {
            flex-grow: 1;
            padding-left: 20px;
        }

        header h1 {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        header h2 {
            font-size: 16px;
            font-weight: normal;
            margin: 5px 0 0 0;
            color: #555555;
        }

        .summary-container {
            width: 85%;
            margin: 0 auto;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .summary-table th,
        .summary-table td {
            padding: 14px 20px;
            text-align: left;
        }

        .summary-table th {
            background-color: #032b4c;
            color: #ffffff;
            font-weight: bold;
            font-size: 16px;
        }

        .summary-table td {
            background-color: #ffffff;
            border-bottom: 1px solid #e0e0e0;
            font-weight: normal;
            font-size: 15px;
        }

        .summary-table tr:nth-child(even) td {
            background-color: #f4f6f8;
        }

        .highlight-row td {
            background-color: #e0f7e9;
            color: #097a3d;
            font-weight: bold;
        }

        /* Resaltar Totales y Ganancia */
        .total-sales-row td {
            background-color: #97d569;
            color: #032b4c;
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #032b4c;
            border-bottom: 2px solid #032b4c;
        }

        .total-sales-row:first-child td {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .total-sales-row:last-child td {
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #cccccc;
            padding-top: 8px;
        }

        .clearfix {
            clear: both;
        }
    </style>
</head>

<body>
    <header>
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo Empresarial">
        @endif
        <div class="header-text">
            <h1>Informe Operacional Mensual</h1>
            <h2>Período: {{ $mes }}</h2>
        </div>
    </header>

    <div class="clearfix"></div>

    <div class="summary-container">
        <table class="summary-table">
            <thead>
                <tr>
                    <th colspan="2">Resumen Financiero y Operacional</th>
                </tr>
            </thead>
            <tbody>
                <!-- Totales principales -->
                <tr class="total-sales-row">
                    <td>Total de Ingresos por Ventas</td>
                    <td>${{ number_format($totalVentas, 2) }}</td>
                </tr>
                <tr class="total-sales-row">
                    <td>Ganancia Neta</td>
                    <td>${{ number_format($gananciaNeta, 2) }}</td>
                </tr>

                <!-- Detalle de pagos -->
                <tr>
                    <td>Total Ingresos por Efectivo</td>
                    <td>${{ number_format($totalEfectivo, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Ingresos por ATH Móvil</td>
                    <td>${{ number_format($totalAth, 2) }}</td>
                </tr>
                <tr>
                    <td>Total de Transacciones en Efectivo</td>
                    <td>{{ $transaccionesEfectivo }}</td>
                </tr>
                <tr>
                    <td>Total de Transacciones por ATH Móvil</td>
                    <td>{{ $transaccionesAth }}</td>
                </tr>

                <!-- Resaltar cajero activo -->
                <tr class="highlight-row">
                    <td>Cajero con Mayor Número de Transacciones</td>
                    <td>{{ $cajeroMasActivo }}</td>
                </tr>
                <tr class="highlight-row">
                    <td>Usuario con Más Compras</td>
                    <td>{{ $usuarioMasCompras }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <footer>
        Este informe fue generado automáticamente el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}.
        Confidencial.
    </footer>
</body>

</html>