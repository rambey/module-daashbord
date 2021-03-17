{* Pour le fonctionnement du js : voir le fichier js/admin/dashboard.js *}
<section id="dashstatics" class="panel widget">
<header class="panel-heading">
		<i class="icon-bar-chart"></i> {$shop_name}: {l s='EN chiffres' d='Modules.Dashproducts.Admin'}
		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="#" onclick="toggleDashConfig('dashstatics'); return false;" title="{l s='Configure' d='Admin.Actions'}">
				<i class="process-icon-configure"></i>
			</a>
			<a class="list-toolbar-btn" href="#"  onclick="refreshDashboard('dashstatics'); return false;"  title="{l s='Refresh' d='Admin.Actions'}">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
	</header>

	<section id="dashproducts_config" class="dash_config hide">
		<header><i class="icon-wrench"></i> {l s='Configuration' d='Admin.Global'}</header>
		{$dashproducts_config_form}
	</section>

<section>
		<nav>
			<ul class="nav nav-pills">
				<li class="active">
					<a href="#chart_line" data-toggle="tab">
						<i class="icon-fire"></i>
						<span class="hidden-inline-xs">{l s='chart Line' d='Modules.Dashproducts.Admin'}</span>
					</a>
				</li>
				<li>
					<a href="#chart_pie" data-toggle="tab">
						<i class="icon-trophy"></i>
						<span class="hidden-inline-xs">{l s='chart Pie' d='Modules.Dashproducts.Admin'}</span>
					</a>
				</li>
			</ul>
		</nav>

		<div class="tab-content panel">
			<div class="tab-pane active" id="chart_line">
				
				<canvas id="myChart"></canvas>
			</div>
			<div class="tab-pane" id="chart_pie">
				 <canvas id="barChart"></canvas>
				
			</div>

		</div>

	</section>
</section>