<?$this->beginWidget('ContentWrapper', array('layout' => 'wrapper'))?>

<? if ($success = Flash::get('success')) { ?>
<div id="success" class="ui-widget" style="width:600px;">
	<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
		<span class="ui-icon ui-icon-closethick" style="cursor: pointer; float: right; margin-top: .3em;"></span>
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span><?=$success?></p>
	</div>
</div>
<?$this->beginScript('domReady')?>
$('#success .ui-icon-closethick').click(function() {
	$(this).parents('.ui-widget:first').hide();
});
<?$this->endScript()?>
<? } ?>

<?if ($this->person->hasErrors()) {?>
<div id="error-summary" class="ui-widget" style="width:600px;">
	<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
		<span class="ui-icon ui-icon-closethick" style="cursor: pointer; float: right; margin-top: .3em;"></span>
		<?=$this->modelErrorSummary($this->person, '<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>%s</p>')?>
	</div>
</div>
<?$this->beginScript('domReady')?>
$('#error-summary .ui-icon-closethick').click(function() {
	$(this).parents('.ui-widget:first').hide();
});
<?$this->endScript()?>
<?}?>

<?=$this->formBegin(array('enctype' => 'multipart/form-data'))?>
<table width="600" cellpadding="4" cellspacing="0" border="0" style="float:left;">
	<colgroup>
		<col width="20%"/>
		<col width="80%"/>
	</colgroup>
	<tbody>
		<tr>
			<td colspan="2" style="padding-bottom:10px;"><span class="required">(*)</span> Campos obrigatórios</td>
		</tr>
		<tr>
			<td valign="top"><?=$this->modelLabel($this->person, 'name')?></td>
			<td><?
				echo $this->modelText($this->person, 'name', array('size' => 30, 'maxlength' => 50));
				//echo $this->modelError($this->person, 'name');
			?></td>
		</tr>
		<tr>
			<td valign="top"><?=$this->modelLabel($this->person, 'sex')?></td>
			<td><?
				echo $this->modelSelect($this->person, 'sex', $this->sex);
				//echo $this->modelError($this->person, 'sex');
			?></td>
		</tr>
		<tr>
			<td valign="top"><?=$this->modelLabel($this->person, 'birth_date')?></td>
			<td><?
				echo $this->widget('JuiDatePicker', array('model' => $this->person, 'modelAttr' => 'birth_date'));
				//echo $this->modelError($this->person, 'birth_date');
			?></td>
		</tr>
		<tr>
			<td valign="top"><?=$this->modelLabel($this->person, 'address')?></td>
			<td><?
				echo $this->modelText($this->person, 'address', array('size' => 60, 'maxlength' => 100));
				//echo $this->modelError($this->person, 'address');
			?></td>
		</tr>
		<tr>
			<td valign="top"><?=$this->modelLabel($this->person, 'id_country')?></td>
			<td><?
				echo $this->modelSelect($this->person, 'id_country', $this->countries);
				//echo $this->modelError($this->person, 'id_country');
			?></td>
		</tr>
		<tr>
			<td valign="top"><?=$this->modelLabel($this->person, 'picture')?></td>
			<td><?
				echo $this->modelFile($this->person, 'picture');
				//echo $this->modelError($this->person, 'picture');
			?></td>
		</tr>
		<tr>
			<td valign="top"><?=$this->modelLabel($this->person, 'notes')?></td>
			<td><?
				echo $this->modelTextArea($this->person, 'notes', array('rows' => 5, 'cols' => 60));
				//echo $this->modelError($this->person, 'notes');
			?></td>
		</tr>
		<tr>
			<td valign="top"><?=$this->modelLabel($this->person, 'active')?></td>
			<td><?
				echo $this->modelCheckbox($this->person, 'active');
				//echo $this->modelError($this->person, 'active');
			?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<?=$this->formSubmit('Salvar')?>
				<?=$this->formSubmit('Toggle Active', array('name' => 'toggle'))?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				Criado há <?=$this->timeAgoInWords($this->person->add_date, true)?><br/>
				Atualizado há <?=$this->timeAgoInWords($this->person->update_date, true)?><br/>
			</td>
		</tr>
	</tbody>
</table>
<?=$this->formEnd()?>

<?$this->endWidget()?>