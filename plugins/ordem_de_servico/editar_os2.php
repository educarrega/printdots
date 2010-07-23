<? @session_start();
#require_once "../../conn.php";
include_once "../producao/producao_editar.php";
login();
#echo $codigo;
#echo $gravar;

if($codigo){
$QR = mysql_query("SELECT * FROM producao WHERE codigo=$codigo");
$rs = mysql_fetch_array($QR);
	$gravar = "editar_os";
}else{
	$gravar = "incluir_os";
}
$uid = time(YmdHis);
?>

<style type="text/css">
	@import url("plugins/producao/producao.css");
</style>

<script language="JavaScript" type="text/javascript">
$('#editar_<?= $rs[codigo] ?>').bind('submit',function(){
	var formContent = $('#editar_<?= $rs[codigo] ?>').serialize();
	<? if($codigo){ ?>
		$.post("utf2iso.php?file=plugins/ordem_de_servico/editar_os2.php", formContent, function(data){/*alert(data)*/});
		if(document.editar.gravar.value=='duplicar_os'){ window.location.hash = '#'}
	<? }else{ ?>
		$.post("utf2iso.php?file=plugins/producao/producao_editar.php",formContent,function(data){
			//window.location.hash = '#';
			//alert(data);
			if(confirm('O.S. adicionada!\nAdicionar mais uma (Cancelar/Esc)\nou ir Ã  lista de produção na OS '+ data +' (OK/Enter)')){
			$('#content_right').load("utf2iso.php?file=plugins/producao/producao.php",{codigo:data});
			window.location.hash = '#';
			}else{
			$('#content_right').load("utf2iso.php?file=plugins/ordem_de_servico/adicionar_os.php");			
			window.location.hash = '#';
			}
		});
	<? } ?>	
	return false;
});

$(document).ready(function(){
	//frame_equipamentos
	$('#frame_equipamento_<?= $rs[codigo] ?>').load("utf2iso.php?file=plugins/equipamento/equipamento_os_editar.php",{ producao:'<?= $rs[codigo] ?>', data_prevista:'<?= $rs[data_prevista] ?>', target: '<?= $target ?>'});
	
	//frame contas_receber
	<? if(plugin("contas_receber") && $rs[agencia]){ ?>
	$('#contas_receber_editar_os_<?= $rs[codigo] ?>').load("utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php",{ codigo_producao:'<?= $rs[codigo] ?>',codigo_cliente:'<?= $rs[agencia] ?>',titulo_servico: '<?= $rs[titulo_servico] ?>', uid: '<?= md5($uid)?>', target: '<?= $target ?>'});
	<? } ?>
	
	//frame_historico
	$('#editar_os_historico_<?= $codigo ?>').load('utf2iso.php?file=plugins/ordem_de_servico/editar_os_historico.php',{codigo: '<?= $codigo ?>'});
	
	//transporte
	var transporte_obs = $('[name=transporte_obs]').val();
	$('#email_transporte').hide();
	if(transporte_obs.length > 1){
		$('#email_transporte').show();
	}
	
	$('[name=transporte_obs]').keyup(function(){
	var transporte_obs = $('[name=transporte_obs]').val();
	if(transporte_obs.length > 1){
		$('#email_transporte').slideDown();
	}else{
		$('#email_transporte').slideUp();
	}
	});
	
	//titulos
	$('[title]').tooltip({ 
		track: false, 
		delay: 0, 
		showURL: false, 
		showBody: true, 
		fade: 30 
	});
	
	//previsoes
	$('#autorizado_data').focus(function(){
		$(this).calendario({
			target:'#autorizado_data'
		});
	});
	$('#data_entrega').focus(function(){
		$(this).calendario({
			target:'#data_entrega'
		});
	});
	$('#data_prevista').focus(function(){
		$(this).calendario({
			target:'#data_prevista'
		});
	});

});

$(".producao_os_thumbs_atualizar_<?= $rs[codigo] ?>").click(function(){
	$('#producao_os_<?= $rs[codigo] ?>').load("utf2iso.php?file=plugins/ordem_de_servico/editar_os2.php",{ codigo:'<?= $rs[codigo] ?>',uid:'<?= $uid ?>', target: '<?= $target ?>'});
});
$("#producao_os_thumbs_ampliar_<?= $rs[codigo] ?>").click(function(){
	$("#full_frame").html('<img src="users/<?= controle('settings_folder')?>/thumbs/<?= $rs[codigo] ?>.jpg?t=<?= md5($uid) ?>" width="100%">');
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(250,function(){
			$("#full_background").click(function(){
				$("#full_background").hide();
			})
		});
	});
});

