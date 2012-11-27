<?php



$this->pageTitle ="Categorizer";



?>




<?php
	           
    $this->widget('zii.widgets.CMenu', array(
    'items'=>array(
        array('label'=>'Create', 'url'=>array('categorizer/create')),
        array('label'=>'Remove', 'url'=>array('categorizer/remove')),
        array('label'=>'Info', 'url'=>array('categorizer/info')),
        array('label '=>'Train', 'url'=>array('categorizer/train')),
         ),
));
    
    
?>


<?php
if(isset($info)):
?>

<?php  print_r($info)?>

<?php
endif;
?>

