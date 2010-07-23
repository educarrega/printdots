<?
session_start();
#require_once "../../conn.php";
$QR = mysql_query("SELECT * FROM pedido WHERE codigo=$codigo");
$rs = mysql_fetch_array($QR);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imprimir Pedido <?= $codigo ?></title>
	<style type="text/css">
	@import url("pedido_print.css");
	</style>
	<script language="javascript" src="scripts.js" type="text/javascript"></script>
	<script language="javascript" src="../../jquery.js" type="text/javascript"></script>
</head>

<body>
<div id="imprimir">
<b>Modo Visualização</b><br/>Clique aqui para imprimir esta página sem este aviso.
</div>

<div id="pagina">
<div class="titulo total">
Pedido de Compra: #<?= $codigo ?>
</div>

<div class="cabecalio_direita">
	<div class="titulo">Fornecedor</div>
	<div class="conteudo">
	<?
	$SQLa = mysql_query("SELECT * FROM cliente WHERE codigo='$rs[cliente_codigo]'");
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
	
	<div class="titulo">Materiais e Serviços</div>
	<?
	$SQLs = "SELECT * FROM pedido WHERE codigo_rel='$codigo'";
	#echo $SQL;
	$SQLs = mysql_query($SQLs);
	while($rss = mysql_fetch_array($SQLs)){
	?>

	<div class="pedido_item">

		<div class="pedido_results">
			<span>Quantidade</span><br />
			<?= $rss[material_quantidade] ?>&nbsp;
		</div>

		<div class="pedido_results">
			<span>Valor unitário</span><br/>
			<?= $rss[valor_unitario] ?>&nbsp;
		</div>
	
		<div class="pedido_results">
			<span>Valor total</span><br/>
			<?= $rss[valor_total] ?>&nbsp;
			<? $total_com_acrescimo = ($rss[valor_total]+$total_com_acrescimo) ?>
		</div>
		
		<div class="pedido_results">
			<span>Acréscimo</span><br/>
			<?= $rss[valor_acrescimo] ?>&nbsp;
			<? $total_de_acrescimo = ($rss[valor_acrescimo]+$total_de_acrescimo) ?>
		</div>		
			
		<?
		$SQLp = "SELECT titulo,descricao,codigo_fornecedor,peso,largura,altura,profundidade,base_calculo FROM material WHERE codigo='$rss[material_codigo]'";
		#echo $SQLp;
		$SQLpdt = mysql_query($SQLp);
		$pdt = mysql_fetch_array($SQLpdt);
		?>

		<div class="pedido_results">
			<span>CÃ³digo Material</span><br />
			<?= $rss[material_codigo] ?>&nbsp;
		</div>
				
		<div class="pedido_results_r">
			<span>Base de Cálculo</span><br />
			<?= $pdt[base_calculo] ?>&nbsp;
		</div>
		
		<div class="pedido_results">
			<span>Peso</span><br />
			<?= $rss[peso] ?> Kg
		</div>
				
		<div class="pedido_results_r">
			<span>Cod.Fornecedor</span><br />
			<?= $pdt[material_codigo] ?>&nbsp;
		</div>
		
		<hr class="clear_left" />
		
		<div class="pedido_results_m">
			<span>Material</span><br/>
			<?= $pdt[titulo] ?><br />
			<small><?= $pdt[descricao] ?></small><br/>
		</div>
		
		<div class="clear_right"></div>
		<div class="clear_left"></div>
	</div>
	<? }#while
	$total_com_acrescimo = number_format($total_com_acrescimo,2,'.','');
	?>

	<div class="titulo">Descrição do pedido e outras considerações</div>
	<div class="conteudo">
	<?= nl2br(($rs[descricao])) ?>&nbsp;
	</div>
	
</div>

<div class="cabecalio_esquerda">
	<div class="titulo">Data de Entrada</div>
	<div class="conteudo">
	<?= mydate($rs[data_criacao]) ?>
	</div>
	
	<div class="titulo">Valor sem Frete</div> 
	<div class="conteudo">
	R$ <?= $total_com_acrescimo ?>
	</div>

	<div class="titulo">Frete / Peso <small>(estimado)</small></div> 
	<div class="conteudo">
	R$ <?= $rs[transporte_valor] ?>, <?= @number_format($rs[peso],2,",",".") ?> kg<br />
	<?= $rs[transporte_lancar] ? "À Pagar na chegada" : "Incluso no pedido" ?>
	</div>

	<div class="titulo">Valor Total</div>
	<div class="conteudo total">
	R$ <?= $rs[valor_total] ?>
	</div>
		
	<div class="titulo">Impressão deste Pedido:</div>
	<div class="conteudo" id="impressao">
	<small><?= substr($rs[impressao],0,strpos($rs[impressao],"\n")) ?></small>
	</div>
	
	<div class="titulo">Notificação por Email:</div>
	<div class="conteudo" style="height: 25px; overflow: hidden; font-size: 9px">
	<?= ($rs[email]) ?>&nbsp;
	</div>
</div>

<div class="clear_left"></div>
<div class="clear_right"></div>

<div class="metade">
	<div class="titulo">Serviço de transporte</div>
	<div class="conteudo">
	<? if($rs[transporte_codigo]) {
	$SQLt = mysql_query("SELECT * FROM cliente WHERE codigo=$rs[transporte_codigo]");
	$rst = mysql_fetch_array($SQLt); ?>
	<b><?= nl2br(($rst[nome_fantasia])) ?></b><br />
	Contato: <?= $rst[contato] ?><br />
	Email: <?= $rst[email] ?><br />
	Telefone:<?= $rst[telefone] ?><br />
	Descrição: <?= nl2br($rst[obs]) ?><br />
	<? } ?>
	<br />
	<? if($rs[entrega_obs]){ ?>
		<small>
		<b>Instruções adicionais</b><br />
		<?= (nl2br($rs[entrega_obs])) ?><br />
		</small>
	<? } ?>
	</div>
</div>

<div class="metade">
	<div class="titulo">Controles</div>
	<div class="conteudo">
	<b>Nome do recebedor:</b><br />
	_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _  _ _ _ _ _  <br /><br />
	Data/Hora do recebimento:  &nbsp; _ _ / _ _ / _ _ _ _ &nbsp;&nbsp; _ _ : _ _ <br /><br />
	
	<b>Outras informações relevantes:</b><br />
	<br /><br /><br /><br />
	</div>
</div>
<div class="clear_left"></div>
<div class="clear_right"></div>
<br /><br />
</div>
</body>

<script language="javascript">
$("#imprimir").click(function(){
	$("#imprimir").hide();
	$('#impressao').load('pedido_print_gravar.php',{codigo: '<?= $codigo ?>'},function(){
	//alert(data);
	print();
	});
});
</script>

</html>
