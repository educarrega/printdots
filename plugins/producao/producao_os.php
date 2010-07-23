<? 
@session_start();
#require_once "../../conn.php";
include_once "producao_editar.php";

if($codigo){
	$SQL = "SELECT * FROM producao WHERE codigo=$codigo";
	#echo $SQL;
	#die;
	$QR = mysql_query($SQL);
	$rs = mysql_fetch_array($QR);
}
if(!is_array($st)){
	#status que serao utilizados nos registros
	$qrs = mysql_query("SELECT * FROM producao_status WHERE exibir=1 ORDER BY ordem");
	while($rsc = mysql_fetch_array($qrs)){
		$stt[$rsc[codigo]] = ($rsc[titulo]);
		$std[$rsc[codigo]] = ($rsc[descricao]);
		//echo $rsc[titulo];
	}
}

?>

<!-- scripts-->
<script language="JavaScript" type="text/javascript">

//status
$("#producao_os_status_<?= $target ?>").click(function(){
	$("#producao_os_status_select_<?= $target ?>").fadeIn(500);
	$("#full_background").fadeIn(function(){
		$("#full_background").click(function(){
		$("#full_background").hide();
		$("#producao_os_status_select_<?= $target ?>").hide();	
		})
		
	});
});

<? foreach($stt as $k=>$v){ ?>
	$("#images_status_<?= $target ?>_<?= $k ?>").click(function(){
<? if($k==5){ ?>
	
	$("#producao_os_status_select_<?= $target ?>").hide();
	
	alert_status('Para informar CONCLUÍDO, você deverá preencher a informação de [Conhecimento de Transporte \/ Retirada].\n\nCaso não preencha, o status da ordem de serviço será alterado para ENTREGA.');
	
	$("#full_frame").fadeIn(function(){
	$("#full_frame").load('campo_detalhes.php',{tabela: 'producao', campo: 'transporte_obs', codigo: '<?= $rs[codigo] ?>', titulo: 'Conhecimento de Transporte / Retirada', editar: '1'});
	});
	
	$("#full_background").click(function(){
	$("#producao_os_<?= $target ?>").load("utf2iso.php?file=plugins/producao/producao_os.php",{codigo:'<?= $rs[codigo] ?>', info_aberto: 'historico', status:'<?= $k ?>', gravar:'status_os', target: '<?= $target ?>'});
	});

<? }elseif($k==1){ ?>

	$("#producao_os_status_select_<?= $target ?>").hide();
	
	$("#full_frame").fadeIn(function(){
	$("#full_frame").load('utf2iso.php?file=plugins/ordem_de_servico/editar_os_historico.php',{codigo: '<?= $rs[codigo] ?>', view: '1', status: '<?= $k ?>'});
	});
	
<? }else{ ?>

	$("#producao_os_<?= $target ?>").load("utf2iso.php?file=plugins/producao/producao_os.php",{codigo:'<?= $rs[codigo] ?>', status:'<?= $k ?>', gravar:'status_os', target: '<?= $target ?>'},function(){$("#full_background").hide();});
	
<? } ?>
	});
<? } ?>
	$("#images_status_<?= $target ?>_0").click(function(){	
	$("#full_background").hide();
	$("#producao_os_status_select_<?= $target ?>").hide();
	});

//image
$("#producao_os_thumbs_<?= $target ?>").click(function(){
	$("#full_frame").html("<img src='users/<?= controle("settings_folder") ?>/thumbs/<?= $rs[codigo] ?>.jpg' width='100%'>");
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
	});

});

//cliente
$("#tab_cliente_<?= $target ?>").click(function(){
	$("#full_frame").load('utf2iso.php?file=plugins/cliente/cliente_os.php',{agencia: '<?= $rs[agencia] ?>'});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
	});
});
$("#bt_info_cliente_busca_<?= $target ?>").click(function(){
	$("#content_right").load('utf2iso.php?file=plugins/producao/producao.php',{agencia: '<?= $rs[agencia] ?>'});
	window.location.hash = "#";
});


//arquivos
$("#tab_arquivos_<?= $target ?>").click(function(){
	$("#full_frame").load('campo_detalhes.php',{tabela: 'producao', campo: 'digital_obs', codigo: '<?= $rs[codigo] ?>', titulo: 'Arquivos'<? if(plugin("ordem_de_servico")){ ?>, editar: '1'<? } ?>});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $target ?>").load('utf2iso.php?file=plugins/producao/producao_os.php',{codigo: '<?= $rs[codigo] ?>', info_aberto: 'arquivos', target: '<?= $target ?>'});
		});

	});
});

