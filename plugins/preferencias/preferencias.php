<?
@session_start();
#require_once "../../conn.php";
?>

<style type="text/css">
@import url("plugins/producao/producao.css");
@import url("fieldset.css");
</style>

<table class="days_top">
<tr>
<td class="days_top_1"></td>
<td class="days_top_2 days_top_click">Preferências</td>
<td class="days_top_3"></td>
<td class="days_top_4">&nbsp;</td>
<td class="days_top_5"></td>
</tr>
</table>

<div id="days_middle">
    

<fieldset class="fieldset_infos">
    <legend>Imagem de exibição da empresa</legend>
    <div><br />
    Informe qual a imagem deseja utilizar como face no sistema.  <br /> 
    </div>
    
    <iframe src="upload_form.php?diretorio=users/<?= controle("settings_folder") ?>/images&nome=<?= controle("settings_folder") ?>&extensao=png&preview=1" class="iframe_upload"></iframe>
    
    <br /><br />
    <div class="clear_left"></div>
</fieldset>

<fieldset class="fieldset_infos">
    <legend>Dados cadastrais da empresa</legend>
    <label>Nome da Empresa</label>
    <input type="text" id="nome_empresa" name="empresa" value="<?= controle("settings_empresa") ?>"><br />
    <input type="image" src="images/bt_check.png" class="imagem" />
</fieldset>
</div>

<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>
