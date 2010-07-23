<?
@session_start();
#require_once "../../conn.php";

function quantidade_os_status($codigo_status){
    if($codigo_status){
        $wrs = "WHERE status='$codigo_status'";
    }else{
        $wrs = "";
    }
    $SQLst = "SELECT count(codigo) as qt FROM producao $wrs";
    $QRst = mysql_query($SQLst);
    $rst = mysql_fetch_array($QRst);
    $qt_status = $rst[qt];
    return $qt_status;
}

function quantidade_cliente($tipo){
    if($tipo=="ativo") $wrs = "WHERE tipo_cliente ='1' AND ativo='1'";
    if($tipo=="inativo") $wrs = "WHERE tipo_cliente ='1' AND ativo!='1'";
    if($tipo=="fornecedor") $wrs = "WHERE tipo_fornecedor ='1' AND ativo='1'";
    if($tipo=="representante") $wrs = "WHERE tipo_representante ='1' AND ativo='1'";
    if($tipo=="transporte") $wrs = "WHERE tipo_transporte ='1' AND ativo='1'";
    $SQLc = "SELECT count(codigo) as qt FROM cliente $wrs";
    $QRc = mysql_query($SQLc);
    $rsc = mysql_fetch_array($QRc);
    $cliente_qt = $rsc[qt];
    return $cliente_qt;
}

function quantidade_usuario($tipo){
    if($tipo=="ativo") $wru = " AND ativo='1'";
    if($tipo=="inativo") $wru = " AND ativo!='1'";
    $SQLu = "SELECT count(codigo) as qt FROM controle WHERE titulo LIKE '%user%' $wru";
    $QRu = mysql_query($SQLu);
    $rsu = mysql_fetch_array($QRu);
    $usuario_qt = $rsu[qt];
    return $usuario_qt;
}

$SQLdt = "SELECT MAX(data_prevista) as data_prevista FROM producao";
    $QRdt = mysql_query($SQLdt);
    $rsdt = mysql_fetch_array($QRdt);
    $data_grafico = $rsdt[data_prevista];
    
?>

<style type="text/css">
@import url("plugins/producao/producao.css");
@import url("fieldset.css");
@import url("plugins/pagina_inicial/pagina_inicial.css");

.recursos{
    list-style-type: none;
    margin-left: 0;
    padding-left: 0;
}
.recursos li img{
    vertical-align: middle;
    width: 18px;
    padding: 3px;
}

.entry{
    padding-left: 30px;
    background: url('images/bt_printdots_24.png') no-repeat left top;
}
.entry .title{
    font-size: 18px;
    color: #CCC;
    border-bottom: 1px dotted #444;
}
.entry small{
    font-size: 9px;
    color: #777;
}
</style>

<table class="days_top">
<tr>
<td class="days_top_1"></td>
<td class="days_top_2 days_top_click">Início</td>
<td class="days_top_3"></td>
<td class="days_top_4">&nbsp;</td>
<td class="days_top_5"></td>
</tr>
</table>

<div id="days_middle">

<div class="colunas_2">

<fieldset>
    <legend>Empresa</legend>
    <div id="producao_os_thumbs" class="producao_os_thumbs">
	<img src="users/<?= controle("settings_folder") ?>/images/<?= controle("settings_folder") ?>.png?t=<?= $uid ?>" width="100%">
    </div>
    <h2><?= controle("settings_empresa") ?></h2>
    Logado como: <?= $_SESSION[user_detalhes] ?><br />
    <div class="clear_left"></div>
    <br />
    
    <label>E-mail</label>
    Nome: <?= controle("email_from_name") ?><br />
    Conta: <?= controle("email_from") ?><br />
    Empresa: <?= controle("codigo_empresa") ?><br />
    
    <label>Conta de usuário</label>
    Data de criação: <?= mydate(controle("settings_data_criacao")) ?><br />
    Data de expiração: <?= mydate(controle("settings_data_expiracao")) ?><br />
    
    <label>Usuários desta empresa</label>
    Ativos: <?= quantidade_usuario('ativo') ?><br />
    Inativos: <?= quantidade_usuario('inativo') ?><br />
    
    <div class="clear_left"></div>
    <br />
</fieldset>

