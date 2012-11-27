<?php 
	
	$this->pageTitle=Yii::app()->name; 
	
?>

<div class="page-header">
<h2>API Agenda Oberta</h2>
</div>

<p>Aquí aniria una explicació general sobre l'Api...</p>



<section>
<div class="row">
	
	<div class="span8">
	<dl>
		<dt><a href="<?php echo $this->createUrl('category/index')?>">Categories</a></dt>
		<dd>Permet obtenir el llistat de categories que fem servir a Agenda Oberta per a classificar esdeveniments.
			<br/><a href="<?php echo $this->createUrl('category/index')?>"><?php echo Yii::app()->createAbsoluteUrl('category/index') ?></a>
		</dd>
		<dt><a href="<?php echo $this->createUrl('event/index')?>">Esdeveniments</a></dt>
		<dd>Permet cercar esdeveniments que tindran lloc en una data o marge de dates, juntament amb altres paràmetres de cerca opcionals.
			Resposta disponible en diferents formats (json, xml, ...)
			<br/><a href="<?php echo $this->createUrl('event/index')?>"><?php echo Yii::app()->createAbsoluteUrl('event/index') ?></a>
		</dd>
	</dl>
	</div>
	
</div>
	</section>