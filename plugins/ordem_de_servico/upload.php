<?
require_once "../../upload.php";
?>
<style type="text/css">
@import url("../../estilos.css");
@import url("../../fieldset.css");
@import url("../producao/producao.css");
</style>


<body style="background: #5b5b5b;">

<form action="upload.php" name="upload" method="post" enctype="multipart/form-data" target="_self">
<input type="hidden" name="codigo" value="<?= $codigo ?>" />
<input type="hidden" name="diretorio" value="plugins/producao/thumbs" />
<input type="hidden" name="extensao" value="jpg" />

	<div id="producao_os_thumbs" class="producao_os_thumbs">
	<img src="../producao/thumbs/<?= $codigo ?>.jpg?t=<?= $uid ?>" width="100%">
	</div> 
	
	<input type="file" name="file" /> 
	<input type="image" src="../../images/bt_avancar.png" name="enviar" align="absmiddle" style="width: 20px; margin-top: 22px;">
	<br/><br/>
	
	&nbsp;&nbsp;<a href="?codigo=<?= $codigo ?>"><img class="producao_os_thumbs_atualizar" src="../../images/bt_atualizar.png" width="24" title="Atualizar a imagem"/></a>

</form>

</body>
