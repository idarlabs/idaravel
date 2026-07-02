@php
    $isPostMethod = ($method !== 'GET');
@endphp

<form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" {{ $attributes }}>
    @if($isPostMethod)
        @csrf
        @if(in_array($method, ['PUT', 'PATCH', 'DELETE']))
            @method($method)
        @endif
    @endif

    @foreach($form as $name => $config)
        @php
            $item = $config['value'] ?? null;
            $label = $config['label'] ?? '';
            $col = $config['col'] ?? 'col-12';
            $wrapperClass = $config['wrapper_class'] ?? 'form-group';
            
            if (!$item) continue;

            $cleanId = str_replace(['[', ']'], '', $name);

            $type = $item['input_type'] ?? '';
            $bootstrapClass = 'form-control';
            
            if (($item['_type'] ?? '') === 'idar-select') {
                $bootstrapClass = 'form-select';
            } elseif (in_array($type, ['radio', 'checkbox'])) {
                $bootstrapClass = 'form-check-input';
            } elseif (($item['_type'] ?? '') === 'idar-button') {
                $bootstrapClass = 'btn btn-primary';
            }

            if (isset($item['attr']['class'])) {
                $bootstrapClass = $item['attr']['class'];
            }

            $attrString = '';
            $attributesArray = $item['attr'] ?? [];
            $attributesArray['class'] = $bootstrapClass;

            foreach ($attributesArray as $key => $val) {
                if (in_array(strtolower($key), ['required', 'readonly', 'disabled', 'multiple', 'checked', 'selected'])) {
                    if ($val) {
                        $attrString .= " {$key}";
                    }
                } else {
                    $attrString .= " {$key}=\"{$val}\"";
                }
            }
        @endphp

        @if(($item['_type'] ?? '') === 'idar-button')
            <div class="{{ $col }} mt-4">
                <button type="submit" name="{{ $name }}" {!! $attrString !!}>
                    {{ $item['text'] }}
                </button>
            </div>

        @elseif(($item['_type'] ?? '') === 'idar-input')
            @php
                $data = $item['data'] ?? [];
            @endphp

            @if($type === 'hidden')
                <input type="hidden" name="{{ $name }}" id="{{ $cleanId }}" value="{{ old($name) }}" {!! $attrString !!}>
            @else
                <div class="{{ $col }}">
                    <div class="{{ $wrapperClass }}">
                        @if($label && !in_array($type, ['radio', 'checkbox']))
                            <label for="{{ $cleanId }}" class="form-label">{{ $label }}</label>
                        @endif

                        @if($type === 'select')
                            <select name="{{ $name }}" id="{{ $cleanId }}" {!! $attrString !!}>
                                <option value="">-- Pilih --</option>
                                @foreach($data as $key => $val)
                                    <option value="{{ $key }}" {{ old($name) == $key ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>

                        @elseif($type === 'textarea')
                            <textarea name="{{ $name }}" id="{{ $cleanId }}" {!! $attrString !!}>{{ old($name) }}</textarea>

                        @elseif($type === 'radio')
                            @if($label)
                                <label class="form-label d-block">{{ $label }}</label>
                            @endif
                            @foreach($data as $key => $val)
                                <div class="form-check form-check-inline">
                                    <input type="radio" name="{{ $name }}" id="{{ $cleanId . '_' . $key }}" value="{{ $key }}" {{ old($name) == $key ? 'checked' : '' }} {!! $attrString !!}>
                                    <label class="form-check-label" for="{{ $cleanId . '_' . $key }}">{{ $val }}</label>
                                </div>
                            @endforeach

                        @elseif($type === 'checkbox')
                            <div class="form-check">
                                <input type="checkbox" name="{{ $name }}" id="{{ $cleanId }}" value="1" {{ old($name) == 1 ? 'checked' : '' }} {!! $attrString !!}>
                                @if($label)
                                    <label class="form-check-label" for="{{ $cleanId }}">{{ $label }}</label>
                                @endif
                            </div>

                        @else
                            <input type="{{ $type }}" name="{{ $name }}" id="{{ $cleanId }}" value="{{ old($name) }}" {!! $attrString !!}>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    @endforeach

    {{ $slot }}
</form>