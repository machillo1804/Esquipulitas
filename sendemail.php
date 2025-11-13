<?php

/**
 * sendemail.php - Script PHP para procesar y enviar el formulario de contacto.
 * * NOTA: La función mail() de PHP puede ser poco fiable. Si tienes problemas de entrega,
 * considera usar una librería SMTP como PHPMailer.
 */

// --- 1. CONFIGURACIÓN DEL RECEPTOR ---
// Define el nombre y la dirección de correo electrónico del destinatario
define( "RECIPIENT_NAME", "Web Laboratorio Esquipulitas" ); 
define( "RECIPIENT_EMAIL", "machillo18@gmail.com" );


// --- 2. LECTURA Y LIMPIEZA DE VALORES DEL FORMULARIO ---
$success = false;

// Limpieza de datos (usando las expresiones regulares originales)
// Nombre (permite letras, números, espacios y algunos caracteres especiales)
$userName = isset( $_POST['username'] ) ? preg_replace( "/[^\s\S\.\-\_\@a-zA-Z0-9]/", "", $_POST['username'] ) : "";
// Email (solo permite el formato básico de un correo)
$senderEmail = isset( $_POST['email'] ) ? preg_replace( "/[^\.\-\_\@a-zA-Z0-9]/", "", $_POST['email'] ) : "";
// Teléfono (permite letras, números, espacios y algunos caracteres especiales)
$userPhone = isset( $_POST['phone'] ) ? preg_replace( "/[^\s\S\.\-\_\@a-zA-Z0-9]/", "", $_POST['phone'] ) : "";
// Asunto / Motivo de Consulta (DEBE coincidir con el atributo name="subject" en el HTML)
$userSubject = isset( $_POST['subject'] ) ? preg_replace( "/[^\s\S\.\-\_\@a-zA-Z0-9]/", "", $_POST['subject'] ) : "";
// Mensaje (elimina posibles intentos de inyección de encabezados)
$message = isset( $_POST['message'] ) ? preg_replace( "/(From:|To:|BCC:|CC:|Subject:|Content-Type:)/i", "", $_POST['message'] ) : "";

// --- 3. VALIDACIÓN DE CAMPOS ---
// Si todos los valores requeridos existen y no están vacíos
if ( $userName && $senderEmail && $userPhone && $userSubject && $message) {
  
  // --- 4. PREPARACIÓN DEL CORREO ---
  $recipient = RECIPIENT_NAME . " <" . RECIPIENT_EMAIL . ">";
  $subject = "Consulta Web - Motivo: " . $userSubject; // Asunto del correo que recibirás

  // Construcción de encabezados robustos
  // El correo parece venir del servidor, pero puedes responder al remitente real
  $headers = "From: " . $userName . " <" . $senderEmail . ">\r\n";
  $headers .= "Reply-To: " . $senderEmail . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/plain; charset=UTF-8\r\n"; // Soporte para caracteres especiales (ñ, tildes)

  // Cuerpo del mensaje formateado para mejor lectura
  $msgBody = "--- Detalles de la Consulta ---\n";
  $msgBody .= "Nombre: " . $userName . "\n";
  $msgBody .= "Email: " . $senderEmail . "\n";
  $msgBody .= "Teléfono: " . $userPhone . "\n";
  $msgBody .= "Asunto/Motivo: " . $userSubject . "\n";
  $msgBody .= "------------------------------\n";
  $msgBody .= "Mensaje:\n" . $message . "\n";
  $msgBody .= "------------------------------\n";
  
  // --- 5. INTENTO DE ENVÍO ---
  $success = mail( $recipient, $subject, $msgBody, $headers );

  // --- 6. REDIRECCIÓN TRAS EL ENVÍO ---
  if ($success) {
    // Redirección exitosa
    header('Location: contactenos.html?message=Successfull');
  } else {
    // Redirección fallida (fallo interno de mail())
    header('Location: contactenos.html?message=Failed_Server_Error');
  }
}

else {
	// Redirección fallida (faltan campos)
  	header('Location: contactenos.html?message=Failed_Missing_Fields');	
}

?>