//impressora
$("#tab_impressora_<?= $target ?>").click(function(){
	$("#full_frame").load('utf2iso.php?file=plugins/equipamento/equipamento_os_editar.php',{producao: '<?= $rs[codigo] ?>'});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$('#days_middle_<?= $rs[data_prevista] ?>').load("utf2iso.php?file=plugins/producao/producao_fila.php",{data_prevista: '<?= $rs[data_prevista] ?>', info_aberto: 'impressora', target: '<?= $target ?>'});
		});
	});
});

$("#bt_info_impressora_busca_<?= $target ?>").click(function(){
	$("#content_right").load('utf2iso.php?file=plugins/producao/producao.php',{equipamento: '<?= $rs[digital_plotter] ?>', filtro:'Equipamento', order:'digital_plotter', tabela: 'equipamento', campo: 'titulo', data_prevista: '<?= $data_prevista ?>', info_aberto: 'impressora'});
});

//transporte
$("#tab_transporte_<?= $target ?>").click(function(){
	$("#full_frame").load('campo_detalhes.php',{tabela: 'producao', campo: 'entrega_obs', codigo: '<?= $rs[codigo] ?>', titulo: 'Transporte', editar: '1'});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $target ?>").load('utf2iso.php?file=plugins/producao/producao_os.php',{codigo: '<?= $rs[codigo] ?>', info_aberto: 'transporte', target: '<?= $target ?>'});
		});
	});
});

//historico
$("#tab_historico_<?= $target ?>").click(function(){
	$("#full_frame").slideDown(function(){
		$("#full_frame").load('utf2iso.php?file=plugins/ordem_de_servico/editar_os_historico.php',{codigo: '<?= $rs[codigo] ?>', view: '1'});
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $target ?>").load('utf2iso.php?file=plugins/producao/producao_os.php',{codigo: '<?= $rs[codigo] ?>', info_aberto: 'historico', target: '<?= $target ?>'});
		});
	});
});

<? if(plugin('contas_receber')){ ?>
//faturamento
$("#bt_mais_detalhes_vencimento_<?= $target ?>").click(function(){
	$("#detalhes_faturamento_<?= $target ?>").hide();
	$("#detalhes_vencimento_<?= $target ?>").fadeIn(300);
});
$("#bt_menos_detalhes_vencimento_<?= $target ?>").click(function(){
	$("#detalhes_faturamento_<?= $target ?>").fadeIn(300);
	$("#detalhes_vencimento_<?= $target ?>").hide();
});
$("#tab_faturamento_<?= $target ?>").click(function(){
	$("#full_frame").load('utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php', {codigo_producao: '<?= $rs[codigo] ?>', codigo_cliente: '<?= $rs[agencia] ?>', info_aberto: 'faturamento', view: '1'});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $target ?>").load('utf2iso.php?file=plugins/producao/producao_os.php',{codigo: '<?= $rs[codigo] ?>', info_aberto: 'faturamento', target: '<?= $target ?>'});
		});
	});
});
<? } ?>

//notificacoes
$("#bt_send_os_<?= $target ?>").click(function(){
	send_os('<?= $rs[codigo] ?>');
	var p = setTimeout(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $target ?>").load('utf2iso.php?file=plugins/producao/producao_os.php',{codigo: '<?= $rs[codigo] ?>', target: '<?= $target ?>'});
		});
	}, 5000);
});
$("#bt_print_os_<?= $target ?>").click(function(){
	print_os('<?= $rs[codigo] ?>');
	var p = setTimeout(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $target ?>").load('utf2iso.php?file=plugins/producao/producao_os.php',{codigo: '<?= $rs[codigo] ?>', target: '<?= $target ?>'});
		});
	}, 5000);
});
$("#bt_faturamento_os_<?= $target ?>").click(function(){
	$("#full_frame").load('utf2iso.php?file=plugins/contas_receber/contas_receber_editar_os.php', {codigo_producao: '<?= $rs[codigo] ?>', codigo_cliente: '<?= $rs[agencia] ?>', view: '1'});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $target ?>").load('utf2iso.php?file=plugins/producao/producao_os.php',{codigo: '<?= $rs[codigo] ?>', info_aberto: 'faturamento', target: '<?= $target ?>'});
		});
	});
});
$("#bt_transp_os_<?= $target ?>").click(function(){
	send_transp('<?= $rs[codigo] ?>');
	var p = setTimeout(function(){
		$("#full_background").fadeIn(500);
		$("#full_background").click(function(){
		$("#producao_os_<?= $target ?>").load('utf2iso.php?file=plugins/producao/producao_os.php',{codigo: '<?= $rs[codigo] ?>', info_aberto: 'transporte', target: '<?= $target ?>'});
		});
	}, 5000);
});

