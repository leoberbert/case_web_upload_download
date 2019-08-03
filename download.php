<?php

$token = "<ACCESS_TOKEN>"; 

$arquivoPDF = urldecode($_GET['arq']); 

$in_filepath = "/upload/".$arquivoPDF;

header("Content-type: application/pdf");
header("Content-Disposition: attachment; filename=".$arquivoPDF."");
$out_filepath = readfile($arquivoPDF);

$out_fp = fopen($out_filepath, 'w+');
if ($out_fp === FALSE)
    {
    //echo \"fopen error cant open $out_filepath\n";
	echo "<script language='javascript'>alert('fopen error cant open!');</script>";
	echo "<script language='javascript'>window.location.href = 'list.php';</script>";
    //return (NULL);
    }

$url = 'https://content.dropboxapi.com/2/files/download';

$header_array = array(
    'Authorization: Bearer ' . $token,
    'Content-Type:',
    'Dropbox-API-Arg: {"path":"' . $in_filepath . '"}'
);


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
curl_setopt($ch, CURLOPT_FILE, $out_fp);

//PROBLEMA SSL
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$metadata = null;
curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$metadata)
    {
    $prefix = 'dropbox-api-result:';
    if (strtolower(substr($header, 0, strlen($prefix))) === $prefix)
        {
        $metadata = json_decode(substr($header, strlen($prefix)), true);
        }
    return strlen($header);
    }
);

$output = curl_exec($ch);

//echo "$output\n";

if ($output === FALSE)
    {
    //echo "curl error: " . curl_error($ch);
	echo "<script language='javascript'>alert('curl error!');</script>";
	echo "<script language='javascript'>window.location.href = 'list.php';</script>";
    }

curl_close($ch);
fclose($out_fp);
echo "<script language='javascript'>alert('Download efetuado com sucesso!');</script>";
echo "<script language='javascript'>window.location.href = 'list.php';</script>";
//var_dump($metadata);
//return($metadata);
//} 

// dbx_get_file()


?>