//notificacoes
$("#bt_send_os_<?= $rs[codigo] ?>").click(function(){
	send_os('<?= $rs[codigo] ?>');
	var p = setTimeout(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $rs[codigo] ?>").load('utf2iso.php?file=plugins/ordem_de_servico/editar_os2.php',{codigo: '<?= $rs[codigo] ?>', target: '<?= $target ?>'});
		});
	}, 5000);
});
$("#bt_print_os_<?= $rs[codigo] ?>").click(function(){
	print_os('<?= $rs[codigo] ?>');
	var p = setTimeout(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $rs[codigo] ?>").load('utf2iso.php?file=plugins/ordem_de_servico/editar_os2.php',{codigo: '<?= $rs[codigo] ?>', target: '<?= $target ?>'});
		});
	}, 5000);
});
$("#bt_transp_os_<?= $rs[codigo] ?>").click(function(){
	send_transp('<?= $rs[codigo] ?>');
	var p = setTimeout(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $rs[codigo] ?>").load('utf2iso.php?file=plugins/ordem_de_servico/editar_os2.php',{codigo: '<?= $rs[codigo] ?>', target: '<?= $target ?>'});
		});
	}, 5000);
});

// voltar a os sem editar
$(".bt_voltar_<?= $target ?>").click(function(){
	$("#producao_os_<?= $target ?>").load("utf2iso.php?file=plugins/producao/producao_os.php",{codigo:'<?= $rs[codigo] ?>', target: '<?= $target ?>'});
	$('#producao_os_<?= $target ?>').css({'overflow':'hidden', 'height': '130px'});
	start_Int();
	window.location.hash = '#producao_os_<?= $target ?>';
});

<? if(plugin("cliente")){ ?>
// adicionar cliente
$('#bt_add_cliente').click(function(){
	$('#add_cliente').load('utf2iso.php?file=plugins/cliente/cliente_editar.php',function(){
		//$('#bt_add_cliente').fadeOut();
		$('#add_cliente').slideDown('slow');
	});	
});
<? } ?>
$("#agencia_select_<?= $target ?>").change(function(){
	var codigo_cliente = $("#agencia_select_<?= $target ?>").val();
	if(codigo_cliente!='0' && codigo_cliente!='' && codigo_cliente!=' ' && codigo_cliente!=false){
		$('#contas_receber_editar_os_<?= $rs[codigo] ?>').load("utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php",{ codigo_producao:'<?= $rs[codigo] ?>',codigo_cliente:codigo_cliente,titulo_servico: '<?= $rs[titulo_servico] ?>', uid: '<?= md5($uid)?>', target: '<?= $target ?>'});
	}else{
		$('#contas_receber_editar_os_<?= $rs[codigo] ?>').html('');
	}
	//alert(codigo_cliente);
});

</script>

<? if($codigo){ ?>
<fieldset class="fieldset_atencao">
<legend><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /> <?= $rs[codigo] ?></legend>
Retornar ao modo de fila desta Ordem de Serviço.
</fieldset>
<? } ?>


<fieldset class="fieldset_thumbs">
<legend><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /> Arquivo de pré visualização</legend>
	<img src="images/bt_ampliar.png" class="producao_os_thumbs_ampliar" id="producao_os_thumbs_ampliar_<?= $rs[codigo] ?>" title="Ampliar a Imagem" />
	<iframe src="upload_form.php?codigo=<?= $rs[codigo] ?>&diretorio=users/<?= controle("settings_folder") ?>/thumbs&extensao=jpg&preview=1" class="iframe_upload"></iframe>
	<div class="clear_left">&nbsp;</div>
</fieldset>


<form action="" name="editar" id="editar_<?= $rs[codigo] ?>" method="post">
<input type="hidden" name="codigo" value="<?= $rs[codigo] ?>" />
<input type="hidden" name="gravar" value="<?= $gravar ?>" />
<input type="hidden" name="target" value="<?= $target ?>" />
<? if(!$codigo){ //obter o retorno do codigo apos a inclusao para mandar para a fila de producao ?>
<input type="hidden" name="retorno" value="codigo" />
<? } ?>

<? if(plugin("cliente")){ ?>
<div id="add_cliente"></div>
<? } ?>