//prioridade
$("#bt_acima_<?= $target ?>").click(function(){
	$('#days_middle_<?= $data_prevista ?>').load("utf2iso.php?file=plugins/producao/producao_fila.php",{codigo:'<?= $rs[codigo] ?>', digital_prioridade:'<?= $rs[digital_prioridade] ?>', gravar:'prioridade_os_mais', data_prevista: '<?= $data_prevista ?>'});
});
$("#bt_abaixo_<?= $target ?>").click(function(){
	$('#days_middle_<?= $data_prevista ?>').load("utf2iso.php?file=plugins/producao/producao_fila.php",{codigo:'<?= $rs[codigo] ?>', digital_prioridade:'<?= $rs[digital_prioridade] ?>', gravar:'prioridade_os_menos', data_prevista: '<?= $data_prevista ?>'});
});

//scroll em todos os campos infos
$(".producao_os_editor").mouseover(function(){
	$(this).addClass('scroll')
});
$(".producao_os_editor").mouseout(function(){
	$(this).removeClass('scroll')
});

//ocultar as tabs iniciais
$(document).ready(function(){

	$("#tab_cliente_<?= $target ?>").mouseover(function(){
		$(oculta_tabs('<?= $target ?>'));
		$("#producao_os_infos_cliente_<?= $target ?>").fadeIn(300);
	});
	$("#tab_arquivos_<?= $target ?>").mouseover(function(){
		$(oculta_tabs('<?= $target ?>'));
		$("#producao_os_infos_arquivos_<?= $target ?>").fadeIn(300);
	});
	$("#tab_impressora_<?= $target ?>").mouseover(function(){
		$(oculta_tabs('<?= $target ?>'));
		$("#producao_os_infos_impressora_<?= $target ?>").fadeIn(300);
	});
	$("#tab_transporte_<?= $target ?>").mouseover(function(){
		$(oculta_tabs('<?= $target ?>'));
		$("#producao_os_infos_transporte_<?= $target ?>").fadeIn(300);
	});
	$("#tab_historico_<?= $target ?>").mouseover(function(){
		$(oculta_tabs('<?= $target ?>'));
		$("#producao_os_infos_historico_<?= $target ?>").fadeIn(300);
	});
	<? if(plugin('contas_receber')){ ?>
	$("#tab_faturamento_<?= $target ?>").mouseover(function(){
		$(oculta_tabs('<?= $target ?>'));
		$("#producao_os_infos_faturamento_<?= $target ?>").fadeIn(300);
	});
	<? } ?>
	<?
	#quando retorna de alguma atualizacao, mostrar qual info?
	if(!$info_aberto) $info_aberto = 'impressora';
	?>
	$(oculta_tabs('<?= $target ?>'));
	$("#producao_os_infos_<?= $info_aberto ?>_<?= $target ?>").show();

	$('[title]').tooltip({ 
	track: false, 
	delay: 0, 
	showURL: false, 
	showBody: true,
	positionLeft: true,
	fade: 5 
	});
	
	<? if($incluir_os){ ?>
	$('#producao_os_<?= $target ?>').load("utf2iso.php?file=plugins/ordem_de_servico/editar_os2.php",{codigo:'<?= $rs[codigo] ?>', target: '<?= $target ?>'});
	$('#producao_os_<?= $target ?>').css({'overflow':'none', 'height': 'auto'});
	$('#producao_os_<?= $target ?>').slideDown();
	<? } ?>
	
});
</script>


