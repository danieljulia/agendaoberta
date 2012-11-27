<?php

$this->layout = '//layouts/main';

$this->pageTitle=Yii::app()->name . ' - Categories';

?>

<div class="page-header">
<h2>Categories</h2>
</div>

<section>
<table class="table table-bordered table-striped">
<thead>
<tr>
<th>Id</th>
<th>Nom</th>
</tr></thead>
<tbody>
<?php foreach ($categories as $r): ?>
<tr>
<td><?php echo $r->id ?></td>
<td><?php echo CHtml::encode($r->name) ?></td>
</tr>
<?php endforeach ?>
</tbody>
</table>

</section>