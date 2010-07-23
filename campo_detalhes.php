<? 
@session_start();
include_once "conn.php";

# $tabela
# $campo
# $codigo
# $titulo (se nao passado, assume o nome do campo)
# $editar passar o #id do campo para editar (se deseja que o campo venha em mode edicao, sempre atualizando dentro do id informado)
# retorno (defaut-nada html, ou text( sem titulo ))

//if($codigo && $tabela && $campo){

	if($gravar){
		$tabela = $_POST[tabela];
		$campo = $_POST[gravar];
		$valor = $_POST[$campo];
		$codigo = $_POST[codigo];
		
		$cols = "$campo='$valor'";
		$condition = "WHERE codigo=$codigo";
		upd($tabela,$cols,$condition);
		//echo $condition;
		atualizar();
	}else{
		//print_r($_POST);
		$SQL = "SELECT $campo FROM $tabela WHERE codigo=$codigo";
		$QR = mysql_query($SQL);
		$rs = mysql_fetch_array($QR);
		if(!isset($titulo)) $titulo = $campo;
	
		if($retorno == "text"){
			return nl2br($rs[$campo]);
		}else{ #html ?>
			<style type="text/css">
			@import url("fieldset.css");
			</style>
			<fieldset class="fieldset_infos">
			<legend><?= $titulo ?></legend>
			<label>Dados</label>
			<? if(!isset($editar)){ ?>
			<div class="div_textarea_scroll"><?= nl2br($rs[$campo]) ?></div>
			<? }else{ ?>
			<script language="javascript">
			$('#<?= $campo ?>_dt_<?= $codigo ?>').keyup(function(){
				$('#status_gravacao').html('<img src="images/loader.gif" align="absmiddle"> Gravando texto...');
				clearTimeout(g);
				var g = setTimeout(function(){
				var formContent = $("#form_dados_<?= $campo ?>").serialize();
				//alert(formContent);
				$.post("campo_detalhes.php",formContent,function(){
				$('#status_gravacao').html('Você está no modo edição, basta digitar seu texto');
				});
				}, 2000);
			});
			</script>
			<form id="form_dados_<?= $campo ?>" name="form_dados_<?= $campo ?>" action="" method="post">
			<input type="hidden" name="tabela" value="<?= $tabela ?>" />
			<input type="hidden" name="codigo" value="<?= $codigo ?>" />
			<input type="hidden" name="titulo" value="<?= $titulo ?>" />
			<input type="hidden" name="gravar" value="<?= $campo ?>" />
			<? 
			$rs_campo = str_replace("<br>","\n",$rs[$campo]);
			$rs_campo = str_replace("<br/>","\n",$rs_campo);
			$rs_campo = str_replace("<br />","\n",$rs_campo);
			$rs_campo = strip_tags($rs_campo);
			?>
			<textarea rows="6" id="<?= $campo ?>_dt_<?= $codigo ?>" name="<?= $campo ?>"><?= $rs_campo ?></textarea>
			<div id="status_gravacao">Você está no modo edição, basta digitar seu texto</div>
			<!--<input type="image" id="bt_gravar" src="images/button_ok.png" />-->
			</form>
			<? } ?>
			<br />
			</fieldset>
			<?
		} #retorno
	} #gravar
//} #codigo tabela e campo?>
