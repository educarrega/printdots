<?php
session_start();
if($intranet){
	require_once "../../conn.php";
	
	if($destinatario){
		#dados faturamento
		$codigo_empresa = controle('codigo_empresa');
		$SQLf = "SELECT * FROM cliente WHERE codigo=$codigo_empresa";
		$QRf = mysql_query($SQLf);
		$ft = mysql_fetch_array($QRf);
		$faturamento = "
		Razão Social: $ft[razao_social]
		Nome Fantasia: $ft[nome_fantasia]
		CNPJ: $ft[cnpj]
		Insc. Estadual: $ft[inscrest]
		Cidade/UF: $ft[cidade] - $ft[estado]
		Endereço: $ft[endereco]
		CEP: $ft[cep]
		Telefone: $ft[telefone]
		Contato: $ft[contato]
		Email: $ft[email]
		";
		
		$empresa = $ft[nome_fantasia];
		$endereco = "$ft[endereco] - $ft[cidade] $ft[estado] - $ft[cep] - $ft[telefone]";
		$site = "$ft[website]";
		if($site && !strstr($site,'http://')) $site = "http://$site";
		$from = controle('email_from');
		$fromname = controle('email_from_name');
		$bcc = controle('email_bcc');
		$assunto = str_replace("\n","",$titulo);
		$assunto = str_replace("\t","",$assunto);
		$lines = file('email_os_modelo.php');
		$l_count = count($lines);
		for($x = 0; $x< $l_count; $x++){
			$mensagem.= $lines[$x];
		}
		
		#equipamento produto quantidade
		$SQLe = "SELECT quantidade FROM producao_itens WHERE codigo_producao=$codigo_producao";
		#echo $SQLe;
		$SQLe = mysql_query($SQLe);
		while($eq = mysql_fetch_array($SQLe)){
			$quantidade = $eq[quantidade]+$quantidade;
		}
		
		#vencimento
		$vlt = '';
		$SQLv = "SELECT * FROM contas_receber WHERE codigo_producao=$codigo_producao ORDER BY data_venc ASC";
		$SQLv = mysql_query($SQLv);
		while($vc = mysql_fetch_array($SQLv)){
			$vlt++;
			$dvc = explode("-",$vc[data_venc]);
			$vencimento .= "$dvc[2]/$dvc[1]/$dvc[0] &nbsp; R$ ";
			$vencimento .= number_format($vc[valor],2,",",".");
			$vencimento .= " &nbsp; <a href='". controle("printdots_ip") ."/users/". controle("settings_folder") ."/receber/$vc[documento].pdf' target='_blank'>$vc[documento]</a><br>";
			$bv = ($vc[bv_valor]+$bv);
			if($vlt<mysql_num_rows($SQLv)) $vencimento .= "";
		}
		if($bv) $vencimento .= "BV: R$ ".number_format($bv,2,",",".")."";
		
		#dados
		$SQL = "SELECT * FROM producao WHERE codigo=$codigo_producao";
		$SQL = mysql_query($SQL);
		$em = mysql_fetch_object($SQL);

		#sistema de transporte
		$SQLt = mysql_query("SELECT nome_fantasia FROM cliente WHERE codigo=". $em->cod_transporte);
		#echo $em->cod_transporte;
		$rst = mysql_fetch_object($SQLt);
		
		$email_dados = $em->email;
		$mensagem = str_replace("{titulo}",$titulo,$mensagem);
		if($nome) $mensagem = str_replace("Parceiro",($nome),$mensagem);
		$mensagem = str_replace("{codigo}",$codigo_producao,$mensagem);
		$mensagem = str_replace("{miniatura}",controle("printdots_ip") ."/users/". controle("settings_folder") ."/thumbs/$codigo_producao.jpg",$mensagem);
		$mensagem = str_replace("{titulo_servico}",($em->titulo_servico),$mensagem);
		$mensagem = str_replace("{agencia}",($agencia),$mensagem);
			$au = explode("-",$em->autorizado_data);
		$mensagem = str_replace("{autorizado_data}","$au[2]/$au[1]/$au[0]",$mensagem);
			$dp = explode("-",$em->data_prevista);
		$mensagem = str_replace("{data_prevista}","$dp[2]/$dp[1]/$dp[0]",$mensagem);
		$mensagem = str_replace("{chegada}",$chegada,$mensagem);
		$mensagem = str_replace("{quantidade}",$quantidade,$mensagem);
		$mensagem = str_replace("{digital_obs}",$em->digital_obs,$mensagem);
		$mensagem = str_replace("{orcamento_obs}",nl2br(($em->orcamento_obs)),$mensagem);
		$mensagem = str_replace("{transporte_servico}",$rst->nome_fantasia,$mensagem);
		$mensagem = str_replace("{entrega_obs}",nl2br($em->entrega_obs),$mensagem);
		$mensagem = str_replace("{frete_tipo}",$em->frete_tipo,$mensagem);
		$mensagem = str_replace("{vencimento}",$vencimento,$mensagem);
		$mensagem = str_replace("{pontos}",$em->ponto,$mensagem);
		$mensagem = str_replace("{id}",$em->id,$mensagem);
		$mensagem = str_replace("{empresa}",$empresa,$mensagem);
		$mensagem = str_replace("{from}",$from,$mensagem);
		$mensagem = str_replace("{site}",$site,$mensagem);
		$mensagem = str_replace("{endereco}",$endereco,$mensagem);
		$mensagem = utf8_decode($mensagem);
		
		#INICIO DO EMAIL
		$headers = "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= 'From:' .$from;
		#$headers .= 'Bcc:' .$email.'\r\n';

		include "../../functions_email.php";
		#$enviado = 1; //teste somente
		#echo "$from,$fromname,$bcc,$destinatario,$assunto,$mensagem,$headers";
		#$envio = email($from,$fromname,$bcc,$destinatario,$assunto,$mensagem,$headers);
		
		if($enviado){
			$email_dados = date("d/m/Y H:i:s")." | $nome | $destinatario | ". $_SESSION[user_detalhes] ."\n". $email_dados;
			$SQL = "UPDATE producao SET email='$email_dados' WHERE codigo=$codigo_producao";
			mysql_query($SQL);
			atualizar();
		}
		#FIM DO EMAIL
		?>
		
		<style type="text/css">
		@import url("../../fieldset.css");
		.conteudo{
		width: 700px;
		margin: 0px auto;
		</style>
		
		<? if($enviado){ ?>
		<div class="conteudo">
		<fieldset class="fieldset_ok">
			<?= utf8_decode("
			<legend>Confirmação</legend>
			<div style='color:#FFF;'>
				<b>O e-mail foi enviado com sucesso!</b><br />
				<b>$titulo</b><br />
				De: $fromname | $from<br />
				Para: $nome | $destinatario<br />
				CÃ³pia Oculta: $bcc<br />
			</div><br />
			") ?>
		</fieldset>
		<? echo $mensagem;  ?>
		</div>
		<? die();
		}else{ ?>
		<body class="body">
		<div class="conteudo">
		<fieldset class="fieldset_atencao">
			<legend>Erro</legend>
			<div>
				<?= utf8_decode("
				<b>Houve um erro ao enviar a mensagem, verifique:</b>
				<ul>
				<li>se há conexão com a internet</li>
				<li>se o e-mail do destinatário foi informado corretamente</li>
				<li>se a conta informada como padrão no sistema realmente existe no servidor</li>
				</ul>
				<b>Dados Informados</b><br />
				$titulo <br />
				De: $fromname | $from <br />
				Para:  $nome |  $destinatario <br />
				<hr />
				") ?>
				<a href='javascript:history.back()' title="Voltar"><img class="image" src="../../images/bt_voltar_pq.png"></a>
			</div><br />
		</fieldset>
		</div>
		<? die();
		}#enviado
	}else{ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Enviando Email da Ordem de Serviço</title>
<style type="text/css">
@import url("../../fieldset.css");
input[type="text"]{
	width: 98%;
}
.conteudo{
width: 700px;
margin: 0px auto;
}

</style>
</head>

<script language="javascript" src="../../jquery.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.pngFix.js" type="text/javascript"></script>
<script language="javascript" src="../../jquery.tooltip.js" type="text/javascript"></script>


<script language="javascript">

//altura = screen.height;
//largura = screen.width;
//self.resizeTo(760,altura-120);
//self.moveTo(largura/2-380,60);
self.focus();
	
//titulos
$('[title]').tooltip({ 
	track: true, 
	delay: 2, 
	showURL: false, 
	showBody: true, 
	fade: 300 
});
</script>
	
<body class="body">
<div class="conteudo">

<fieldset class="fieldset_atencao">
<legend>Atenção</legend>
	<b>Por Favor, informe e confira os dados abaixo antes de encaminhar este e-mail.</b><br />
	Este e-mail será enviado para o cliente, com cÃ³pia oculta para a sua conta.<br /><br />
</fieldset>
<? } 

	#registro
	$SQL = "SELECT titulo_servico,agencia,email FROM producao WHERE codigo=$codigo_producao";
	$SQL = mysql_query($SQL);
	$rs = mysql_fetch_object($SQL);
	$titulo_servico = ($rs->titulo_servico);
	$agencia = $rs->agencia;
	$email = ($rs->email);
	
	#agencia
	$SQL = "SELECT * FROM cliente WHERE codigo=$agencia";
	$SQL = mysql_query($SQL);
	$ag = mysql_fetch_object($SQL);
	
?>
<form name="producao" method="post" action="">
<fieldset class="fieldset_infos">
<legend>Dados para enviar o E-mail</legend>

	<label for="titulo">Título/assunto</label>
	<input type="text" name="titulo" value="Abertura de OS: <?= $codigo_producao ?> - <?= $titulo_servico ?> - Newdoor">
	<label for="nome">Nome</label>
	<input type="text" name="nome" value="<?= $ag->contato ?>" />
	<? $destinatario = explode("\n",strip_tags($ag->email)) ?>
	<label for="destinatario">E-mail destinatário</label>
	<input type="text" name="destinatario" value="<?= $destinatario[0] ?>" />
	<label for="agencia">Agência</label>
	<input type="text" name="agencia" value="<?= ($ag->nome_fantasia) ?>" />
	<label for="chegada">Chegada / Entrega / Disponível para retirada</label>
	<input type="text" name="chegada" value="<?= date('d/m/Y',mktime(0,0,0,date(m),date(d)+3,date(Y))) ?>" />
	<input type="hidden" name="intranet" value="<?= $intranet ?>">
	<input type="hidden" name="codigo_producao" value="<?= $codigo_producao ?>">
	<div>&nbsp;</div>
	<input type="image" src="../../images/bt_email_send.png" name="submit" title="Enviar o Email" />
</form>
</fieldset>

<fieldset class="fieldset_historico">
	<legend>HistÃ³rico</legend>
	<label>Notificações enviadas para esta Ordem de Serviço</label>
	<div class="div_textarea_scroll"><?= nl2br(str_replace(" . ","\n",$email)) ?></div>
</fieldset>
</div>
</body>
</html>	
<? } ?>

