<?
@session_start();
@include_once "conn.php";
ob_start();
if(isset($file)) $object = include $file;
if(isset($content)) $object = $content;
$ob_contents = ob_get_contents();
ob_end_clean();
echo utf8_encode($ob_contents);

// este arquivo recebe arquivos para include ou texto para ser convertido
// utilizado em caso de recarregamento jquery de conteudo iso
// uso 1: $('#target').load('includes/utf2iso.php',{file:'arquivo_iso.php',id:'$id',parametro2:'results...'});
// uso 2: $.post('includes/utf2iso.php',{file:'arquivo_iso.php',id:'$id',parametro2:'results...'},function(data){$('#target').html(data)});
// uso 3: $.post('includes/utf2iso.php?file=arquivo_iso.php',{id:'$id',parametro2:'results...
?>