<!-- codstatus-->
<div class="producao_os_codstatus">
	O.S.:<br />
	<? if(plugin('ordem_de_servico')){ ?><a href="exclusivo/os_consulta.php?id=<?= $rs[id] ?>&v=1" target="_blank" title="Visualizar resumo da Ordem de Serviço"><? } ?><span class="producao_os_codstatus_os"><?= $rs[codigo] ?></span></a>
	<div style="height: 9px;"><hr /></div>
	
	&nbsp;<br />
	<!--
	Produto: <br />
	<?
	$qrp = mysql_query("SELECT codigo,titulo FROM produto WHERE codigo='". $rs[codigo_produto] ."'");
	$rsp = mysql_fetch_object($qrp);
	?>-->
	<div class="producao_os_codstatus_produto">&nbsp;&nbsp;<br /></div>
	
	
	
	<div style="height: 5px;"><hr /></div>
	<div class="producao_os_status">
	<img id="producao_os_status_<?= $target ?>" src="plugins/producao/images/status_<?= $rs[status] ?>.png" alt="status" title="<?= $std[$rs[status]] ?> - clique para alterar" /><br />
	<?= $stt[$rs[status]] ?>
	</div>
</div>

<div class="producao_os_status_select" id="producao_os_status_select_<?= $target ?>">
<div class="producao_os_status_select_inside">
	<? foreach($stt as $k=>$v){ ?>
		<? 
		$liberado = 1;
		if($k=='1'){
			if(!plugin('ordem_de_servico')) $liberado = 0;
		}
		
		$rss = mysql_fetch_array(sel("producao","status","WHERE codigo=$codigo"));
		if($rss[status]==11 || $rss[status]==5){
			$liberado = 0;
			
			if($rss[status]==11){
				if($k==11 || $k==5) $liberado = 1;
			}
			
			if($rss[status]==5){
				if($k==5) $liberado = 1;
			}
		}
		
		if($liberado){ ?>
		<img id="images_status_<?= $target ?>_<?= $k ?>" src="plugins/producao/images/status_<?= $k ?>.png" alt="status" title="<?= $v ?> - <?= $std[$k] ?>" /> 
		<? } ?>
	<? } ?>
		<!--<img src="plugins/producao/images/status_transp.png" />-->
		<img id="images_status_<?= $target ?>_0" src="plugins/producao/images/status_fechar.png" alt="status" title="Fechar este diálogo" /> 
</div>
</div>

<!-- miniatura -->
<? $settings_folder = controle("settings_folder"); ?>
<div <? if(existe("users/$settings_folder/thumbs/$rs[codigo].jpg",0)){ ?>id="producao_os_thumbs_<?= $target ?>" title="Visualizar imagem completa"<? } ?> class="producao_os_thumbs"><img class="producao_os_thumbs_img" src="<?= existe("users/$settings_folder/thumbs/$rs[codigo].jpg",1) ?>?t=<?= md5($uid) ?>" /></div>


<!-- ostitulo -->
<div class="producao_os_titulo">
<?= ($rs[titulo_servico]) ?>
</div>

<!-- unidades / equipamentos -->
<?
$rs_quantidade = 0;
$rsc_mph = 0;
$rs_m2 = 0;
$rs_m2t = 0;

