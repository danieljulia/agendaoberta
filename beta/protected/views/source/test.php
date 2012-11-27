<?php

$cs = Yii::app()->getClientScript();
/* @var $cs CClientScript */
?>

<p>Parsejant <?php echo count($items) ?> esdeveniments de prova (n'hi poden haver més)</p>

<?php if ($items): ?>
<table class="table table-bordered table-striped">
	<tr>
		<th>Data Inici</th>
		<th>Hora Inici</th>
		<th>Data Fi</th>
		<th>Hora Fi</th>
		<th>Sumari</th>
                <th>Photo</th>
		<th>Descripció</th>
		<th>Lloc</th>
	</tr>
<?php foreach ($items as $r): ?>
	<tr>
		<td><?php echo $r->startdate ?></td>
		<td><?php echo $r->starttime ?></td>
		<td><?php echo $r->enddate ?></td>
		<td><?php echo $r->endtime ?></td>
		<td><a target='event' href='<?php echo $r->url ?>'><?php echo $r->summary ?></a></td>
                <td><img width='200' src='<?php echo $r->photo ?>'/></td>
		<td><?php echo $r->description ?></td>
		<td><?php echo $r->location ?></td>
		

	</tr>
<?php endforeach; ?>
</table>
<?php endif ?>