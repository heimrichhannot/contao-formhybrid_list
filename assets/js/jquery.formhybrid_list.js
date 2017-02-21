(function($)
{
    var FORMHYBRID_LIST = {
        init: function()
        {
            this.initPagination();
            this.initMasonry();
            this.initComments();
            this.initShare();
        },
        initShare: function()
        {
            $('.formhybrid-list .share, .frontendedit-list .share').on('click', function(event)
            {
                if (typeof bootbox !== 'undefined')
                {
                    var $this = $(this);

                    event.preventDefault();

                    $.get($this.attr('href'), function(url)
                    {
                        bootbox.alert($this.data('message') + '<br><br><textarea onclick="this.select()" class="form-control">' + url + '</textarea>');
                    });
                }
            });
        },
        initPagination: function()
        {
            $('.formhybrid-list .ajax-pagination').each(function()
            {
                var $list = $(this).closest('.formhybrid-list'),
                    $items = $list.find('.items'),
                    id = '#' + $list.attr('id');

                $list.jscroll({
                    loadingHtml: '<div class="loading">Lade...</div>',
                    nextSelector: '.ajax-pagination a.next',
                    autoTrigger: $list.data('infinitescroll') == 1,
                    contentSelector: id,
                    callback: function()
                    {
                        var $jscrollAdded = $(this),
                            $newItems = $jscrollAdded.find('.item');

                        $newItems.hide();

                        $jscrollAdded.imagesLoaded( function() {
                            $items.append($newItems.fadeIn(300));

                            if ($list.attr('data-fhl-masonry') == 1)
                            {
                                $items.masonry('appended', $newItems);

                                // whyever not items, but works...
                                $list.masonry();
                            }

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
                        });
                    }
                });
            });
        },
        initMasonry: function()
        {
            $('.formhybrid-list[data-fhl-masonry="1"]').each(function()
            {
                var $this = $(this).find('.items');

                var $grid = $this.imagesLoaded( function() {
                    $grid.masonry({
                        // fitWidth: true,
                        itemSelector: '.item'
                    });

                    $grid.masonry('stamp', $grid.find('.stamp-item'));

                    // update due to stamps
                    $grid.masonry();
                });
            });
        },
        initComments: function()
        {
            $('body').on('submit', '.formhybrid-reader .ce_comments .form form', function(e)
            {
                var $form = $(this),
                    $wrapper = $form.closest('.formhybrid-reader');

                $form.find('input[type="submit"]').addClass('disabled').attr('disabled', 'disabled');

                e.preventDefault();

                $.ajax({
                    type: $form.attr('method'),
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    success: function(data)
                    {
                        // thanks to contao comments module we must call the page again
                        $.ajax({
                            type: $form.attr('method'),
                            url: $form.attr('action'),
                            success: function(data)
                            {
                                console.log(data);
                                $wrapper.replaceWith($(data).find('.formhybrid-reader'));
                            }
                        });
                    }
                });
            });

            $('body').on('submit', '.formhybrid-reader .ce_comments .actions form', function(e)
            {
                var $form = $(this),
                    $wrapper = $form.closest('.formhybrid-reader');

                $form.find('button.delete').addClass('disabled').attr('disabled', 'disabled');

                e.preventDefault();

                $.ajax({
                    type: $form.attr('method'),
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    success: function(data)
                    {
                        $form.closest('.comment_formhybrid_list').fadeOut().remove();
                    }
                });
            });
        }
    };

    $(document).ready(function()
    {
        FORMHYBRID_LIST.init();
    });
})(jQuery);