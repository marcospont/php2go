<?$this->beginWidget('ContentWrapper', array('layout' => 'wrapper'))?>

<?=$this->widget('JuiUploader', array(
	'multiple' => true,
	'style' => 'width:500px;',
	'uploadUrl' => 'sandbox/upload'
))?><br/>

<?=$this->formBegin()?>

<?=$this->formText('prev')?>
<?=$this->widget('TinyMCE', array(
	'name' => 'editor',
	'value' => 'My name is <b>Marcos</b>',
	'template' => 'advanced',
	'contentCss' => 'css/demo.css',
	'readonly' => false,
	'customParams' => array(
		'file_browser_callback' => 'ajaxfilemanager'
	)
))?>
<?=$this->formText('next')?>

<?=$this->formEnd()?>

<?$this->endWidget()?>