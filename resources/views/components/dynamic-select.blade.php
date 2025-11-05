@props([
    'name',
    'label',
    'options',
    'selected' => null,
    'required' => false,
    'placeholder' => '-- Pilih --',
    'addNewText' => 'Tambah Baru',
    'addNewUrl' => null,
    'addNewModal' => null,
    'groupBy' => null,
])

<div class="form-group">
    <label for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>

    <div class="input-group">
        <select name="{{ $name }}" id="{{ $name }}" class="form-control {{ $errors->has($name) ? 'is-invalid' : '' }}" {{ $required ? 'required' : '' }}>
            <option value="">{{ $placeholder }}</option>

            @if($groupBy)
                {{-- Grouped options --}}
                @foreach($options->groupBy($groupBy) as $groupName => $groupedOptions)
                    <optgroup label="{{ $groupName }}">
                        @foreach($groupedOptions as $option)
                            <option value="{{ $option->id }}" {{ old($name, $selected) == $option->id ? 'selected' : '' }}>
                                {{ $option->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            @else
                {{-- Non-grouped options --}}
                @foreach($options as $option)
                    <option value="{{ $option->id }}" {{ old($name, $selected) == $option->id ? 'selected' : '' }}>
                        {{ $option->name }}
                    </option>
                @endforeach
            @endif
        </select>

        @if($addNewModal)
            <div class="input-group-append">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#{{ $addNewModal }}" title="{{ $addNewText }}">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        @elseif($addNewUrl)
            <div class="input-group-append">
                <a href="{{ $addNewUrl }}" class="btn btn-primary btn-sm" title="{{ $addNewText }}" target="_blank">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
        @endif
    </div>

    @error($name)
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>
