function busca_status(dados){
	$('.producao_painel_busca_middle_interno').html(dados);
}
function documento(caminho){
	win = open(caminho);
}
function upload_documento(diretorio,codigo,nome,extensao,preview,campo_retorno){
	$("#full_frame").load('upload_iframe.php',{diretorio:diretorio,codigo:codigo,nome:nome,extensao:extensao,preview:preview});
	$("#full_frame").slideDown(function(){
		$("#full_background").fadeIn(250,function(){
			$("#full_background").click(function(){
				$.post("get_temp.php",{},function(data){
					//alert(data);
					$(campo_retorno).val(data);
				});
				$("#full_background").hide();
			})
		});
	});
}
function check(valor,codigo,campo,click){
	var check = '1';
	var botao = 'images/bt_check.png';
	if(valor){
		check = '0';
		botao = 'images/bt_atencao.png';
	}
	
	var imagem = "<img src='"+botao+"' alt=''>";
	if(click){
		imagem = "<img src='"+botao+"' alt='' onclick='check("+check+","+codigo+",\""+campo+"\",1)'>";
	}
		
	//alert('#'+campo+'_'+codigo);
	//alert('#ativo_'+codigo);
	$('#'+campo+'_'+codigo).val(check);
	$('#check_'+campo+'_'+codigo).html(imagem);
}

function atualiza_producao(){
	$.post('utf2iso.php?file=plugins/producao/producao_reload.php',function(data){
		if(data){
		if(confirm('Houve uma atualização na Lista de Produção, deseja recarrega-la agora?\n'+data)){
		$('#content_right').load('utf2iso.php?file=plugins/producao/producao.php');
		}
		}
	});
}
var intval="";
function start_Int(tempo){
	if(intval==""){
		intval = window.setInterval("atualiza_producao()",tempo);
		//alert('atualizando');
	}else{
		stop_Int();
	}
}
function stop_Int(){
	if(intval!=""){
		window.clearInterval(intval);
		intval = "";
		//alert('parado');
	}
}
//start_Int('tempo em milisegundos');

function nl2br(text){
	text = escape(text);
	if(text.indexOf('%0D%0A') > -1){
		re_nlchar = /%0D%0A/g ;
	}else if(text.indexOf('%0A') > -1){
		re_nlchar = /%0A/g ;
	}else if(text.indexOf('%0D') > -1){
		re_nlchar = /%0D/g ;
	}
	return unescape( text.replace(re_nlchar,'<br />') );
}