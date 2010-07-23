<? @session_start();
#require_once "../../conn.php";

	$wr = "WHERE codigo=$codigo_pedido";
	$qr = mysql_query("SELECT data_lancamento,transporte_valor FROM pedido $wr");
	$rs = mysql_fetch_array($qr);
	if(substr(mydate($rs[data_lancamento]),0,2)!="00"){
		$efetivado = 1;
	}
	
	$SQLs = "SELECT * FROM pedido WHERE codigo_rel='$codigo_pedido'";
	#echo $SQL;
	$SQLs = mysql_query($SQLs);
	if(mysql_num_rows($SQLs)==0){ ?>
	<div class="pedido_item">
	Nenhum item informado neste pedido.
	</div>
	<? }else{ ?>
	
	<script>
	$('[title]').tooltip({ 
	track: false, 
	delay: 0, 
	showURL: false, 
	showBody: true, 
	fade: 30 
	});
	</script>
	
	<?
	while($rss = mysql_fetch_array($SQLs)){
	?>
	
	<div class="pedido_item" id="pedido_item_<?= $rss[codigo] ?>">
	<script language="javascript">
	function editar_item_<?= $rss[codigo] ?>(){
		//alert('<?= $rss[codigo] ?>');
		borda_item_<?= $codigo_pedido ?>('<?= $rss[codigo] ?>');
		editar_item_<?= $codigo_pedido ?>('<?= $rss[codigo] ?>','<?= $rss[material_codigo] ?>','<?= $rss[material_quantidade] ?>','<?= @($rss[peso]/$rss[material_quantidade]) ?>','<?= $rss[valor_unitario] ?>','<?= $rss[valor_acrescimo] ?>');
	};
	</script>
	

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
		$peso_total = ($rss[peso]+$peso_total);
		?>
		
		<div class="pedido_results">
			<span>Base de Cálculo</span><br />
			<?= $pdt[base_calculo] ?>&nbsp;
		</div>
		
		<div class="pedido_results">
			<span>Peso total</span><br />
			<?= $rss[peso] ?>&nbsp;
		</div>
		
		<div class="pedido_results">
			<span>CÃ³digo Material</span><br />
			<?= $rss[material_codigo] ?>&nbsp;
		</div>
				
		<div class="pedido_results_r">
			<span>Cod. Fornecedor</span><br />
			<?= $pdt[material_codigo] ?>&nbsp;
		</div>
		<hr />
		
		<div class="pedido_results_m">
			<span>Material</span><br/>
			<?= $pdt[titulo] ?><br />
			<small><?= $pdt[descricao] ?></small><br/>
		</div>
		
		<? if(!$efetivado){ ?>
		<div class="bt_controles_item">		
			<img src="images/bt_editar.png" alt="editar" title="Editar o item do pedido" align="absmiddle" onclick="editar_item_<?= $rss[codigo] ?>();" /> &nbsp; 
			<img src="images/bt_excluir.png" alt="excluir" title="Excluir o item do pedido" align="absmiddle" onclick="excluir_item_<?= $codigo_pedido ?>('<?= $rss[codigo] ?>')" />
		</div>
		<? } ?>
		
		<div class="clear_right"></div>
		<div class="clear_left"></div>
	</div>
	<? }#while
	$total_com_acrescimo = number_format($total_com_acrescimo,2,'.','');
	$total_sem_acrescimo = number_format(($total_com_acrescimo-$total_de_acrescimo),2,'.','');
	upd("pedido","peso='$peso_total', valor_total='". @($total_com_acrescimo+$rs[transporte_valor]) ."',valor_acrescimo='$total_de_acrescimo', material_codigo='". $pdt[material_codigo] ."'","WHERE codigo='$codigo_pedido'");
	?>
	
	<div class="pedido_coluna_3">
	<label for="parcial_sa">Total parcial sem acréscimos</label>
	<input type="text" class="text" name="total_sa" id="total_sa_<?= $codigo_pedido ?>" value="<?= $total_sem_acrescimo ?>" READONLY />
	</div>

	<div class="pedido_coluna_3">
	<label for="parcial_ca">Total parcial com acréscimos</label>
	<input type="text" class="text" name="total_ca" id="total_ca_<?= $codigo_pedido ?>" value="<?= $total_com_acrescimo ?>" READONLY />
	</div>

	<div class="pedido_coluna_3">
	<label for="peso_estimado">Peso total estimado (Kg)</label>
	<input type="text" class="text" name="peso" id="peso_<?= $codigo_pedido ?>" value="<?= $peso_total ?>" READONLY />
	</div>
	<div class="clear_left"></div>
	
	<script language="javascript">
	var velho = 0;
	function borda_item_<?= $codigo_pedido ?>(codigo){
		$('#pedido_item_'+velho).css({'border-color': '#999999'});
		velho = codigo;
		$('#pedido_item_'+codigo).css({'border-color': '#FFCC00'});
	}
	$(document).ready(function(){
	$('#pedido_valor_acrescimo_<?= $codigo_pedido ?>').val('<?= $total_de_acrescimo ?>');
	pedido_totalizacao_<?= $codigo_pedido ?>();
	});
	</script>
	<? } ?>	
