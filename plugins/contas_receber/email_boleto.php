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
		if($site && !strstr($site,'http://')) $site = "http://$site";
		$site = "$ft[website]";
		$from = controle('email_from');
		$fromname = controle('email_from_name');
		$bcc = controle('email_bcc');
		$assunto = str_replace("\n","",$titulo);
		$assunto = str_replace("\t","",$assunto);
		$lines = file('email_boleto_modelo.php');
		$l_count = count($lines);
		for($x = 0; $x< $l_count; $x++){
			$mensagem.= $lines[$x];
		}
		
		for($v=0;$v<$vencimentos;$v++){
			$venc .= "Data: $vencimento[$v] | R$ $valor[$v] | Documento: <a href='http://printdots.com.br/app/users/".controle("settings_folder") ."/receber/$documento[$v]' target='_blank'>$documento[$v]</a><br>";
		}
		$mensagem = str_replace("{vencimento}",$venc,$mensagem);
		
	
		if($nome) $mensagem = str_replace("Parceiro",$nome,$mensagem);
		$mensagem = str_replace("{titulo}",$titulo,$mensagem);
		$mensagem = str_replace("{producao}",$codigo_producao,$mensagem);
		$mensagem = str_replace("{autorizado}",$autorizado,$mensagem);
		$mensagem = str_replace("{vencimento}",$venc,$mensagem);
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
				
		if($enviado){
			$SQL = "SELECT email_vencimento FROM producao WHERE codigo=$codigo_producao";
			$SQL = mysql_query($SQL);
			$em = mysql_fetch_object($SQL);
			$email_dados = date("d/m/Y H:i:s")." | $nome | $destinatario | ". $_SESSION[user_detalhes] ."\n". $em->email_vencimento;
			mysql_query("UPDATE producao SET email_vencimento='$email_dados ' WHERE codigo='$codigo_producao'");
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
	}else{	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Enviando Email do Boleto</title>
<style type="text/css">
@import url("../ordem_de_servico/ordem_de_servico.css");
@import url("../../fieldset.css");
.full{
	width: 98%;
}
.conteudo{
width: 700px;
margin: 0px auto;
</style>

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

<?
	#vencimentos/documentos
	$SQL = "SELECT * FROM contas_receber WHERE codigo_producao=$codigo_producao ORDER BY data_vencimento";
	$QRv = mysql_query($SQL);
	$vencimentos = 0;
	while($vc = mysql_fetch_array($QRv)){
		$vencimento[] = mydate($vc[data_vencimento]);
		$documento[] = $vc[documento];
		$valor[] = $vc[valor];
		$vencimentos++;
	}
	
	#dados da OS
	$SQL = "SELECT * FROM producao WHERE codigo=$codigo_producao";
	$QRo = mysql_query($SQL);
	$os = mysql_fetch_object($QRo);
	$titulo = "OS ". $codigo_producao ." : ". $os->titulo_servico ."";
	$autorizado = mydate($os->autorizado_data);
	$codigo_cliente = $os->agencia;
	$email_dados = $os->email_vencimento;
	
	#dados do cliente
	$SQL = "SELECT email,contato FROM cliente WHERE codigo=$codigo_cliente";
	$QRc = mysql_query($SQL);
	$ag = mysql_fetch_object($QRc);
?>

<form name="vencimento" method="post" action="">
<fieldset class="fieldset_email">
<legend>Dados para enviar o E-mail</legend>

	<label for="titulo">Título/assunto</label>
	<input class="full" type="text" name="titulo" value="Vencimentos: <?= $titulo ?>" />
	<label for="nome">Nome</label>
	<input class="full" type="text" name="nome" value="<?= $ag->contato ?>" />
	<? $destinatario = explode("\n",strip_tags($ag->email)) ?>
	<label for="destinatario">E-mail destinatário</label>
	<input class="full" type="text" name="destinatario" value="<?= $destinatario[0] ?>" />
	<label for="autorizado">Autorizado em:</label>
	<input class="full" type="text" name="autorizado" value="<?= $autorizado ?>" />
	
	<label for="vencimento">Vencimento --------------- Valor ------------------------ Documento ---------------</label>
	<? for($v=0;$v<$vencimentos;$v++){?>
	<input  type="text" name="vencimento[]" value="<?= $vencimento[$v] ?>" /> 
	<input  type="text" name="valor[]" value="<?= $valor[$v] ?>" /> 
	<input  type="text" name="documento[]" value="<?= $documento[$v] ?>" />
	<br />
	<? } ?><hr />
	<input type="hidden" name="vencimentos" value="<?= $vencimentos ?>">
	<input type="hidden" name="intranet" value="<?= $intranet ?>">
	<input type="hidden" name="codigo_producao" value="<?= $codigo_producao ?>">
	<div class="">&nbsp;</div>
	<input type="image" src="../../images/bt_email_send.png" name="submit" title="Enviar o E-mail" />
</form>
</fieldset>

<fieldset class="fieldset_historico">
<legend>HistÃ³rico</legend>
	<label>Notificações enviadas para estes Vencimentos</label>
	<div class="div_textarea_scroll"><?= nl2br($email_dados); ?></div>
</fieldset>

</div>
</body>
</html>	
	<? } #destinatario ?>

<? } #intranet?>
