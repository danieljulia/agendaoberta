<?php
/**
 * Bootstrap html helper
 *
 */


class B
{
	

	public static $errorCss='error';

	public static function mergeClasses(&$options,$classes) {
		if (!$classes) return;
		if (is_array($classes)) $classes = implode(' ',$classes);
		if (!empty($options['class'])) {
			$options['class'] .= ' '.$classes;
		} else {
			$options['class'] = $classes;
		}
	}
	
	
	public static function label($model,$attribute,$options=array()) {
		self::mergeClasses($options, 'control-label');
		return CHtml::activeLabel($model, $attribute, $options);
	}
	
	public static function labelEx($model,$attribute,$options=array()) {		
		self::mergeClasses($options, 'control-label');
		return CHtml::activeLabelEx($model, $attribute, $options);
	}

	public static function startGroup($model,$attribute,$options=array())
	{
		$classes = array('control-group');
		CHtml::resolveName($model,$attribute);
		$error=$model->getError($attribute);
		if($error!='') $classes[] = self::$errorCss;
		
		//by default, include label
		$label = 'labelEx';
		if (isset($options['label'])) {
			if ($options['label']=='label') {
				$label = 'label';
			} elseif (!$options['label']) {
				$label = false;
			}
			unset($options['label']);
		}

		self::mergeClasses($options, $classes);
		$group = CHtml::tag('div',$options,false,false);
		if ($label) {
			$group .= "\n".self::$label($model,$attribute);
		}
		 
		$group .= "\n<div class=\"controls\">\n";
		return $group;
	}
	
	public static function endGroup() {
		return "\n</div></div>";
	}
	
	
	public static function error($model,$attribute,$options=array())
	{
		CHtml::resolveName($model,$attribute);
		$error=$model->getError($attribute);
		if($error=='') return '';
		
		//by default, mode is block
		$mode = 'block';
		if (isset($options['mode']) && $options['mode']=='inline') {
			$mode = 'inline';
			unset($options['mode']);
		}

		$classes = 'help-'.$mode;
		self::mergeClasses($options, $classes);
		return CHtml::tag($mode=='block'?'p':'span',$options,$error);
	}	

	/*
	public static function startControlGroup($model,$attribute,$htmlOptions=array())
	{
		$classes = array('control-group');
		CHtml::resolveName($model,$attribute);
		$error=$model->getError($attribute);
		if($error!='') $classes[] = self::$errorCss;		
		
		$options = self::mergeClasses($htmlOptions, $classes);
		$group = CHtml::tag('div',$options);
		
		return $group;
	}
	
	public static function endControlGroup() {
		return '</div>';
	}
*/

	
	
	public static function submit($label, $options=array()) {
		$options['type'] = 'submit';
		self::mergeClasses($options, 'btn btn-primary');
		return CHtml::button($label,$options);
	}

	public static function btn($label, $options=array()) {
		self::mergeClasses($options, 'btn');
		return CHtml::button($label,$options);
	}

	public static function alert($txt, $class='error', $dismiss=true) {
		if (!$txt) return '';
		$pre = '';
		if ($dismiss) $pre = "<button class=\"close\" data-dismiss=\"alert\" type=\"button\">×</button>\n";
		return "<div class=\"alert alert-{$class}\">\n".$pre.$txt."\n</div>";
	}
	
	public static function errorSummary($model,$header=null,$footer=null,$htmlOptions=array())
	{
		$content='';
		if(!is_array($model))
			$model=array($model);
		if(isset($htmlOptions['firstError']))
		{
			$firstError=$htmlOptions['firstError'];
			unset($htmlOptions['firstError']);
		}
		else
			$firstError=false;
		foreach($model as $m)
		{
			foreach($m->getErrors() as $errors)
			{
				foreach($errors as $error)
				{
					if($error!='')
						$content.="<li>$error</li>\n";
					if($firstError)
						break;
				}
			}
		}
		if($content!=='')
		{
			if($header===null)
				$header='<p>'.Yii::t('yii','Please fix the following input errors:').'</p>';
			
			$header="<button class=\"close\" data-dismiss=\"alert\" type=\"button\">×</button>\n".$header;
			
			if(!isset($htmlOptions['class']))
				$htmlOptions['class']='alert alert-error';
			return CHtml::tag('div',$htmlOptions,$header."\n<ul>\n$content</ul>".$footer);
		}
		else
			return '';
	}
	
