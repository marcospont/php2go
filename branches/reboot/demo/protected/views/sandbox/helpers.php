<?$this->beginWidget('ContentWrapper', array('layout' => 'wrapper'))?>

<p>
	<?=$this->htmlLink('widgets1', 'Widgets 1', array('post' => true))?>
	<?=$this->htmlMailto('marcos.pont@gmail.com')?>
	<?=$this->htmlMailto('marcos.pont@gmail.com?subject=Olá', 'Marcos Pont', array('encode' => 'js'))?>
	<?=$this->htmlMailto('marcos.pont@gmail.com', 'Marcos Pont', array('encode' => 'hex'))?>
</p>
<p>
	<?=$this->htmlButton('HtmlButton')?>
	<?=$this->htmlButtonTo('widgets1', 'HtmlButtonTo')?>
	<?=$this->formButton('FormButton')?>
	<?=$this->formSubmit('FormSubmit')?>
	<?=$this->formReset('FormReset')?>
	<?=$this->formImage('images/submit.gif')?>

</p>
<p>
	<?=$this->ajaxLink('ajax', 'AjaxLink', array('confirm' => 'Confirm?', 'update' => 'updateMe'))?>
	<?=$this->ajaxButton('ajax', 'AjaxButton', array('confirm' => 'Confirm?', 'update' => 'updateMe'))?>
</p>
<p>
	<?=$this->popupLink('xml', 'PopupLink', array('confirm' => 'Confirm?', 'specs' => 'width=300,height=300'))?>
	<?=$this->popupButton('xml', 'PopupButton', array('confirm' => 'Confirm?', 'specs' => 'width=300,height=300'))?>
</p>
<p>
	<?=$this->juiButton('JuiButton')?>
	<?=$this->juiButtonTo('widgets1', 'JuiButtonTo')?>
	<?=$this->juiSubmitButton('JuiSubmit')?>
	<?=$this->juiResetButton('JuiReset')?>
	<?=$this->juiAjaxButton('ajax', 'JuiAjax', array('confirm' => 'Confirm?', 'type' => 'POST', 'update' => 'updateMe'))?>
</p>

<div id="updateMe"></div>

<?$this->beginPlaceholder('test', 'set', 'key1')?>
eeeeeeeeeeeeeeeeeee
<?$this->endPlaceholder()?>
<?$this->beginPlaceholder('test', 'set', 'key2')?>
Lorem ipsum vis autem placerat maiestatis no, eos vitae imperdiet te, usu augue equidem ceteros ne. Est an iusto suscipiantur, id volutpat pericula disputationi eam. Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu. Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus. Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.
<?$this->endPlaceholder()?>

<?$this->beginPlaceholder('html')?>
Lorem ipsum vis <b>autem</b> placerat maiestatis no, <b>eos vitae imperdiet te</b>, <i>usu augue equidem ceteros ne</i>. <i>Est an iusto suscipiantur, id volutpat pericula disputationi eam</i>. Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu. Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus. Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.
<?$this->endPlaceholder()?>

<p style="color:red;">
	<?=$this->placeholder('test')->key1?>
</p>
<p style="color:blue;">
	<?=$this->textHighlight($this->placeholder('test')->key2, array('lorem', 'id'), '<span style="color:red">\1</span>')?><br/>
</p>
<p>
	<?=$this->textExcerpt($this->placeholder('test')->key2, 'iusto', 40)?>
</p>

<p>
<?=$this->textTruncate($this->placeholder('html'), 80, array('html' => true))?>
</p>

<p>
<?=nl2br($this->textWrap('Lorem ipsum vis autem placerat maiestatis no, eos vitae imperdiet te, usu augue equidem ceteros ne. Est an iusto suscipiantur, id volutpat pericula disputationi eam. Sea in iisque mentitum suavitate, ex exerci accusata voluptaria eum, vide quodsi postulant ad usu. Dico detraxit conclusionemque per ne, pri te mentitum urbanitas rationibus. Dicat mandamus ex duo, eum offendit pericula disputando ex, id quem patrioque eam.'))?>
</p>

<p>
<?=$this->textAutoLink('Visit www.php2go.com.br. My email is marcos.pont@gmail.com.', 'all', array('target' => '_blank'))?>
</p>

<table width="500" cellpadding="3" cellspacing="0" border="1">
<?=$this->partialLoop('loop', $this->countries)?>
</table>

<?=$this->htmlDefinitionList(array('foo' => 'bar', 'baz' => 'schleben'))?>

<?$this->endWidget()?>