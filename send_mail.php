<?php
header('Access-Control-Allow-Origin: *');
$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
$mail->CharSet = "UTF-8"; //enable accentuate character
$mail->Encoding = '8bit';
try {
    //Server settings
    $mail->SMTPDebug = 4;                                 // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = $mail_host;                             // Specify main and backup SMTP servers
    $mail->AuthType = 'LOGIN';
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = $mailAccount;                       // SMTP username
    $mail->Password = $mail_password;                     // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = $mail_port;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true));                             // TCP port to connect to

    //Recipients
    $mail->setFrom($mailAccount, "Val'Acro");
    $mail->addAddress($login_mail, "Val'Acro User");     // Add a recipient  // Name is optional
    $mail->addReplyTo($mailAccount, "Val'Acro");

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $sujet;
    $mail->Body    = str_replace('"',' ',$message_html);
    $mail->AltBody = str_replace('"',' ',$message_txt);
    //AltBody correspond au message si le webmail du client ne lit pas le html
    //str_replace permet d'enlever les quotes autour du mot de passe provenant de la bdd

    $mail->send();
    //echo 'Message has been sent';
} catch (Exception $e) {
    //echo 'Message could not be sent. Mailer Error: ' ,$mail->ErrorInfo;
}
?>