<?php

echo '<div id="resourcecategory_form_con"   class="client-val-form">';

if($model->isNewRecord)
{
	echo $_POST['create_root']=='true'
		? '<h3 id="create_header">Create New Root Resourcecategory </h3>'
		: '<h3 id="create_header">Create New Resourcecategory </h3>';
}
else
{
	echo '<h3 id="update_header">Update Resourcecategory <?php  echo $model->name;  ?> (ID:<?php   echo $model->id;  ?>) </h3>';
}

$baseUrl = Yii::app()->request->baseUrl;

?>

<div id="success-resourcecategory" class="notification success png_bg" style="display:none;">
	<a href="#" class="close"><img src="<?php echo "$baseUrl/css/images/icons/cross_grey_small.png";  ?>"
		title="Close this notification" alt="close" />
	</a>
</div>

<div  id="error-resourcecategory" class="notification errorshow png_bg" style="display:none;">
	<a href="#" class="close"><img src="<?php echo "$baseUrl/css/images/icons/cross_grey_small.png";  ?>"
		title="Close this notification" alt="close" /></a>
</div>

<div class="form">

<?php

$formId='resourcecategory-form';
$ajaxUrl = $model->isNewRecord
	? ($_POST['create_root']!='true'
		? CController::createUrl('resourcecategory/create')
		: CController::createUrl('resourcecategory/createRoot'))
	: CController::createUrl('resourcecategory/update');

$val_error_msg='Error.Resourcecategory was not saved.';

$val_success_message=($model->isNewRecord)
	? ($_POST['create_root']!='true'
		? 'Resourcecategory was created successfuly.'
		:'Root Resourcecategory was created successfuly.')
	: 'Resourcecategory was updated successfuly.';

$success='function(data)
{
	var response= jQuery.parseJSON (data);

	if (response.success ==true)
	{
		jQuery("#'.Resourcecategory::ADMIN_TREE_CONTAINER_ID.'").jstree("refresh");

		$("#success-resourcecategory").fadeOut(1000, "linear",function()
		{
			$(this).append("<div> '.$val_success_message.'</div>").fadeIn(2000, "linear")
		});

		$("#resourcecategory-form").slideToggle(1500);'.$updatesuccess.'
	}
	else
	{
		$("#error-resourcecategory").hide().show().css({"opacity": 1 }).append("<div>"+response.message+"</div>").fadeIn(2000);
		jQuery("#'.Resourcecategory::ADMIN_TREE_CONTAINER_ID.'").jstree("refresh");
	}
}';

$js_afterValidate="js:function(form,data,hasError)
{
	if(!hasError)
	{
		//if there is no error submit with  ajax
		jQuery.ajax(
		{
			'type':'POST',
			'url':'$ajaxUrl',
			'cache':false,
			'data':$(\"#$formId\").serialize(),
			'success':$success
		});
		//cancel submission with regular post request,ajax submission performed above.
		return false;
	}
	//if has not error submit via ajax
	else
	{
		//if there is validation error don't send anything
		//cancel submission with regular post request,validation has errors.
		return false;       
	}                    
}";


$form=$this->beginWidget('CActiveForm', array(
	'id'=>'resourcecategory-form',
	// 'enableAjaxValidation'=>true,
	'enableClientValidation'=>true,
	'focus'=>array($model,'name'),
	'errorMessageCssClass' => 'input-notification-error  error-simple png_bg',
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
		'validateOnType'=>false,
		'afterValidate'=>$js_afterValidate,
		'errorCssClass' => 'err',
		'successCssClass' => 'suc',
		'afterValidateAttribute' => 'js:function(form, attribute, data, hasError)
			{
				if(!hasError)
				{
					$("#success-"+attribute.id).fadeIn(500);
					$("label[for=\'Resourcecategory_"+attribute.name+"\']").removeClass("error");
				}
				else
				{
					$("label[for=\'Resourcecategory_"+attribute.name+"\']").addClass("error");
					$("#success-"+attribute.id).fadeOut(500);
				}
			}'
	),
)); 

 ?>
<?php echo $form->errorSummary($model, '<div style="font-weight:bold">Please correct these errors:</div>', NULL, array('class' => 'errorsum notification errorshow png_bg')); ?><p class="note">Fields with <span class="required">*</span> are required.</p>

  

 <div class="row" >
  <?php echo $form->labelEx($model,'name'); ?>    <?php  echo $form->textField($model,'name',array('size'=>60,'maxlength'=>128,'value'=>$_POST['name'],'style'=>'width:75%;'));  ?>       <span  id="success-Resourcecategory_name"  class="hid input-notification-success  success png_bg"></span>
    <div><small></small> </div>
     <?php   echo $form->error($model,'name');  ?>    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'dutycategory_id'); ?>
		<?php echo $form->textField($model,'dutycategory_id'); ?>
             <span  id="success-Resourcecategory_dutycategory_id"  class="hid input-notification-success  success png_bg"></span>
           <div><small></small> </div>
		<?php echo $form->error($model,'dutycategory_id'); ?>
	</div>



<input type="hidden" name= "YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken; ?>"  />
  <input type="hidden" name= "parent_id" value="<?php echo $_POST['parent_id']; ?>"  />

  <?php  if (!$model->isNewRecord): ?>    <input type="hidden" name= "update_id" value=" <?php echo $_POST['update_id']; ?>"  />
     <?php endif; ?>      
    
   <div class="row buttons">
 <?php   echo  CHtml::submitButton($model->isNewRecord ? 'Submit' : 'Save',array('class' => 'button align-right')); ?>	</div>
     
 <?php  $this->endWidget(); ?></div><!-- form -->

</div>
<script  type="text/javascript">

//Close button:

$(".close").click(function()
{
	$(this).parent().fadeTo(400, 0, function ()
	{
		// Links with the class "close" will close parent
		$(this).slideUp(600);
	});

	return false;
});


</script>


