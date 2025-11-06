/**
 * CashFlow Tracker - Custom Table Handler
 * Auto-apply responsive table with text truncation and modal detail
 */

(function($) {
    'use strict';

    /**
     * Configuration
     */
    const CONFIG = {
        maxLength: 25,          // Default max characters before truncate
        tooltipDelay: 300,      // Tooltip show delay (ms)
        scrollShadowClass: {
            left: 'has-scroll-left',
            right: 'has-scroll-right'
        }
    };

    /**
     * Truncate text if longer than maxLength
     * @param {string} text - Original text
     * @param {number} maxLength - Maximum length
     * @returns {string} - Truncated text
     */
    function truncateText(text, maxLength) {
        if (!text) return '';
        text = text.toString().trim();
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

    /**
     * Check if element is truncated
     * @param {jQuery} $element - jQuery element
     * @returns {boolean}
     */
    function isTextTruncated($element) {
        const element = $element[0];
        return element.scrollWidth > element.clientWidth;
    }

    /**
     * Create text detail modal
     */
    function createTextDetailModal() {
        if ($('#textDetailModal').length) return;

        const modalHtml = `
            <div class="modal fade text-detail-modal" id="textDetailModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="detail-label">Content:</div>
                            <div class="detail-value" id="textDetailContent"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modalHtml);
    }

    /**
     * Show text detail modal
     * @param {string} title - Modal title
     * @param {string} content - Full content
     */
    function showTextDetailModal(title, content) {
        const $modal = $('#textDetailModal');
        $modal.find('.modal-title').text(title);
        $modal.find('#textDetailContent').text(content);
        $modal.modal('show');
    }

    /**
     * Apply truncate to table cells
     * @param {jQuery} $table - jQuery table element
     */
    function applyTruncateToTable($table) {
        // Target columns that typically have long text
        const targetSelectors = [
            'td:has(.truncate-text)',  // Already marked
            'td:not(.col-action):not(.col-id):not(.col-status):not(.col-icon)'  // Auto-detect
        ];

        $table.find('tbody tr').each(function() {
            const $row = $(this);

            $row.find('td').each(function(index) {
                const $cell = $(this);

                // Skip if already processed
                if ($cell.data('truncate-processed')) return;

                // Skip action columns, icons, small content
                if ($cell.hasClass('col-action') ||
                    $cell.hasClass('col-icon') ||
                    $cell.hasClass('col-id') ||
                    $cell.hasClass('col-status')) {
                    return;
                }

                // Get cell text (skip if has input/button/badge)
                if ($cell.find('input, button, select, textarea, .badge').length > 0) {
                    return;
                }

                const originalText = $cell.text().trim();

                // Skip empty or short text
                if (!originalText || originalText.length <= 5) return;

                // Determine max length based on column class
                let maxLength = CONFIG.maxLength;
                if ($cell.hasClass('col-description') || $cell.hasClass('truncate-lg')) {
                    maxLength = 40;
                } else if ($cell.hasClass('col-name') || $cell.hasClass('truncate-md')) {
                    maxLength = 30;
                } else if ($cell.hasClass('truncate-sm')) {
                    maxLength = 15;
                } else if ($cell.hasClass('truncate-xl')) {
                    maxLength = 50;
                }

                // Check if text needs truncation
                if (originalText.length > maxLength) {
                    const truncated = truncateText(originalText, maxLength);
                    const columnName = $table.find('thead th').eq(index).text().trim() || 'Detail';

                    // Wrap in span with truncate class
                    const $span = $('<span>')
                        .addClass('truncate-text is-truncated')
                        .text(truncated)
                        .attr('data-original', originalText)
                        .attr('data-column', columnName)
                        .attr('title', 'Click to view full text')
                        .on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            showTextDetailModal(columnName, originalText);
                        });

                    // Replace cell content
                    $cell.html($span);

                    // Initialize Bootstrap tooltip
                    $span.tooltip({
                        title: originalText,
                        placement: 'top',
                        trigger: 'hover',
                        delay: { show: CONFIG.tooltipDelay, hide: 100 },
                        boundary: 'window'
                    });
                }

                $cell.data('truncate-processed', true);
            });
        });
    }

    /**
     * Wrap table with responsive container
     * @param {jQuery} $table - jQuery table element
     */
    function makeTableResponsive($table) {
        // Skip if already wrapped
        if ($table.parent().hasClass('table-responsive-custom')) {
            return $table.parent();
        }

        // Add custom table class
        if (!$table.hasClass('table-custom')) {
            $table.addClass('table-custom');
        }

        // Wrap with responsive container
        const $wrapper = $('<div>')
            .addClass('table-responsive-custom')
            .attr('data-table-wrapper', 'true');

        $table.wrap($wrapper);
        return $table.parent();
    }

    /**
     * Update scroll shadow indicators
     * @param {jQuery} $wrapper - Table wrapper element
     */
    function updateScrollShadow($wrapper) {
        const wrapper = $wrapper[0];
        const scrollLeft = wrapper.scrollLeft;
        const scrollWidth = wrapper.scrollWidth;
        const clientWidth = wrapper.clientWidth;
        const scrollRight = scrollWidth - clientWidth - scrollLeft;

        // Left shadow
        if (scrollLeft > 5) {
            $wrapper.addClass(CONFIG.scrollShadowClass.left);
        } else {
            $wrapper.removeClass(CONFIG.scrollShadowClass.left);
        }

        // Right shadow
        if (scrollRight > 5) {
            $wrapper.addClass(CONFIG.scrollShadowClass.right);
        } else {
            $wrapper.removeClass(CONFIG.scrollShadowClass.right);
        }
    }

    /**
     * Apply column classes based on content
     * @param {jQuery} $table - jQuery table element
     */
    function applyColumnClasses($table) {
        // Skip if already applied
        if ($table.data('columns-classified')) return;

        const $headers = $table.find('thead th');
        const $rows = $table.find('tbody tr');

        $headers.each(function(index) {
            const headerText = $(this).text().toLowerCase().trim();
            let columnClass = '';

            // Detect column type by header text
            if (headerText.match(/^(no|#|id)$/)) {
                columnClass = 'col-id';
            } else if (headerText.match(/(action|aksi|tindakan)/)) {
                columnClass = 'col-action';
            } else if (headerText.match(/(date|tanggal|waktu|time|created|updated)/)) {
                columnClass = 'col-date';
            } else if (headerText.match(/(status|state)/)) {
                columnClass = 'col-status';
            } else if (headerText.match(/(amount|jumlah|saldo|balance|total|harga|price)/)) {
                columnClass = 'col-amount';
            } else if (headerText.match(/(name|nama|title|judul)/)) {
                columnClass = 'col-name';
            } else if (headerText.match(/(category|kategori|type|tipe|group|grup)/)) {
                columnClass = 'col-category';
            } else if (headerText.match(/(description|deskripsi|note|catatan|keterangan)/)) {
                columnClass = 'col-description';
            } else if (headerText.match(/(icon|ikon)/)) {
                columnClass = 'col-icon';
            }

            if (columnClass) {
                $(this).addClass(columnClass);
                $rows.each(function() {
                    $(this).find('td').eq(index).addClass(columnClass);
                });
            }
        });

        $table.data('columns-classified', true);
    }

    /**
     * Initialize table enhancements
     * @param {jQuery} $table - jQuery table element
     */
    function initTable($table) {
        // Skip if not a valid table
        if (!$table.is('table') || $table.data('table-initialized')) {
            return;
        }

        // Apply column classes first
        applyColumnClasses($table);

        // Make table responsive
        const $wrapper = makeTableResponsive($table);

        // Apply text truncation
        applyTruncateToTable($table);

        // Setup scroll shadow
        updateScrollShadow($wrapper);
        $wrapper.on('scroll', function() {
            updateScrollShadow($(this));
        });

        // Mark as initialized
        $table.data('table-initialized', true);

        // Re-check scroll shadow after images/content load
        setTimeout(() => updateScrollShadow($wrapper), 500);
    }

    /**
     * Initialize all tables on page
     */
    function initAllTables() {
        // Create modal once
        createTextDetailModal();

        // Find all tables (exclude specific ones)
        $('table').not('.no-custom-table, .calendar-table').each(function() {
            initTable($(this));
        });
    }

    /**
     * Refresh table (re-apply truncation)
     * @param {jQuery} $table - jQuery table element
     */
    window.refreshTable = function($table) {
        if (!$table || !$table.length) {
            initAllTables();
            return;
        }

        $table.removeData('table-initialized');
        $table.find('td').removeData('truncate-processed');
        $table.find('.truncate-text').each(function() {
            const original = $(this).data('original');
            if (original) {
                $(this).parent().text(original);
            }
        });

        initTable($table);
    };

    /**
     * Public API
     */
    window.TableCustom = {
        init: initAllTables,
        refresh: window.refreshTable,
        truncate: truncateText,
        config: CONFIG
    };

    /**
     * Auto-initialize on document ready
     */
    $(document).ready(function() {
        initAllTables();

        // Re-initialize when modal is shown (for dynamic content)
        $(document).on('shown.bs.modal', '.modal', function() {
            const $modal = $(this);
            setTimeout(() => {
                $modal.find('table').each(function() {
                    if (!$(this).data('table-initialized')) {
                        initTable($(this));
                    }
                });
            }, 100);
        });

        // Re-initialize on window resize (debounced)
        let resizeTimeout;
        $(window).on('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                $('.table-responsive-custom').each(function() {
                    updateScrollShadow($(this));
                });
            }, 250);
        });

        // Handle dynamic content (AJAX, etc)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    $(mutation.addedNodes).find('table').each(function() {
                        if (!$(this).data('table-initialized')) {
                            setTimeout(() => initTable($(this)), 100);
                        }
                    });
                }
            });
        });

        // Observe body for changes
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

})(jQuery);
