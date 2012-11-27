<?php

class UtilsController extends AdminController
{


	public function actionIndex()
	{
		

		
	}

	
	public function actionUpdate_fb_img()
	{

		$rs = Yii::app()->db->createCommand("SELECT u.id, u.fb_id, f.details FROM ao_user u INNER JOIN ao_user_fb f ON u.fb_id=f.fb_id")->queryAll(true);
		
		$comm = Yii::app()->db->createCommand("UPDATE ao_user SET image_url = :url WHERE id = :id");
		$i = 0;
		foreach ($rs as $r) {
			$i++;
			$details = json_decode($r['details'], true);
			$id = !empty($details['username'])?$details['username']:$r['fb_id'];
			$comm->execute(array(':url'=>'https://graph.facebook.com/'.$id.'/picture', ':id'=>$r['id']));
			echo $r['fb_id'].': '.$id."<br/>\n";
		}
		
	}

}
