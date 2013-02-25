$(document).ready(function(){
	var $menu = $('#menu-head'),
		$menuItem = $menu.find('>li:gt(0)'),
		margin = 40;
	$menuItem.css('margin-left',margin);
	while ($menu.height() > 20)
		$menuItem.css('margin-left', margin--);
});