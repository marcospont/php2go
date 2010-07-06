<?$this->beginWidget('ContentWrapper', array('layout' => 'wrapper'))?>

<br/><br/>

<?if ($this->search->hasErrors()) {?>
<div id="error-summary" class="ui-widget" style="width:600px;">
	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
		<span class="ui-icon ui-icon-closethick" style="cursor: pointer; float: right; margin-top: .3em;"></span>
		<?=$this->modelErrorSummary($this->search, '<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>%s</p>')?>
	</div>
</div>
<?$this->beginScript('domReady')?>
$('#error-summary .ui-icon-closethick').click(function() {
	$(this).parents('.ui-widget:first').hide();
});
<?$this->endScript()?>
<?}?>

<?=$this->formBegin()?>
<?$this->beginWidget('JuiAccordionContainer', array('style' => 'width:600px;', 'active' => ($this->results ? 1 : 0)))?>
	<?$this->beginWidget('JuiAccordionPane', array('title' => 'Pesquisar'))?>
		<table width="100%" cellpadding="4" cellspacing="0" border="0" style="float:left;">
			<colgroup>
				<col width="20%"/>
				<col width="80%"/>
			</colgroup>
			<tbody>
				<tr>
					<td valign="top"><?=$this->modelLabel($this->search, 'name')?></td>
					<td><?=$this->searchTextFilter($this->search, 'name', array('size' => 30, 'maxlength' => 50))?></td>
				</tr>
				<tr>
					<td valign="top"><?=$this->modelLabel($this->search, 'id_country')?></td>
					<td><?=$this->modelSelect($this->search, 'id_country', $this->countries)?></td>
				</tr>
				<tr>
					<td valign="top"><?=$this->modelLabel($this->search, 'birth_date')?></td>
					<td><?=$this->searchInterval($this->search, 'birth_date', array('mask' => 'date'), array('surround' => 'Entre %s e %s'))?></td>
				</tr>
				<tr>
					<td valign="top"><?=$this->modelLabel($this->search, 'is_user')?></td>
					<td><?=$this->modelCheckbox($this->search, 'is_user')?></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<?=$this->juiSubmitButton('Submit')?>
					</td>
				</tr>
			</tbody>
		</table>
	<?$this->endWidget()?>
	<?$this->beginWidget('JuiAccordionPane', array('title' => 'Resultados'))?>
		<?if ($this->results) {?>
		<table width="100%" cellpadding="2" cellspacing="0" border="0">
			<thead>
				<tr>
					<td width="60%">Name</td>
					<td width="15%">Sex</td>
					<td width="25%">Country</td>
				</tr>
			</thead>
			<tbody>
				<?if (count($this->results)) {?>
				<?foreach ($this->results as $person) {?>
				<tr>
					<td><?=$person->name?></td>
					<td><?=$person->sex?></td>
					<td><?=$person->country->name?></td>
				</tr>
				<?}?>
				<?} else {?>
				<tr>
					<td align="center" colspan="3">Sua pesquisa não retornou resultados.</td>
				</tr>
				<?}?>
			</tbody>
		</table>
		<?}?>
	<?$this->endWidget()?>
<?$this->endWidget()?>
<?=$this->formEnd()?>

<?$this->endWidget()?>