<?php
@session_start();
?>
<style type="text/css">
@import url("estilos.css");
@import url("fieldset.css");
@import url("plugins/producao/producao.css");
</style>

<fieldset class="fieldset_acima">
<legend>Enviar Arquivos</legend>
<label>Clique no botão e informe o arquivo que deseja enviar.</label>
<br /><br />
<iframe id="upload_iframe" name="upload_iframe" src="upload_form.php?diretorio=<?= $diretorio ?>&codigo=<?= $rs[codigo] ?>&nome=<?= $nome ?>&extensao=<?= $extensao ?>&preview=<?= $preview ?>&link=<?= $link ?>" class="iframe_upload"></iframe>
</fieldset>