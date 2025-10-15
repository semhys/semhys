<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors','1');
header('Content-Type: application/json; charset=utf-8');

// Prefer a config file placed outside the webroot (one level up). Fall back to includes/config.php for local dev.
$external_config = realpath(__DIR__ . '/../config.php');
if ($external_config && is_readable($external_config)) {
  require_once $external_config;
} else {
  require_once __DIR__ . '/includes/config.php';
}

function respond(array $a, int $code=200){ if($code!==200) http_response_code($code); echo json_encode($a, JSON_UNESCAPED_UNICODE); exit; }
function logit(string $msg): void {
  @file_put_contents(__DIR__ . '/contact.log', '['.date('Y-m-d H:i:s')."] $msg\n", FILE_APPEND);
}



if($_SERVER['REQUEST_METHOD']!=='POST'){
  respond(['ok'=>false,'error'=>'Método no permitido'],405);
}


// Permitir datos JSON además de POST clásico
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
  $input = json_decode(file_get_contents('php://input'), true);
  $name     = trim((string)($input['name'] ?? ''));
  $email    = trim((string)($input['email'] ?? ''));
  $location = trim((string)($input['location'] ?? ''));
  $message  = trim((string)($input['message'] ?? ''));
} else {
  $name     = trim((string)($_POST['name'] ?? ''));
  $email    = trim((string)($_POST['email'] ?? ''));
  $location = trim((string)($_POST['location'] ?? ''));
  $message  = trim((string)($_POST['message'] ?? ''));
}

if($name==='' || $email==='' || $location==='' || $message===''){
  respond(['ok'=>false,'error'=>'Datos incompletos'],422);
}
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
  respond(['ok'=>false,'error'=>'Correo inválido'],422);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try{
  /* ====== Conexión BD ====== */
  $cn = db();

  /* Crear tabla si no existe */
  $cn->query("
    CREATE TABLE IF NOT EXISTS leads_semhys (
      id INT AUTO_INCREMENT PRIMARY KEY,
      `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      name VARCHAR(100) NOT NULL,
      email VARCHAR(100) NOT NULL,
      location VARCHAR(100) NULL,
      message TEXT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");

  /* Compatibilidad por si tu tabla tuviera city en vez de location */
  $col = 'location';
  $hasLocation = $cn->query("SHOW COLUMNS FROM leads_semhys LIKE 'location'")->num_rows>0;
  $hasCity     = $cn->query("SHOW COLUMNS FROM leads_semhys LIKE 'city'")->num_rows>0;
  if(!$hasLocation && $hasCity){ $col = 'city'; }

  /* Insertar */
  $sql = "INSERT INTO leads_semhys (name,email,{$col},message) VALUES (?,?,?,?)";
  $st  = $cn->prepare($sql);
  $st->bind_param('ssss', $name, $email, $location, $message);
  $st->execute();
  $id = $st->insert_id;
  $st->close();

  logit("INSERT ok id=$id name='$name' email='$email' $col='$location'");

  /* ====== Envío de correos por SMTP con PHPMailer ====== */
  require __DIR__ . '/phpmailer/src/PHPMailer.php';
  require __DIR__ . '/phpmailer/src/SMTP.php';
  require __DIR__ . '/phpmailer/src/Exception.php';

  $admin_to   = 'contact@semhys.com';
  $sbj_admin  = "Nuevo contacto SEMHYS (#$id)";
  $body_admin = "Nuevo lead:\n\nNombre: $name\nEmail: $email\nCiudad/Estado: $location\n\nMensaje:\n$message\n\nID: $id";

  $sbj_user   = "Hemos recibido tu mensaje – SEMHYS";
  $body_user  = "Hola $name,\n\nGracias por contactarnos. Hemos recibido tu mensaje y te responderemos pronto.\n\nCopia de tu mensaje:\n$message\n\nSEMHYS";

  $mail_admin_ok = false;
  $mail_user_ok  = false;

  $m1 = new PHPMailer\PHPMailer\PHPMailer(true);
  try{
    $m1->isSMTP();
    $m1->Host       = SMTP_HOST;
    $m1->Port       = SMTP_PORT;
    $m1->SMTPAuth   = true;
    $m1->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $m1->Username   = SMTP_USER;
    $m1->Password   = SMTP_PASS;
    $m1->CharSet    = 'UTF-8';
    $m1->setFrom(FROM_EMAIL, FROM_NAME);
    $m1->addAddress($admin_to);
    $m1->addReplyTo($email, $name);
    $m1->Subject = $sbj_admin;
    $m1->Body    = $body_admin;
    $mail_admin_ok = $m1->send();
  }catch(Throwable $e){
    logit("MAIL admin ERROR: ".$e->getMessage());
  }

  $m2 = new PHPMailer\PHPMailer\PHPMailer(true);
  try{
    $m2->isSMTP();
    $m2->Host       = SMTP_HOST;
    $m2->Port       = SMTP_PORT;
    $m2->SMTPAuth   = true;
    $m2->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $m2->Username   = SMTP_USER;
    $m2->Password   = SMTP_PASS;
    $m2->CharSet    = 'UTF-8';
    $m2->setFrom(FROM_EMAIL, FROM_NAME);
    $m2->addAddress($email, $name);
    $m2->addReplyTo(FROM_EMAIL, FROM_NAME);
    $m2->Subject = $sbj_user;
    $m2->Body    = $body_user;
    $mail_user_ok = $m2->send();
  }catch(Throwable $e){
    logit("MAIL user ERROR: ".$e->getMessage());
  }

  logit("MAIL admin=" . ($mail_admin_ok?'1':'0') . " user=" . ($mail_user_ok?'1':'0'));

  respond(['ok'=>true,'id'=>$id,'mail'=>['admin'=>$mail_admin_ok,'user'=>$mail_user_ok]]);
}catch(Throwable $e){
  logit("ERROR: ".$e->getMessage());
  respond(['ok'=>false,'error'=>'DB: '.$e->getMessage()],500);
}