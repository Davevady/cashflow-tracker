@php
    // Font Awesome 5 Icons (Solid)
    $icons = [
        'fa fa-wallet', 'fa fa-money-bill-wave', 'fa fa-coins', 'fa fa-dollar-sign', 'fa fa-credit-card',
        'fa fa-university', 'fa fa-piggy-bank', 'fa fa-hand-holding-usd', 'fa fa-chart-line', 'fa fa-chart-pie',
        'fa fa-shopping-cart', 'fa fa-shopping-bag', 'fa fa-gift', 'fa fa-receipt', 'fa fa-file-invoice-dollar',
        'fa fa-home', 'fa fa-car', 'fa fa-motorcycle', 'fa fa-bus', 'fa fa-subway',
        'fa fa-utensils', 'fa fa-coffee', 'fa fa-pizza-slice', 'fa fa-hamburger', 'fa fa-ice-cream',
        'fa fa-heartbeat', 'fa fa-hospital', 'fa fa-pills', 'fa fa-syringe', 'fa fa-medkit',
        'fa fa-graduation-cap', 'fa fa-book', 'fa fa-university', 'fa fa-school', 'fa fa-chalkboard-teacher',
        'fa fa-tshirt', 'fa fa-shoe-prints', 'fa fa-hat-cowboy', 'fa fa-glasses', 'fa fa-ring',
        'fa fa-mobile-alt', 'fa fa-laptop', 'fa fa-tv', 'fa fa-keyboard', 'fa fa-mouse',
        'fa fa-gamepad', 'fa fa-futbol', 'fa fa-basketball-ball', 'fa fa-dumbbell', 'fa fa-running',
        'fa fa-plane', 'fa fa-ship', 'fa fa-hotel', 'fa fa-suitcase', 'fa fa-map-marked-alt',
        'fa fa-bolt', 'fa fa-lightbulb', 'fa fa-plug', 'fa fa-battery-full', 'fa fa-fire',
        'fa fa-wifi', 'fa fa-phone', 'fa fa-envelope', 'fa fa-comment', 'fa fa-comments',
        'fa fa-users', 'fa fa-user', 'fa fa-user-tie', 'fa fa-user-friends', 'fa fa-user-circle',
        'fa fa-briefcase', 'fa fa-building', 'fa fa-industry', 'fa fa-wrench', 'fa fa-tools',
        'fa fa-cog', 'fa fa-cogs', 'fa fa-clipboard', 'fa fa-file-alt', 'fa fa-folder',
        'fa fa-film', 'fa fa-music', 'fa fa-headphones', 'fa fa-camera', 'fa fa-video',
        'fa fa-heart', 'fa fa-star', 'fa fa-trophy', 'fa fa-crown', 'fa fa-gem',
        'fa fa-tree', 'fa fa-leaf', 'fa fa-seedling', 'fa fa-paw', 'fa fa-dog',
        'fa fa-cat', 'fa fa-fish', 'fa fa-frog', 'fa fa-horse', 'fa fa-dove',
        'fa fa-gas-pump', 'fa fa-oil-can', 'fa fa-charging-station', 'fa fa-fan', 'fa fa-temperature-high',
        'fa fa-broom', 'fa fa-soap', 'fa fa-spray-can', 'fa fa-bath', 'fa fa-shower',
        'fa fa-couch', 'fa fa-bed', 'fa fa-door-open', 'fa fa-warehouse', 'fa fa-store',
        'fa fa-paint-brush', 'fa fa-palette', 'fa fa-pen', 'fa fa-pencil-alt', 'fa fa-marker',
        'fa fa-birthday-cake', 'fa fa-cocktail', 'fa fa-wine-glass', 'fa fa-beer', 'fa fa-mug-hot',
        'fa fa-box', 'fa fa-archive', 'fa fa-truck', 'fa fa-shipping-fast', 'fa fa-dolly',
        'fa fa-handshake', 'fa fa-donate', 'fa fa-hand-holding-heart', 'fa fa-hands-helping', 'fa fa-praying-hands',
        'fa fa-rocket', 'fa fa-satellite', 'fa fa-shuttle-van', 'fa fa-tractor', 'fa fa-taxi',
        'fa fa-umbrella', 'fa fa-cloud', 'fa fa-sun', 'fa fa-moon', 'fa fa-snowflake',
        'fa fa-tag', 'fa fa-tags', 'fa fa-barcode', 'fa fa-qrcode', 'fa fa-ticket-alt',
    ];

    $inputName = $name ?? 'icon';
    $generatedId = 'icon_' . uniqid();
    $inputId = $id ?? $generatedId;
    $inputValue = old($inputName, $value ?? '');
@endphp

