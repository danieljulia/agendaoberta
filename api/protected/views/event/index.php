<?php
	
$this->pageTitle=Yii::app()->name . ' - Esdeveniments';


$today = date('Y-m-d');
$y = date('Y'); 
$m = date('m'); 
$max = cal_days_in_month(CAL_GREGORIAN, date('n'), $y); 
$firstInMonth = "$y-$m-01";
$lastInMonth = "$y-$m-$max";


$url = Yii::app()->createAbsoluteUrl('event/search');
$url2 =  Yii::app()->createAbsoluteUrl('event/search',array('out'=>'.json'));
$url3 = Yii::app()->createAbsoluteUrl('event/search',array('out'=>'.xml'));
?>

<div class="page-header">
<h2>Esdeveniments</h2>
</div>


<p>Aquest servei permet cercar esdeveniments que tindran lloc en una data o marge de dates.</p>

<p>La url per accedir al servei és:</p>

<p><code><a href="<?php echo $url ?>"><?php echo $url ?></a></code></p>

<p>Per a obtenir la resposta en diferents formats, afegiu l'extensió corresponent:</p>

<p><code><a href="<?php echo $url2 ?>"><?php echo $url2 ?></a></code>, 
<code><a href="<?php echo $url3 ?>"><?php echo $url3 ?></a></code>
</p>

<p>Vegeu l'apartat de <a href="#formats">formats</a> per a més informació</p>

<section>
<h3>Paràmetres obligatoris</h3>

<!--
<table class="table">

<thead>
<tr>
<th>Paràmetre</th>
<th>Descripció</th>
<th>Exemple</th>
</tr></thead>
<tbody>

<tr>
<td><code>d</code></td>
<td>Data, en format <abbr title="any - mes - dia">aaaa-mm-dd</abbr>, per a la qual es realitza la cerca. 
	<br/>Si voleu fer cerques entre dues dates afegiu el paràmetre <code>e</code>, detallat més endavant.</td>
<td>
	<?php $url = Yii::app()->createAbsoluteUrl('event/search',array('d'=>$today)); ?>
	Esdeveniments per avui: <a href="<?php echo $url ?>"><?php echo $url ?></a>
</td>
</tr>

</tbody>
</table>

-->
<p>Api Key (properament)</p>
<p>Si no s'especifica cap més paràmetre, es realitza una cerca dels esdeveniments per a les properes 8 hores. 
	Es pot ajustar aquest número a través del paràmetre <code>h</code>.
	
	<br/><span class="label label-info">Nota</span> Es descarten els esdeveniments que no tenen una hora d'inici definida.</p>
</section>


<section>
<h3>Paràmetres opcionals</h3>

<table class="table">
<thead>
<tr>
<th>Paràmetre</th>
<th>Descripció</th>
<th>Exemple</th>
</tr></thead>
<tbody>

	
<tr>
<td><code>h</code></td>
<td>Número d'hores (1-12) per a la qual es vol realitzar la cerca bàsica (esdeveniments que començaran dintre 
	de les properes <code>h</code> hores) 
</td>
<td>
	<?php $url = Yii::app()->createAbsoluteUrl('event/search',array('h'=>1)); ?>	
	Esdeveniments que comencen en la propera hora:
	<a href="<?php echo $url ?>"><?php echo $url ?></a>
</td>
</tr>	

<tr>
<td><code>d</code></td>
<td>Data, en format <abbr title="any - mes - dia">aaaa-mm-dd</abbr>, per a la qual es realitza la cerca. 
<br/>Aquest paràmetre s'ignora si es fa servir també el paràmetre <code>h</code>.
<br/>Si voleu fer cerques entre dues dates afegiu el paràmetre <code>e</code>.
</td>
<td>
	<?php $url = Yii::app()->createAbsoluteUrl('event/search',array('d'=>$today)); ?>
	Esdeveniments per avui: <a href="<?php echo $url ?>"><?php echo $url ?></a>
</td>
</tr>

<tr>
<td><code>e</code></td>
<td>Data, en format <abbr title="any - mes - dia">aaaa-mm-dd</abbr>, utilitzada conjuntament amb <code>d</code> per a fer 
	cerques entre dues dates.</td>
