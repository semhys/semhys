<?php
/* ===================================================================
   SEMHYS – Diagnóstico rápido de BD y SMTP
   Guardar como: /public_html/diag.php
   Uso:
     - Ver diagnóstico sin cambios: /diag.php
     - Insertar fila de prueba en BD: /diag.php?write=1
     - Enviar correo de prueba:      /diag.php?send=1&to=correo@dominio.com
   =================================================================== */

declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function ok($label, $msg=''){ echo "<div style='padding:8px;border-left:6px solid #0a7d2c;background:#e8f7ee;margin:8px 0'><b>✅ ".h($label)."</b>".($msg?": ".h($msg):"")."</div>"; }
function bad($label, $msg=''){ echo "<div style='padding:8px;border-left:6px solid #b00020;background:#fde8ea;margin:8px 0'><b>❌ ".h($label)."</b>".($msg?": ".h($msg):"")."</div>"; }
function info($label, $msg=''){ echo "<div style='padding:8px;border-left:6px solid #0f1f34;background:#eef3ff;margin:8px 0'><b>ℹ️ ".h($label)."</b>".($msg?": ".h($msg):"")."</div>"; }

echo "<div style='max-width:980px;margin:24px auto;font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif'>";
echo "<h1>Diagnóstico SEMHYS</h1>";

/* ==========  AJUSTA AQUÍ (según tu instalación)  ================== */
<?php
// diag.php - DISABLED
// This endpoint has been intentionally disabled for security.
// See diag_DISABLED_README.txt for restoration instructions.

http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['ok' => false, 'error' => 'Disabled']);
exit;
const LEADS_TABLE = 'leads_semhys';
/* ================================================================== */

$doWrite = isset($_GET['write']);
$doSend  = isset($_GET['send']);
$to      = isset($_GET['to']) ? trim((string)$_GET['to']) : SMTP_USER;

echo "<h2>1) Entorno PHP</h2>";
info('PHP version', PHP_VERSION);
$needExt = ['mysqli','openssl','curl','mbstring'];
$allOk = true;
foreach($needExt as $ext){
  if(extension_loaded($ext)){ ok("Extensión $ext"); }
  else { bad("Extensión $ext", 'No cargada'); $allOk = false; }
}
if($allOk) ok('Extensiones requeridas cargadas');

/* ===================== 2) Base de Datos =========================== */
echo "<h2>2) Conexión a Base de Datos</h2>";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  $conn->set_charset('utf8mb4');
  ok('Conexión MySQL', DB_NAME . ' (utf8mb4)');

  // Ping
  if($conn->ping()){ ok('Ping MySQL'); }

  // Verificar tabla leads_semhys
  $res = $conn->query("SHOW TABLES LIKE '". $conn->real_escape_string(LEADS_TABLE) ."'");
  if($res->num_rows===1){
    ok('Tabla encontrada', LEADS_TABLE);
  }else{
    bad('Tabla no encontrada', LEADS_TABLE.' (crea la tabla antes de probar inserción)');
  }

  // Inserción de prueba (solo si ?write=1)
  if($doWrite){
    $stmt = $conn->prepare("INSERT INTO ".LEADS_TABLE." (name,email,location,message) VALUES (?,?,?,?)");
    $name='[DIAG] Nombre'; $email='diag@semhys.com'; $loc='DiagCity'; $msg='Inserción de prueba';
    $stmt->bind_param('ssss', $name,$email,$loc,$msg);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    ok('Inserción de prueba OK', 'ID='.$id);

    // Limpieza inmediata (no dejar basura)
    $del = $conn->prepare("DELETE FROM ".LEADS_TABLE." WHERE id=?");
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();
    ok('Limpieza OK', "Fila $id eliminada");
  } else {
    info('Inserción en BD', 'No se ejecutó (añade ?write=1 para probar una inserción y limpiar).');
  }

} catch(Throwable $e){
  bad('Error BD', $e->getMessage());
}

