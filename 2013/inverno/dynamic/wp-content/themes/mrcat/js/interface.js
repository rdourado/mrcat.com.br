function ready(){

	$('body').removeClass('no-js');
	
	//
	// Menu
	//
	/*
	var $menu = $('#menu-head'),
		$menuItem = $menu.find('>li:gt(0)'),
		margin = 60;
	if ($menuItem.css('display') != 'inline')
		return true;
	$menuItem.css('margin-left',margin);
	while ($menu.height() > 20)
		$menuItem.css('margin-left', margin--);
	*/
	//
	// Masks
	//
	
	try{
		$('#nascimento','.cform').mask('11/11/1111');
		$('#adm_emp1,#adm_emp2,#adm_emp3,#demit_emp1,#demit_emp2,#demit_emp3','.cform').mask('11/1111');
		$('#cep','.cform').mask('99999-999');
		$('#tel,#tel2,#tel2,#tel_emp1,#tel_emp2,#tel_emp3','.cform').mask('(99) 9999-99999');
		$('#cpf','.cform').mask('999.999.999-99', {reverse: true});
		$('#idade,#ndep,#spt','.cform').mask('99');
		$('#sal1_emp1,#sal1_emp2,#sal1_emp3,#sal2_emp1,#sal2_emp2,#sal2_emp3,#sal_pret,#altura,#peso','.cform').mask('000.000.000.000.000,00', {reverse: true});
	}catch(e){}

	//
	// Ajax
	//

	try{
		$('.entry-menu a').unbind('click').click(function(e){
			e.preventDefault();
			var href = $(this).attr('href');
			$.ajax({
				url: href,
				type: 'get',
				data: {ajax:'1'},
				dataType: 'json'
			}).done(function(data){
				$('body').removeClass().addClass(data.slug);
				$('#body').replaceWith(data.post_content);
				ready();
			});
		});
	}catch(e){}

	//
	// Collection
	//

	try{
		var $current_cat = $('.current-cat');
		$('.children').hide();
		$current_cat.parents().map(function(){
			if (this.tagName.toUpperCase() == 'UL') $(this).show();
		});
		$current_cat.children('.children').show();
		$('.categorias-menu a').click(function(){
			if ($(this).next().hasClass('children')) {
				$(this).next().slideToggle();
				return false;
			}
			return true;
		});
	}catch(e){}


	//
	// Fancybox
	//

	try{
		$('.fancybox', '#body').attr('rel', 'gallery').fancybox({ padding: 0 });

		function titleFormat(title, currentArray, currentIndex, currentOpts) {
			var current = $('.products-list>li:eq('+currentIndex+')');
				url = current.find('a').attr('data-url'),
				ref = current.find('img').attr('alt'),
				img = current.find('a').attr('href'),
				fb_url = 'http://www.facebook.com/sharer.php?u='+encodeURIComponent(url)+'&amp;t=Mr.Cat',
				tw_url = 'http://twitter.com/share?url='+encodeURIComponent(url)+'&amp;text='+encodeURIComponent('Gostei muito deste produto da Mr.Cat: '),
				em_url = 'http://www.sharethis.com/share?publisher=e1b87b05-459f-4724-a901-792ee712a634&amp;url='+encodeURIComponent(url)+'&amp;title=Mr.Cat&amp;img='+encodeURIComponent(img),
				html = [];
			
			html.push( '<span class="ref">Referência '+ref+'</span>' );
			html.push( '<span class="box"><b>Compartilhe este produto</b>' );
			//html.push( '<a href="'+em_url+'" target="_blank"><img src="/wp-content/themes/mrcat/img/share-mail.png" alt="por e-mail" /></a>' );
			html.push( '<a href="'+fb_url+'" target="_blank"><img src="/wp-content/themes/mrcat/img/share-fb.png" alt="por Facebook" /></a>' );
			html.push( '<a href="'+tw_url+'" target="_blank"><img src="/wp-content/themes/mrcat/img/share-tw.png" alt="por Twitter" /></a>' );
			html.push( '</span>' );

			return '<div id="fancybox-title-over">'+html.join('')+'</div>';
		}
		$('.products-list>li>a').attr('rel', 'lookbook').fancybox({
			hideOnContentClick: false,
			padding: 50,
			titlePosition: 'over',
			titleFormat: titleFormat
		});
		$('.products-list>li>a.active').trigger('click');
	}catch(e){}

	//
	// Accordion
	//

	try{
		var $acc_head = $('.accordion', '#body'),
			$acc_body = $('.accordion + *', '#body');

		$acc_head.click(function(e){
			e.preventDefault();
			var $this = $(this);
			if (!$this.hasClass('active')) {
				$acc_body.slideUp('normal');
				$this.next().stop(true,true).slideDown('normal');
				$acc_head.removeClass('active');
				$this.addClass('active');
			}
		});

		$acc_body.slideUp(0);
		$acc_head.first().addClass('active').next().slideDown('normal');
	}catch(e){}

	//
	// Lojas
	//

	try{
		var map, geocoder, marker;
		
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(-14.235004,-51.92528);
		var mapOptions = {
			zoom: 15,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
		marker = new google.maps.Marker({ map: map });

		function codeAddress(address) {
			geocoder.geocode( {'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					map.setCenter(results[0].geometry.location);
					marker.setPosition(results[0].geometry.location);
				} else {
					alert("Geocode was not successful for the following reason: " + status);
				}
			});
		}
	}catch(e){}
	try{
		$('#cidade,#bairro,#loja','#filter').each(function(){
			var $this = $(this);
			$this.after($this.clone().attr('id',$this.attr('id')+'_data').attr('name','').hide());
		});

		var $sel_estado = $('#estado','#filter'),
			$sel_cidade = $('#cidade','#filter'), $data_cidade = $('#cidade_data','#filter'),
			$sel_bairro = $('#bairro','#filter'), $data_bairro = $('#bairro_data','#filter'),
			$sel_loja 	= $('#loja','#filter'),   $data_loja   = $('#loja_data','#filter');

		$sel_loja.change(function(){
			$('.cidades-list,.loja-item','#body').hide();
			var $loja = $('#'+$(this).val());
			$loja.show().parent().parent().parent().parent().parent().show();
			codeAddress($loja.find('span').text() + ',' + $sel_cidade.val() + ',' + $sel_estado.val() + ', Brasil');
		});
		$sel_bairro.change(function(){
			$sel_loja.empty().html( $data_loja.find('>optgroup[data-label="'+$('>:selected',this).attr('data-val')+'"]').html() ).trigger('change');
		});
		$sel_cidade.change(function(){
			$sel_bairro.empty().html( $data_bairro.find('>optgroup[data-label="'+$('>:selected',this).attr('data-val')+'"]').html() ).trigger('change');
		});
		$sel_estado.change(function(){
			$sel_cidade.empty().html( $data_cidade.find('>optgroup[label="'+$(this).val()+'"]').html() ).trigger('change');
		}).find('>option:first').remove();
		
		$sel_estado.val('Rio de Janeiro').trigger('change');
		$sel_cidade.val('Rio de Janeiro').trigger('change');
		$sel_bairro.val('Leblon').trigger('change');

	}catch(e){}

	//
	// Slider
	//

	try{
		$('.slider', '#body').each(function(){
			var $this = $(this), 
				sizes = [], 
				wid = 0,
				show = parseInt($this.attr('data-show')),
				margin = parseInt($('li', this).show().css('float', 'left').first().css('marginRight'));
			if (!show) show = 1;
			$('img', this).each(function(){
				var $this = $(this);
				sizes.push({w: $this.width() + margin, h: $this.height()});
				wid += sizes[sizes.length - 1].w;
			});
			$this.data('sizes', sizes).data('current', 0).width(wid);
			$this.data('show', show).data('margin', margin);
			$this.css({padding:0, position:'relative'});
			$this.wrap('<div class="slider-container"></div>');
		});
		$('.slider-container').wrap('<div class="slider-wrap"></div>').css({
			overflow: 'hidden',
			height:   '100%',
			width:    '100%'
		});
		$('.slider-wrap').each(function(){
			var $this 	= $(this),
				$slider = $this.find('>.slider-container>.slider'),
				sizes 	= $slider.data('sizes'),
				margin 	= $slider.data('margin'),
				show 	= $slider.data('show');
			
			var w = 0, h = 0;
			for (var i = 0; i < show; i++) {
				w += sizes[i].w ? sizes[i].w : 0;
				h = sizes[i].h > h ? sizes[i].h : h;
			}
			w -= margin;
			
			$this.css({
				marginLeft:  'auto',
				marginRight: 'auto',
				position: 	 'relative',
				height: 	 h,
				width: 		 w
			}).prepend('<a href="" class="slider-prev"></a><a href="" class="slider-next"></a>');
		});
		$('.slider-prev').hide();
		$('.slider-prev,.slider-next').unbind('click').click(function(e){
			e.preventDefault();
			var $this 		= $(this),
				$wrap 		= $this.parent(),
				is_prev 	= $this.hasClass('slider-prev'),
				$container 	= is_prev ? $this.next().next() : $this.next(),
				$slider 	= $container.find('>.slider');
				sizes 		= $slider.data('sizes'),
				current 	= $slider.data('current'),
				margin 		= $slider.data('margin'),
				show 		= $slider.data('show'),
				diff 		= sizes.length % show;
			
			if (is_prev) current = current - show >= 0 ? current - show : 0;
			else current = current + show <= sizes.length - diff ? current + show : sizes.length - diff;
			
			current <= 0 ? $('.slider-prev', $wrap).fadeOut() : $('.slider-prev', $wrap).fadeIn();
			current >= sizes.length - 1 ? $('.slider-next', $wrap).fadeOut() : $('.slider-next', $wrap).fadeIn();
			
			if (current != $slider.data('current') && current < sizes.length) {

				var w = 0, h = 0, l = 0, r = 0;
				for (var i = current; i < current + show; i++) {
					w += sizes[i] ? sizes[i].w : 0;
					h = (sizes[i] && sizes[i].h) > h ? sizes[i].h : h;
					l += sizes[i-show] ? sizes[i-show].w : 0;
					r += sizes[i] ? sizes[i].w : 0;
				}

				$slider.data('current', current).animate({
					left: is_prev ? '+='+r : '-='+l
				}, 'slow');
				$wrap.animate({
					width: w - margin,
					height: h
				}, 'slow').css('overflow', 'visible');

			}
		});
		$('body').unbind('keydown').keydown(function(e){
			var keyCode = e.keyCode || e.which,
				arrow = {left: 37, up: 38, right: 39, down: 40};
			if (keyCode == arrow.left)
				$('.slider-prev').trigger('click');
			else if (keyCode == arrow.right)
				$('.slider-next').trigger('click');
		});
	}catch(e){}

}
$(document).ready(ready);

