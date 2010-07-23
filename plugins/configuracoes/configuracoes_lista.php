<? 
session_start();
#require_once "../../conn.php";
login();
?>

<? if(!$codigo){
	## RELATORIO INICIAL ##	
	$qr = mysql_query("SELECT Count(ativo) as ativos FROM controle WHERE ativo=1");
	$at = mysql_fetch_object($qr);
	$ativos = $at->ativos;

	$qr = mysql_query("SELECT Count(ativo) as inativos FROM controle WHERE ativo!=1");
	$at = mysql_fetch_object($qr);
	$inativos = $at->inativos;
	?>
	


	<table class="days_top">
	<tr>
	<td class="days_top_1"></td>
	<td class="days_top_2 days_top_click">Configurações</td>
	<td class="days_top_3"></td>
	<td class="days_top_4">&nbsp;</td>
	<td class="days_top_5"></td>
	</tr>
	</table>

	<div id="days_middle" class="days_middle suggestionList">

<? } ?>

<? 
$_SESSION[volta] = "0";
$_SESSION[contagem] = "0";
$busca = strtoupper(acento(urldecode($busca)));

function configuracoes($rel,$codigo,$busca){
?>
	<script language="javascript">
	$('[title]').tooltip({ 
	track: false, 
	delay: 0, 
	showURL: false, 
	showBody: true, 
	fade: 30 
	});
	</script>
	
<?
if(!$codigo){
	if(!isset($filter)) {
		$filter_qr = "WHERE rel='$rel'";
		if($_SESSION[volta]=="0"){
			$filter_qr = "WHERE codigo='$rel'";
			$_SESSION[volta] = "1";
			$rel = NULL;
		}
	}

	$wr = "$filter_qr  ORDER BY ordem, descricao, titulo, ativo DESC";
}else{
	$wr = "WHERE codigo='$codigo'";
}

$SQL = "SELECT * FROM controle $wr";
#echo $SQL;
$QR = mysql_query($SQL);
if(mysql_num_rows($QR)>0){


if(!$codigo) {
echo '<ul style="height:1px; line-height: 1px;">&nbsp</ul>';
echo '<fieldset class="fieldset_recursivo" id="fieldset_recursivo_'. $rel.'"><ul>';
}

while($rs = mysql_fetch_array($QR)){
$conteudo = strtoupper(acento(urldecode($rs[codigo].$rs[rel].$rs[titulo].$rs[valor].$rs[descricao].$rs[ativo])));
#echo $conteudo;
if($busca && strstr($conteudo,$busca) || !$busca) {

	$_SESSION[contagem]++;
	?>
	
	<? if(!$codigo){
	
	if($_SESSION[alter]==0){
		$_SESSION[alter] = 1;
		$bck = " class='alter'";
	}else{
		$_SESSION[alter] = 0;
		$bck = "";
	} }
	
	if(strstr($rs[titulo],"user")) $bck = " class='user'";
	
	if(!$codigo){ ?>
	

	
	
	<li<?= $bck ?> id="configuracoes_<?= $rs[codigo] ?>" onmouseover="controles(<?= $rs[codigo] ?>)" >
	<? } 

	?>

	
	
	<? 
	$SQLrel = "SELECT Count(codigo) as totalrel FROM controle WHERE rel='$rs[codigo]'";
	$QRrel = mysql_query($SQLrel);
	$rsrel = mysql_fetch_array($QRrel);
	$totalrel = $rsrel[totalrel];
	?>
	
	<div id="bt_configuracoes_<?= $rs[codigo] ?>" class="bt_controles">
	
	<? #if(controle("user_codigo")=="1") { ?>
	<img src="images/bt_editar.png" onclick="editar_configuracoes('<?= $rs[codigo] ?>')" alt="editar" title="Editar esta Configuração" /> 
	<? #} ?>
	
	<? if(controle("user_codigo")=="1") { ?>
	<img src="images/bt_mais.png" onclick="adicionar_configuracoes('<?= $rs[codigo] ?>')"  alt="adicionar" title="Adicionar mais uma entrada abaixo para esta Configuração" />
	<? } ?>
	
	<? if(controle("user_codigo")=="1") { ?>
	<img src="images/bt_duplicar.png" onclick="duplicar_configuracoes('<?= $rs[codigo] ?>')"  alt="duplicar" title="Duplicar este conjunto de Configurações para este ou outro usuário" />
	<? } ?>
	
	<? if(controle("user_codigo")=="1") { ?>
	<? if($totalrel==0){ ?>
	<img src="images/bt_excluir.png" onclick="excluir_configuracoes('<?= $rs[codigo] ?>')"  alt="excluir" title="Excluir esta Configuração" />
	<? } ?>
	<? } ?>
	
	</div>

	<div class='colunas_0'>
		<div class="producao_busca_indice">
		<?
		echo "<span id='configuracoes_status_". $rs[codigo] ."'>";
		if($rs[ativo]=="1"){
		echo "<img src='images/bt_check.png' class='configuracoes_status' title='Configuração Ativa' onclick='configuracoes_status(". $rs[codigo] .",0)' >";
		}else{
		echo "<img src='images/bt_atencao.png' class='configuracoes_status' title='Configuração Inativa' onclick='configuracoes_status(". $rs[codigo] .",1)' >";
		} 
		echo "</span>"; ?>
		</div>
		
		<? if($totalrel>0){ ?>
		<div class="producao_busca_indice"><img onclick="tree_fieldset(<?= $rs[codigo] ?>)" src="images/bt_up_down.png" alt="+" title="<?= $totalrel ?> Sub-categorias: Mostrar/Ocultar" /></div>
		<? } ?>
	</div>
	
	<div class='colunas_1'>
		<div class="configuracoes_titulo"><?= $rs[descricao] ?>&nbsp;</div>
		<div class="configuracoes_descricao">
		<?= $rs[titulo] ?> 
		<? if($rs[valor] && !strstr($rs[titulo],"user")){
			echo " |  $rs[valor]<br />";
		} ?>
		</div>
	</div>
				
	<div class="clear_left"></div>
	

<?
} #if in busca
?>	
	
	<? if(!$codigo){ ?>
	</li><hr class="hr_absolute" />
	<? configuracoes($rs[codigo],'',$busca) ?>
	<? } ?>
	
	
<? } # while 
if(!$codigo) {
echo "</ul></fieldset>";
}

} #if rows
} #function configuracoes
?>

