<div class="ui-helper-clearfix">
	<?=$this->menuRender()?>
</div>
<?$this->beginScript('domReady')?>
$(".menu li").each(function(){
	$(this).hover(function(){
		$(this).find('ul:eq(0)').show();
	}, function(){
		$(this).find('ul:eq(0)').hide();
	});
});
<?$this->endScript()?>

<?=$this->content?>