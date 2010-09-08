;(function($) {

	$.fn.uploader = function() {
		var args = $.makeArray(arguments);
		return this.each(function() {
			var uploader;
			if (args.length == 1 && typeof(args[0]) == 'object') {
				uploader = $(this).data('uploader');
				if (!uploader) {
					var self = $(this);
					var params = convertParams(self, args[0]);
					for (var name in events) {
						params[events[name]] = function(localName, swfName) {
							return function() {
								var ev = $.Event(localName);
								self.trigger(ev, $.makeArray(arguments));
								return !ev.isDefaultPrevented();
							};
						}(name, events[name]);
					}
					self.data('uploader', new $.uploader(self, params));
				}
			} else if (args.length > 0 && typeof(args[0]) == 'string') {
				var method = args.shift(), uploader = $(this).data('uploader');
				if (uploader && uploader[method])
					uploader[method].apply(uploader, args);
			}
		});
	};

	$.uploader = function(el, params) {
		this.el = el;
		this.files = {};
		this.filesCount = 0;
		this.uploading = false;
		this.init(params);
	};
	$.uploader.swfUrl = '';
	$.uploader.messages = {};
	$.uploader.prototype = {
		init: function(params) {
			var self = this;
			this.el.find('.send').click(function() {
				toggleBtn(self, '.add', false);
				toggleBtn(self, '.send', false);
				toggleBtn(self, '.clear', false);
				self.swfu.startUpload();
			});
			this.el.find('.clear').click(function() {
				self.clearQueue();
			});
			this.el.bind('onSwfError', function(event) {
				throw $.sprintf($.uploader.messages.swfError, self.swfu.settings.minimum_flash_version);
			});
			this.el.bind('onSwfReady', function(event) {
				moveSwf(self);
				toggleBtn(self, '.add', !self.swfu.settings.disabled);
			});
			this.el.bind('onFileQueued', function(event, file) {
				self.files[file.id] = file;
				self.filesCount++;
				renderFile(self, file);
				moveSwf(self);
				toggleBtn(self, '.send', true);
			});
			this.el.bind('onUploadStart', function(event, file) {
				self.uploading = true;
				renderStart(self, file.id);
			});
			this.el.bind('onUploadProgress', function(event, file, bytesLoaded, bytesTotal) {
				renderProgress(self, file.id, bytesLoaded, bytesTotal);
			});
			this.el.bind('onUploadError', function(event, file, errorCode, message) {
				switch (errorCode) {
					case (SWFUpload.UPLOAD_ERROR.HTTP_ERROR) :
						message = $.uploader.messages.httpError;
						break;
				}
				renderError(self, file.id, message);
			});
			this.el.bind('onUploadSuccess', function(event, file, serverData, response) {
				try {
					var serverData = $.parseJSON(serverData);
					if (serverData.success || serverData == 1 || serverData === true) {
						renderSuccess(self, file.id, $.sprintf($.uploader.messages.success, file.name));
						self.el.trigger('onResponseSuccess', [file]);
					} else {
						renderError(self, file.id, serverData.message);
						self.el.trigger('onResponseError', [file]);
					}
				} catch(e) {
					renderError(self, file.id, serverData);
					self.el.trigger('onResponseError', [file]);
				}
			});
			this.el.bind('onQueueComplete', function(event) {
				var stats = self.swfu.getStats();
				self.uploading = false;
				toggleBtn(self, '.add', true);
				if (stats.files_queued > 0)
					toggleBtn(self, '.send', true);
				toggleBtn(self, '.clear', true);
			});
			this.swfu = new SWFUpload(params);
			$(window).unload(function() {
				self.swfu.destroy();
			});
		},
		disable: function() {
			if (this.uploading)
				this.swfu.cancelUpload();
			this.swfu.setButtonDisabled(true);
			this.swfu.setButtonCursor(SWFUpload.CURSOR.ARROW);
			toggleBtn(this, '.add', false);
		},
		enable : function() {
			if (!this.uploading) {
				this.swfu.setButtonDisabled(false);
				this.swfu.setButtonCursor(SWFUpload.CURSOR.HAND);
				toggleBtn(this, '.add', true);
			}
		},
		clearQueue: function() {
			if (this.uploading) {
				this.swfu.cancelQueue();
				this.uploading = false;
			}
			this.files = {};
			this.filesCount = 0;
			resetFiles(this);
			renderTotals(this);
		},
		removeFile: function(id) {
			if (this.files[id]) {
				this.swfu.cancelUpload(id, false);
				$('#' + id).remove();
				delete this.files[id];
				this.filesCount--;
				moveSwf(this);
				renderTotals(this);
			}
		}
	};

	var events = {
		'onBeforeSwfLoad': 'swfupload_pre_load_handler',
		'onSwfError': 'swfupload_load_failed_handler',
		'onSwfReady': 'swfupload_loaded_handler',
		'onFileQueued': 'file_queued_handler',
		'onQueueError': 'file_queue_error_handler',
		'onDialogOpen': 'file_dialog_start_handler',
		'onDialogComplete': 'file_dialog_complete_handler',
		'onUploadStart': 'upload_start_handler',
		'onUploadProgress': 'upload_progress_handler',
		'onUploadError': 'upload_error_handler',
		'onUploadSuccess': 'upload_success_handler',
		'onUploadComplete': 'upload_complete_handler',
		'onQueueComplete': 'queue_complete_handler'
	};

	var defaults = {
		'preserve_relative_urls': true,
		'requeue_on_error': false,
		'file_size_limit': 0,
		'file_upload_limit': 0,
		'prevent_swf_caching': true,
		'button_image_url': '',
		'button_text': '',
		'button_text_style': '',
		'button_text_top_padding': 0,
		'button_text_left_padding': 0,
		'button_window_mode': 'transparent'
	};

	function convertParams(el, params) {
		var addBtn = el.find('.add');
		var params = $.extend({}, defaults, params);
		php2go.csrfAugment(params.params);
		params['upload_url'] = params.uploadUrl || '';
		params['file_post_name'] = params.fileParamName || 'Filedata';
		params['post_params'] = params.params || null;
		params['use_query_string'] = !!params.useQueryString;
		params['http_success'] = params.httpSuccess || [];
		params['assume_success_timeout'] = params.assumeSuccessTimeout || 0,
		params['file_types'] = params.fileTypes || '*.*';
		params['file_types_description'] = params.fileTypesDescription || null;
		params['file_queue_limit'] = params.queueLimit || 0;
		params['flash_url'] = $.uploader.swfUrl;
		params['button_width'] = addBtn.width();
		params['button_height'] = addBtn.height();
		params['button_action'] = (params.multiple ? SWFUpload.BUTTON_ACTION.SELECT_FILES : SWFUpload.BUTTON_ACTION.SELECT_FILE);
		params['button_disabled'] = !!params.disabled;
		params['button_placeholder'] = $('#' + el.attr('id') + '-swf')[0];
		params['button_cursor'] = (!!params.disabled ? SWFUpload.CURSOR.ARROW : SWFUpload.CURSOR.HAND);
		params['debug'] = !!params.debug;
		return params;
	}

	function moveSwf(uploader) {
		var btn = uploader.el.find('.add');
		var swf = uploader.el.find('.ui-uploader-container');
		var offset = btn.offset();
		swf.css('left', btn[0].offsetLeft).css('top', btn[0].offsetTop).width(btn.width()).height(btn.height());
	}

	function toggleBtn(uploader, selector, enabled) {
		uploader.el.find(selector).button((enabled ? 'enable' : 'disable'));
	}

	function resetFiles(uploader) {
		var rows = uploader.el.find('.ui-uploader-body tr').get();
		$.each(rows, function(index, row) {
			if (!$(row).is('.ui-helper-hidden'))
				$(row).remove();
		});
	}

	function renderFile(uploader, file) {
		var tpl = uploader.el.find('.ui-uploader-body tr.ui-helper-hidden');
		var row = tpl.clone().attr('id', file.id).removeClass('ui-helper-hidden');
		row.find('td.name div').html(file.name);
		row.find('td.size div').html(formatSize(file.size));
		row.find('a.remove').click(function() {
			uploader.removeFile(file.id);
		});
		tpl.parent().append(row);
		renderTotals(uploader);
	}

	function sortFiles(rows) {
		rows.sort(function(a, b) {
			var nameA = $(a).children('td.name').text().toLowerCase();
			var nameB = $(b).children('td.name').text().toLowerCase();
			if (nameA < nameB)
				return -1;
			if (nameA > nameB)
				return 1;
			return 0;
		});
	}

	function renderTotals(uploader) {
		var total = 0;
		$.each(uploader.files, function(id, file) {
			total += file.size;
		});
		uploader.el.find('.ui-uploader-footer .count').html(uploader.filesCount);
		uploader.el.find('.ui-uploader-footer .total').html(formatSize(total));
	}

	function renderStart(uploader, id) {
		var file = $('#' + id), body = uploader.el.find('.ui-uploader-body');
		var top = file.position().top, height = body.height();
		file.find('td.status').html($('<div class="bar"></div>').progressbar());
		if ((top+file.height()) > height)
			body.scrollTop(body.scrollTop() + ((top+file.height())-height));
	}

	function renderProgress(uploader, id, bytesLoaded, bytesTotal) {
		var value = Math.ceil((bytesLoaded/bytesTotal)*100);
		$('#' + id).find('td.status div.bar').progressbar('value', value);
	}

	function renderError(uploader, id, message) {
		$('#' + id).find('td.status').html("<a class=\"error\" href=\"#\" title='" + (message || '') + "'>&nbsp;</a>");
	}

	function renderSuccess(upload, id, message) {
		$('#' + id).find('td.status').html("<a class=\"success\" href=\"#\" title='" + (message || '') + "'>&nbsp;</a>");
	}

	function formatSize(fileSize) {
		var suffix = 'KB';
		var size = Number(fileSize);
		if (size >= 1048576) {
			size = size / 1048576;
			suffix = 'MB';
		} else {
			size = size / 1024;
		}
		return size.toFixed(2).toString() + ' ' + suffix;
	}

})(jQuery);