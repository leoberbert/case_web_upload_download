<?php

include("log.php");

logMe(":1:Iniciando o sistema\n");

logMe(":I:Declarando variaveis\n");
// Pasta onde o arquivo vai ser salvo
$_UP['pasta'] = '';

// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 100; // 10Mb

// Array com as extensões permitidas
$_UP['extensoes'] = array(
    'pdf'
);

// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
$_UP['renomeia'] = false;

// Array com os tipos de erros de upload do PHP
$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';

logMe(":I:Verificando erros de upload no arquivo\n");
// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
if ($_FILES['arquivo']['error'] != 0) {
    logMe("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']] . "\n");
    logMe(":X:Finalizando o sistema\n---------------------------------------------\n");
    die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']]);
    exit; // Para a execução do script
}
logMe(":I:Finalizando verificanção de erros de upload no arquivo\n");
// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar

// Faz a verificação da extensão do arquivo
//$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));

$arquivo    = $_FILES['arquivo']['name'];
$split      = explode('.', $arquivo);
$comandoend = end($split);
$lower      = strtolower($comandoend);
$extensao   = $lower;

//var_dump($extensao);
logMe(":I:Validando extensões do arquivo\n");
if (array_search($extensao, $_UP['extensoes']) === false) {
    //echo "Por favor, envie arquivos com a seguinte extensão: pdf";	
    logMe(":I:Por favor, envie arquivos com a seguinte extensão: pdf\n");
    logMe(":X:Finalizando o sistema\n---------------------------------------------\n");
	echo "<script language='javascript'>alert('Por favor, envie arquivos com a seguinte extensão: pdf');</script>";	
	echo "<script language='javascript'>window.location.href = 'index.php';</script>";
}
// Faz a verificação do tamanho do arquivo
else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
    //echo "O arquivo enviado é muito grande, envie arquivos de até 10Mb.";	
    logMe(":I:O arquivo enviado é muito grande, envie arquivos de até 10Mb.\n");
    logMe(":X:Finalizando o sistema\n---------------------------------------------\n");
	echo "<script language='javascript'>alert('O arquivo enviado é muito grande, envie arquivos de até 10Mb.');</script>";
	echo "<script language='javascript'>window.location.href = 'index.php';</script>";
}

// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
else {
    // Primeiro verifica se deve trocar o nome do arquivo
    if ($_UP['renomeia'] == true) {
        // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
        $nome_final = time() . '.jpg';
    } else {
        // Mantém o nome original do arquivo
        $nome_final = $_FILES['arquivo']['name'];
    }
    
    logMe(":I:Movendo arquivo para pasta de destino.\n");
    // Depois verifica se é possível mover o arquivo para a pasta escolhida
    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
        // Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
        
        $local_file = $_UP['pasta'] . $nome_final;
        $arquivo = $local_file;
        $path = $arquivo;

        $fp = fopen($path, 'rb');
        //$size = filesize($path);
        
        $cheaders = array(
            'Authorization: Bearer <ACCESS_TOKEN>',
            'Content-Type: application/octet-stream',
            'Dropbox-API-Arg: {"path":"/upload/' . $path . '", "mode":"add"}'
        );
        
        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        //curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		//PROBLEMA SSL
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		
        $response = curl_exec($ch);
        //echo $response;
        curl_close($ch);
        //fclose($fp);
        //echo "Upload efetuado com sucesso!";
        logMe(":I:Upload efetuado com sucesso!\n");
		echo "<script language='javascript'>alert('Upload efetuado com sucesso!');</script>";
    } else {
        // Não foi possível fazer o upload, provavelmente a pasta está incorreta
        //echo "Não foi possível enviar o arquivo, tente novamente";		
        logMe(":I:Não foi possível enviar o arquivo, tente novamente\n");
        logMe(":X:Finalizando o sistema\n---------------------------------------------\n");
		echo "<script language='javascript'>alert('Não foi possível enviar o arquivo, tente novamente!');</script>";
		echo "<script language='javascript'>window.location.href = 'index.php';</script>";
    }
    
    logMe(":X:Finalizando o sistema\n---------------------------------------------\n");
}
$local_file = $_UP['pasta'] . $nome_final;
unlink ($local_file);
echo "<script language='javascript'>window.location.href = 'index.php';</script>";

?>
