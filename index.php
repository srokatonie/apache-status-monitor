<?php

// Copy .env.example to .env and fill details

//CRON COMMAND
// */5 * * * * /usr/bin/php /path_to_apache-monitor/index.php > /dev/null 2>&1


require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$recipientEmail = $_ENV['RECIPIENT'];
$smtp = array();
$smtp['Host'] = $_ENV['SMTP_HOST'];
$smtp['Username'] = $_ENV['SMTP_USER'];
$smtp['Password'] = $_ENV['SMTP_PASS'];
$smtp['SMTPAuth'] = true;
//Enable implicit TLS encryption
$smtp['SMTPSecure'] = PHPMailer::ENCRYPTION_STARTTLS;
//TCP port to connect to; use 587 if you have set 
 // `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
$smtp['Port'] = 587; 
                
echo "Running Apache check... ";
$apacheRunning = isApacheRunning();

if (!$apacheRunning) {
  print_r("Apache not running.\n");
  sendNotification($smtp, $recipientEmail);
  restartApache();
} else {
  print_r("Apache OK.\n");
}

function checkCommand() {
  return 'service apache2 status | grep -i running';
}

function restartCommand() {
  return 'service apache2 restart';
}

function sendNotification($smtp, $recipientEmail) {
  $mail = new PHPMailer(true);
  echo "Sending email... ";

  try {
      $mail->isSMTP();
      $mail->Host       = $smtp['Host'];
      $mail->Username   = $smtp['Username'];
      $mail->Password   = $smtp['Password'];
      $mail->SMTPAuth   = $smtp['SMTPAuth']; 
      $mail->SMTPSecure = $smtp['SMTPSecure'];
      $mail->Port       = $smtp['Port'];

      //Recipients
      $mail->setFrom($smtp['Username']);
      $mail->addAddress($recipientEmail);

      //Content
      $mail->isHTML(false);
      $mail->Subject = 'Problem witth FD Apache';
      $mail->Body    = 'Apache down...';

      $mail->send();
      echo "Message has been sent.\n";
  } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
  }
}

function isApacheRunning() {
  $command = checkCommand();
  $output = shell_exec($command);
  if (strpos($output, '(running)')) {
    return true;
  } else {
    return false;
  }
}

function restartApache() {
  $command = restartCommand();
  $output = shell_exec($command);
}