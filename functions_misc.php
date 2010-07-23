<?
@session_start();

// tratamento automatico de variaveis
extract($_POST);
extract($_GET);

// escolha do fundo de tela
function background(){
	$diretorio = getcwd(); 
	$ponteiro  = opendir($diretorio."/images/background/");
	while ($arquivos = readdir($ponteiro)) {
	    $itens[] = $arquivos;
	}
	for ($i=0; $i<=15; $i++){
		$random = array_rand($itens);
		if (strlen($itens[$random])>2) $background = $itens[$random];
		$i++;
	}
	return $background;
}

//verifica se existe o plugin esta instalado para o usuario manipular os recursos
function plugin($nome){
	$sessions = implode($_SESSION);
	$plugindir = "plugins/$nome";
	if(strstr($sessions,$plugindir)) return true;
}

//verifica existencia do arquivo
function existe($arq,$retorno){
	//1 - proprio arquivo testado
	//2 - true ou false
	//3 - retorna um arquivo informado para retorno
	$obj = @controle("dirbase")."/$arq";
	#$_SESSION[obj] = $obj;
	if(file_exists($obj)){
		if($retorno){
			return $arq;
		}else{
			return true;
		}
	}else{
		if($retorno){
			if(strlen($retorno)>1){
				return $retorno;
			}else{
				#return $obj;
				return "images/bt_excluir.png";
			}
		}else{
			return false;
		}
	}
}

//tratamento da data
function mydate($date){
	#moldes reconhecidos como data
	if(strlen($date)<11 && strlen($date)>1 && !strstr($date,"00/") && !strstr($date,"/00") && !strstr($date,"00-") && !strstr($date,"-00")){
	      
		#distingue os separdores
		$sep = "-";
		$glu = "/";
		if(strstr($date,"/")){
			$sep = "/";
			$glu = "-";
		}
		
		#cria o array
		$tm = explode($sep,$date);
		
		#preenche arrays vazios
		if(!strlen($tm[2])) $tm[2] = date("Y");
		if(!strlen($tm[1])) $tm[1] = date("m");
		if(!strlen($tm[0])) $tm[0] = date("d");
		
		#adicionar 0 antes do dia e mes se necessario
		if(strlen($tm[2])<2) $tm[2] = "0$tm[2]";
		if(strlen($tm[1])<2) $tm[1] = "0$tm[1]";
		
		#previne datas e meses fora do escopo
		if($tm[1]>12) $tm[1] = "12";
		if($sep == "-"){
		if($tm[2]>31) $tm[2] = "31";
		if(strlen($tm[0])<4) $tm[0] = "20$tm[0]";
		}
		
		#resposta ao solicitante
		return "$tm[2]"."$glu"."$tm[1]"."$glu"."$tm[0]";
	
	# se receber o valor padrao
	}elseif(strlen($date)<11 && strstr($date,"00")){
		if(strstr($date,"-")) return "00/00/0000";
		if(strstr($date,"/")) return "0000-00-00 "; 
	
	#se nao entender a data
	}else{
		return $date;
	}
}

//short meses
function mymonth($mes){
	$meses = ",Jan,Fev,Mar,Abr,Mai,Jun,Jul,Ago,Set,Out,Nov,Dez";
	$m = explode(',',$meses);
	return $m[$mes];
}

//tratamento de minutos para hora
function m2h($tempo) {
	if($tempo<1){
		$tempo = $tempo*0.60;
	}else{	
		$tempo = $tempo*0.98;
	}
	$expl = explode(".", $tempo);
	$h = $expl[0];
	$m = substr($expl[1],0,2);
	if ($m>=60) {
		$h = $h+1;
		$m = $m-60;
		if($m<1 || $m="60") $m="00";
	}
	$m = substr($m."00",0,2);
	$hours = $h . "h:" . $m ."m";
	return $hours;
}

function m2p($m,$pd){
	if($m && $pd){
	$SQLp = "SELECT ppm FROM produto WHERE codigo=$pd";
	$QRp = mysql_query($SQLp);
	$rsp = mysql_fetch_object($QRp);
	$ppm = $rsp->ppm;
	return $m*$ppm;
	}
}

