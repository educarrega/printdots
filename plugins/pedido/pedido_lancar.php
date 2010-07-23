<?
@session_start();
#require_once "../../conn.php";

$SQL = "SELECT pedido.*,cliente.nome_fantasia, cliente.razao_social,cliente.cidade, cliente.estado, cliente.telefone FROM pedido,cliente WHERE pedido.codigo='$codigo' AND cliente.codigo=cliente_codigo";
$qr = mysql_query($SQL);
$rs = mysql_fetch_array($qr);

?>

<script language="javascript">

function parcelas(alvo,codigo_cliente,codigo_parcela){
	$('#'+alvo).load('utf2iso.php?file=plugins/contas_pagar/contas_pagar_lancar_pedido.php',{codigo_pedido: '<?= $codigo ?>', alvo:alvo, codigo_cliente: codigo_cliente, codigo_parcela: codigo_parcela});
	window.location.hash = "#"+alvo;
}
<? if($codigo_parcela){ ?>
//alert(<?= $codigo_parcela ?>);
parcelas('lancamento_fornecedor',codigo_cliente,'<?= $codigo_parcela ?>');
<? } ?>

$('#bt_efetivar').click(function(){
	$.post('utf2iso.php?file=plugins/pedido/pedido_editar.php',{codigo:'<?= $codigo ?>', gravar:'efetivar'},function(){
	alert('Pedido efetivado!');
	$('#full_frame').load('utf2iso.php?file=plugins/pedido/pedido_lancar.php',{codigo:'<?= $codigo ?>'});
	});
});

$('.recarregar').click(function(){
	$('#full_frame').load('utf2iso.php?file=plugins/pedido/pedido_lancar.php',{codigo:'<?= $codigo ?>'});
});

</script>

<br />
<? if($rs[ativo] && $rs[data_lancamento]){ ?>
	<fieldset class="fieldset_carteira" style="background: green">
	<legend>Pedido Efetivado</legend>
		<img class="imagem" style="float:right;" src="images/button_ok.png" alt="grava" />
		Data da Efetivação: <?= mydate($rs[data_lancamento]) ?><br />
	</fieldset>
<? }else{ ?>
	<fieldset class="fieldset_atencao">
	<legend>Importante</legend>
		Um Pedido sÃ³ deve ser efetivado em Contas Ã  Pagar com todas as referências de custos e frete em mãos.<br />
		Utilize os campos abaixo para informar as parcelas e valores do lançamento.
		<br /><br />
	</fieldset>
<? } ?>

<br />
<fieldset class="fieldset_infos">
<legend class="recarregar" title="Clique para recarregar" style="cursor:pointer">Conferência do lançamento do Pedido #<?= $rs[codigo] ?></legend>
	<br />
	<fieldset style="width: 98%;">
	<legend>Fornecedor</legend>
		
		<div class="colunas_2">
		<?= $rs[cliente_codigo] ?> . <?= $rs[nome_fantasia] ?><br />
		<?= $rs[razao_social] ?><br />
		<?= $rs[cidade] ?> <?= $rs[estado] ?> <?= $rs[cep] ?><br />
		<?= $rs[telefone] ?> <?= $rs[cliente_contato] ?><br />
		</div>
		
		<?
		$valor_total = $rs[valor_total];
		if($rs[transporte_lancar]) $valor_total = ($rs[valor_total]-$rs[transporte_valor]);
		?>
		<div class="colunas_2">
		<label>Totalizações no Pedido</label>
		Total: <b>R$ <?= number_format($valor_total,2,'.',''); ?></b><br />
		<small>(Acréscimos: R$ <?= $rs[valor_acrescimo] ?>)</small>
		</div>
		
		<div class="clear_left"></div>
		<br />

	<div id="lancamento_fornecedor"></div>
	<script>parcelas('lancamento_fornecedor','<?= $rs[cliente_codigo] ?>','')</script>
	<br />
	</fieldset>
	
	<?
	if($rs[transporte_lancar]){
	$SQLt = "SELECT * FROM cliente WHERE codigo='$rs[transporte_codigo]'";
	$qt = mysql_query($SQLt);
	$rt = mysql_fetch_array($qt);
	?>
	<br />
	<fieldset style="width: 98%;">
	<legend>Transporte</legend>
		<div class="colunas_2">
		<?= $rt[codigo] ?> . <?= $rt[nome_fantasia] ?><br />
		<?= $rt[razao_social] ?><br />
		<?= $rt[cidade] ?> <?= $rt[estado] ?> <?= $rt[cep] ?><br />
		<?= $rt[telefone] ?> <?= $rt[contato] ?><br />
		</div>
		
		<div class="colunas_2">
		<label>Totalizações no Pedido</label>
		<b>R$ <?= number_format($rs[transporte_valor],2,'.',''); ?></b><br />
		<?= $rs[peso] ?> Kg<br />
		</div>
		
		<div class="clear_left"></div>
		<br />
	
	
	<div id="lancamento_transporte"></div>
	<script>parcelas('lancamento_transporte','<?= $rt[codigo] ?>','')</script>
	<br />
	</fieldset>
	<br />
	<? } ?>
	
	<?
	$efetivar = 0;
	$SQLc = "SELECT codigo_pedido FROM contas_pagar WHERE codigo_pedido='$rs[codigo]' AND codigo_cliente='$rs[cliente_codigo]'";
	$qc = mysql_query($SQLc);
	$rc = mysql_fetch_array($qc);
	if(!$rs[ativo] && $rc[codigo_pedido]) $efetivar = 1;
	
	if($rs[transporte_lancar]){
	$SQLe = "SELECT codigo_pedido FROM contas_pagar WHERE codigo_pedido='$rs[codigo]' AND codigo_cliente='$rt[codigo]'";
	//echo $SQLc;
	$qe = mysql_query($SQLe);
	$re = mysql_fetch_array($qe);
	if(!$re[codigo_pedido]) $efetivar = 0;
	}
	
	if($efetivar){ ?>
	<fieldset class="fieldset_atencao">
	<legend>Efetivação</legend>
		
		<b>Importante!</b><br />
		O Pedido apÃ³s efetivado, torna-se permanentemente bloqueado para exclusão e alteração de valores, ficando  disponivel apenas para a edição da data do pagamento e o valor de acréscimo em caso de juros, garantindo assim sua integridade refencial no sistema, e a precisão dos dados obtidos nos relatÃ³rios.
		<img class="imagem" id="bt_efetivar" src="images/button_ok.png" alt="grava" title="Gravar Dados" style="float:right; cursor:pointer; width: 36px" />
		<br /><br /><br />
	</fieldset>
	<br />
	<? } ?>

</fieldset>
