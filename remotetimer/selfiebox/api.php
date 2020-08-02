<?php

$response = array();
$content = file_get_contents("php://input");
$decoded = json_decode($content, true);

$mailto = $decoded['email'];
$image = $decoded['image'];
$file_name = $decoded['filename'];
$from_name = "SelfieBox";
$from_mail = "noreply@morrisons2.hu";

$subject = "My amazing party in Morrisons";
//$message = "<h1>Próba mail!</h1><p><p>Üdv: I</p>";
$htmlContent = "<h1>Ilyenek voltunk a Morrisonsban</h1><p>Ugye jól néztünk ki!</p>";
$response['result'] = 'success';

save($file_name, base64_decode($image));

$success = mail_attachment($mailto, $from_mail, $from_name, $subject, $htmlContent, $image, $file_name);
if (!$success) {
    $response['result'] = 'failed';
}

/* send back the result */
echo json_encode($response);

function save($file_name, $image)
{
    file_put_contents('pictures/' . $file_name, $image);
}

//Taken from: https://www.codexworld.com/send-email-with-attachment-php/

function mail_attachment($mailto, $from_mail, $from_name, $subject, $htmlContent, $image, $file_name)
{
    $encoded_content = chunk_split($image);

//header for sender info
    $headers = "From: $from_name" . " <" . $from_mail . ">";

//boundary
    $semi_rand = md5(time());
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

//headers for attachment
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

//multipart boundary
    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
        "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n";

//preparing attachment

    $message .= "--{$mime_boundary}\n";

    $data = chunk_split($image);
    $message .= "Content-Type: application/octet-stream; name=\"" . $file_name . "\"\n" .
        "Content-Description: " . $file_name . "\n" .
        "Content-Disposition: attachment;\n" . " filename=\"" . $file_name . "\";\n" .
        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";

    $message .= "--{$mime_boundary}--";
    $returnpath = "-f" . $from_mail;

//send email
    $sentMailResult = @mail($mailto, $subject, $message, $headers, $returnpath);
    return $sentMailResult;
}
