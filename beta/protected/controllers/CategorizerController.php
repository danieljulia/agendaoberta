<?php


class CategorizerController extends AdminController {

        private $categorizer;
        
        public  function init(){
       
            $this->categorizer=new Categorizer();
            
        }
        public function actionIndex() {
            
            $this->render('index');

        }
        
	public function actionCreate() {
            
            $this->init();
            $this->categorizer->createClassifier();
            $this->categorizer->createClasses();
            //$this->categorizer->train();
            $this->render('index',array());
            
        }
        
        public function actionRemove(){
              $this->init();
            $this->categorizer=new Categorizer();
            $this->categorizer->removeClassifier();
            
               $this->render('index',array());
        }
        
        
         public function actionInfo(){
                $this->init();
              $this->categorizer=new Categorizer();
             $info=$this->categorizer->getInformation();
            $this->render('index',array("info"=>$info));
             
         }
         
    	  public function actionTrain($id){
               $this->init();
             
   
               $this->categorizer=new Categorizer();
            $this->categorizer->train($id);
              $this->render('index',array());
         
        }
         
        public function actionTest($id){
              $this->init();
             $this->categorizer=new Categorizer();
             
             
             print "trying to classify...";
     
            $ev=Event::model()->findByPk($id);
    
                print $ev->summary;
                print_r( $this->categorizer->classify($ev) );
                $this->render('index',array());
            
        }
        
      
}
               
?>
