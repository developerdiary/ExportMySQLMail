<?php

define('HOST', 'YOUR HOST IP');
define('USERNAME', 'YOUR USERNAME');
define('PASSWORD', 'YOUR PASSWORD');
define('DATABASE', 'YOUR DATABASE NAME');

function create_csv_file($data) {
    
    mysql_connect(HOST, USERNAME, PASSWORD);

    mysql_select_db(DATABASE);
    
    $data = mysql_query('SELECT field1, field2, field3, field4 FROM tablename');

    // Open temp file pointer
    if (!$fp = fopen('php://temp', 'w+')) return FALSE;
    
    fputcsv($fp, array('ID', 'user_login', 'user_pass'));
    
    // Loop data and write to file pointer
    while ($line = mysql_fetch_assoc($data)) fputcsv($fp, $line);
    
    // Place stream pointer at beginning
    rewind($fp);

    // Return the data
    return stream_get_contents($fp);

}

function send_attach_mail($csvData, $body, $to = 'abc@developerdiary.in', $subject = 'Website Report', $from = 'noreply@developerdiary.in') {

    // This will provide plenty adequate entropy
    $multipartSep = '-----'.md5(time()).'-----';

    // Arrays are much more readable
    $headers = array(
        "From: $from",
        "Reply-To: $from",
        "Content-Type: multipart/mixed; boundary=".$multipartSep.""
    );

    // Make the attachment
    $attachment = chunk_split(base64_encode(create_csv_file($csvData))); 

    // Make the body of the message
    $body = "--$multipartSep\r\n" . "Content-Type: text/plain; charset=ISO-8859-1; format=flowed\r\n"
        . "Content-Transfer-Encoding: 7bit\r\n"
        . "\r\n"
        . "$body\r\n"
        . "--$multipartSep\r\n"
        . "Content-Type: text/csv\r\n"
        . "Content-Transfer-Encoding: base64\r\n"
        . "Content-Disposition: attachment; filename=Website-Report.csv\r\n"
        . "\r\n"
        . "$attachment\r\n"
        . "--$multipartSep--";

    // Send the email, return the result
    return @mail($to, $subject, $body, implode("\r\n", $headers));

}

$array = array(array(1,2,3,4,5,6,7,8,9,10,11,12,13), array(1,2,3,4,5,6,7,8,9,10,11,12,13), array(1,2,3,4,5,6,7,8,9,10,11,12,13));

send_csv_mail($array, "Report - www.developerdiary.in");