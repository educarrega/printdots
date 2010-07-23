<? ###
@session_start();
require_once "conn.php";
if($diretorio){
	// diretorio é o local q precisa salvar o documento a partir da raiz do sistema
	// codigo é passado se for salvar na pasta producao/thumbs para dar o nome da imagem como a os
	// nome é ussumido como novo nome + extensao, mesmo havendo codigo
	// a extensão é a que precisa passar se for imagens para thumbs

	if($_FILES["file"]){
		$dirbase = controle("dirbase");
		$uploaddir = "$dirbase/$diretorio/";
		
		if($codigo) $novo_nome = $codigo;
		if($nome) $novo_nome = $nome;
		if($extensao && $novo_nome) $novo_nome .= ".$extensao";
		if(!$nome && !$codigo && $extensao) $novo_nome = $_FILES['file']['name'].".$extensao";
		if(!$nome && !$codigo && !$extensao) $novo_nome = $_FILES['file']['name'];
	
		$uploadfile = $uploaddir.$novo_nome;
		copy($_FILES['file']['tmp_name'], $uploadfile);
		
		#serve para o retorno no upload_form.php
		$arquivo = str_replace("$dirbase/","",$uploadfile);
		$documento = $novo_nome;
	}
	
	//if($excluir_imagem)if(existe("images/miniatura/".$codigo.".jpg"))unlink($diretorio.$codigo.".jpg");
	//echo $dirbase;
		
}
?>