<fieldset class="fieldset_infos">
<legend><? if($codigo){ ?><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /><? } ?> Informações Primárias do Serviço</legend>

	<? if(plugin("cliente")){ ?>
	<legend style="float:right; margin-top: 20px;">
	<img id="bt_add_cliente" src="images/bt_mais.png" title="Incluir novo Cliente" /> 
	<img class="producao_os_thumbs_atualizar_<?= $rs[codigo] ?>" src="images/bt_atualizar.png" width="24" title="Atualizar os Clientes" class="bt" />
	</legend>
	<? } ?>
	
	<label for="agencia">Cliente/Contratante</label>
	<select name="agencia" id="agencia_select_<?= $target ?>"> 
		<option value="0">Escolha</option>
		<?
		$SQLa = mysql_query("SELECT codigo,nome_fantasia,cidade,estado FROM cliente WHERE ativo=1 ORDER BY nome_fantasia, nome_fantasia, razao_social");
		while($ag = mysql_fetch_array($SQLa)){
		?><option value="<?= $ag[codigo] ?>" <? if($ag[codigo]==$rs[agencia]) echo selected ?>><?= ($ag[nome_fantasia]) ?> - <?= ($ag[cidade]) ?> <?= ($ag[estado]) ?></option><?
		}?>
		<optgroup label="Inativos">
			<?
			$SQLa = mysql_query("SELECT codigo,nome_fantasia,cidade,estado FROM cliente WHERE ativo=0 OR ativo='' ORDER BY nome_fantasia, nome_fantasia, razao_social");
			while($ag = mysql_fetch_array($SQLa)){
			?><option value="<?= $ag[codigo] ?>" style="color:#999999" <? if($ag[codigo]==$rs[agencia]) echo selected ?>><?= ($ag[nome_fantasia]) ?> - <?= ($ag[cidade]) ?> <?= ($ag[estado]) ?></option><?
			}?>
		</optgroup>
	</select> 
	
	<br />

	<label for="titulo_servico">Título do Trabalho</label>
	<input class="titulo_servico" type="text" name="titulo_servico" value="<?= $rs[titulo_servico] ?>"/>
	<br />

	<label for="digital_obs">Observações Gerais da Produção / Briefing</label>
	<textarea rows="4" name="digital_obs"><?= ($rs[digital_obs]) ?></textarea>
	<br />

	<div class="imageright"><hr /><input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" /></div>
</fieldset>

<div id="frame_equipamento_<?= $rs[codigo] ?>"></div>

<fieldset class="fieldset_previsoes">
<legend><? if($codigo){ ?><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /><? } ?> Datas</legend>
	<div class="colunas_3">
	<label for="autorizado_data">Data da autorização</label>
	<input type="text" class="text" name="autorizado_data" id="autorizado_data" value="<? if(!$codigo){ ?><?= date('d/m/Y') ?><? }else{ ?><?= mydate($rs[autorizado_data]) ?><? } ?>" onblur="if(this.value=='')this.value='<?= date('d/m/Y') ?>'" />
	</div>

	<div class="colunas_3">	
	<label for="data_prevista">Data prevista para término</label>
	<input type="text" class="text" name="data_prevista" id="data_prevista" value="<? if(!$codigo){ ?><?= date('d/m/Y') ?><? }else{ ?><?= mydate($rs[data_prevista]) ?><? } ?>" onblur="if(this.value=='')this.value='<?= date('d/m/Y') ?>'" />
	</div>

	<div class="colunas_3">	
	<label for="data_entrega">Data de finalização</label>
	<input type="text" class="text" name="data_entrega" id="data_entrega" value="<? if(!$rs[data_entrega]){ echo '00/00/0000'; }else{ echo mydate($rs[data_entrega]); }?>" onBlur="if(this.value=='')this.value='00/00/0000'" onfocus="if(this.value=='00/00/0000')this.value=''" />
	</div>
	<div class="imageright"><hr /><input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" /></div>
</fieldset>

<fieldset class="fieldset_status">
<legend><? if($codigo){ ?><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /><? } ?> Estágio de produção</legend>
	
	<? $qrs = mysql_query("SELECT * FROM producao_status WHERE exibir=1 ORDER BY ordem");
	$stts = 0;
	while($rsc = mysql_fetch_array($qrs)){ ?>
		<div class="status_radio" onclick="document.editar.status[<?= $stts ?>].checked=true;" title="Clique para escolher">
		<?= ($rsc[titulo]) ?>
		<img src="plugins/producao/images/status_<?= $rsc[codigo] ?>.png"><br />
		<input type="radio" name="status" value="<?= $rsc[codigo] ?>" <? if(!$codigo && $stts==1){ echo " checked";} ?><? if($rsc[codigo]==$rs[status]) echo " checked" ?>>
		</div> 
	<? $stts++;
	} ?>
	<div class="imageright"><hr /><input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" /></div>
</fieldset>

