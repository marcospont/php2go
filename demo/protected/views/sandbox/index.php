<?$this->beginWidget('ContentWrapper', array('layout' => 'wrapper'))?>

<?=$this->formBegin()?>
<?$this->beginWidget('JuiTitlePane', array('title' => 'Formulário', 'collapsible' => true, 'attrs' => array('style' => 'width:600px;')))?>
<table width="100%" cellpadding="4" cellspacing="0" border="0">
	<colgroup>
		<col width="25%"/>
		<col width="75%"/>
	</colgroup>
	<tbody>
		<tr>
			<td><?=$this->formLabel('text', 'Text')?></td>
			<td><?=$this->formText('text')?></td>
		</tr>
		<tr>
			<td><?=$this->formLabel('textarea', 'Textarea')?></td>
			<td><?=$this->formTextarea('textarea')?></td>
		</tr>
		<tr>
			<td><?=$this->formLabel('password', 'Password')?></td>
			<td><?=$this->formPassword('password')?></td>
		</tr>
		<tr>
			<td><?=$this->formLabel('file', 'File')?></td>
			<td><?=$this->formFile('file')?></td>
		</tr>
		<tr>
			<td><label>Checkbox</label></td>
			<td><?=$this->formCheckbox('checkbox')?><?=$this->formLabel('checkbox', 'Checkbox')?></td>
		</tr>
		<tr>
			<td valign="top"><label>Checkbox Group</label></td>
			<td><?=$this->formCheckboxGroup('checkboxGroup', '1', array('1' => 'Option 1', '2' => 'Option 2'))?></td>
		</tr>
		<tr>
			<td><label>Radio</label></td>
			<td><?=$this->formRadio('radio', '1')?><?=$this->formLabel('radio', 'Radio')?></td>
		</tr>
		<tr>
			<td valign="top"><label>Radio Group</label></td>
			<td><?=$this->formRadioGroup('radioGroup', '1', array('1' => 'Option 1', '2' => 'Option 2'))?></td>
		</tr>
		<tr>
			<td valign="top"><?=$this->formLabel('dropDown', 'Drop Down')?></td>
			<td><?=$this->formSelect('dropDown', null, $this->clients, array('prompt' => 'Choose an option'))?></td>
		</tr>
		<tr>
			<td valign="top"><?=$this->formLabel('listBox', 'List Box')?></td>
			<td><?=$this->formSelect('listBox', null, array(
				'Group 1' => array(
					'1' => 'Option 1', '2' => 'Option 2', '3' => 'Option 3'
				),
				'Group 2' => array(
					'4' => 'Option 4', '5' => 'Option 5', '6' => array('Option 6', true)
				)
			), array('multiple' => true, 'size' => 5))?></td>
		</tr>
		<tr>
			<td valign="top"><label>Image</label></td>
			<td><?=$this->formImage('images/submit.gif')?></td>
		</tr>
		<tr>
			<td><label>Buttons</label></td>
			<td>
				<?=$this->juiSubmitButton('Submit')?>
				<?=$this->juiResetButton('Reset')?>
				<?=$this->juiButton('Button')?>
			</td>
		</tr>
	</tbody>
</table>
<?$this->endWidget()?>
<?=$this->formEnd()?>

<?$this->endWidget()?>