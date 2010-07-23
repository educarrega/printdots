<?
session_start();
#require_once "../../conn.php";
$QR = mysql_query("SELECT * FROM producao WHERE codigo=$codigo_producao");
$rs = mysql_fetch_array($QR);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imprimir O.S. <?= $codigo_producao ?></title>
	<style type="text/css">
	@import url("print_os.css");
	</style>
	<script language="javascript" src="scripts.js" type="text/javascript"></script>
	<script language="javascript" src="../../jquery.js" type="text/javascript"></script>
</head>

<body>
<div id="imprimir">
<b>Modo Visualização</b><br/>Clique aqui para imprimir esta página sem este aviso.
</div>

<div id="pagina">


<div class="cabecalio_esquerda">
	<div class="thumbs">
	<img id="miniatura" src="../../users/<?= controle("settings_folder"); ?>/thumbs/<?= $rs[codigo] ?>.jpg" alt="">
	</div>
	
	<div class="quantidade" id="quantidade"></div>
	
	<div class="titulo">Entrada | Previsto</div>
	<div class="conteudo">
	<?= mydate($rs[autorizado_data]) ?> | <?= mydate($rs[data_prevista]) ?>
	</div>
		
	<div class="titulo">Impressão desta O.S.:</div>
	<div class="conteudo" id="impressao">
	<small><?= substr($rs[impressao],0,strpos($rs[impressao],"\n")) ?></small>
	</div>
	
	<div class="titulo">Notificação abertura:</div>
	<div class="conteudo" style="height: 25px; overflow: hidden; font-size: 9px">
	<?= ($rs[email]) ?>&nbsp;
	</div>
</div>

<div class="cabecalio_direita">
	<div class="titulo">Ordem de Serviço</div>
	<div class="conteudo">
	<h2><?= $rs[codigo] ?> - <?= ($rs[titulo_servico]) ?></h2>
	</div>
	
	<div class="titulo">Arquivos, medidas e instruções</div>
	<div class="conteudo">
	<?= nl2br(($rs[digital_obs])) ?>
	<?
	#para OS antigas
	$SQLpi = mysql_query("SELECT * FROM producao_itens WHERE codigo_producao='$rs[codigo]'");
	if(mysql_num_rows($SQLpi)==0){
	
		$SQLeq = mysql_query("SELECT * FROM equipamento WHERE codigo='$rs[digital_plotter]'");
		$eq = mysql_fetch_array($SQLeq);
		
		$SQLpd = mysql_query("SELECT * FROM produto WHERE codigo='$rs[codigo_produto]'");
		$pd = mysql_fetch_array($SQLpd);
		
		$rs_quantidade = $rs[quantidade];
		$rs_m2t = $rs[m2t];
		$rs_mph = $eq[mph];
		$rs_pmt = @($rs[m2t]*$pd[ppm]);
		$rs_m2h = @($rs[m2t]/$eq[mph]);
		?>
		<hr />
		<span>Produto:</span> 
		<?= $pd[titulo] ?><br />
		<span>Equipamento:</span> 
		<?= $eq[titulo] ?><br />
		<span>DimensÃµes:</span> 
		<?= $rs[m2] ?> mÂ² unitario <br />
		<span>Tempo:</span> 
		unitário: <?= @m2h($rs[m2]/$eq[mph]) ?><br />
		<span>Peso:</span> 
		unitário: <?= @number_format(($pd[ppm]*$rs[m2]),2,",",".") ?> kg
		<?
	
	}else{
	
	#para novas os
	while($pi = mysql_fetch_array($SQLpi)){ 
	
		$SQLeq = mysql_query("SELECT * FROM equipamento WHERE codigo='$pi[codigo_equipamento]'");
		$eq = mysql_fetch_array($SQLeq);
		
		$SQLpd = mysql_query("SELECT * FROM produto WHERE codigo='$pi[codigo_produto]'");
		$pd = mysql_fetch_array($SQLpd);
		
		$rs_quantidade = ($rs_quantidade+$pi[quantidade]);
		$rs_m2t = ($rs_m2t+$pi[m2t]);
		$rs_mph = ($rs_mph+$eq[mph]);
		$rs_pmt = ($rs_pmt+($pi[m2t]*$pd[ppm]));
		$rs_m2h = ($rs_m2h+($pi[m2t]/$eq[mph]));
		?>
		<hr />
		<div class="sub_quantidade"><?= $pi[quantidade] ?></div>
		<span>Item:</span> 
		<b><?= $pi[descricao] ?></b><br />
		<span>Produto:</span> 
		<?= $pd[titulo] ?><br />
		<span>Equipamento:</span> 
		<?= $eq[titulo] ?><br />
		<span>DimensÃµes:</span> 
		<?= $pi[largura] ?>L X <?= $pi[altura] ?>A | <?= $pi[m2u] ?> mÂ² unitario | <?= $pi[m2t] ?> mÂ² total <br />
		<span>Tempo:</span> 
		unitário: <?= @m2h($pi[m2u]/$eq[mph]) ?> | total: <?= @m2h($pi[m2t]/$eq[mph]) ?> <br />
		<span>Peso:</span> 
		unitário: <?= @number_format(($pd[ppm]*$pi[m2u]),2,",",".") ?> kg | total: <?= @number_format(($pd[ppm]*$pi[m2t]),2,",",".") ?> kg<br />
		<div class="clear_left"></div>	
	
	<? } 
	} ?>
	</div>
