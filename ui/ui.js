jQuery(document).ready(function($) {
	$('.p2p_results').delegate('a', 'click', function() {
		var $self = $(this),
			$metabox = $self.parents('.p2p_metabox'),
			$list = $metabox.find('.p2p_connected'),
			post_id = $self.attr('name');

		$list.append(
			$metabox.find('.connection-template').html()
				.replace( '%post_id%', post_id )
				.replace( '%post_title%', $self.html() )
		);

		$metabox.find('.p2p_connected .howto').remove();

		var $connected = $metabox.find('.p2p_to_connect');

		$connected.val( $connected.val() + post_id + ',' );

		return false;
	});

	$('.p2p_search :text').keypress(function (ev) {
		if ( 13 === ev.keyCode )
			return false;
	});

	var delayed, old_value = '';

	$('.p2p_search :text').keyup(function (ev) {

		if ( undefined !== delayed ) {
			clearTimeout(delayed);
		}

		var $self = $(this),
			$metabox = $self.parents('.p2p_metabox'),
			$results = $metabox.find('.p2p_results'),
			$spinner = $metabox.find('.waiting');

		delayed = setTimeout(function() {
			if ( !$self.val().length ) {
				$results.html('');
				return;
			}

			if ( $self.val() === old_value ) {
				return;
			}
			old_value = $self.val();

			$spinner.show();
			
			var data = {
				action: 'p2p_search',
				q: $self.val(),
				box_id: $metabox.attr('id').replace('p2p-box-', ''),
				reversed: +$metabox.hasClass('reversed')
			};

			$.getJSON(ajaxurl, data, function(data) {
				$spinner.hide();

				$results.html('');

				$.each(data, function(id, title) {
					$results.append('<li><a href="#" name="' + id + '">' + title + '</a></li>');
				});
			});
		}, 400);
	});
});

