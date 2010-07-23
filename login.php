<? 
@session_start();

if($_GET[logout]){
	session_unset();
	session_destroy();
	?>
	<script>window.location = ".";</script>
	<?
	die;
}

if($_SESSION[user_image]){
	$user_image = $_SESSION[user_image];
}else{
	$user_image = "images/user.png";
}

if($_SESSION[user_logged]) $load = "adm.php";

if($_GET[empresa]){
	//linka com o bd do cliente
	require "conn.php";
	$QR_user = sel("users","*","WHERE login='$_GET[empresa]' AND ativo='1'");
	$rs_user = mysql_fetch_array($QR_user);
	if($rs_user[user]){
		$_SESSION[settings_empresa] = $rs_user[empresa];
		$_SESSION[settings_user] = $rs_user[user];
		$_SESSION[settings_password] = $rs_user[password];
		$_SESSION[settings_dbase]= $rs_user[dbase];
		$_SESSION[settings_folder]= $rs_user[folder];
		$_SESSION[settings_url]= $rs_user[url];
		$_SESSION[settings_data_criacao]= $rs_user[data_criacao];
		$_SESSION[settings_data_expiracao]= $rs_user[data_expiracao];
		$_SESSION[user_error] = NULL;
		//$user_image = existe("users/$rs_user[user]/images/$rs_user[user].png","images/user.png");
		$user_image = "users/$rs_user[folder]/images/$rs_user[folder].png";
	}else{
		$_SESSION[settings_user] = NULL;
		$_SESSION[user_error] = "A Empresa não confere, informe novamente.";
	}
	$_SESSION[user_image] = $user_image;
}elseif($_GET[pass]){
	require "conn.php";
	$SQLsh = "SELECT * FROM controle WHERE titulo LIKE 'user%' AND valor='".$_GET[pass]."' AND ativo='1'";
	$cn = mysql_query($SQLsh);
	$rs = mysql_fetch_object($cn);
	if($rs){
		$_SESSION[user_detalhes]=$rs->descricao;
		$_SESSION[user_codigo]=$rs->codigo;
		$_SESSION[user_logged] = 1;
		$load = "adm.php";
		
		function prefs($cod_pref){
			#sessions diversas
			$SQLp = "SELECT * FROM controle WHERE rel='".$cod_pref."' AND titulo!='menu' AND  ativo='1'";
			$cnp = mysql_query($SQLp);
			while($rsp = mysql_fetch_array($cnp)){
				$_SESSION[$rsp[titulo]] = $rsp[valor];
				$_SESSION[$rsp[titulo]."_codigo"] = $rsp[codigo];
				$_SESSION[$rsp[titulo]."_descricao"] = $rsp[descricao];
				prefs($rsp[codigo]);
			}
		}
		prefs($rs->codigo);

	}else{
		$_SESSION[user_error] = "Seu usuário não confere, informe novamente.";
		$load = "login.php";
	}
}

if($load){ ?>
	<script>
	$(document).ready(function(){
	//alert(data);
	$('#content').load('utf2iso.php?file=<?= $load ?>');
	});
	</script>
<? } ?>

<script>

$(document).ready(function(){
	$('#panel_login').hide();
	$('#panel_login').fadeIn('slow');
	$('input:first', document.forms[0]).focus();
});

$('[title]').tooltip({ 
	track: true, 
	delay: 2, 
	showURL: false, 
	showBody: true, 
	fade: 300 
});


$('#form_login').bind('submit',function(){
	formContent = $('#form_login').serialize();
	//alert(formContent);
	$('#content').load("utf2iso.php",formContent);
	return false;
});

</script>

<div id="panel_login">
<div id="panel_container">
<div class="panel_container_top"></div>
<div class="panel_container_middle">

<div id="user_picture">
<div class="user_image">
<img src="<?= $user_image ?>" />
</div>
</div>

<form id="form_login" action="">
	<input type="hidden" name="file" id="file" value="login.php" />
<? if(!$_SESSION[settings_user]){ ?>
	<label for="login_user">Empresa</label>
	<input id="login_user" class="input_login" type="text" name="empresa" title="informe a Empresa" />
<? }else{ ?>
	Olá <?= $_SESSION[settings_empresa] ?><br />
	<?#= $_SESSION[obj] ?>
	<label for="login_user">Senha</label>
	<input id="login_pass" class="input_login" type="password" name="pass" title="informe seu senha" />
<? } ?>
	<input id="login_bt" type="image" src="images/bt_entrar.png" />
	<div class="clear_left"></div>

<? if (isset($_SESSION[user_error])){ ?>
	<div class="login_error"><?= $_SESSION[user_error] ?></div><br />
<? } ?>

<? if (isset($_SESSION[user_error]) || $_SESSION[settings_user]){ ?>
	<div>
	<a href="login.php?logout=1" style="margin:0; border:0; padding:0; height:20px; width:95%; background: none;">
	Trocar de Empresa/Usuário
	</a>
	</div>
<? } ?>

</form>
<br />
      
</div>
<div class="panel_container_bottom"></div>
</div>
</div>