/* iPhone */

function isAppleDevice(){
    return (
        (navigator.userAgent.toLowerCase().indexOf("ipad") > -1) ||
        (navigator.userAgent.toLowerCase().indexOf("iphone") > -1) ||
        (navigator.userAgent.toLowerCase().indexOf("ipod") > -1)
    );
}

/* Newsletter */
if (typeof newsletter_check !== "function") {
	window.newsletter_check = function (f) {
		var re = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-]{1,})+\.)+([a-zA-Z0-9]{2,})+$/;
		if (!re.test(f.elements["ne"].value)) {
			alert("Informe o seu e-mail");
			return false;
		}
		if (f.elements["nn"] && (f.elements["nn"].value == "" || f.elements["nn"].value == f.elements["nn"].defaultValue)) {
			alert("Informe o seu nome");
			return false;
		}
		if (f.elements["ny"] && !f.elements["ny"].checked) {
			alert("You must accept the privacy statement");
			return false;
		}
		return true;
	}
}

/* WebFont */
/*
var done = false;
WebFontConfig = {
	google: { families: [ 'Ubuntu:400,700,400italic:latin' ] },
	fontactive: function(fontFamily, fontDescription) {
		var iOS = isAppleDevice();
		if ( !iOS && !done ) {
			done = true;
			var $menu = $('#menu-head'),
				$menuItem = $menu.find('>li:gt(0)'),
				margin = 100;
			if ($menuItem.css('display') != 'inline')
				return true;
			$menuItem.css('margin-left',margin);
			while ($menu.height() > 20)
				$menuItem.css('margin-left', margin--);
		}
	}
};
(function() {
var wf = document.createElement('script');
wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
wf.type = 'text/javascript';
wf.async = 'true';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(wf, s);
})();
*/
