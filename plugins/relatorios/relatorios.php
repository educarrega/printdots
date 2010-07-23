<style type="text/css">
@import url("plugins/producao/producao.css");
@import url("plugins/ordem_de_servico/fieldset.css");
</style>

<div id="days_top_<?= $data_prevista ?>" class="days_top">
	<span id="days_date_<?= $data_prevista ?>" class="days_date">
	Preferências
	</span>
</div>

<div id="days_middle_carregando" class="days_middle">
<img src="images/loader.gif" class="loader" />
</div>

<div id="days_middle">
relatorios em breve
</div>

<div id="days_stats_top_<?= $data_prevista ?>" class="days_stats_top"></div>

<div id="days_stats_middle_<?= $data_prevista ?>" class="days_stats_middle"></div>

<div id="days_stats_bottom_<?= $data_prevista ?>" class="days_stats_bottom"></div>
