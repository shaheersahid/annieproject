(function ($) {
    'use strict';

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function bindLiveSearch($input) {
        if (!$input.length) {
            return;
        }

        var $form = $input.closest('form');
        var $wrapper = $input.closest('.header-search-wrapper, .mobile-search, .live-search-field');
        if (!$wrapper.length) {
            $wrapper = $form;
        }

        $wrapper.addClass('live-search-host');
        $wrapper.css('position', 'relative');

        var $dropdown = $('<div class="live-search-results" hidden></div>');
        $wrapper.append($dropdown);

        var timer = null;
        var lastQuery = '';
        var activeIndex = -1;
        var suggestUrl = $input.data('suggest-url') || '/products/suggest';

        function hideResults() {
            $dropdown.attr('hidden', true).empty();
            activeIndex = -1;
        }

        function renderProducts(payload, query) {
            var products = payload.products || [];
            if (!products.length) {
                $dropdown.html(
                    '<div class="live-search-empty">No deals found for “' + escapeHtml(query) + '”</div>' +
                    '<a class="live-search-view-all" href="' + escapeHtml(payload.view_all_url || '#') + '">Search all deals</a>'
                ).removeAttr('hidden');
                return;
            }

            var html = products.map(function (product, index) {
                return (
                    '<a class="live-search-item" href="' + escapeHtml(product.url) + '" data-index="' + index + '">' +
                        '<img src="' + escapeHtml(product.image) + '" alt="" loading="lazy">' +
                        '<span class="live-search-meta">' +
                            '<span class="live-search-name">' + escapeHtml(product.name) + '</span>' +
                            (product.category ? '<span class="live-search-category">' + escapeHtml(product.category) + '</span>' : '') +
                            '<span class="live-search-price">' + escapeHtml(product.price) + '</span>' +
                        '</span>' +
                    '</a>'
                );
            }).join('');

            html += '<a class="live-search-view-all" href="' + escapeHtml(payload.view_all_url || '#') + '">View all results</a>';
            $dropdown.html(html).removeAttr('hidden');
            activeIndex = -1;
        }

        function setActive(nextIndex) {
            var $items = $dropdown.find('.live-search-item');
            if (!$items.length) {
                return;
            }

            activeIndex = (nextIndex + $items.length) % $items.length;
            $items.removeClass('is-active');
            $items.eq(activeIndex).addClass('is-active');
        }

        function fetchSuggestions(query) {
            if (query.length < 2) {
                lastQuery = '';
                hideResults();
                return;
            }

            if (query === lastQuery && !$dropdown.attr('hidden') && $dropdown.children().length) {
                return;
            }

            lastQuery = query;
            $dropdown.html('<div class="live-search-empty">Searching…</div>').removeAttr('hidden');

            $.getJSON(suggestUrl, { q: query })
                .done(function (payload) {
                    if ($input.val().trim() !== query) {
                        return;
                    }
                    renderProducts(payload || {}, query);
                })
                .fail(function () {
                    $dropdown.html('<div class="live-search-empty">Search unavailable. Try again.</div>').removeAttr('hidden');
                });
        }

        $input.on('input', function () {
            var query = $.trim($input.val());
            clearTimeout(timer);
            timer = setTimeout(function () {
                fetchSuggestions(query);
            }, 220);
        });

        $input.on('keydown', function (event) {
            var $items = $dropdown.find('.live-search-item');

            if (event.key === 'ArrowDown' && $items.length) {
                event.preventDefault();
                setActive(activeIndex + 1);
            } else if (event.key === 'ArrowUp' && $items.length) {
                event.preventDefault();
                setActive(activeIndex - 1);
            } else if (event.key === 'Enter' && activeIndex > -1) {
                var href = $items.eq(activeIndex).attr('href');
                if (href) {
                    event.preventDefault();
                    window.location.href = href;
                }
            } else if (event.key === 'Escape') {
                hideResults();
            }
        });

        $input.on('focus', function () {
            var query = $.trim($input.val());
            if (query.length >= 2 && $dropdown.children().length) {
                $dropdown.removeAttr('hidden');
            }
        });

        $(document).on('click.liveSearch', function (event) {
            if (!$(event.target).closest($wrapper).length) {
                hideResults();
            }
        });
    }

    $(function () {
        bindLiveSearch($('#q'));
        bindLiveSearch($('#mobile-search'));
        bindLiveSearch($('#list-search'));
    });
})(jQuery);
