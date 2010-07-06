<?$this->beginWidget('ContentWrapper', array('layout' => 'wrapper'))?>

<style type="text/css">
	.area {width: 400px; float: left; padding: 10px;}
	.wide {width: 820px; float: left; padding: 10px;}
	.box {width: 380px; float: left; padding: 10px; margin: 10px; border: 1px solid #ddd;}
	.draggable {width: 80px; float: left; padding: 5px; margin: 3px; border: 1px solid #ddd; text-align: center; background-color: white; cursor: move;}
	.hover {background-color: #feca40}
	#selectable .ui-selecting { background: #feca40; }
	#selectable .ui-selected { background: #f39814; color: white; }
	#selectable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
	#selectable li { margin: 3px; padding: 0.4em; height: 18px; border: 1px solid #ddd; cursor: pointer; }
	.sortable { list-style-type: none; margin: 0; padding: 0; width: 40%; float: left; }
	.sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; height: 18px; border: 1px solid #ddd; cursor: move; }
	.sortable li span { position: absolute; margin-left: -1.3em; }
</style>

<div class="area">
	<!-- title pane -->
	<?$this->beginWidget('JuiTitlePane', array(
		'id' => 'titlePane',
		'animation' => 'blind',
		'collapsible' => true,
		'draggable' => array(
			'cursor' => 'move',
			'revert' => 'invalid',
			'zIndex' => 1000
		),
		'title' => 'Title Pane'
	))?>
		Lorem ipsum vis autem placerat maiestatis no, eos vitae imperdiet te, usu augue equidem ceteros ne.
		Est an iusto suscipiantur, id volutpat pericula disputationi eam.
		Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu.
		Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus.
		Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.
	<?$this->endWidget()?>
</div>
<div class="box ui-helper-reset ui-corner-all">
	<!-- dialog -->
	<?$this->beginWidget('JuiDialog', array(
		'id' => 'dialog',
		'buttons' => array(
			'Ok' => '$(this).dialog("close");',
			'Cancel' => '$(this).dialog("close");'
		),
		'title' => 'My Dialog',
		'trigger' => 'dialogLink'
	))?>
		Hello! This is my dialog!
	<?$this->endWidget()?>
	<?=$this->juiButton('Open Dialog', array('id' => 'dialogLink', 'primaryIcon' => 'ui-icon-newwin'))?><br/><br/>
	<!-- progress bar -->
	<label>Progress Bar</label><br/>
	<?=$this->widget('JuiProgressBar', array(
		'id' => 'progressbar',
		'value' => 0
	))?><br/>
	<!-- slider -->
	<label>Slider</label><br/>
	<?=$this->widget('JuiSliderInput', array('name' => 'slider', 'max' => 10, 'attrs' => array('style' => 'margin-left:5px;margin-top:5px;width:300px;')))?><br/>
</div>

<div style="clear:both;"></div>

<div class="box ui-helper-reset ui-corner-all">
	<!-- datepicker -->
	<?=$this->formLabel('date', 'Date Picker')?><br/>
	<?=$this->widget('JuiDatePicker', array('name' => 'date', 'attrs' => array('size' => 20)))?><br/><br/>
	<!-- masked input -->
	<?=$this->formLabel('masked', 'Masked Input')?><br/>
	<?=$this->formText('masked', null, array('mask' => '(99) 9999-9999'))?><br/><br/>
	<!-- password meter -->
	<?=$this->formLabel('password', 'Password')?><br/>
	<?=$this->formPassword('password')?>
	<?=$this->widget('PasswordStrength', array(
		'password' => 'password'
	))?>
</div>
<div class="box ui-helper-reset ui-corner-all">
	<!-- auto complete -->
	<?=$this->formLabel('autocomplete', 'Auto Complete')?><br/>
	<?=$this->widget('AutoComplete', array(
		'name' => 'autocomplete',
		'url' => 'getClients',
		'max' => 10,
		'selectFirst' => true
	))?><br/><br/>
	<!-- rating -->
	<?=$this->formLabel('rating', 'Rating:')?>&nbsp;<span id="rating-title"></span><br/>
	<?=$this->widget('StarRating', array(
		'name' => 'rating',
		'value' => 3,
		'cancelShow' => false,
		'captionEl' => 'rating-title',
		'options' => array(
			1 => 'Ruim',
			2 => 'Razoável',
			3 => 'Bom',
			4 => 'Ótimo'
		)
	))?><br/><br/>
	<!-- button -->
	<?=$this->juiButton('Save', array('primaryIcon' => 'ui-icon-check'))?>
</div>

<div style="clear:both;"></div>

<div class="area">
	<!-- accordion -->
	<?$this->beginWidget('JuiAccordionContainer', array(
		'id' => 'accordion',
		'collapsible' => true
	))?>
		<?$this->beginWidget('JuiAccordionPane', array('title' => 'Pane 1'))?>
			Lorem ipsum vis autem placerat maiestatis no, eos vitae imperdiet te, usu augue equidem ceteros ne. Est an iusto suscipiantur, id volutpat pericula disputationi eam. Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu. Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus. Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.
		<?$this->endWidget()?>
		<?$this->beginWidget('JuiAccordionPane', array('title' => 'Pane 2'))?>
			Lorem ipsum vis autem placerat maiestatis no, eos vitae imperdiet te, usu augue equidem ceteros ne. Est an iusto suscipiantur, id volutpat pericula disputationi eam. Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu. Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus. Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.
		<?$this->endWidget()?>
		<?$this->beginWidget('JuiAccordionPane', array('title' => 'Pane 3'))?>
			Lorem ipsum vis autem placerat maiestatis no, eos vitae imperdiet te, usu augue equidem ceteros ne. Est an iusto suscipiantur, id volutpat pericula disputationi eam. Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu. Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus. Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.
		<?$this->endWidget()?>
	<?$this->endWidget()?>
</div>
<div class="area">
	<!-- tabs -->
	<?$this->beginWidget('JuiTabContainer', array(
		'id' => 'tabs'
	))?>
		<?$this->beginWidget('JuiTabPane', array('label' => 'Tab 1'))?>
			Lorem ipsum vis autem placerat maiestatis no, eos vitae imperdiet te, usu augue equidem ceteros ne. Est an iusto suscipiantur, id volutpat pericula disputationi eam. Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu. Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus. Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.
		<?$this->endWidget()?>
		<?$this->beginWidget('JuiTabPane', array('label' => 'Tab 2'))?>
			Lorem ipsum vis autem placerat maiestatis no, eos vitae imperdiet te, usu augue equidem ceteros ne. Est an iusto suscipiantur, id volutpat pericula disputationi eam. Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu. Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus. Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.
		<?$this->endWidget()?>
		<?$this->beginWidget('JuiTabPane', array('label' => 'Tab 3'))?>
			Lorem ipsum vis autem placerat maiestatis no, eos vitae imperdiet te, usu augue equidem ceteros ne. Est an iusto suscipiantur, id volutpat pericula disputationi eam. Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu. Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus. Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.
		<?$this->endWidget()?>
		<?$this->widget('JuiTabPane', array('label' => 'Tab 4', 'url' => 'tabContent'))?>
	<?$this->endWidget()?>
</div>

<div style="clear:both;"></div>

<div class="area">
	<!-- draggable -->
	<?$this->beginWidget('JuiDraggable', array(
		'id' => 'draggable',
		'cursor' => 'move',
		'helper' => 'clone',
		'revert' => 'invalid',
		'scope' => 'test'
	))?>
		<?for ($i=0; $i<12; $i++) {?>
		<div class="draggable">Draggable <?=$i+1?></div>
		<?}?>
	<?$this->endWidget()?>
</div>
<div class="box">
	<!-- droppable -->
	<?$this->beginWidget('JuiDroppable', array(
		'id' => 'droppable',
		'hoverClass' => 'hover',
		'scope' => 'test',
		'attrs' => array(
			'style' => 'width:380px;height:100px;'
		)
	))?>
	<?$this->endWidget();?>
</div>

<div style="clear:both;"></div>

<div class="area">
	<!-- selectable -->
	<?$this->beginWidget('JuiSelectable', array(
		'id' => 'selectable',
		'tagName' => 'ul'
	))?>
		<?for ($i=0; $i<10; $i++) {?>
		<li>Selectable <?=$i+1?></li>
		<?}?>
	<?$this->endWidget()?>
</div>
<div class="area">
	<!-- sortables -->
	<?$this->beginWidget('JuiSortable', array(
		'tagName' => 'ul',
		'id' => 'sortable1',
		'class' => 'sortable',
		'connectWith' => '.sortable'
	))?>
		<?for ($i=0; $i<10; $i++) {?>
		<li><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Sortable <?=$i+1?></li>
		<?}?>
	<?$this->endWidget()?>
	<?$this->beginWidget('JuiSortable', array(
		'tagName' => 'ul',
		'id' => 'sortable2',
		'class' => 'sortable',
		'connectWith' => '.sortable'
	))?>
		<?for ($i=0; $i<10; $i++) {?>
		<li><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Sortable <?=$i+1?></li>
		<?}?>
	<?$this->endWidget()?>
</div>

<div style="clear:both;"></div>

<!-- resizable -->
<?$this->beginWidget('JuiResizable', array(
	'id' => 'resizable',
	'class' => 'box',
	'style' => 'height:200px;'
))?>
	I'm resizable.
<?$this->endWidget()?>

<div style="clear:both;"></div>

<div class="wide">
	<!-- image crop -->
	<?=$this->widget('ImageCropper', array(
		'id' => 'crop',
		'src' => $this->url('images/landscape.jpg')
	))?>
</div>

<?$this->endWidget()?>