</div>
<script language="javascript">
$('#quantidade').html('<?= $rs_quantidade?>');
$("#imprimir").click(function(){
	$("#imprimir").hide();
	$('#impressao').load('print_os_gravar.php',{codigo: '<?= $rs[codigo] ?>'},function(){
	//alert(data);
	print();
	});
});
</script>

<div class="cabecalio_direita">
	<div class="umterco">
		<div class="titulo">Metragem Total</div> 
		<div class="conteudo">
		<?= $rs_m2t ?> m&#178;
		</div>
	</div>
	<div class="umterco">
		<div class="titulo">Tempo Estimado</div> 
		<div class="conteudo">
		<?= m2h($rs_m2h) ?>
		</div>
	</div>
	<div class="umterco">
		<div class="titulo">Peso Total</div>
		<div class="conteudo">
		<?= @number_format($rs_pmt,2,",",".") ?> kg
		</div>
	</div>

</div>

<div class="clear_left"></div>
<div class="clear_right"></div>

<div class="metade">
	<div class="titulo">Cliente</div>
	<div class="conteudo">
	<?
	$SQLa = mysql_query("SELECT * FROM cliente WHERE codigo='$rs[agencia]'");
	$ag = mysql_fetch_array($SQLa);
	?><b><?= ($ag[nome_fantasia]) ?></b><br />
	<small>
	<?= ($ag[razao_social]) ?><br />
	<?= ($ag[cnpj]) ?><br />
	<?= ($ag[endereco]) ?><br />
	<?= ($ag[cidade]) ?> <?= ($ag[estado]) ?> | 
	CEP: <?= ($ag[cep]) ?><br />
	Fone: <?= ($ag[telefone]) ?> | <?= ($ag[contato]) ?>
	<?# if($ag[obs]){ ?><!--
		<small>
		<b>Informações relevantes cadastradas para este cliente</b><br />
		<?= ($ag[obs]) ?><br />
		</small>-->
	<? #} ?>
	</small>
	</div>
</div>
<div class="metade">
	<div class="titulo">Informações de entrega</div>
	<div class="conteudo">
	<? if($rs[cod_transporte]) {
	$SQLt = mysql_query("SELECT nome_fantasia,razao_social,telefone,obs FROM cliente WHERE codigo=$rs[cod_transporte]");
	$rst = mysql_fetch_array($SQLt); ?>
	<b><?= strtoupper($rst[nome_fantasia]) ?></b><br />
	<? if($rst[razao_social]){ ?><b><?= strtoupper($rst[razao_social]) ?></b><br /><? } ?>
	(Tel:<?= $rst[telefone] ?>)
	<? } ?>
	<br />
	<? if($rst[obs]){ ?>
		<small>
		<?= (nl2br($rst[obs])) ?><br />
		</small>
	<? } ?>
	<? if($rs[entrega_obs]){ ?>
		<b>Informações adicionais da entrega</b><br />
		<?= (nl2br($rs[entrega_obs])) ?><br />
	<? } ?>
	</div>
</div>

<div class="clear_left"></div>

<div class="titulo">Controles</div>
<div class="metade">
	<div class="conteudo">
	<b>Operadores e Impressão:</b><br />
	_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _  <br /><br />
	Início:  &nbsp; _ _ / _ _ / _ _ _ _ &nbsp;&nbsp; _ _ : _ _ <br /><br />
	Término: &nbsp; _ _ / _ _ / _ _ _ _ &nbsp;&nbsp; _ _ : _ _ <br /><br />

	<b>Pacote:</b><br />
	_ _ _ _ _ _ _ _ _ _ _ &nbsp;&nbsp; _ _ / _ _ / _ _ _ _ &nbsp;&nbsp; _ _ : _ _ <br />
	</div>
</div>
<div class="metade">
	<div class="conteudo">
	<b>Acabamento:</b><br />
	_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _   <br /><br />
	Início:  &nbsp; _ _ / _ _ / _ _ _ _ &nbsp;&nbsp; _ _ : _ _ <br /><br />
	Término: &nbsp; _ _ / _ _ / _ _ _ _ &nbsp;&nbsp; _ _ : _ _ <br /><br />
	
	<b>Expedição/Retirada/Instalação:</b><br />
	_ _ _ _ _ _ _ _ _ _ _ &nbsp;&nbsp; _ _ / _ _ / _ _ _ _ &nbsp;&nbsp; _ _ : _ _ <br />
	</div>
</div>

<div class="clear_left"></div>

<div class="conteudo" style="height: 350px">
<b>Notas de produção:</b><br />
</div>


</div>
</body>
</html>
