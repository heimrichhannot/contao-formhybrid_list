(function($)
{
    FORMHYBRID_LIST = {
        init: function()
        {
            this.initPagination();
            this.initMasonry();
            this.initComments();
            this.initShare();
            this.initProximitySearch();
        },
        initProximitySearch: function()
        {
            $('.formhybrid-list[data-fhl-prox-search="1"], .frontendedit-list[data-fhl-prox-search="1"]').each(function()
            {
                var $wrapper = $(this),
                    $form = $wrapper.find('form'),
                    $useLocation = $form.find('input[name="proxUseLocation"]');

                FORMHYBRID_LIST.initProximitySearchFields($wrapper);

                $useLocation.on('click', function()
                {
                    if ($useLocation.is(':checked'))
                    {
                        HASTE_PLUS.getCurrentLocation(function(lat, lng)
                        {
                            $form.find('input[name="proxLocation"]').val(lat + ',' + lng);
                        });
                    }
                    else
                    {
                        $form.find('input[name="proxLocation"]').val('');
                    }

                    FORMHYBRID_LIST.initProximitySearchFields($wrapper);
                });
            });
        },
        initProximitySearchFields: function($wrapper)
        {
            // if current location is set, postal, city and country aren't relevant anymore
            var fields = [],
                $form = $wrapper.find('form'),
                $checkbox = $form.find('input[name="proxUseLocation"]');

            if ($wrapper.data('fhlProxSearchCity'))
            {
                fields.push($wrapper.data('fhlProxSearchCity'));
            }

            if ($wrapper.data('fhlProxSearchPostal'))
            {
                fields.push($wrapper.data('fhlProxSearchPostal'));
            }

            if ($wrapper.data('fhlProxSearchState'))
            {
                fields.push($wrapper.data('fhlProxSearchState'));
            }

            if ($wrapper.data('fhlProxSearchCountry'))
            {
                fields.push($wrapper.data('fhlProxSearchCountry'));
            }

            $.each(fields, function(key, field)
            {
                if ($checkbox.is(':checked'))
                {
                    $form.find('.form-group.' + field).hide().find('input, select').val('');
                }
                else
                {
                    $form.find('.form-group.' + field).show();
                }
            });
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
            $('.formhybrid-list, .frontendedit-list').find('.ajax-pagination').each(function()
            {
                var $list = $(this).closest('.formhybrid-list, .frontendedit-list'),
                    $items = $list.find('.items'),
                    id = '#' + $list.attr('id');

                $list.jscroll({
                    loadingHtml: '<div class="loading"><span>Lade...</span></div>',
                    nextSelector: '.ajax-pagination a.next',
                    autoTrigger: $list.data('infinitescroll') == 1,
                    contentSelector: id,
                    callback: function()
                    {
                        var $jscrollAdded = $(this),
                            $newItems = $jscrollAdded.find('.item');

                        $newItems.hide();

                        $jscrollAdded.imagesLoaded(function()
                        {
                            $items.append($newItems.fadeIn(300));

                            if ($list.attr('data-fhl-masonry') == 1)
                            {
                                $items.masonry('appended', $newItems);

                                // whyever not items, but works...
                                $list.masonry();
                            }

                            // remove item counters...
                            $items.find('.item').removeClass(function(index, cssClass)
                            {
                                var matches = cssClass.match(/item_\d+/g);

                                if (matches.length > 0)
                                {
                                    return matches[0];
                                }
                            });

                            //... and readd them again
                            $items.find('.item').each(function(index)
                            {
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
            $('.formhybrid-list[data-fhl-masonry="1"], .frontendedit-list[data-fhl-masonry="1"]').each(function()
            {
                var $this = $(this).find('.items'),
                    options = $(this).data('masonry-options');

                var $grid = $this.imagesLoaded(function()
                {
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