<div class="icon-picker-wrapper">
    <div class="d-flex align-items-center">
        <input type="text" name="{{ $inputName }}" id="{{ $inputId }}" class="form-control mr-2" placeholder="e.g. fa fa-wallet" value="{{ $inputValue }}">
        <button type="button" class="btn btn-outline-secondary open-icon-picker" data-toggle="modal" data-target="#iconPickerModal" data-icon-target="#{{ $inputId }}" data-target-input="#{{ $inputId }}">
            Pilih Icon
        </button>
    </div>
    <small class="text-muted">Gunakan Font Awesome icons (contoh: fa fa-wallet, fa fa-coins)</small>
    <div class="mt-2" id="preview_{{ $inputId }}">
        @if($inputValue)
            <i class="{{ $inputValue }}"></i>
            <small class="text-muted ml-2">Preview</small>
        @endif
    </div>
</div>

<!-- Modal Icon Picker (singleton) -->
@once
<div class="modal fade" id="iconPickerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Icon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control mb-3" id="iconSearchInput" placeholder="Cari icon...">
                <div class="row" id="iconList" style="max-height: 400px; overflow:auto;">
                    @foreach($icons as $cls)
                        <div class="col-4 mb-2">
                            <button type="button" class="btn btn-light w-100 text-center icon-item p-2" data-icon-class="{{ $cls }}" title="{{ $cls }}">
                                <i class="{{ $cls }} fa-2x"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="iconChooseBtn" disabled>Pilih Icon</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
    <script>
    (function waitForjQuery(){
        if (window.jQuery) {
            (function($){
                let targetInputSelector = null;
                let selectedIconClass = null;

                // Prefer explicit class to capture opener and remember input target
                $(document).on('click', '.open-icon-picker', function(){
                    const btn = $(this);
                    targetInputSelector = btn.data('icon-target') || btn.data('targetInput') || btn.attr('data-target-input');
                    window.__iconPickerTarget = targetInputSelector;
                });

                $('#iconPickerModal').on('show.bs.modal', function(e){
                    const btn = $(e.relatedTarget);
                    // jQuery .data() converts data-target-input to targetInput
                    targetInputSelector = window.__iconPickerTarget || (btn && (btn.data('icon-target') || btn.data('targetInput') || btn.attr('data-target-input')));
                    $('#iconSearchInput').val('');
                    $('#iconList .icon-item').show();
                    selectedIconClass = null;
                    $('#iconChooseBtn').prop('disabled', true);
                    $('#iconList .icon-item').removeClass('active');
                });

                $('#iconSearchInput').on('keyup', function(){
                    const q = $(this).val().toLowerCase();
                    $('#iconList .icon-item').each(function(){
                        const txt = $(this).data('icon-class').toLowerCase();
                        $(this).toggle(txt.indexOf(q) !== -1);
                    });
                });

                // Single click: mark selection (highlight), enable button
                $('#iconList').on('click', '.icon-item', function(){
                    $('#iconList .icon-item').removeClass('active');
                    $(this).addClass('active');
                    selectedIconClass = $(this).data('icon-class');
                    $('#iconChooseBtn').prop('disabled', !selectedIconClass);
                });

                // Double click: immediately apply and close
                $('#iconList').on('dblclick', '.icon-item', function(){
                    selectedIconClass = $(this).data('icon-class');
                    applyChosenIcon();
                });

                // Button choose: apply selection
                $('#iconChooseBtn').on('click', function(){
                    applyChosenIcon();
                });

                function applyChosenIcon(){
                    const cls = selectedIconClass;
                    if (!cls) return;
                    const selector = targetInputSelector || window.__iconPickerTarget;
                    if (selector) {
                        const $input = $(selector);
                        $input.val(cls).trigger('input').trigger('change');
                        $('#preview_' + $input.attr('id')).html('<i class="' + cls + '"></i><small class="text-muted ml-2">Preview</small>');
                        $input.focus();
                    }
                    $('#iconPickerModal').modal('hide');
                    // reset state
                    window.__iconPickerTarget = null;
                    selectedIconClass = null;
                    $('#iconChooseBtn').prop('disabled', true);
                    $('#iconList .icon-item').removeClass('active');
                }

                // Live preview when typing
                $(document).on('input change', '.icon-picker-wrapper input[type="text"]', function(){
                    const $input = $(this);
                    const cls = $input.val();
                    const $wrap = $input.closest('.icon-picker-wrapper');
                    const previewId = 'preview_' + $input.attr('id');
                    const $prev = $wrap.find('#' + previewId);
                    if (cls && (cls.startsWith('fa ') || cls.startsWith('fas ') || cls.startsWith('far ') || cls.startsWith('flaticon-') || cls.startsWith('icon-'))) {
                        $prev.html('<i class="' + cls + ' fa-2x"></i><small class="text-muted ml-2">Preview</small>');
                    } else {
                        $prev.text('');
                    }
                });
            })(window.jQuery);
        } else {
            setTimeout(waitForjQuery, 50);
        }
    })();
    </script>
@endonce