//verifica se o item esta ativado.
function ativado($table,$cod){
	$ativado = mysql_fetch_object(sel($table,"ativo","WHERE codigo='$codigo'"));
	return $ativado->ativo;
}

//limite de caracteres
function sub_str($text,$limit){
	if(strlen($text)>$limit){
		$text = substr($text,0,$limit);
		$text = substr($text,0,strrpos($text," ")).chr(133);
	}
	return $text;
}

// remover acentuacao
function acento($s){
	
	$from = array('�','à','â','�','ª',
	'%A1','%A0','%A2','%A3','%C2%AA',
	'Á','�','Â','Ã',
	'%81','%80','%82','%83',
	'�','è','�',
	'%A9','%A8','%AA',
	'É','È','Ê',
	'%89','%88','%8A',
	'�','ì','î','ĩ',
	'%AD','%AC','%AE','%A9',
	'�','Ì','Î','Ĩ',
	'%8D','%8C','%8E','%C4',
	'ó','ò','ô','õ','º',
	'%B3','%B2','%B4','%B5','%C2%BA',
	'Ó','Ò','Ô','Õ',
	'%93','%92','%94','%95',
	'ú','ù','û',
	'%BA','%B9','%BB',
	'�','Ù','Û',
	'%9A','%99','%9B',
	'�','%A7',
	'Ç','%87',
	'%20');
	$to = array('a','a','a','a','a',
	'a','a','a','a','a',
	'A','A','A','A',
	'A','A','A','A',
	'e','e','e',
	'e','e','e',
	'E','E','E',
	'E','E','E',
	'i','i','i','i',
	'i','i','i','i',
	'I','I','I','I',
	'I','I','I','I',
	'o','o','o','o','o',
	'o','o','o','o','o',
	'O','O','O','O',
	'O','O','O','O', 
	'u','u','u',
	'u','u','u',
	'U','U','U',
	'U','U','U',  
	'c','c',
	'C','C',
	' ');
	
	for($i=0;$i<count($from);$i++){
		$s = str_replace($from[$i],$to[$i],$s);
	}
	
#	$from = implode($from);
#	$to = implode($to);
#	$s = strtr($s, $from, $to);
	
	return $s;
}

// entregar o dia da semana para as OS
$semana = explode(",","Domingo,Segunda,Ter�a,Quarta,Quinta,Sexta,S�bado,");


// mata a sessao aberta 
function login(){
	if($_SESSION["user_logged"]=='' || !isset($_SESSION["user_logged"])){ ?>
		<script type="text/javascript">
		$(document).ready(function(){
		alert('Aten��o, seu tempo de conex�o expirou.\nPor favor, refa�a seu login.');
		$('#content').load("login.php");
		});
		</script>
	<? die; }
}

//atualizar as infos de atualizacoes do sistema
function atualizar(){
	login();
	$table = "controle";
	$cols = "valor='".date("d/m/Y H:i:s")." por $_SESSION[user_detalhes]'";
	$condition = " WHERE titulo='alteracao' LIMIT 1";
	upd($table,$cols,$condition);
}

//obter valor de um controle padrao ou por usuario
//passar o titulo do controle, ele avalia a SESSION[titulo] criada no login
function controle($opcao){
	if(!$_SESSION[$opcao] || $opcao=='alteracao'){
		$SQL_op = mysql_fetch_object(sel("controle","valor","WHERE titulo='$opcao' AND ativo='1' LIMIT 1 "));
		if(!$SQL_op->valor){
			
			if($opcao=='mensagem_interval') $valor = '60000'; //tempo padrao do sistema sem usuario
			//exemplos
			//if($opcao=='settings_folder') $valor = '/var/www/sites/printdots.com.br/app';
			if($opcao=='printdots_ip') $valor = 'http://200.158.67.22/printdots.com.br/app';
			if($opcao=='dirbase') $valor = '/home/printdots/public_html/app';
			//if($opcao='e') $valor = '5';
			
			return $valor;
			$_SESSION[$opcao] = $valor;
		}else{
			return $SQL_op->valor;
			$_SESSION[$opcao] = $SQL_op->valor;
		}
	}else{
		return $_SESSION[$opcao];
	}
}

// alguns recursos requerem identificador unico
$uid = time(YmdHis);
//print_r($_SESSION);
?>
