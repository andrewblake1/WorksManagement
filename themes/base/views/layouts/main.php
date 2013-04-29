<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	
<?php
// Suppress Yii's autoload of JQuery
// We're loading it at bottom of page (best practice)
// from Google's CDN with fallback to local version 

		Yii::app()->clientScript->scriptMap=array(
			// TODO: remove this once the bug is fixed in future release of yiibooster
			/* NB: currently version 1.9 of jqueryUI introduces tootip which conflicts with bootstrap.
			 * new version of bootstrap or yiibooster will resolve it - several people working on it
			 * There is a jquery-ui solution evidently using $.widget.bridge from https://github.com/twitter/bootstrap/issues/6303
			 */
			'jquery-ui.min.js'=>'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js',
		);

		// Load Yii's generated javascript at bottom of page
		// instead of the 'head', ensuring it loads after JQuery
//		Yii::app()->getClientScript()->coreScriptPosition = CClientScript::POS_END;
	?>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/bootstrap-and-responsive.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/style.css" />
</head>
<body>

<div class="container">
	<div class="row">
		<header class="span12">

			<?php $this->navbar() ?>
			
			<!-- breadcrumbs -->
			<?php if(isset($this->breadcrumbs)):?>
				<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
					'links'=>$this->breadcrumbs,
				)); ?>
			<?php endif?>

			<!-- tabs -->
			<?php
			$tabs = $this->tabs;
			if($tabs)
			{
				// tabs is multi-dimensional i.e. multiple layers
				foreach($tabs as $tabMenu)
				{
					$this->widget('bootstrap.widgets.TbMenu', array(
						'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
						'stacked'=>false, // whether this is a stacked menu
						'items'=>$tabMenu,
					));
				}
			}
			?>
				
			<?php
			if(!empty($this->heading))
			{
				echo '<h2>'.CHtml::encode($this->heading);
				// if we should be showing the new button
				if(Yii::app()->controller->action->id == 'admin')
				{
					$this->exportButton();
					$this->newButton();
//					$this->deleteSelectedButton();
				}
				echo '</h2>';
			}
			?>
		</header>
	</div>
	
	<div class="row">
		<div class="span12">
			<?php echo $content; ?>
		</div>
	</div>

</div>
	
<!--<script type="text/javascript">
// handle jQuery plugin naming conflict between jQuery UI and Bootstrap
$.widget.bridge('uibutton', $.ui.button);
$.widget.bridge('uitooltip', $.ui.tooltip);
</script>-->
</body>
</html>