


<p>
<span class="label label-success"><?php echo $data->created ?></span>

<?php echo CHtml::link(CHtml::encode($data->event->summary),array('event/view','id'=>$data->event->id,'ajax'=>true),array('class'=>'view-detail')) ?>

</p>
