<? @session_start();
#require_once "../../conn.php";
include_once "contas_receber_editar.php";

//se receber algum vencimento para editar, carrega os dados nos campos do formulario
if($codigo){
	$SQL = "SELECT * FROM contas_receber WHERE codigo='$codigo'";
	$SQL = mysql_query($SQL);
	$rs = mysql_fetch_array($SQL);
}

//fix para mudancas de titular do trabalho
upd("contas_receber","codigo_cliente='$codigo_cliente'","WHERE codigo_producao='$codigo_producao'");

//formulario de inclusao
?>

<style type="text/css">
@import url("plugins/contas_receber/contas_receber_relatorio.css");
<? #if(!$view){ ?>
@import url("jquery.click-calendario-1.0.css");
<? #} ?>
</style>

<script language="javascript">

$('#contas_receber_form_<?= $codigo_producao ?>').bind('submit',function(){
	var formContent = $('#contas_receber_form_<?= $codigo_producao ?>').serialize();
	$('.contas_receber_editar_os_<?= $codigo_producao ?>').load("utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php", formContent);
	return false;
});

$('#bt_novo_vencimento').click( function() {
	$('[name=gravar]').val('incluir');
	var formContent = $('#contas_receber_form_<?= $codigo_producao ?>').serialize();
	$('.contas_receber_editar_os_<?= $codigo_producao ?>').load("utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php", formContent);
});

//titulos
$('[title]').tooltip({ 
	track: true, 
	delay: 2, 
	showURL: false, 
	showBody: true, 
	fade: 300 
});

<? #if(!$view){ ?>
//funcoes standalone
$('#data_vencimento').focus(function(){
	$(this).calendario({target:'#data_vencimento'	});
});
$('#data_pagamento').click(function(){	
	$(this).calendario({target:'#data_pagamento'});
});
$('#bv_data_pagamento').click(function(){
	$(this).calendario({target:'#bv_data_pagamento'});
});
<? #} ?>

$('#orcamento_obs').keyup(function(){
	$('#status_gravacao').html('<img src="images/loader.gif" align="absmiddle"> Gravando texto...');
	//clearTimeout(g);
	var g = setTimeout(function(){
		var content = $('#orcamento_obs').val();
		$.post("utf2iso.php?file=plugins/producao/producao_editar.php",{gravar: "faturamento_os", codigo:"<?= $codigo_producao ?>", orcamento_obs: content, retorno: "orcamento_obs"},function(data){
		$("#orcamento_obs").text(data);
		$('#status_gravacao').html('Você está no modo edição, basta digitar seu texto');
		});
	}, 2000);
});

</script>

<? if($view){
//se foi pressionado gravar
if($orcamento_obs){
	$table = "producao";
	$cols = "orcamento_obs='$orcamento_obs'";
	$condition = " WHERE codigo=$codigo_producao";
	upd($table,$cols,$condition);
}
//leitura
$QRp = mysql_query("SELECT titulo_servico,orcamento_obs FROM producao WHERE codigo=$codigo_producao");
$rsp = mysql_fetch_array($QRp);
} ?>

<div class="contas_receber_editar_os_<?= $codigo_producao ?>">

<? if($view){ ?>
<fieldset class="fieldset_infos">
<legend>Faturamento: OS <?= $codigo_producao ?> <?= $rsp[titulo_servico] ?></legend>
<label>Instruções de Faturamento</label>
<textarea rows="6" id="orcamento_obs" title="Seu texto é salvo automaticamente ao digitar"><?= ($rsp[orcamento_obs]) ?></textarea>
<div id="status_gravacao">Você está no modo edição, basta digitar seu texto</div>
<br /><br />
</fieldset>

<? if(plugin("contas_receber")){ ?>
<a name="contas_receber_editar_os_<?= $codigo_producao ?>"></a>
<fieldset class="fieldset_faturamento">
<legend>Vencimentos</legend>
<? } ?>
<? } ?>

