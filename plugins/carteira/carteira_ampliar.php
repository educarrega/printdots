<? @session_start();
#require_once "../../conn.php";
if($codigo){
	$SQLag = "SELECT * FROM carteira WHERE codigo='$codigo'";
	$qr = mysql_query($SQLag);
	$rs = mysql_fetch_array($qr);
	?>

	<fieldset class="fieldset_infos">
	<legend>Informações Cadastrais</legend>
		<h1><?= $rs[titulo] ?></h1>
		<b><?= $rs[descricao] ?></b>
		<br /><hr /><br />
		
		<? for($t=1;$t<mysql_num_fields($qr);$t++){
		if(mysql_field_name($qr,$t)!="titulo" && mysql_field_name($qr,$t)!="descricao"){ ?>
		<div class="colunas_3">
		<label><?= str_replace("_"," ",mysql_field_name($qr,$t)) ?></label>
		
		
		<? if(mysql_field_len($qr,$t)=='1'){ ?>
		<? $check = $rs[mysql_field_name($qr,$t)]; ?>
		<div id="check_<?= mysql_field_name($qr,$t) ?>_<?= $rs[codigo]?>" class="checkbox" title="Ativo/Inativo"></div>
		<? $check = $check ? 0 : 1; ?>
		<script language="javascript">check(<?= $check ?>,<?= $rs[codigo]?>,'<?= mysql_field_name($qr,$t) ?>',0)</script>
		
		<? }else{ ?>
		<?= nl2br($rs[mysql_field_name($qr,$t)]) ?><br />
		<? } ?>
		
		
		</div>
		<? } 
		} ?>
		<div class="clear_left"></div>
		<br /><br />
	</fieldset>

	<fieldset class="fieldset_relatorios">
	<legend>Estatísticas (em breve)</legend>
	
		<? if(plugin('bancos')){ ?>
		<div class="colunas_3">
		<label>Ordens de serviço</label>
		Sendo produzidas: 0<br />
		Produzidas neste mês: 0<br />
		Produzidas neste ano: 0<br />
		Produzidas no total: 0<br />
		Último pedido: 00/00/0000
		</div>
		<? } ?>
		
		<div class="clear_left"></div>
		<br /><br />
	</fieldset>
<? } ?>