<td>
	<?php $url = Yii::app()->createAbsoluteUrl('event/search',array('d'=>$firstInMonth, 'e'=>$lastInMonth)); ?>	
	Esdeveniments dins el mes en curs:
	<a href="<?php echo $url ?>"><?php echo $url ?></a>
</td>
</tr>


<tr>
<td><code>cat</code></td>
<td>Categoria (id o nom, obtinguts del llistat de <a href="<?php echo $this->createUrl('category/index')?>">categories</a>).</td>
<td><?php $url = Yii::app()->createAbsoluteUrl('event/search',array('d'=>date('Y-m-d'),'cat'=>1)); ?>
	Esdeveniments per avui de la categoria "Infantil i juvenil": <a href="<?php echo $url ?>"><?php echo $url ?></a>
</td>
</tr>

<tr>
<td><code>geo</code></td>
<td>Coordenades en format <abbr title="latitud, longitud">lat,lng</abbr> respecte les quals es vol realitzar la cerca. Si no especifiqueu un radi (paràmetre <code>rad</code>), s'agafa per defecte un radi de 3km.</td>
<td><?php $url = Yii::app()->createAbsoluteUrl('event/search',array('d'=>date('Y-m-d'),'geo'=>"41.386871,2.170161")); ?>
	Esdeveniments per avui al centre de Barcelona: <a href="<?php echo $url ?>"><?php echo $url ?></a>
</td>
</tr>

<tr>
<td><code>rad</code></td>
<td>Radi en km. (1 - 100) sobre el qual es realitza la cerca geolocalitzada.</td>
<td><?php $url = Yii::app()->createAbsoluteUrl('event/search',array('d'=>date('Y-m-d'),'geo'=>"41.386871,2.170161",'rad'=>20)); ?>
	Esdeveniments per avui en un radi de 20km. del centre de Barcelona: <a href="<?php echo $url ?>"><?php echo $url ?></a>
</td>
</tr>

<tr>
<td><code>q</code></td>
<td>Paraula clau, per a la cerca en el sumari, descripció i localització de l'esdeveniment.</td>
<td><?php $url = Yii::app()->createAbsoluteUrl('event/search',array('d'=>date('Y-m-d'),'q'=>'parc')); ?>
	Esdeveniments per avui que continguin la paraula "parc": <a href="<?php echo $url ?>"><?php echo $url ?></a>
</td>
</tr>


<tr>
<td><code>pag</code></td>
<td>Número de pàgina (1 - 100). Donat que el nombre màxim d'esdeveniments que es tornen per cada consulta 
	és de 20, podeu fer servir aquest paràmetre per obtenir resultats adicionals en cerques amb 
	més de 20 resultats.
	<br/><em>Veieu l'apartat de formats per més informació.</em>

</td>
<td><?php $url = Yii::app()->createAbsoluteUrl('event/search',array('d'=>date('Y-m-d'),'pag'=>2)); ?>
	Esdeveniments per avui, després dels 20 primers: <a href="<?php echo $url ?>"><?php echo $url ?></a>
	
	
</td>
</tr>


<tr>
<td><code>after_id</code></td>
<td>Id d'esdeveniment a partir del qual voleu realitzar la cerca. 
Aquest paràmetre és útil quan guardem esdeveniments en local, i cridem periòdicament el servei web 
per obtenir possibles esdeveniments introduïts recentment. És a dir, volem repetir una consulta que 
ja hem fet anteriorment, però sense rebre tots els esdeveniments que ja tenim guardats localment.

<br/><em>Veieu l'apartat de fomats per a més informació.</em>

</td>
<td><?php $url = Yii::app()->createAbsoluteUrl('event/search',array('d'=>date('Y-m-d'),'after_id'=>75)); ?>
	Esdeveniments per avui, descartant els esdeveniments amb id més petit o igual a 75: <a href="<?php echo $url ?>"><?php echo $url ?></a>

	
</td>
</tr>


</tbody>
</table>


    <div class="alert alert-info">
			<strong>Nota:</strong> L'ordre en què es retornen els esdeveniments és per data d'inici, 
			excepte si es fa servir el paràmetre <code>geo</code>, passant llavors a ordenar-se de menor a major distància al punt demanat.
			En versions posteriors afegirem un paràmetre per a poder especificar l'ordre desitjat.
    </div>
