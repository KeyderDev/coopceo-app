<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gracias por su compra</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width:600px; margin:0 auto; background-color:#fff; padding:20px; border-radius:8px;">
        {{-- Logo --}}
        <div style="text-align:center; margin-bottom:20px;">
            <a href="{{ config('app.url') }}">
                <img src="{{ asset('images/coopceopng.png') }}" alt="Logo" style="max-width:200px;">
            </a>
        </div>

        <h1 style="text-align:center;">¡Gracias por su compra, {{ $cliente->nombre ?? $cliente->name }}!</h1>

        <p>Detalles de su orden:</p>
        <ul>
            <li><strong>Fecha:</strong> {{ now()->format('d/m/Y') }}</li>
            <li><strong>Hora:</strong> {{ now()->format('H:i') }}</li>
            <li><strong>Total de la orden:</strong> ${{ number_format($order->total, 2) }}</li>
            <li><strong>Cajero:</strong> {{ $order->cajero->username ?? $order->cajero->nombre ?? 'Sistema' }}</li>
            <li><strong>Método de pago:</strong> {{ ucfirst($order->metodo_pago) }}</li>
        </ul>

        <p style="text-align:center;">Gracias por confiar en nosotros.</p>
    </div>
</body>
</html>
