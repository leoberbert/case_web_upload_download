<?php

function logMe($msg){

    //$t = microtime(true);
    //$micro = sprintf("%06d",($t - floor($t)) * 1000000);
    //$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
    
    //$d->format("Y-m-d H:i:s.u"); // note at point on "u"

    $fp = fopen("aplication.log", "a");
    $escreve = fwrite($fp, date("d/m/Y H:i:s").$msg);
    fclose($fp);

}

?>