<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="color-scheme" content="light only">
  <meta name="supported-color-schemes" content="light">
  <style>
    /* Fallback para clientes que respetan prefers-color-scheme */
    @media (prefers-color-scheme: dark) {
      .force-light { background-color: #ffffff !important; color: #033a64 !important; }
      .force-accent { color: #97d569 !important; }
      .noinvert { -webkit-filter:none !important; filter:none !important; }
    }
    /* ajustes generales seguros */
    body { -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f4f4f4" style="background-color:#f4f4f4;">
    <tr>
      <td align="center">

        <!-- contenedor centrado (MSO wrapper) -->
        <!--[if mso]><table width="600" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
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

          <!-- BODY -->
          <tr>
            <!-- bgcolor + inline color + class force-light -->
            <td bgcolor="#ffffff" style="background-color:#ffffff; padding:20px; color:#033a64; font-size:15px; line-height:1.6;" class="force-light">
              <!-- envolver el contenido también en <font> por compatibilidad máxima -->
              <div style="color:#033a64;">
                <font color="#033a64">{!! nl2br(e($bodyText)) !!}</font>
              </div>

              <!-- botón como tabla + fallback VML para Outlook -->
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:20px 0;">
                <tr>
                  <td align="center" bgcolor="#97d569" style="background-color:#97d569; border-radius:6px;">
                    <!--[if mso]>
                      <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $portalUrl ?? '#' }}" style="height:44px; v-text-anchor:middle; width:220px;" arcsize="8%" stroke="f" fillcolor="#97d569">
                        <w:anchorlock/>
                        <center style="color:#033a64; font-weight:bold;">Abrir portal</center>
                      </v:roundrect>
                    <![endif]-->
                    <!--[if !mso]><!-- -->
                    <!--<![endif]-->
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td align="center" bgcolor="#033a64" style="background-color:#033a64; padding:14px; font-size:12px; line-height:1.3;">
              <font color="#97d569">&copy; {{ date('Y') }} COOPCEO. Todos los derechos reservados.</font><br>
              <a href="{{ $portalUrl ?? '#' }}" style="color:#97d569; text-decoration:none;"><font color="#97d569">Visita nuestro portal</font></a>
            </td>
          </tr>

        </table>
        <!--[if mso]></td></tr></table><![endif]-->

      </td>
    </tr>
  </table>
</body>
</html>
