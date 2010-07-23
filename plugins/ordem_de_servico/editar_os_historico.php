<? 
@session_start();
#require_once "../../conn.php";

	if($gravar && $historico_text){
		$tabela = "producao";
		$cols = "historico = '$historico_data | $historico_user \n $historico_text \n\n$historico_completo'";
		$condition = "WHERE codigo=$codigo";
		upd($tabela,$cols,$condition);
	}

	$SQL = "SELECT historico FROM producao WHERE codigo=$codigo";
	$QR = mysql_query($SQL);
	$rs = mysql_fetch_array($QR);
	
	?>
	
<? if($view){ ?><div id="editar_os_historico_<?= $codigo ?>"><? } ?>
	<script language="javascript">
	
	$('[title]').tooltip({ 
		track: false, 
		delay: 0, 
		showURL: false, 
		showBody: true, 
		fade: 30 
	});
	
	<? if($gravar && $historico_text && $status){ ?>
	//exclusivo para alteracao de status para exlcluir na lista de producao
	alert_status('Pronto, Ordem de Serviço marcada como "EXCLUIDA"!\n\nLembre-se: ela ainda pode ser pesquisada e reativada.');
	$("#full_frame").hide();
	$("#full_background").hide();	
	$("#producao_os_<?= $codigo ?>").load("utf2iso.php?file=plugins/producao/producao_os.php",{codigo:'<?= $codigo ?>', info_aberto: 'historico', status:'<?= $status ?>', gravar:'status_os', target: '<?= $codigo ?>'});
	<? } ?>
	
	<? if($status && !$gravar){ ?>
	alert_status('Para EXCLUIR uma Ordem de Serviço, é necessário informar o motivo pelo qual está sendo removida da listagem do sistema.\n\nImportante: por segurança, um registro jamais é excluído fisicamente do banco de dados, podendo o mesmo ser resgatado e reativado Ã  qualquer momento, via pesquisa de seu nÃºmero de Ordem de Serviço.');
	<? } ?>
	
	$('#form_historico_<?= $codigo ?>').bind('submit',function(){
	var formContent = $("#form_historico_<?= $codigo ?>").serialize();
	//alert(formContent);
	$("#editar_os_historico_<?= $codigo ?>").load("utf2iso.php?file=plugins/ordem_de_servico/editar_os_historico.php",formContent);
	return false;
	});
	</script>
	
	<fieldset class="fieldset_historico">
	<legend><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /> HistÃ³rico de atividades internas</legend>
		<form id="form_historico_<?= $codigo ?>" name="form_historico_<?= $codigo ?>" action="" method="post">

		<label for="historico_text">
		Informações relevantes ocorridas - Relate<br />
		<small>(telefonemas, notificações, recados, alterações de ordens de serviço)</small>
		</label>
		
		<input type="hidden" name="codigo" value="<?= $codigo ?>"  />
		<input type="hidden" name="gravar" value="1"  />
		<input type="hidden" name="view" value="<?= $view ?>"  />
		<input type="hidden" name="status" value="<?= $status ?>"  />
		<input type="hidden" name="historico_data" value="<?= date('d/m/Y h:i:s') ?>"  /> 
		<input type="hidden" name="historico_user" value="<?= $_SESSION[user_detalhes] ?>"  />
		<input type="hidden" name="historico_completo" value="<?= ($rs[historico]) ?>" />

		<input type="text" value="<?= date('d/m/Y h:i:s') ?>" disabled="disabled"/> 
		<input type="text" value="<?= $_SESSION[user_detalhes] ?>" disabled="disabled" /><br />
		
		<textarea rows="4" name="historico_text"></textarea>

		<div class="imageright"><hr />
		<input type="image" class="image" title="Adicionar uma entrada no histÃ³rico" id="bt_gravar" src="images/bt_mais.png" />
		<!--<input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" />-->
		</div>
		
		<label>Historico</label>
		<div class="div_textarea_scroll"><?= nl2br(($rs[historico])) ?>...</div>
		</form>
	</fieldset>
	
<? if($view){ ?></div><? } ?>