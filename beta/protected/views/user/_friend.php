


<p>
<span class="label label-success"><?php echo $data->created ?></span>
<?php echo CHtml::link(CHtml::encode($data->friend->username.' ('.$data->friend->fullname.')'),array('user/view','id'=>$data->friend->id),array('target'=>'_blank')) ?>
</p>