<fieldset class="fieldset_infos">
    <legend>Recursos Disponíveis</legend>
    <ul class="recursos">
    <?
    #categorias de menus
    
    $SQL3 = "SELECT * FROM controle WHERE rel='". controle("menu") ."' AND ativo='1' ORDER BY ordem, descricao ASC, titulo ASC";
    #echo $SQL3;
    $cn3 = mysql_query($SQL3);
    while($rs3 = mysql_fetch_array($cn3)){
            
            $usr = explode(',',$rs3[usuario]);
            if(in_array($_SESSION[user_codigo],$usr)){
    
            #sessions de opcoes de plugins
            $SQL4 = "SELECT * FROM controle WHERE rel='".$rs3[codigo]."' AND ativo='1'";
            #echo $SQL4;
            $cn4 = mysql_query($SQL4);
            while($rs4 = mysql_fetch_array($cn4)){
                    $_SESSION[$rs4[titulo]] = $rs4[valor];
                    $_SESSION[$rs4[titulo]."_codigo"] = $rs4[codigo];
            }
            #criar a sessao de plugins ativos para utilizacao na function plugins()
            $_SESSION[$rs3[titulo]] = $rs3[valor];
                    
            # montagem de cada botao do menu?>
            <script>
            $('#menu_i_<?= $rs3[codigo] ?>').click(function(){
            $('#content_right').load('utf2iso.php?file=<?= $rs3[valor] ?>');
            stop_Int();
            });
            </script>
            <li><a id="menu_i_<?= $rs3[codigo] ?>" href="#"><img src="<?= $rs3[titulo] ?>" alt="" /><?= $rs3[descricao] ?></a></li>
            
            <? }#usuario
    } ?>
    </ul>
    
    <div class="clear_left"></div>
    <br />
</fieldset>

</div>

<div class="colunas_2">

<fieldset class="fieldset_atencao">
    <legend>Importante</legend>
    Você está utilizando o PrintDots em caráter experimental, isto significa que alguns bugs podem ocorrer durante sua administração.<br />
    Sendo assim, contamos com sua colaboração para o apontamento das falhas, para que as mesmas sejam sanadas de forma rápida e consistente.<br />
    Para reportar as falhas, utilize os seguintes canais:<br />
    1 - E-mail:<b>printdots.com.br@gmail.com</b><br />
    2 - Comentário no site: <b><a href="http://printdots.com.br/?page_id=173" target="_blank">http://printdots.com.br/?page_id=173</a></b><br />
    A equipe do Printdots conta com você!<br /><br />
</fieldset>

<fieldset class="fieldset_equipamentos">
    <legend>Ordens de Serviço</legend>

    <div id="pagina_inicial_grafico_producao"></div>
    
    <label>10 mais recentes <small>(até 7 dias)</small></label>
    <ul class="recursos">
        <?
        $SQL = "SELECT codigo,titulo_servico,data_prevista FROM producao WHERE (status!='1' AND status!='') AND TO_DAYS(NOW()) - TO_DAYS(data_prevista) <= '7' ORDER BY data_prevista DESC LIMIT 10";
        $QR = mysql_query($SQL);
        while($rs = mysql_fetch_array($QR)){ ?>
        <li><?= $rs[codigo] ?> - <b><?= $rs[titulo_servico] ?></b> - <?= mydate($rs[data_prevista]) ?></li>
        <? } ?>
        <li>...</li>
    </ul>
    
    <label>Total de Ordens de Serviço: <b><?= quantidade_os_status(0) ?></b></label>
    <ul class="recursos">
        <?
        $SQLs = "SELECT * FROM producao_status WHERE exibir='1' ORDER BY titulo";
        $QRs = mysql_query($SQLs);
        while($rss = mysql_fetch_array($QRs)){ ?>
        <li>
        <img style="width: 15px;" src="plugins/producao/images/status_<?= $rss[codigo] ?>.png"> <?= $rss[titulo] ?>: <b><?= quantidade_os_status($rss[codigo]) ?></b>
        </li>
        <? } ?>
    </ul>

    <script language="javascript">
        $('#bt_lista_producao').click(function(){
        $('#content_right').load("utf2iso.php?file=plugins/producao/producao.php");
        stop_Int();
        });
    </script>
    <a id="bt_lista_producao" href="#"><img src="images/bt_producao.png" alt="" width="20" /> Fila de Produção.</a><br /><br />
    <div class="clear_left"></div>
</fieldset>


<fieldset class="fieldset_clientes">
    <legend>Clientes e Fornecedores</legend>    
    <ul class="recursos">
        <li>Clientes ativos: <?= quantidade_cliente('ativo') ?></li>
        <li>Clientes inativos: <?= quantidade_cliente('inativo') ?></li>
        <li>Fornecedores: <?= quantidade_cliente('fornecedor') ?></li>
        <li>Representantes: <?= quantidade_cliente('representante') ?></li>
        <li>Transporte: <?= quantidade_cliente('transporte') ?></li>
    </ul>
    <div class="clear_left"></div>
