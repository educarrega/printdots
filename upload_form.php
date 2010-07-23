<?
@session_start();
require_once "upload.php";

#passar na chamada do iframe os paramentros
#diretorio  -> local onde deseja salvar o arquivo enviado, obrigatorio
#codigo     -> para os ou itens que possuem codigo e podem renomear os arquivos enviados com seu índice
#nome       -> ussume o arquivo com este nome, mesmo havendo codigo
#extensao   -> se deseja mudar a extensao do arquivo, ex: codigo.extensao
#preview    -> no caso de querer mostrar a imagem 0/1

if(!$arquivo){
    if($codigo) $novo_nome = $codigo;
    if($nome) $novo_nome = $nome;
    if($extensao && $novo_nome) $novo_nome .= ".$extensao";
    $arquivo = "$diretorio/$novo_nome";
}

upd("controle","valor='$documento'","WHERE titulo='temp'");

?>
<style type="text/css">
@import url("estilos.css");
@import url("fieldset.css");
@import url("plugins/producao/producao.css");
</style>


<body style="background: #5b5b5b;">

<form action="upload_form.php" name="upload" method="post" enctype="multipart/form-data" target="_self">

<input type="hidden" name="codigo" value="<?= $codigo ?>" />
<input type="hidden" name="nome" value="<?= $nome ?>" />     
<input type="hidden" name="diretorio" value="<?= $diretorio ?>" />
<input type="hidden" name="extensao" value="<?= $extensao ?>" />
<input type="hidden" name="preview" value="<?= $preview ?>" />
      
    <? if($preview){ ?>  
	<div id="producao_os_thumbs" class="producao_os_thumbs">
	<img src="<?= $arquivo ?>?t=<?= $uid ?>" width="100%">
	</div> 
    <? } ?>
    
	<input type="file" name="file" /> 
	<input type="image" src="images/bt_avancar.png" name="enviar" align="absmiddle" style="width: 20px; margin-top: 22px;"><br />
        
    <? if($documento){ ?>
	<img src="images/bt_check.png" width="24px" />
        <a href="<?= $arquivo ?>?<?= $uid ?>" target="_blank"><?= $documento ?></a><br />
    <? } ?>

</form>

</body>
