<?php
namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email{
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;   
    }
    public function enviarConfirmacion(){
        //Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ca5ac445c58fa3';
        $mail->Password = '163693909d4d91';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com','AppSalon.com');
        $mail->Subject = 'Confirma tu Cuenta';

        //set HTML
        $mail->isHTML(TRUE);
        $mail->Charset ='UFT-8';

        $contenido = "<html>";
        $contenido .= "<p><strong> Hola " . $this->nombre . "</strong> has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace: </p>";
        $contenido .= "<p>Presiona Aqui: <a href='http://localhost:4000/confirmar-cuenta?token=" . $this->token . "'> Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no Solicitaste esta cuenta, puedes ignorar el mensaje!</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //Enviar el email
        $mail->send();
    }
    public function enviarInstrucciones(){
        //Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ca5ac445c58fa3';
        $mail->Password = '163693909d4d91';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com','AppSalon.com');
        $mail->Subject = 'Reestablecer tu Password';

        //set HTML
        $mail->isHTML(TRUE);
        $mail->Charset ='UFT-8';

        $contenido = "<html>";
        $contenido .= "<p><strong> Hola " . $this->nombre . "</strong> has solicitado reestablecer tu password. Sigue el siguiente enlace. </p>";
        $contenido .= "<p>Presiona Aqui: <a href='http://localhost:4000/recuperar?token=" . $this->token . "'> Reestablecer Password</a></p>";
        $contenido .= "<p>Si tu no Solicitaste esta cambio, puedes ignorar el mensaje!</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //Enviar el email
        $mail->send();
    }
}