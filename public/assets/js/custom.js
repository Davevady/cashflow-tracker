/**
 * CashFlow Tracker - Custom JavaScript
 * Format Rupiah dan utility functions
 */

(function($) {
    'use strict';

    /**
     * Format angka ke format Rupiah
     * @param {number} angka - Angka yang akan diformat
     * @param {string} prefix - Prefix (default: 'Rp ')
     * @returns {string} - String dengan format Rupiah
     */
    window.formatRupiah = function(angka, prefix = 'Rp ') {
        const numberString = angka.toString().replace(/[^,\d]/g, '');
        const split = numberString.split(',');
        const sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix + rupiah;
    };

    /**
     * Parse format Rupiah ke angka
     * @param {string} rupiah - String format Rupiah
     * @returns {number} - Angka murni
     */
    window.parseRupiah = function(rupiah) {
        return parseInt(rupiah.replace(/[^0-9]/g, '')) || 0;
    };

    /**
     * Auto format input amount ke format Rupiah
     */
    window.initRupiahFormat = function() {
        // Selector untuk semua input amount
        const rupiahInputs = $('input[name="amount"], .rupiah-input');

        rupiahInputs.each(function() {
            const $input = $(this);

            // Simpan nilai asli di data attribute
            if ($input.val() && !$input.data('original-value')) {
                $input.data('original-value', $input.val());
            }

            // Format nilai awal jika ada
            if ($input.val()) {
                const formatted = formatRupiah($input.val(), '');
                $input.val(formatted);
            }

            // Event ketika input
            $input.on('keyup', function(e) {
                let value = $input.val();

                // Format value
                const formatted = formatRupiah(value, '');
                $input.val(formatted);
            });

            // Event ketika focus (untuk clear prefix)
            $input.on('focus', function() {
                $input.addClass('rupiah-focused');
            });

            // Event ketika blur (untuk validasi)
            $input.on('blur', function() {
                $input.removeClass('rupiah-focused');
                if (!$input.val()) {
                    $input.val('');
                }
            });
        });

        // Intercept form submit untuk convert ke angka murni
        $('form').on('submit', function(e) {
            const $form = $(this);

            // Find all rupiah inputs in this form
            $form.find('input[name="amount"], .rupiah-input').each(function() {
                const $input = $(this);
                const rawValue = parseRupiah($input.val());

                // Create hidden input dengan nilai asli
                const $hidden = $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', $input.attr('name'))
                    .val(rawValue);

                // Rename original input to prevent submission
                $input.attr('name', $input.attr('name') + '_display');

                // Append hidden input
                $form.append($hidden);
            });
        });
    };

    /**
     * Format angka dengan pemisah ribuan tanpa Rp
     * @param {string|number} value - Nilai yang akan diformat
     * @returns {string} - String dengan pemisah ribuan
     */
    window.formatNumber = function(value) {
        return formatRupiah(value, '');
    };

    /**
     * Auto format number inputs (non-rupiah)
     */
    window.initNumberFormat = function() {
        $('.number-input').on('keyup', function() {
            const $input = $(this);
            const formatted = formatNumber($input.val());
            $input.val(formatted);
        });
    };

    /**
     * Initialize all formatters
     */
    $(document).ready(function() {
        // Initialize Rupiah format
        initRupiahFormat();

        // Initialize Number format
        initNumberFormat();

        // Re-initialize when modal is shown
        $('.modal').on('shown.bs.modal', function() {
            initRupiahFormat();
        });

        // Handle dynamic content
        $(document).on('DOMNodeInserted', function(e) {
            if ($(e.target).is('input[name="amount"], .rupiah-input')) {
                initRupiahFormat();
            }
        });
    });

})(jQuery);
