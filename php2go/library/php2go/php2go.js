 ;(function($) {
	var csrfTokenName;
	var csrfTokenValue;
	var php2go = {
		csrfInit: function(tokenName, tokenValue) {
			csrfTokenName = tokenName;
			csrfTokenValue = tokenValue;
			$('form').each(function() {
				php2go.csrfEnable(this);
			});
			$(document).ajaxSend(function(event, request, settings) {
				if (settings.type && settings.type.toLowerCase() == 'post') {
					settings.data = settings.data || "";
					settings.data += (settings.data ? "&" : "") + csrfTokenName + "=" + encodeURIComponent(csrfTokenValue);
					request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				}
			});
		},
		csrfEnable: function(form) {
			if ($(form).is('form') && $(form).attr('method').toLowerCase() == 'post' && !form.elements[csrfTokenName])
				$(document.createElement('input')).attr({name: csrfTokenName, value: csrfTokenValue, type: 'hidden'}).appendTo($(form));
		},
		csrfAugment: function(obj) {
			if (csrfTokenName && csrfTokenValue) {
				obj = obj || {};
				obj[csrfTokenName] = csrfTokenValue;
			}
		},
		post: function(sender, url, params) {
			var act, form = $(sender).parents('form:first');
			if (form.length == 0) {
				form = $('<form method="post" style="display:none;"></form>').appendTo($(sender).parent());
				(url != '') && (form.attr('action', url));
			} else {
				if (url != '') {
					act = form.attr('action');
					form.attr('action', url);
				}
			}
			var inputs = [], params = params || {};
			if (csrfTokenName && csrfTokenValue)
				params[csrfTokenName] = csrfTokenValue;
			$.each(params, function(name, value) {
				inputs.push($(document.createElement('input')).attr({name: name, value: value, type: 'hidden'}).appendTo(form));
			});
			form.data('submitter', sender);
			form.trigger('submit');
			if (act)
				form.attr('action', act);
			$.each(inputs, function() {
				this.remove();
			});
		}
	};
	window.php2go = php2go;
})(jQuery);