$QRpi = mysql_query("SELECT * FROM producao_itens WHERE codigo_producao='". $rs[codigo] ."'");
if(mysql_num_rows($QRpi)>0){
	$table_produtos = "produtos_itens";
	while($rsi = mysql_fetch_array($QRpi)){
		#os mesmos dados do else com multiplos produtos
		
		$calcular_tempos = 1;
		if(strstr($codigos_producao,"|$rs[codigo]|")) $calcular_tempos = 0;
		
		if($calcular_tempos){
		$qr = mysql_query("SELECT * FROM equipamento WHERE codigo='". $rsi[codigo_equipamento] ."'");
		$rsc = mysql_fetch_object($qr);
		if($rsc->mph>0){
			$rsc_mph = $rsc->mph;
			$min[$rsi[codigo_equipamento]] = ($min[$rsi[codigo_equipamento]] + ($rsi[m2t]/$rsc->mph)); #somar os minutos por dia
			$m2[$rsi[codigo_equipamento]] = ($m2[$rsi[codigo_equipamento]] + $rsi[m2t]); #somar todos os m2 do dia
			$ps[$rsi[codigo_equipamento]] = ($ps[$rsi[codigo_equipamento]] + m2p($rsi[m2t],$rsi[codigo_produto])); #somar todos os kg do dia
			$rs_m2 = ($rs_m2+$rsi[m2u]);
			$rs_m2t = ($rs_m2t+$rsi[m2t]);
			$rs_m2h = ($rs_m2h+($rsi[m2t]/$rsc->mph));
			$rs_psu = ($rs_psu+(m2p($rsi[m2u],$rsi[codigo_produto])));
			$rs_pst = ($rs_pst+(m2p($rsi[m2t],$rsi[codigo_produto])));
		}
		
		} #recalculo
		$codigos_producao = "|$rs[codigo]|";
		$rs_quantidade = ($rs_quantidade+$rsi[quantidade]);
	}
}else{
	$table_produtos = "producao";
	$qr = mysql_query("SELECT * FROM equipamento WHERE codigo='". $rs[digital_plotter] ."'");
	$rsc = mysql_fetch_object($qr);
	if($rsc->mph>0){
		$rsc_mph = $rsc->mph;
		$min[$rs[digital_plotter]] = ($min[$rs[digital_plotter]] + ($rs[m2t]/$rsc->mph)); #somar os minutos por dia
		$m2[$rs[digital_plotter]] = ($m2[$rs[digital_plotter]] + $rs[m2t]); #somar todos os m2 do dia
		$ps[$rs[digital_plotter]] = ($ps[$rs[digital_plotter]] + m2p($rs[m2t],$rs[codigo_produto])); #somar todos os kg do dia
		$rs_m2 = $rs[m2];
		$rs_m2t = $rs[m2t];
		$rs_m2h = ($rs_m2h+($rs[m2t]/$rsc->mph));
		$rs_psu = m2p($rs[m2],$rs[codigo_produto]);
		$rs_pst = m2p($rs[m2t],$rs[codigo_produto]);
	}
	$rs_quantidade = $rs[quantidade];
}

?>
<div class="producao_os_unidades">
<h1><?= $rs_quantidade ?></h1>

<? if($rsc_mph){ ?>
	
	<div title="MÂ² unitário">
	<? 
	$m2u = explode(".",$rs_m2);
	echo $m2u[0];
	if($m2u[1]!="00") echo ".".$m2u[1];
	?>mÂ².u
	</div>
	
	<div title="MÂ² total">
	<? 
	$m2e = explode(".",$rs_m2t);
	echo $m2e[0];
	if($m2e[1]!="00") echo ".".$m2e[1];
	?> mÂ².t
	</div>
	
	<div title="Tempo estimado">
	<? if($rs_m2h){ echo m2h($rs_m2h); } ?> 
	</div>
	
	<div title="Peso unitário">
	<? if($rs_quantidade>0) echo number_format($rs_psu,2,",",".")." kg.u" ?>
	</div>
	
	<div title="Peso total">
	<? if($rs_quantidade>1) echo number_format($rs_pst,2,",",".")." kg.t"; ?>
	</div>
	
<? } ?>

<div title="Data de autorização">
<?= mydate($rs[autorizado_data]) ?>
</div>
</div>

<!-- infos/tabs -->
<div class="producao_os_infotabs">
	<img class="producao_os_tabs_img" id="tab_cliente_<?= $target ?>" src="images/bt_clientes.png" alt="c" title="Cliente: Solicitante do trabalho"/> 
	<img class="producao_os_tabs_img" id="tab_arquivos_<?= $target ?>" src="images/bt_arquivos.png" alt="a" title="Instruções: Localicação em disco e informações gerais"/> 
	<img class="producao_os_tabs_img" id="tab_impressora_<?= $target ?>" src="images/bt_impressoras.png" alt="p" title="Itens: Quantidades, Equipamentos e Produtos"/> 
	<img class="producao_os_tabs_img" id="tab_transporte_<?= $target ?>" src="images/bt_transporte.png" alt="t" title="Transporte: Entrega, retirada ou instalação"/> 
	<img class="producao_os_tabs_img" id="tab_historico_<?= $target ?>" src="images/bt_infos.png" alt="i" title="HistÃ³rico: Informações geradas durante o trabalho"/> 
	<? if(plugin('contas_receber')){ ?>
	<img class="producao_os_tabs_img" id="tab_faturamento_<?= $target ?>" src="images/bt_contas_receber.png" alt="f" title="Faturamento: Dados para cobrança"/> 
	<? } ?>