<? if(!$codigo) { ?>
<div class="configuracoes_clip">
<div class="configuracoes_container">
<ul></ul>
<? configuracoes($_SESSION["user_codigo"],'',$busca) ?>
</div>
</div>
<? }else{ 
configuracoes('',$codigo,'');
} ?>

<? if(!$codigo){ ?>
	</div>

	<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

	<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

	<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>


	<?
	$tx_filter = "$_SESSION[contagem] Configurações encontradas";
	if($_SESSION[contagem]=='1') $tx_filter = "$_SESSION[contagem] Configuração encontrada";
	if($_SESSION[contagem]=='0') $tx_filter = "<img src=\"images/bt_atencao.png\" align=\"absmiddle\" width=\"24\"> Nenhuma Configuração encontrada";
	if($busca) $tx_filter .= " em \"$busca\"";
	?>

	<script type="text/javascript">
	busca_status('<?= $tx_filter ?>. <?= $ativos ?> ativas e <?= $inativos ?> inativas.');

	function editar_configuracoes(codigo){
		$("#full_frame").load('utf2iso.php?file=plugins/configuracoes/configuracoes_editar.php',{codigo: codigo},function(){
		$("#full_frame").slideDown(function(){
			$("#full_background").fadeIn(500);
		});
		});
	}
	
	function excluir_configuracoes(codigo){
		if(confirm('Deseja mesmo excluir estas Configurações?\nEsta ação é irreversível!')){
		$.post('utf2iso.php?file=plugins/configuracoes/configuracoes_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
		$('#configuracoes_todas').load('utf2iso.php?file=plugins/configuracoes/configuracoes_lista.php');
		busca_status('A Configuração foi excluída.');
		});
		}
	}

	function duplicar_configuracoes(codigo){
		$.post('utf2iso.php?file=plugins/configuracoes/configuracoes_editar.php',{codigo: codigo, gravar: 'duplicar'}, function(data){
		//editar_configuracoes(data);
		$('#configuracoes_todas').load('utf2iso.php?file=plugins/configuracoes/configuracoes_lista.php');
		busca_status('Nova configuração adicionada.');
		});
	}
	
	function adicionar_configuracoes(codigo){
		$.post('utf2iso.php?file=plugins/configuracoes/configuracoes_editar.php',{codigo: codigo, gravar: 'incluir'}, function(data){
		editar_configuracoes(data);
		$('#configuracoes_todas').load('utf2iso.php?file=plugins/configuracoes/configuracoes_lista.php');
		busca_status('Nova configuração adicionada.');
		});
	}
	
	function configuracoes_status(codigo,status){
		$.post('utf2iso.php?file=plugins/configuracoes/configuracoes_editar.php',{codigo: codigo, gravar: 'status', status: status});
		$('#configuracoes_'+ codigo).load('utf2iso.php?file=plugins/configuracoes/configuracoes_lista.php',{codigo: codigo});
	}
	
	var last_codigo = 0;
	function controles(codigo){
		$('#bt_configuracoes_'+last_codigo).hide();
		last_codigo = codigo;
		$('#bt_configuracoes_'+codigo).show();
	}
	
	function tree_fieldset(codigo){
		$('#fieldset_recursivo_'+codigo).slideToggle();
	}
	</script>
<? } ?>
