<?php

namespace Includes\PHPMailer;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use SRC\Controller\Views;

class Email
{
    private static $SMTP_ADDRESS;
    private static $SMTP_EMPRESA;
    private static $SMTP_USERNAME;
    private static $SMTP_PASSWORD;
    private static $SMTP_PORT;
    private static $SMTP_STARTTLS;

    public static function config($Empresa, $Host, $Username, $Password, $Port, $TLS)
    {
        self::$SMTP_EMPRESA = $Empresa;
        self::$SMTP_ADDRESS = $Host;        
        self::$SMTP_USERNAME = $Username;
        self::$SMTP_PASSWORD = $Password;
        self::$SMTP_PORT = $Port;
        self::$SMTP_STARTTLS = $TLS;
    }

    public static function geraEmail($data){
        
        $coponetes = [
           "empresa" => self::$SMTP_EMPRESA.' - '.date('d/m/Y')
        ];

        $coponetes = array_merge($coponetes, (array) $data);

        $data->mensagem = Views::render($data->templete, $coponetes);

        return self::sedEmail($data);
    }

    public static function sedEmail($data)
    {
        $mail = new PHPMailer(true);
        
        // $data = new \stdClass();
        // $data->email = "mailer@softapps.com.br";
        // $data->name = "softapps";
        // $data->addemil = "joaops2305@gmail.com";
        // $data->addname = "João Pereira";
        // $data->assunto = "Email TLS False";
        // $data->mensagem = "Testando";

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;     //Habilitar saída de depuração detalhada
            $mail->isSMTP();                             //Send using SMTP
            $mail->Host         = self::$SMTP_ADDRESS;   //Defina o servidor SMTP para enviar
            $mail->Username     = self::$SMTP_USERNAME;  //SMTP username
            $mail->Password     = self::$SMTP_PASSWORD;  //SMTP password            
            $mail->Port         = self::$SMTP_PORT;      //Porta TCP para conexão; use 587 se você configurou `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->SMTPAuth     = true;                  //Enable SMTP authentication
            $mail->SMTPAutoTLS  = false; 

            $mail->SMTPSecure = filter_var(self::$SMTP_STARTTLS, FILTER_VALIDATE_BOOLEAN) ? PHPMailer::ENCRYPTION_SMTPS : false;

            //Recipients
            $mail->setFrom(self::$SMTP_USERNAME, self::$SMTP_EMPRESA);
            $mail->addAddress($data->addemil); //Add a recipient
            $mail->addReplyTo(self::$SMTP_USERNAME, 'Information');
            $mail->addCC(self::$SMTP_USERNAME);
            $mail->addBCC(self::$SMTP_USERNAME);

            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject = utf8_decode($data->assunto);
            $mail->Body = utf8_decode($data->mensagem);

            $mail->send();
            return 1;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    }
}