<? if(plugin("contas_receber")){ ?>
<label>Incluir e editar os vencimentos: (<?= $codigo_cliente ?>) <?= campo("cliente","nome_fantasia","WHERE codigo='$codigo_cliente'") ?></label>

	<form action="" method="POST" id="contas_receber_form_<?= $codigo_producao ?>" name="contas_receber_form">
		<input type="hidden" name="codigo_producao" value="<?= $codigo_producao ?>" />
		<input type="hidden" name="codigo_cliente" value="<?= $codigo_cliente ?>" />
		<input type="hidden" name="view" value="<?= $view ?>" />
		<? if($codigo){ ?>
		<input type="hidden" name="codigo" value="<?= $codigo ?>" />
		<input type="hidden" name="gravar" value="alterar" />
		<? }else{ ?>
		<input type="hidden" name="gravar" value="incluir" />
		<? } ?>
		
		<div class="box_venc">
			Vencimento
			<input type="text" name="data_vencimento" id="data_vencimento" value="<?
			if($rs[data_vencimento]){
				echo mydate($rs[data_vencimento]);
			}else{
				echo '00/00/0000';
			}
			?>" onfocus="if(this.value=='00/00/0000')this.value=''" onblur="if(this.value=='')this.value='00/00/0000'" />
		</div>
	
		<div class="box_venc">
			Valor
			<input type="text" name="valor" value="<?= str_replace('.',',',$rs[valor]) ?>" />
		</div>
	
		<div class="box_venc">
			Recebido
			<input type="text" name="data_pagamento" id="data_pagamento" value="<?
			if($rs[data_pagamento]){
				echo mydate($rs[data_pagamento]);
			}else{
				echo '00/00/0000';
			}
			?>" onfocus="if(this.value=='00/00/0000')this.value=''" onblur="if(this.value=='')this.value='00/00/0000'" />
		</div>
	
		<div class="box_venc" style="width:195px">
			<? if($codigo){ ?>
			<? if(trim($rs[documento])){ ?>
			<img src="images/bt_ampliar.png" class="documento" alt="abrir" title="Visualizar o Documento" onclick="documento('users/<?= controle("settings_folder") ?>/receber/<?= trim($rs[documento]) ?>')" />
			<? }else{ ?>
			<img src="images/bt_acima.png" class="documento" alt="enviar" title="Enviar um Documento" onclick="upload_documento('users/<?= controle("settings_folder") ?>/receber','','','','','#documento');" />
			<!--upload_documento(diretorio,codigo,nome,extensao,preview,link,campo_retorno)-->
			<? } ?>
			<? }else{ ?>
			<img src="images/bt_acima.png" class="documento" alt="enviar" title="Enviar um Documento" onclick="upload_documento('users/<?= controle("settings_folder") ?>/receber','','','','','#documento');" />
			<? } ?>
			Documento
			<input type="text" name="documento" value="<?= trim($rs[documento]) ?>" id="documento" />
		</div>
		
		<div class="box_venc" style="width:85px">
			Carteira
			<select name="codigo_carteira" id="codigo_carteira" style="margin:2px; padding:0; width: 100%;">
			<? $qrc = mysql_query("SELECT codigo,titulo FROM carteira WHERE ativo='1' ORDER BY titulo");
			while($rc = mysql_fetch_array($qrc)){ ?>
			<option value="<?= $rc[codigo] ?>" <? if($rc[codigo]==$rs[carteira]) echo selected ?> ><?= $rc[codigo] ?> - <?= $rc[titulo] ?></option>
			<? } ?>
			</select>
		</div>
		
		
		<div class="clear_left"></div>
		<div class="box_venc">
			BV Beneficiado
			<select name="bv_fornecedor" id="bv_fornecedor" style="margin:2px; padding:0; width: 100%;">
			<option value="" selected >Nenhum</option>
			<? $qf = mysql_query("SELECT codigo,nome_fantasia FROM cliente WHERE tipo_fornecedor='1' AND ativo='1' ORDER BY nome_fantasia");
			while($rf = mysql_fetch_array($qf)){ ?>
			<option value="<?= $rf[codigo] ?>" <? if($rf[codigo]==$rs[bv_fornecedor]) echo selected ?> ><?= $rf[nome_fantasia] ?></option>
			<? } ?>
			</select>
		</div>
		
		<div class="box_venc">
			Valor BV
			<input type="text" name="bv_valor" value="<?= str_replace('.',',',$rs[bv_valor]) ?>" />
		</div>
		
		<div class="box_venc">
			Pago em
			<input type="text" name="bv_data_pagamento" id="bv_data_pagamento" value="<?
			if($rs[bv_data_pagamento]){
				echo mydate($rs[bv_data_pagamento]);
			}else{
				echo '00/00/0000';
			} ?>" onfocus="if(this.value=='00/00/0000')this.value=''" onblur="if(this.value=='')this.value='00/00/0000'" />
		</div>
		
		<div class="box_venc" style="width:195px">
			<? if($codigo){ ?>
			<? if(trim($rs[bv_documento])){ ?>
			<img src="images/bt_ampliar.png" class="documento" alt="abrir" title="Visualizar o Documento" onclick="documento('users/<?= controle("settings_folder") ?>/pagar/<?= trim($rs[bv_documento]) ?>')" />
			<? }else{ ?>
			<img src="images/bt_acima.png" class="documento" alt="enviar" title="Enviar um Documento" onclick="upload_documento('users/<?= controle("settings_folder") ?>/pagar','','','','','#bv_documento');" />
			<!--upload_documento(diretorio,codigo,nome,extensao,preview,link,campo_retorno)-->
			<? } ?>
			<? }else{ ?>
			<img src="images/bt_acima.png" class="documento" alt="enviar" title="Enviar um Documento" onclick="upload_documento('users/<?= controle("settings_folder") ?>/pagar','','','','','#bv_documento');" />
			<? } ?>
			Comprovante BV
			<input type="text" name="bv_documento" value="<?= trim($rs[bv_documento]) ?>" id="bv_documento" />
		</div>
		
		<div class="clear_right"></div>
		<div class="clear_left"></div>
		
		<div class="box_venc_duplo">
			Observações
			<textarea name="obs" ><?= ($rs[obs]) ?></textarea>
		</div>
		

		<div class="box_venc_duplo" style="text-align: right">
			Parcelas
			<input type="text" name="parcelas" style="width: 20px;" value="1" /><!--
			<select name="parcelas">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			</select>-->
			
			
			Período
			<input type="text" name="periodo" style="width: 20px;" value="0" /><!--
			<select name="periodo">
			<option value="0">Nenhum</option>
			<option value="7">7 dias</option>
			<option value="14">14 dias</option>
			<option value="21">21 dias</option>
			<option value="28">28 dias</option>
			<option value="30">30 dias</option>
			<option value="45">45 dias</option>
			</select>-->
			

		
			<div style="text-align: right; margin: 10px 0 0 0;">
			<? if(!$codigo){ ?>
			<input type="image" src="images/bt_mais.png" title="Adiciona ou duplica o vencimento em branco ou preenchido, baseado no nÃºmero de parcelas e períodos informados [Enter]" class="bt_novo_vencimento"/> 
			<? }else{ ?>
			<input type="image" src="images/bt_check.png" title="Atualiza somente o vencimento atual, ignorando Parcelas e Períodos [Enter]" align="top" /> 
			<img src="images/bt_mais.png" title="Duplica o vencimento em branco ou preenchido, baseado no nÃºmero de parcelas e períodos, sendo a primeira parcela na data informada ou hoje caso não preenchida" id="bt_novo_vencimento" />	
			<? } ?>
			</div>
		</div>		
	</form>
<div class="clear_left"></div>

<? 
// fim do formulario

//vencimentos
if($codigo_producao){ ?>

	<?
	$wr = "codigo_producao='$codigo_producao'";
	$SQL = "SELECT * FROM contas_receber WHERE $wr ORDER BY data_vencimento DESC";
	#echo $SQL;
	$SQL = mysql_query($SQL);
	while($rs = mysql_fetch_array($SQL)){ 
	if(!$vencimentos){ ?><label>Parcelas e Vencimentos</label><? }
	$cor = "transparent";
	$border = "";
	$vencimentos = 1;
	if($rs[data_vencimento] && (!$rs[data_pagamento] || strstr($rs[data_pagamento],'0000'))) if(strtotime($rs[data_vencimento])<strtotime(date("Y-m-d"))) $cor="#AA0000";
	if($rs[data_pagamento] && !strstr($rs[data_pagamento],'0000')) $cor = "#004455";
	if($rs[codigo]==$codigo) $border='border: 2px solid #FFCC00;';
	?>

	<div class="vencimento_dia" style="background-color:<?= $cor ?>; <?= $border ?>">
	
		<div class="bt_controles_item">
			<img id="bt_editar_vencimento_<?= $rs[codigo] ?>" src="images/bt_editar.png" alt="editar" title="Editar o vencimento" align="absmiddle" style="width:20px" /> &nbsp; 
			<img id="bt_delete_vencimento_<?= $rs[codigo] ?>" src="images/bt_excluir.png" alt="excluir" title="Excluir o vencimento" align="absmiddle" style="width:20px; height:19px;" />
		</div>
	
		<div class="venc_results">
			<span>Vencimento</span><br />
			<?
			if($rs[data_vencimento]){
				echo mydate($rs[data_vencimento]);
			}
			?>&nbsp;
		</div>
	
		<div class="venc_results">	
			<span>Valor</span><br />
			<?= str_replace(".",",",$rs[valor]) ?>&nbsp;
		</div>
	
		<div class="venc_results">
			<span>Recebido</span><br />
			<?
			if($rs[data_pagamento]){
				echo mydate($rs[data_pagamento]);
			}
			?>&nbsp;
		</div>
	
		<div class="venc_results" style="width: 200px">
			<? #if($rs[documento]){ ?>
			<img src="images/bt_ampliar.png" class="documento" style="margin-top: 10px" alt="" onclick="documento('users/<?= controle("settings_folder") ?>/receber/<?= trim($rs[documento]) ?>')"  title="Abrir o documento informado" />
			<? #} ?>
			<span>Documento</span><br />
			<small><?= $rs[documento] ?>&nbsp;</small>
		</div>
		
		<div class="venc_results_r" style="width: 90px; overflow: hidden;">
			<span>Carteira</span><br />
			<? $qrc = mysql_query("SELECT titulo FROM carteira WHERE codigo='$rs[codigo_carteira]'");
			$rc = mysql_fetch_array($qrc); ?>
			<?= $rs[codigo_carteira] ?> - <?= $rc[titulo] ?>&nbsp;
		</div>
		
		
		<? if($rs[bv_valor]){ ?>
		<div class="clear_left"></div>
				
		<div class="venc_results">
			<span>Bv pago dia</span><br />
			<?
			if($rs[bv_data_pagamento]){
				echo mydate($rs[bv_data_pagamento]);
			}
			?>&nbsp;
		</div>
		
		<div class="venc_results">
			<span>BV valor</span><br />
			<?= str_replace(".",",",$rs[bv_valor]) ?>&nbsp;
		</div>
	
		<div class="venc_results" style="width: 200px;">
			<? #if($rs[bv_documento]){ ?>
			<img src="images/bt_ampliar.png" class="documento" style="margin-top: 10px" alt="" onclick="documento('users/<?= controle("settings_folder") ?>/pagar/<?= trim($rs[bv_documento]) ?>')"  title="Abrir o documento informado" />
			<? #} ?>
			<span>BV Documento</span><br />
			<small><?= $rs[bv_documento] ?>&nbsp;</small>
		</div>
		
		<div class="venc_results_r" style="width: 164px;">
			<span>BV Beneficiado</span><br />	
			<small>
			<? $qf = mysql_query("SELECT codigo,nome_fantasia FROM cliente WHERE codigo='$rs[bv_fornecedor]'");
			$rf = mysql_fetch_array($qf); ?>
			<?= $rf[codigo] ?> - <?= $rf[nome_fantasia] ?>
			</small>
		</div>
		<? } ?>
		
		<div class="clear_left"></div>
		<hr />
		<div class="venc_results_f">
		<span>Observações</span><br />
		<?= nl2br($rs[obs]) ?>&nbsp;
		</div>
		
		<div class="clear_right"></div>
		<div class="clear_left"></div>
	</div>
	
	<script language="javascript">
	$(document).ready(function(){
		$("#bt_delete_vencimento_<?= $rs[codigo] ?>").click(function() {
			if(confirm('Deseja mesmo excluir este vencimento?')){
			$(".contas_receber_editar_os_<?= $codigo_producao ?>").load("utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php",{codigo: '<?= $rs[codigo] ?>', codigo_producao: '<?= $rs[codigo_producao] ?>', codigo_cliente: '<?= $rs[codigo_cliente] ?>', view: '<?= $view ?>', gravar: 'excluir'});
			}
		});
	  			
		$("#bt_editar_vencimento_<?= $rs[codigo] ?>").click(function() {
			$(".contas_receber_editar_os_<?= $codigo_producao ?>").load("utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php",{codigo: '<?= $rs[codigo] ?>', codigo_producao: '<?= $rs[codigo_producao] ?>', codigo_cliente: '<?= $rs[codigo_cliente] ?>', view: '<?= $view ?>'});
			window.location.hash = '#contas_receber_editar_os_<?= $codigo_producao ?>';
		});
		
		<? if($codigo){ ?>
		window.location.hash = '#contas_receber_editar_os_<?= $codigo_producao ?>';
		<? } ?>
		
		$("#bt_venc_os_<?= $codigo_producao ?>").click( function() {
			send_venc(<?= $rs[codigo_producao] ?>);
			var g = setTimeout(function(){
			<? if(!$view){ ?>
				$("#full_background").fadeIn(500);
				$("#full_background").click(function(){
				$(".contas_receber_editar_os_<?= $codigo_producao ?>").load("utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php",{codigo_producao: '<?= $rs[codigo_producao] ?>', codigo_cliente: '<?= $rs[codigo_cliente] ?>', view: '<?= $view ?>'});
				});
			<? }else{ ?>
				$("#full_frame").hide();
			<? } ?>
			}, 3000);
		});	
	});
	</script>
			

	<? } #while
	
	if($vencimentos){ ?>
		<label>Notificação</label>
		<div class="box_venc_full">
			<img src="images/bt_email_send.png" id="bt_venc_os_<?= $codigo_producao ?>" alt="enviar" title="Notificar os vencimentos via e-mail com a inclusão dos Boletos" />
			<?			
			$SQL = "SELECT email_vencimento FROM producao WHERE codigo='$codigo_producao'";
			$SQL = mysql_query($SQL);
			$rs = mysql_fetch_array($SQL); 
			?>
			Notificações de todas as parcelas para o cliente
			<div class="div_textarea_scroll"><?= nl2br(str_replace(" . ","\n",$rs[email_vencimento])) ?></div>
		</div>
	
		<div class="clear_left"></div>
		<div class="clear_right"></div>
		<br /><br /><br />
	<? } #rs
}#producao 
}#plugin?>


<? if($view){ ?>
<? if(plugin("contas_receber")){ ?>
</fieldset>
<? } ?>
</div>
<? } ?>
