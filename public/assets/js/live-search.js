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
        if (!$input.length || $input.data('live-search-bound')) {
            return;
        }

        $input.data('live-search-bound', true);

        var $form = $input.closest('form');
        var $wrapper = $input.closest('.header-search-wrapper, .mobile-search, .live-search-field');
        if (!$wrapper.length) {
            $wrapper = $form.length ? $form : $input.parent();
        }

        $wrapper.addClass('live-search-host').css({
            position: 'relative',
            overflow: 'visible'
        });

        var $dropdown = $('<div class="live-search-results" hidden></div>').css({
            position: 'absolute',
            top: 'calc(100% + 4px)',
            left: '0',
            right: '0',
            zIndex: '9999',
            maxHeight: '320px',
            overflowY: 'auto',
            background: '#fff',
            border: '1px solid #e5edf1',
            borderRadius: '6px',
            boxShadow: '0 12px 28px rgba(15, 23, 42, 0.12)'
        });
        $wrapper.append($dropdown);

        var timer = null;
        var lastQuery = '';
        var activeIndex = -1;
        var suggestUrl = $input.attr('data-suggest-url') || $input.data('suggest-url') || '/products/suggest';
        var request = null;

        function hideResults() {
            $dropdown.attr('hidden', true).empty();
            activeIndex = -1;
        }

        function renderProducts(payload, query) {
            var products = payload.products || [];
            if (!products.length) {
                $dropdown.html(
                    '<div class="live-search-empty" style="padding:10px 11px;font-size:13px;color:#6b7b84;">No deals found for “' + escapeHtml(query) + '”</div>' +
                    '<a class="live-search-view-all" style="display:block;padding:10px 11px;font-size:13px;color:#0097b2;font-weight:600;text-decoration:none;border-top:1px solid #f0f4f7;" href="' + escapeHtml(payload.view_all_url || '#') + '">Search all deals</a>'
                ).removeAttr('hidden');
                return;
            }

            var html = products.map(function (product, index) {
                return (
                    '<a class="live-search-item" href="' + escapeHtml(product.url) + '" data-index="' + index + '" style="display:flex;align-items:center;gap:10px;padding:9px 11px;color:inherit;text-decoration:none;border-bottom:1px solid #f0f4f7;">' +
                        '<img src="' + escapeHtml(product.image) + '" alt="" loading="lazy" style="width:44px;height:44px;object-fit:cover;border-radius:4px;flex:0 0 44px;background:#f3f6f8;">' +
                        '<span class="live-search-meta" style="display:flex;flex-direction:column;gap:2px;min-width:0;">' +
                            '<span class="live-search-name" style="font-size:13px;font-weight:600;color:#24333a;line-height:1.35;">' + escapeHtml(product.name) + '</span>' +
                            (product.category ? '<span class="live-search-category" style="font-size:12px;color:#6b7b84;">' + escapeHtml(product.category) + '</span>' : '') +
                            '<span class="live-search-price" style="font-size:12px;color:#0097b2;font-weight:600;">' + escapeHtml(product.price) + '</span>' +
                        '</span>' +
                    '</a>'
                );
            }).join('');

            html += '<a class="live-search-view-all" style="display:block;padding:10px 11px;font-size:13px;color:#0097b2;font-weight:600;text-decoration:none;border-top:1px solid #f0f4f7;" href="' + escapeHtml(payload.view_all_url || '#') + '">View all results</a>';
            $dropdown.html(html).removeAttr('hidden');
            activeIndex = -1;
        }

        function setActive(nextIndex) {
            var $items = $dropdown.find('.live-search-item');
            if (!$items.length) {
                return;
            }

            activeIndex = (nextIndex + $items.length) % $items.length;
            $items.css('background', '').removeClass('is-active');
            $items.eq(activeIndex).addClass('is-active').css('background', '#f4fafb');
        }

        function fetchSuggestions(query) {
            if (query.length < 2) {
                lastQuery = '';
                if (request && request.abort) {
                    request.abort();
                }
                hideResults();
                return;
            }

            if (query === lastQuery && !$dropdown.attr('hidden') && $dropdown.children().length) {
                return;
            }

            lastQuery = query;
            $dropdown.html('<div class="live-search-empty" style="padding:10px 11px;font-size:13px;color:#6b7b84;">Searching…</div>').removeAttr('hidden');

            if (request && request.abort) {
                request.abort();
            }

            request = $.getJSON(suggestUrl, { q: query })
                .done(function (payload) {
                    if ($input.val().trim() !== query) {
                        return;
                    }
                    renderProducts(payload || {}, query);
                })
                .fail(function (xhr) {
                    if (xhr && xhr.statusText === 'abort') {
                        return;
                    }
                    $dropdown.html('<div class="live-search-empty" style="padding:10px 11px;font-size:13px;color:#6b7b84;">Search unavailable. Try again.</div>').removeAttr('hidden');
                });
        }

        $input.on('input', function () {
            var query = $.trim($input.val());
            clearTimeout(timer);
            timer = setTimeout(function () {
                fetchSuggestions(query);
            }, 180);
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
            if (query.length >= 2) {
                if ($dropdown.children().length && !$dropdown.attr('hidden')) {
                    $dropdown.removeAttr('hidden');
                } else {
                    fetchSuggestions(query);
                }
            }
        });

        $(document).on('click.liveSearch.' + $input.attr('id'), function (event) {
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
