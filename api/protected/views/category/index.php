<?php
	
$this->pageTitle=Yii::app()->name . ' - Categories';


?>

<div class="page-header">
<h2>Categories</h2>
</div>


<p>Aquest servei permet obtenir el llistat de categories que fem servir a Agenda Oberta per a classificar esdeveniments.</p>

<p>Un cop obtingueu les categories, podreu <a href="<?php echo $this->createUrl('event/index')?>">cercar esdeveniments</a> per categoria.</p>

<p>La url per accedir al servei és:</p>

<p><code><a href="<?php echo $this->createUrl('category/list')?>"><?php echo Yii::app()->createAbsoluteUrl('category/list') ?></a></code></p>



<section>
<h3>Formats</h3>

<p>Per a obtenir la resposta en format <code>json</code> o <code>xml</code>, afegiu l'extensió corresponent:</p>

<p><code><a href="<?php echo $this->createUrl('category/list',array('out'=>'.json'))?>"><?php echo Yii::app()->createAbsoluteUrl('category/list',array('out'=>'.json')) ?></a></code></p>

<p><code><a href="<?php echo $this->createUrl('category/list',array('out'=>'.xml'))?>"><?php echo Yii::app()->createAbsoluteUrl('category/list',array('out'=>'.xml')) ?></a></code></p>

</section>