<a name="transporte"></a>
<fieldset class="fieldset_transporte">
<legend><? if($codigo){ ?><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /><? } ?> Transporte, Instalação, Retirada e Notificação</legend>
	<label for="cod_transporte">Transportadora - Método de Retirada</label>
	<select name="cod_transporte"> 
		<?
		#antigo
		#$SQLt = mysql_query("SELECT * FROM transporte WHERE ativo=1 ORDER BY servico");
		#novo
		$SQLt = mysql_query("SELECT * FROM cliente WHERE ativo=1 AND tipo_transporte=1 ORDER BY nome_fantasia");
		while($rst = mysql_fetch_array($SQLt)){
		?><option value="<?= $rst[codigo] ?>" <? if($rst[codigo]==$rs[cod_transporte]) echo selected ?>><?= str_replace('TRANSP. ', '',strtoupper(acento($rst[nome_fantasia]))) ?></option><?
		}?>
	</select>
	
	<select name="frete_tipo">
	<option value="" <? if(!$rs[frete_tipo]) echo selected ?>>Nenhum</option>
	<?
	$fretes = controle('frete_tipo');
	$fretes = explode(',',$fretes);
	foreach($fretes as $k=>$v){
	?><option value="<?= $v ?>" <? if($v==$rs[frete_tipo]) echo selected ?>><?= $v ?></option><?
	} ?>
	<br />

	<label for="entrega_obs">Instruções adicionais para entrega ou transporte</label>
	<textarea rows="6" name="entrega_obs" cols=""><?= $rs[entrega_obs] ?></textarea>
<? if($codigo){#so pode apos criar uma os ?>
	<label for="transporte_obs">Conhecimento do Transporte / Retirada</label>
	<textarea rows="2" name="transporte_obs"><?= ($rs[transporte_obs]) ?></textarea>
	
	<div id="email_transporte">
	<label for="email_transporte">Notificação de Conhecimento para o Cliente</label>
	<img src="images/bt_email_send.png" id="bt_transp_os_<?= $rs[codigo] ?>" align="absmiddle" title="Enviar notificação de Conhecimento de Transporte" />
	<textarea rows="2" name="email_transporte"><?= str_replace(" . ","\n",$rs[email_transporte]) ?></textarea>
	</div>
<? } ?>

	<div class="imageright"><hr /><input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" /></div>
</fieldset>


<fieldset class="fieldset_infos">
<legend><? if($codigo){ ?><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /><? } ?> Faturamento</legend>
	<label for="orcamento_obs">Instruções de Faturamento</label>
	<textarea rows="6" name="orcamento_obs"><?= ($rs[orcamento_obs]) ?></textarea>
	<br />
	<div class="imageright"><hr /><input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" /></div>
</fieldset>

	<? if(plugin("contas_receber")){ ?>
	<? if($codigo){#so pode incluir boletos apos criar uma os ?>
<a name="contas_receber_editar_os_<?= $codigo ?>"></a>
<fieldset class="fieldset_faturamento">
<legend><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /> Vencimentos</legend>
	<div id="contas_receber_editar_os_<?= $rs[codigo] ?>">Carregando...</div>
</fieldset>
	<? } ?>
	<? } ?>


<? if($codigo){#so pode apos criar uma os ?>
<div id="editar_os_historico_<?= $codigo ?>"></div>

<fieldset class="fieldset_notificacoes">
<legend><img class="bt_voltar_<?= $target ?>" src="images/bt_voltar.png" title="Retornar para o modo de fila" /> Notificações e Impressão da Ordem de Serviço</legend>	
	<label for="email">Log de Email da Abertura de Ordem de Serviço</label>
	<img src="images/bt_email_send.png" id="bt_send_os_<?= $rs[codigo] ?>" title="Enviar o Email" />
	<textarea rows="2" name="email" cols=""><?= (str_replace(" . ","\n",$rs[email])) ?></textarea>
	
	
	<label for="impressao">Impressão da Ordem de Serviço</label>
	<img src="images/bt_impressoras.png" id="bt_print_os_<?= $rs[codigo] ?>" align="absmiddle" title="Imprimir a Ordem de Serviço" />
	
	<textarea rows="2" name="impressao" cols=""><?= (str_replace(".","\n",$rs[impressao])) ?></textarea>
</fieldset>
<? } ?>
<hr /><br />

<div align="center">
<? if($codigo){ ?>
	<input type="checkbox" name="duplicar" value="1" onChange="if(this.checked){ if(confirm('Deseja mesmo duplicar esta O.S.')){ document.editar.gravar.value='duplicar_os'}else{document.editar.duplicar.checked=false} }else{ document.editar.gravar.value='editar_os' } //alert(document.editar.gravar.value);">Duplicar a Ordem de Serviço atual com todas as características<br />
<? } ?>

	<input type="image" src="images/button_ok.png" name="submit" class="image" title="Enviar os Dados" />
</div>
</form>
