<div<?=$this->renderAttrs()?>>
	<div class="ui-uploader-container">
		<div id="<?=$this->getId()?>-swf"></div>
	</div>
	<div class="ui-uploader-table ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
		<div class="ui-uploader-header ui-widget-header ui-corner-all">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<thead>
					<tr>
						<th class="name"><?=$this->messages['headerName']?></th>
						<th class="size"><?=$this->messages['headerSize']?></th>
						<th class="status"><?=$this->messages['headerStatus']?></th>
					</tr>
				</thead>
			</table>
		</div>
		<div class="ui-uploader-body" style="height:<?=$this->height;?>px">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tbody>
					<tr class="ui-helper-hidden">
						<td class="name"><div></div></td>
						<td class="size"><div></div></td>
						<td class="status">
							<a class="remove" title="<?=$this->messages['titleRemove']?>" href="#">&nbsp;</a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="ui-uploader-footer">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tfoot>
					<tr>
						<td class="name">
							<span class="count">0</span> <?=__(PHP2GO_LANG_DOMAIN, 'file(s)')?>
						</td>
						<td class="size">
							<span class="total">0.00 KB</span>
						</td>
						<td class="status"></td>
					</tr>
				</tfoot>
			</table>
			<?=$this->juiButton($this->messages['buttonAdd'], array(
				'class' => 'add',
				'id' => $this->getId() . '-add',
				'disabled' => true,
				'primaryIcon' => 'ui-icon-plusthick'
			))?>
			<?=$this->juiButton($this->messages['buttonSend'], array(
				'class' => 'send',
				'id' => $this->getId() . '-send',
				'disabled' => true,
				'primaryIcon' => 'ui-icon-arrowthick-1-n'
			))?>
			<?=$this->juiButton($this->messages['buttonClear'], array(
				'class' => 'clear',
				'id' => $this->getId() . '-clear',
				'primaryIcon' => 'ui-icon-closethick'
			))?>
		</div>
	</div>
</div>