</div>

	
<!-- cliente -->
<div class="producao_os_infos" id="producao_os_infos_cliente_<?= $target ?>">
	
	<img class="bt_acoes_1" id="bt_info_cliente_busca_<?= $target ?>" src="images/bt_lupa.png" title="Visualizar todos os trabalhos deste cliente" alt="[outros trabalhos]" />
 
	
	<div class="producao_os_infos_titulo">Cliente</div>
	<div class="producao_os_editor">
	<?
	#agencia
	$SQLag = mysql_query("SELECT * FROM cliente WHERE codigo='$rs[agencia]'");
	$ag = mysql_fetch_object($SQLag);
	?>
	
	<b><?= (substr("$ag->nome_fantasia",0,30)) ?></b><br />
	<?= ($ag->cidade) ." ". ($ag->estado) ?><br />
	<?= $ag->telefone ?><br />
	
	<? if($ag->obs){ ?>
	<hr />
	<img class="bt_info_1" src="images/bt_infos.png" alt="Infos" /> 
	<?= (nl2br($ag->obs)) ?>
	<? } ?>
	</div>
</div>
<!-- /cliente -->

<!-- arquivo/briefing -->
<div class="producao_os_infos" id="producao_os_infos_arquivos_<?= $target ?>">
	<div class="producao_os_infos_titulo" style="width: 120px;">Instruções de Arquivo</div>
	<div class="producao_os_editor"><?= (nl2br($rs[digital_obs])) ?></div>
	
</div>
<!-- /arquivo -->

<!-- impressora -->
<div class="producao_os_infos" id="producao_os_infos_impressora_<?= $target ?>">
	
	<div class="producao_os_infos_titulo" style="width: 150px;">Itens, Equipamentos e Produtos</div>
	<div id="produto_itens_<?= $target ?>" class="producao_os_editor">
	<?
	if($table_produtos == "produtos_itens"){
		#listar os itens
		$QRpi = mysql_query("SELECT * FROM producao_itens WHERE codigo_producao='". $rs[codigo] ."'");
		while($rsi = mysql_fetch_array($QRpi)){ ?>
		<? 
		$qrp = mysql_query("SELECT titulo FROM equipamento WHERE codigo='". $rsi[codigo_equipamento] ."'");
		$rsp = mysql_fetch_array($qrp);
		$equipamento_produto = substr($rsp[titulo],0,40)." | ";
		$qrpd = mysql_query("SELECT codigo,titulo FROM produto WHERE codigo='". $rsi[codigo_produto] ."'");
		$rspd = mysql_fetch_array($qrpd);
		$equipamento_produto .= substr($rspd[titulo],0,40);
		?>
		<b style="color: #FFF"><?= $rsi[quantidade] ?> - <?= $rsi[descricao] ?></b><br/>
		<?= $rsi[largura] ?>L X <?= $rsi[altura] ?>A | <?= $rsi[m2u] ?> mÂ²u | <?= $rsi[m2t] ?> mÂ²t<br/>
		<?= $equipamento_produto ?>
		<hr />
		<? }
	}else{ #produtos
		$qrp = mysql_query("SELECT * FROM equipamento WHERE codigo='". $rs[digital_plotter] ."'");
		$rsp = mysql_fetch_array($qrp);
		?>
		<?= substr($rsp[titulo],0,40) ?><br />
		<?
		$qrpd = mysql_query("SELECT * FROM produto WHERE codigo='". $rs[codigo_produto] ."'");
		$rspd = mysql_fetch_array($qrpd);
		?>
		<?= $rspd[codigo] ?> - <?= substr($rspd[titulo],0,40) ?><br />
	<? } ?>
	</div>
	
</div>
<!-- /impressora -->

<!-- transporte -->
<div class="producao_os_infos" id="producao_os_infos_transporte_<?= $target ?>">
	<div class="producao_os_infos_titulo">Transporte</div>
	<div class="producao_os_editor">
	<? if($rs[cod_transporte]){
	#$SQLt = mysql_query("SELECT servico,prazo_entrega FROM transporte WHERE codigo='$rs[cod_transporte]'");
	$SQLt = mysql_query("SELECT codigo,nome_fantasia,razao_social,obs FROM cliente WHERE codigo='$rs[cod_transporte]'");
	$rst = mysql_fetch_array($SQLt); ?>
	<b title="<?= nl2br($rst[obs]) ?>"><?= (nl2br($rst[nome_fantasia])) ?></b><br />
	<small>
	<? if($rst[razao_social]) echo "$rst[razao_social]<br>"; ?>
	</small>
	<? } ?>
	<?= (nl2br($rs[entrega_obs])) ?></div>
