<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<?php
		// Suppress Yii's autoload of JQuery
		// We're loading it at bottom of page (best practice)
		// from Google's CDN with fallback to local version 
		Yii::app()->clientScript->scriptMap=array(
			'jquery.js'=>false,
		);
		
		// Load Yii's generated javascript at bottom of page
		// instead of the 'head', ensuring it loads after JQuery
		Yii::app()->getClientScript()->coreScriptPosition = CClientScript::POS_END;
	?>
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width">
	
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/bootstrap-and-responsive.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style.css" />
	
	<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/libs/modernizr-2.5.3-respond-1.1.0.min.js"></script>
</head>
<body>

<div class="container">
	<div class="row">
		<header class="span12">
<?php /*
			<div id="header-top" class="row">
				<div class="span4">
					<a href="<?php echo Yii::app()->request->baseUrl; ?>/">
						<img src="<?php echo Yii::app()->theme->baseUrl; ?>/img/yii.png" alt="" />
					</a>
				</div>
				<div class="span8">
					<p style="text-align:right;">
						Call now on 555 555 555<br />
						Follow us on <a class="badge badge-info" href="#" target="_blank">Twitter</a>
					</p>
				</div>
			</div>
*/ ?>

<?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'fixed'=>false,
    'brand'=>Yii::app()->name,
    'brandUrl'=>'#',
    'collapse'=>true, // requires bootstrap-responsive.css
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                array('label'=>'Tables - development shortcut', 'url'=>'#', 'items'=>array(
					array('label'=>'AuthItem', 'url'=>array('/AuthItem')),
					array('label'=>'Client', 'url'=>array('/Client')),
					array('label'=>'Crew', 'url'=>array('/Crew')),
					array('label'=>'Day', 'url'=>array('/Day')),
					array('label'=>'Duty', 'url'=>array('/Duty')),
					array('label'=>'DutyType', 'url'=>array('/DutyType')),
					array('label'=>'Dutycategory', 'url'=>array('/Dutycategory')),
					array('label'=>'Generic', 'url'=>array('/Generic')),
					array('label'=>'GenericProjectType', 'url'=>array('/GenericProjectType')),
					array('label'=>'GenericTaskType', 'url'=>array('/GenericTaskType')),
					array('label'=>'GenericType', 'url'=>array('/GenericType')),
					array('label'=>'Genericprojectcategory', 'url'=>array('/Genericprojectcategory')),
					array('label'=>'Generictaskcategory', 'url'=>array('/Generictaskcategory')),
					array('label'=>'Material', 'url'=>array('/Material')),
					array('label'=>'Plan', 'url'=>array('/Plan')),
					array('label'=>'Project', 'url'=>array('/Project')),
					array('label'=>'ProjectType', 'url'=>array('/ProjectType')),
					array('label'=>'PurchaseOrders', 'url'=>array('/PurchaseOrders')),
					array('label'=>'ResourceType', 'url'=>array('/ResourceType')),
					array('label'=>'Resourcecategory', 'url'=>array('/Resourcecategory')),
					array('label'=>'Staff', 'url'=>array('/Staff')),
					array('label'=>'Supplier', 'url'=>array('/Supplier')),
					array('label'=>'Task', 'url'=>array('/Task')),
					array('label'=>'TaskType', 'url'=>array('/TaskType')),
                    '---',
                    array('label'=>'Pivot tables'),
					array('label'=>'Assembly', 'url'=>array('/Assembly')),
					array('label'=>'AuthAssignment', 'url'=>array('/AuthAssignment')),
					array('label'=>'TaskTypeToDutyType', 'url'=>array('/TaskTypeToDutyType')),
					array('label'=>'Duty', 'url'=>array('/Duty')),
					array('label'=>'MaterialToTask', 'url'=>array('/MaterialToTask')),
					array('label'=>'ProjectToAuthAssignment', 'url'=>array('/ProjectToAuthAssignment')),
					array('label'=>'ProjectToAuthAssignmentToTaskTypeToDutyType', 'url'=>array('/ProjectToAuthAssignmentToTaskTypeToDutyType')),
					array('label'=>'ProjectToGenericProjectType', 'url'=>array('/ProjectToGenericProjectType')),
					array('label'=>'Reschedule', 'url'=>array('/Reschedule')),
					array('label'=>'TaskToAssembly', 'url'=>array('/TaskToAssembly')),
					array('label'=>'TaskToGenericTaskType', 'url'=>array('/TaskToGenericTaskType')),
					array('label'=>'TaskToResourceType', 'url'=>array('/TaskToResourceType')),
                )),
            ),
        ),
		$this->operations,
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'htmlOptions'=>array('class'=>'pull-right'),
            'items'=>array(
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest),
            ),
        ),
    ),
)); ?>
					
			<?php if(isset($this->breadcrumbs)):?>
				<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
					'links'=>$this->breadcrumbs,
//					'htmlOptions'=>array('class'=>'breadcrumbs breadcrumb'),
				)); ?><!-- breadcrumbs -->
			<?php endif?>

			<?php if(isset($this->formTitle) && $this->formTitle):?>
				<?php echo '<h1>'.CHtml::encode($this->formTitle).'</h1>'; ?>
			<?php endif?>

		</header>
	</div>
	
	<div class="row">
		<div class="span12">
			<?php echo $content; ?>
		</div>
	</div>

</div><!-- container -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/libs/jquery-1.7.2.min.js"><\/script>')</script>
<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/libs/bootstrap/bootstrap.min.js"></script>
<script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/script.js"></script>
<script>
	var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
	(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
	g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
</body>
</html>