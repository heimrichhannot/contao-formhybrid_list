(function ($) {
	var FORMHYBRID_LIST = {
		init: function ()
		{
			this.initPagination();
			this.initComments();
		},
		initPagination: function()
		{
			$('.formhybrid-list .ajax-pagination').each(function() {
				var $list = $(this).closest('.formhybrid-list'),
					$items = $list.find('.items'),
					id = '#' + $list.attr('id');

				$list.jscroll({
					loadingHtml: '<div class="loading">Loading</div>',
					nextSelector: '.ajax-pagination a.next',
					autoTrigger: $list.data('infinitescroll') == 1,
					contentSelector: id,
					callback: function()
					{
						var $jscrollAdded = $(this);

						$jscrollAdded.find('.item').appendTo($items);

						// remove item counters...
						$items.find('.item').removeClass(function(index, cssClass) {
							var matches = cssClass.match(/item_\d+/g);

							if (matches.length > 0)
								return matches[0];
						});

						//... and readd them again
						$items.find('.item').each(function(index) {
							var $item = $(this);

							$(this).addClass('item_' + index).removeClass('odd even first last');

							// odd/even
							if (index % 2 == 0)
							{
								$item.addClass('even');
							}
							else
							{
								$item.addClass('odd');
							}

							// add first and last
							if (index == 0)
							{
								$item.addClass('first');
							}

							if (index == $items.find('.item').length - 1)
							{
								$item.addClass('last');
							}
						});

						$jscrollAdded.find('.ajax-pagination').appendTo($jscrollAdded.closest('.jscroll-inner'));
						$jscrollAdded.remove();
					}
				});
			});
		},
		initComments: function() {
			$('body').on('submit', '.formhybrid-reader .ce_comments .form form', function(e) {
				var $form = $(this),
					$wrapper = $form.closest('.formhybrid-reader');

				$form.find('input[type="submit"]').addClass('disabled').attr('disabled', 'disabled');

				e.preventDefault();

				$.ajax({
					type : $form.attr('method'),
					url : $form.attr('action'),
					data: $form.serialize(),
					success: function(data) {
						// thanks to contao comments module we must call the page again
						$.ajax({
							type: $form.attr('method'),
							url: $form.attr('action'),
							success: function (data) {
								console.log(data);
								$wrapper.replaceWith($(data).find('.formhybrid-reader'));
							}
						});
					}
				});
			});

			$('body').on('submit', '.formhybrid-reader .ce_comments .actions form', function(e) {
				var $form = $(this),
					$wrapper = $form.closest('.formhybrid-reader');

				$form.find('button.delete').addClass('disabled').attr('disabled', 'disabled');

				e.preventDefault();

				$.ajax({
					type : $form.attr('method'),
					url : $form.attr('action'),
					data: $form.serialize(),
					success: function(data) {
						$form.closest('.comment_formhybrid_list').fadeOut().remove();
					}
				});
			});
		}
	};

	$(document).ready(function () {
		FORMHYBRID_LIST.init();
	});
})(jQuery);