</fieldset>

</div>

<div class="clear_left"></div>

<fieldset class="fieldset_printdots">
    <legend>Atualizações Recentes</legend>
    <br />
    <div id="feedContent"></div> 
    <div class="clear_left"></div>
</fieldset>
</div>

<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>

<script type="text/javascript">
    
    function grafico_producao(data){
	//alert(dia);
	$('#pagina_inicial_grafico_producao').load('utf2iso.php?file=plugins/pagina_inicial/pagina_inicial_grafico_producao.php',{selecionado: data});
    } grafico_producao('<?= $data_grafico ?>');
    
    function get_rss_feed(site) {
            //clear the content in the div for the next feed.
            $("#feedContent").empty();
     
            //use the JQuery get to grab the URL from the selected item, put the results in to an argument for parsing in the inline function called when the feed retrieval is complete
            $.get('get_rss.php',site,function(d) {
     
                    //find each 'item' in the file and parse it
                    $(d).find('item').each(function() {
     
                            //name the current found item this for this particular loop run
                            var $item = $(this);
                            // grab the post title
                            var title = $item.find('title').text();
                            // grab the post's URL
                            var link = $item.find('link').text();
                            // next, the description
                            var description = $item.find('description').text();
                            //don't forget the pubdate
                            var pubDate = $item.find('pubDate').text();
                            // tratar algumas tags
                            
                            var html = "<div class=\"entry\">";
                            html += "<div class='title'>" + title + "<\/div>";
                            html += "<small>" + pubDate + "</small><br \/>";
                            html += "<div>" + description + "</div><br \/>";
                            html += "<\/div>";
     
                            //put that feed content on the screen!
                            $('#feedContent').append($(html));  
                    });
            });
     
    };
    get_rss_feed('http://printdots.com.br/?feed=rss2&cat=4');
	
    $('.days_top_2').click(function(){
            $('#content_right').load('utf2iso.php?file=plugins/pagina_inicial/pagina_inicial.php');
    })

    function editar_pedido(codigo){
            $('#pedido_'+ codigo).load('utf2iso.php?file=plugins/pedido/pedido_editar.php',{codigo: codigo},function(){
                    $('#pedido_'+ codigo).slideDown('slow');
                    $('#pedido_'+ codigo).css({'overflow':'none', 'height': 'auto'});
            });
    }
    <? if($editar){ ?>
    editar_pedido(<?= $editar ?>);
    window.location.hash = '#pedido_<?= $editar ?>';
    $("input[name='pedido']:first", document.forms[0]).focus();
    <? } ?>
    
    function lancar_pedido(codigo){
            $("#full_frame").load('utf2iso.php?file=plugins/pedido/pedido_lancar.php',{codigo: codigo});
            $("#full_frame").slideDown(function(){
                    $("#full_background").fadeIn(500);
            });
    }
    
    function excluir_pedido_<?= $codigo ?>(codigo){
            if(confirm('Deseja mesmo excluir este pedido?\nEsta ação é irreversível!')){
            $.post('utf2iso.php?file=plugins/pedido/pedido_editar.php',{codigo: codigo, gravar: 'excluir'},function(){
                    $('#pedido_status_<?= $rs[codigo] ?>').hide();
                    $('#pedido_'+ codigo).slideUp('slow');
                    busca_status('O pedido foi excluído.');
            });
            }
    }
    
    $('#bt_add_pedido').click(function(){
            $('#pedido_todas').load('utf2iso.php?file=plugins/pedido/pedido_editar.php',{gravar: 'incluir'},function(){
                    //$('#bt_add_pedido').fadeOut();
                    //$('#add_pedido').slideDown('slow');
            });	
    });
    
    function print_pedido(codigo){
            win = open("plugins/pedido/pedido_print.php?codigo="+codigo,"pedido_print_"+codigo);
    }
    
    function pedido_send(codigo){
            win = open("plugins/pedido/pedido_send.php?codigo="+codigo,"pedido_send_"+codigo);
    }
    
    var last_codigo = 0;
    function controles(codigo){
            $('#bt_controles_'+last_codigo).hide();
            last_codigo = codigo;
            $('#bt_controles_'+codigo).show();
    }
    
    //titulos
    $('[title]').tooltip({ 
            track: false, 
            delay: 0, 
            showURL: false, 
            showBody: true, 
            fade: 30 
    });
        
        
</script>
