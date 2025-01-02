<?php
if ($_POST['g-recaptcha-response'] == null || !isset($_POST['g-recaptcha-response']) || $_POST['g-recaptcha-response'] == '') {
    // Retorna un error al HTML
    echo json_encode([
        'status' => 'error',
        'title' => 'Error',
        'text' => 'Complete el reCAPTCHA para continuar'
    ]);
    return;
} elseif (isset($_POST['correo']) && isset($_POST['nombre']) && isset($_POST['telefono']) && isset($_POST['mensaje']) && isset($_POST['producto'])) {
    $correo = trim($_POST['correo']);
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $mensaje = trim($_POST['mensaje']);
    $producto = trim($_POST['producto']);

    $message = "Nombre: " . $nombre .  "\n Correo: " . $correo .  "\n Telefono de contacto: " . $telefono  .  "\n Producto: " . $producto  .  "\n Mensaje: " . $mensaje;

    $response = $_POST['g-recaptcha-response'];
    $secret = '6LeZSKwqAAAAAPQ1YbMlFwCEBhGMqVSVaZ6vEiaa';
    $url = "https://www.google.com/recaptcha/api/siteverify";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secret, 'response' => $response)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($result);

    if ($result->success) {
        $to = 'ventas@maspotencia.mx';
        $subject = 'Formulario de contacto';

        $mail = mail($to, $subject, $message);
        if ($mail) {
            echo json_encode([
                'status' => 'success',
                'title' => 'Éxito',
                'text' => 'Formulario enviado con éxito'
            ]);
            return;
        } else {
            echo json_encode([
                'status' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un error al enviar el formulario'
            ]);
            return;
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'title' => 'Error',
            'text' => 'Verificación de reCAPTCHA fallida'
        ]);
        return;
    }
} else {
    echo json_encode([
        'status' => 'error',
        'title' => 'Error',
        'text' => 'Llene todos los campos obligatorios del formulario para continuar'
    ]);
    return;
}
?>
