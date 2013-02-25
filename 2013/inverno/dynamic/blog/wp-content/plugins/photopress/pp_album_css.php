<?php
/* Photopress album style */
$pp_cellwidth = $pp_options['thumbsize'] + 20;
echo '
<style type="text/css" media="screen">
#pp_wrap {
	margin: 10px 0 0 0;
	padding: 0px;
}
.pp_cell {
	display: table;
	float: left;
	width: ' . $pp_cellwidth . 'px;
	height: ' . $pp_cellwidth . 'px;
	margin: 0 15px 15px 0;
	padding: 0px;
	overflow: hidden;
	text-align: center;
}
.pp_incell {
	display: table-cell;
	vertical-align: bottom;
	position: relative;
}
#pp_wrap a {
	text-decoration: none;
}
#pp_page_links {
	text-align: center;
}
.pp_prev, .pp_next {
	margin: 10px 0px;
	display: block;
	padding: 0px;
}
.pp_prev {
	float: left;
}
.pp_next {
	float: right;
}
#pp_prevnext {
	clear: both;
}
#pp_lgphoto {
	clear: both;
}
</style>
';
?>
