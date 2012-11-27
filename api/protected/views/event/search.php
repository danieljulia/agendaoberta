<?php

$this->layout = '//layouts/main';

$this->pageTitle=Yii::app()->name . ' - Esdeveniments';

?>

<div class="page-header">
<h2>Esdeveniments</h2>
</div>

<section>
<table class="table table-bordered table-striped">
<thead>
<tr>
<th>Id</th>
<th>Sumari</th>
<th>Inici</th>
<th>Fi</th>
<th>Descripció</th>
<th>Localització</th>
<th>Url, Imatge</th>
<th>Categories</th>
</tr></thead>
<tbody>
<?php foreach ($events as $r): ?>
<tr>
<td><?php echo $r->id==$maxId?"<strong>$maxId</strong>":$r->id ?></td>	
<td><?php echo CHtml::encode($r->summary) ?></td>
<td><?php echo $r->start ?></td>
<td><?php echo $r->end ?></td>
<td>
	<?php echo CHtml::encode($r->description) ?>
</td>
<td><?php echo CHtml::encode($r->location) ?>
<?php if ($r->geo_lat && $r->geo_lng): ?>
	<a href="http://maps.google.com/?ll=<?php echo $r->geo_lat,',',$r->geo_lng ?>" target="_blank">[mapa]</a>
	<?php endif ?> 
	<?php if (isset($_GET['geo'])) echo ' [',$r->distance,'&nbsp;km]'; ?>
</td>
<td>
	<?php echo $r->url?CHtml::link($r->url,$r->url):'' ?>
	<?php echo $r->imgUrl?'<br/>'.CHtml::image($r->imgUrl,'',array('width'=>150)):'' ?>
</td>
<td><?php if ($r->getCategoryList()) echo implode(', ',$r->getCategoryList()); ?></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
	
	
	
<?php if ($next) echo '<p><a href="',$next,'">Següent &raquo;</a></p>' ?>
	
<p>Temps d'execució: <?php echo round(Yii::getLogger()->getExecutionTime(),3) ?> segons</p>
	
	
</section>