/* ======================= 3) API local ============================= */
echo "<h2>3) API local (api_insert.php)</h2>";
$apiPath = __DIR__ . '/api_insert.php';
if(file_exists($apiPath)){
  ok('Archivo encontrado', 'api_insert.php');
  if(function_exists('curl_init')){
    // Probar POST local (solo estructura). No enviamos nada real salvo que pidas ?write=1.
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on' ? "https":"http") . "://".$_SERVER['HTTP_HOST']."/api_insert.php";

    $payload = [
      'name' => '[DIAG] Nombre',
      'email'=> 'diag@semhys.com',
      'city' => 'DiagCity',
      'message'=>'Mensaje de prueba DIAG'
    ];

    if($doWrite){
      $ch = curl_init($url);
      curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($payload),
        CURLOPT_TIMEOUT => 15
      ]);
      $resp = curl_exec($ch);
      $err  = curl_error($ch);
      $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if($err){ bad('cURL a api_insert.php', $err); }
      else {
        if($code>=200 && $code<300){ ok('cURL a api_insert.php', "HTTP $code | Respuesta: ".substr((string)$resp,0,200)); }
        else { bad('cURL a api_insert.php', "HTTP $code | Respuesta: ".substr((string)$resp,0,200)); }
      }
    } else {
      info('Llamada HTTP', 'No ejecutada (añade ?write=1 para llamar api_insert.php con datos de prueba).');
    }
  }else{
    bad('cURL', 'No disponible para probar api_insert.php desde el servidor.');
  }
}else{
  bad('Archivo faltante', 'api_insert.php no está en /public_html');
}

/* ======================= 4) SMTP/Correo =========================== */
echo "<h2>4) SMTP / Envío de correo</h2>";

$canConnect465 = false; $canConnect587 = false;
$err465=''; $err587='';

function try_socket($host,$port,&$err): bool {
  $errno=0;$errstr='';
  $fp = @fsockopen($host, $port, $errno, $errstr, 10);
  if($fp){
    fclose($fp);
    return true;
  } else {
    $err = "[$errno] $errstr";
    return false;
  }
}

if(try_socket(SMTP_HOST, 465, $err465)){ $canConnect465=true; ok('Socket SMTP 465 (SSL)','Conexión posible'); }
else { bad('Socket SMTP 465 (SSL)',$err465?:'sin detalle'); }

if(try_socket(SMTP_HOST, 587, $err587)){ $canConnect587=true; ok('Socket SMTP 587 (TLS)','Conexión posible'); }
else { bad('Socket SMTP 587 (TLS)',$err587?:'sin detalle'); }

if($doSend){
  // PHPMailer (usando librería incluida en /public_html/phpmailer/src)
  $base = __DIR__ . '/phpmailer/src';
  if(file_exists("$base/PHPMailer.php") && file_exists("$base/SMTP.php") && file_exists("$base/Exception.php")){
    require_once "$base/PHPMailer.php";
    require_once "$base/SMTP.php";
    require_once "$base/Exception.php";

    $tried = [];
    $sent  = false; $lastError = '';

    foreach([
      ['port'=>465,'secure'=>'ssl','label'=>'SMTPS (465)'],
      ['port'=>587,'secure'=>'tls','label'=>'STARTTLS (587)'],
    ] as $opt){
      try{
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->Port       = $opt['port'];
        $mail->SMTPSecure = $opt['secure'];
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->Subject = 'DIAGNÓSTICO SEMHYS – Prueba SMTP';
        $mail->Body    = "Hola,\n\nEste es un correo de PRUEBA del script de diagnóstico.\n\nSi lo recibes, SMTP está OK.\n\nSaludos,\nSEMHYS";
        $mail->send();
        ok('Envío SMTP', $opt['label'].' -> OK (a '. $to .')');
        $sent = true; break;
      }catch(Throwable $e){
        $tried[] = $opt['label'].': '.$e->getMessage();
        $lastError = $e->getMessage();
      }
    }

    if(!$sent){
      bad('SMTP falló', $lastError ?: 'Error desconocido');
      if($tried){ info('Intentos', implode(' | ', $tried)); }
    }
  } else {
    bad('PHPMailer', 'No encontrada en /public_html/phpmailer/src');
  }
} else {
  info('Envío real', 'No se envió (añade ?send=1&to=tu@correo.com para probar).');
}

/* ======================= 5) Resumen =============================== */
echo "<h2>5) Resumen</h2>";
echo "<ul style='line-height:1.6'>";
echo "<li>Si BD OK pero la API falla → revisar <b>api_insert.php</b> (nombres de campos POST: <code>name,email,city,message</code>) y conexión.</li>";
echo "<li>Si SMTP socket OK pero envío falla → credenciales o cifrado/puerto; prueba el otro puerto.</li>";
echo "<li>Si BD inserta con <code>?write=1</code> pero desde la web no llega → el problema está en el fetch del front o en la ruta de <code>api_insert.php</code>.</li>";
echo "</ul>";
echo "<hr><p style='color:#666'>Hecho para detectar rápido el punto exacto de falla sin modificar tu sitio.</p>";
echo "</div>";