	/**
	 * Generates a radio button list.
	 * A radio button list is like a {@link checkBoxList check box list}, except that
	 * it only allows single selection.
	 * @param string $name name of the radio button list. You can use this name to retrieve
	 * the selected value(s) once the form is submitted.
	 * @param string $select selection of the radio buttons.
	 * @param array $data value-label pairs used to generate the radio button list.
	 * Note, the values will be automatically HTML-encoded, while the labels will not.
	 * @param array $htmlOptions addtional HTML options. The options will be applied to
	 * each radio button input. The following special options are recognized:
	 * <ul>	 
	 * <li>separator: string, specifies the string that separates the generated radio buttons. Defaults to new line.</li>
	 * <li>labelOptions: array, specifies the additional HTML attributes to be rendered
	 * for every label tag in the list.</li>
	 * </ul>
	 * @return string the generated radio button list
	 */
	public static function radioButtons($name,$select,$data,$htmlOptions=array())
	{		
		$separator=isset($htmlOptions['separator'])?$htmlOptions['separator']:"\n";
		unset($htmlOptions['template'],$htmlOptions['separator']);

		$labelOptions=isset($htmlOptions['labelOptions'])?$htmlOptions['labelOptions']:array();
		unset($htmlOptions['labelOptions']);

		self::mergeClasses($labelOptions, 'radio');
		
		$items=array();
		$baseID=CHtml::getIdByName($name);
		$id=0;
		foreach($data as $value=>$label)
		{
			$checked=!strcmp($value,$select);
			$htmlOptions['value']=$value;
			$htmlOptions['id']=$baseID.'_'.$id++;
			$option=CHtml::radioButton($name,$checked,$htmlOptions);
			$label=CHtml::label($option.' '.$label,$htmlOptions['id'],$labelOptions);
			$items[]=$label;
		}
		return implode($separator,$items);
	}
	
	
	/**
	 * Generates a radio button list for a model attribute.
	 * The model attribute value is used as the selection.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel $model the data model
	 * @param string $attribute the attribute
	 * @param array $data value-label pairs used to generate the radio button list.
	 * Note, the values will be automatically HTML-encoded, while the labels will not.
	 * @param array $htmlOptions addtional HTML options. The options will be applied to
	 * each radio button input. The following special options are recognized:
	 * <ul>
	 * <li>separator: string, specifies the string that separates the generated radio buttons. Defaults to new line.</li>
	 * <li>encode: boolean, specifies whether to encode HTML-encode tag attributes and values. Defaults to true.</li>
	 * </ul>
	 * Since version 1.1.7, a special option named 'uncheckValue' is available that can be used to specify the value
	 * returned when the radio button is not checked. By default, this value is ''. Internally, a hidden field is
	 * rendered so that when the radio button is not checked, we can still obtain the posted uncheck value.
	 * If 'uncheckValue' is set as NULL, the hidden field will not be rendered.
	 * @return string the generated radio button list
	 * @see radioButtonList
	 */
	public static function radioButtonList($model,$attribute,$data,$htmlOptions=array())
	{
		
		
		CHtml::resolveNameID($model,$attribute,$htmlOptions);
		$selection=CHtml::resolveValue($model,$attribute);
		/*if($model->hasErrors($attribute))
			self::addErrorCss($htmlOptions);*/
		$name=$htmlOptions['name'];
		unset($htmlOptions['name']);

		if(array_key_exists('uncheckValue',$htmlOptions))
		{
			$uncheck=$htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		}
		else
			$uncheck='';

		$hiddenOptions=isset($htmlOptions['id']) ? array('id'=>CHtml::ID_PREFIX.$htmlOptions['id']) : array('id'=>false);
		$hidden=$uncheck!==null ? CHtml::hiddenField($name,$uncheck,$hiddenOptions) : '';

		return $hidden . self::radioButtons($name,$selection,$data,$htmlOptions);		
	}	
}
