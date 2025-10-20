<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="color-scheme" content="light only">
  <meta name="supported-color-schemes" content="light">
  <style>
    @media (prefers-color-scheme: dark) {
      .force-light { background-color: #ffffff !important; color: #033a64 !important; }
      .force-accent { color: #97d569 !important; }
      .noinvert { -webkit-filter:none !important; filter:none !important; }
    }
    body { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; }
    table, th, td { border-collapse: collapse; }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f4f4f4" style="background-color:#f4f4f4;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%; margin:0 auto;" bgcolor="#ffffff">
          
          <!-- HEADER -->
          <tr>
            <td align="center" bgcolor="#033a64" style="background-color:#033a64; padding:20px;">
              <img src="{{ asset('images/coopceopng.png') }}"
                   alt="COOPCEO"
                   width="150"
                   style="display:block; border:0; outline:none; text-decoration:none; width:150px; height:auto; -ms-interpolation-mode:bicubic;"
                   class="noinvert">
              <div style="margin-top:8px; font-size:22px; font-weight:700; line-height:1;">
                <font color="#97d569">COOPCEO</font>
              </div>
            </td>
          </tr>

          <tr>
            <td bgcolor="#ffffff" style="background-color:#ffffff; padding:20px; color:#033a64; font-size:15px; line-height:1.6;" class="force-light">
              <p>Hola <strong>{{ $schedules[0]['nombre'] ?? 'Usuario' }}</strong>,</p>
              <p>A continuación encontrarás tu horario asignado para la semana:</p>

              <table width="100%" cellpadding="8" cellspacing="0" border="1" style="border:1px solid #ddd; margin-top:15px; text-align:center;">
                <thead>
                  <tr style="background-color:#4a90e2; color:white;">
                    <th>Día</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($schedules as $s)
                  <tr>
                    <td>{{ $s['day'] }}</td>
                    <td>{{ $s['start_time'] }}</td>
                    <td>{{ $s['end_time'] }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

              <p style="margin-top:30px;">Gracias por tu compromiso,<br><strong>COOPCEO</strong></p>
            </td>
          </tr>

          <tr>
            <td align="center" bgcolor="#033a64" style="background-color:#033a64; padding:14px; font-size:12px; line-height:1.3;">
              <font color="#97d569">&copy; {{ date('Y') }} COOPCEO. Todos los derechos reservados.</font><br>
              <a href="{{ $portalUrl ?? '#' }}" style="color:#97d569; text-decoration:none;"><font color="#97d569">Visita nuestro portal</font></a>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