</section>



<section>
<h3 id="formats">Formats</h3>


<p>Si no poseu cap extensió, el resultat de la cerca es mostrarà en format html.
	Això està pensat bàsicament per a poder provar de forma senzilla el servei, des d'un navegador.</p>

<p>Per a poder realitzar integracions amb altres aplicacions, podeu fer servir qualsevol dels formats suportats:</p>



<table class="table">
<thead>
<tr>
<th>Extensió</th>
<th>Format</th>
<th>Exemple</th>
</tr></thead>
<tbody>

<tr>
<td><code>.json</code></td>
<td>JSON</td>
<td><?php //http://localhost/oberta-api/event/search.json?d=2012-05-24&q=cervera ?>
	<pre class="pre-scrollable">{"next":false,"max_id":"76","completed_in":0.195,"events":[{"id":"71","summary":"Visita teatralitzada a la Universitat de Cervera","description":"Direcci\u00f3: Pep Oriol. Amb gui\u00f3 de [...]","location":"Lloc de trobada: Pla\u00e7a Universitat","url":"http:\/\/cultura.gencat.cat\/agenda\/fitxa.asp?fitxa_id=31867","start":"2012-04-08 11:30","end":"2012-12-09","img":"http:\/\/cultura.gencat.cat\/agenda\/media\/universitatcervera.JPG","lat":"41.668320","lng":"1.275151"}]}</pre>
</td>
</tr>

<tr>
<td><code>.xml</code></td>
<td>XML</td>
<td><pre class="pre-scrollable"><?php //http://localhost/oberta-api/event/search.xml?d=2012-05-24&q=cervera 

$xml = <<<EOT
<?xml version="1.0"?>
<response>
<next></next>	
<max_id>76</max_id>
<completed_in>0.195</completed_in>
<events>
 <event id="71">
  <start>2012-04-08 11:30</start>
  <end>2012-12-09</end>
  <summary>Visita teatralitzada a la Universitat de Cervera</summary>
  <description>Direcció: Pep Oriol. Amb guió de [...]
	</description>
  <img>http://cultura.gencat.cat/agenda/media/universitatcervera.JPG</img>
  <url>http://cultura.gencat.cat/agenda/fitxa.asp?fitxa_id=31867</url>
  <location lat="41.668320" lng="1.275151">Lloc de trobada: Plaça Universitat</location>
 </event>
</events>
</response>
EOT;
echo CHtml::encode($xml);


?></pre>
</td>
</tr>


<tr>
<td><code>.ics</code></td>
<td>iCalendar</td>
<td>
	Pendent d'implementar
</td>
</tr>


</tbody>
</table>

<p>Observeu que en els formats xml i json, abans del llistat d'esdeveniments, trobareu els següents valors:</p>

<ul>
	<li><strong>next</strong>
		<p>Si hi ha més resultats per a la vostra cerca, contindrà l'uri de la següent crida que heu de fer per obtenir-los
		(que serà la mateixa crida que heu fet, amb el paràmetre de número de pàgina <code>pag</code> incrementat). Per exemple:
		<code>/oberta-api/event/search.xml?d=2012-06-01&amp;e=2012-06-30&amp;pag=2</code>		</p>
	</li>
	<li><strong>max_id</strong>
	<p>És l'id màxim d'esdeveniment present en l'actual llistat. El propòsit és facilitar la recopilació d'esdeveniments, 
	realitzant cerques amb el paràmetre <code>after_id</code>, ignorant esdeveniments que ja tenim guardats en local. 		
	<br/>Cal tenir en compte que els esdeveniments no vénen 
	necessàriament ordenats per id, per la qual cosa, abans de fer servir <code>after_id</code> per a repetir una cerca, 
	hem d'assegurar-nos que n'hem obtingut totes les pàgines, i agafar el <code>max_id</code> més alt de totes elles.
	</p>
	</li>
	<li><strong>completed_in</strong>
	<p>Nombre de segons que ha trigat el sistema en retornar la resposta. 
		L'estat de càrrega del servidor, així com la complexitat de les consultes realitzades (cerques geolocalitzades, per exemple),
		afectaran negativament la velocitat de resposta.
	</p>
	</li>
</ul>
</section>