</div>
<!-- /transporte -->

<!-- historico -->
<div class="producao_os_infos" id="producao_os_infos_historico_<?= $target ?>">
	<div class="producao_os_infos_titulo">HistÃ³rico</div>
	<div class="mais_detalhes" id="mais_detalhes_historico_<?= $target ?>">
	<div class="producao_os_editor"><?= (nl2br(substr($rs[historico],0,400))) ?></i></b></div>
	</div>
</div>
<!-- /historico -->


<!-- faturamento -->
<? if(plugin("contas_receber")){ ?>
<div class="producao_os_infos" id="producao_os_infos_faturamento_<?= $target ?>">
	
<div class="producao_os_infos_titulo">Vencimentos</div>
	<div class="mais_detalhes" id="detalhes_faturamento_<?= $target ?>">
	<div class="producao_os_editor">
	<? if($rs[orcamento_obs]) echo nl2br($rs[orcamento_obs])."<hr />"; ?>
	<?
	$SQLv = "SELECT * FROM contas_receber WHERE codigo_producao='$rs[codigo]' ORDER BY data_vencimento DESC";
	#echo $SQLv;
	$QRv = mysql_query($SQLv);
	while($rsv = mysql_fetch_array($QRv)){ ?>
		<? if($rsv[data_vencimento]) echo mydate($rsv[data_vencimento]) ?> | 
		R$ <?= str_replace(".",",",$rsv[valor]) ?> | 
		Pg: <? if($rsv[data_pagamento]) echo mydate($rsv[data_pagamento]) ?>
		<hr />
	<? } ?>
	</div>
</div>

</div>
<? } ?>
<!-- /faturamento -->

<!-- notificacoes -->
<div class="producao_os_notificacoes">

	<b>Notificações</b><br />
	<? 
	$sts1 = "bt_check.png";
	$sts2 = "bt_atencao.png";

	$impressao = $rs[impressao] ? $sts1 : $sts2;
	$email = $rs[email] ? $sts1 : $sts2;
	$email_transporte = $rs[email_transporte] ? $sts1 : $sts2;
	?>
	<div id="bt_print_os_<?= $target?>" class="producao_os_notificacoes_bt" title="Imprimir a Ordem de Serviço"><img src="images/<?= $impressao ?>" alt="impressão" /> Impressão O.S.</div>
	
	<? if(plugin('ordem_de_servico')){ ?>
	<div id="bt_send_os_<?= $target ?>" class="producao_os_notificacoes_bt" title="Notificar o cliente da abertura da Ordem de Serviço"><img src="images/<?= $email ?>" alt="abertura"/> Not. Abertura</div>
	<? }
	
	if(plugin('contas_receber')){
		$faturado = $rs[email_vencimento] ? $sts1 : $sts2; ?>
		<div id="bt_faturamento_os_<?= $target ?>" class="producao_os_notificacoes_bt" title="Posição do faturamento"><img src="images/<?= $faturado ?>" alt="faturamento" /> Faturado</div>
	<? }
	
	if($rs[status]==5){
	if(plugin('ordem_de_servico')){ ?>
	<div id="bt_transp_os_<?= $target ?>" title="Notificação de entrega" class="producao_os_notificacoes_bt"><img src="images/<?= $email_transporte ?>" alt="entrega" /> Not. Entrega</div>
	<? }
	} ?>

</div>

<!-- controles -->
<div class="producao_os_controles">
	<b>Controles</b><br />
	
	<img id="bt_acima_<?= $target ?>" src="images/bt_acima.png" alt="" title="Mais urgente" /> 

	<img id="bt_abaixo_<?= $target ?>" src="images/bt_abaixo.png" alt="" title="Menos urgente" />

	<? if(plugin("ordem_de_servico")){ ?>
	<img id="bt_editar_<?= $target ?>" onclick="editar_producao('<?= $target ?>','<?= $rs[codigo] ?>','')" src="images/bt_editar.png" title="Editar Ordem de Serviço" />
	<? } ?>
</div>
