<?php $this->pageTitle=Yii::app()->name; ?>


<div class="page-header">
<h2><?php echo Yii::t('admin', 'Welcome, {name}', array('{name}'=>'<em>'.Yii::app()->user->name.'</em>')) ?></h2>
</div>

<div class="well">
				
<?php echo Yii::t('admin','Last login:')?> <?php echo Yii::app()->user->getState('last_login') ?>

</div>





<h2>General stats</h2>
<ul>
<?php
foreach($stats as $key=>$value):
?>
    <li><?=$key?>:<strong> <?=$value?> </strong></li>
<?php
endforeach;
?>
</ul>

<h2>Sources parsed last 24h</h2>
<ul>
<?php
foreach($sources as $source):
?>
    <li>
<?php echo CHtml::link($source->name." / new items: ".$source->new,
        array('source/update/'.$source->id)); ?>
 </li>
<?php endforeach; ?>
</ul>

<h2>Sources with errors</h2>
<ul>
<?php
foreach($source_error as $source):
?>
    <li>
<?php echo CHtml::link($source->name." / errors: ".$source->error,
        array('source/update/'.$source->id)); ?>
 </li>
<?php endforeach; ?>
</ul>



<h2>Latest created events</h2>
<ul>
<?php
foreach($events as $ev):
   
    ?>
    <li>
        <?=$ev->created?> : 
        <?php
        if($ev->summary=="") $ev->summary="----<UNTITLED-----";
        ?>
      <?php echo  CHtml::link(CHtml::encode($ev->summary), array("event/view", "id"=>$ev->id, "ajax"=>1)); ?>
      [<?php echo CHtml::link($ev->source->name, array('source/update/'.$ev->source->id)); ?>]
 </li>
<?php endforeach; ?>
</ul>
