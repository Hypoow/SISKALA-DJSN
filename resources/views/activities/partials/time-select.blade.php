@php
    $fieldName = $name;
    $fieldId = $id ?? $fieldName;
    $fieldValue = (string) old($fieldName, $value ?? '');
    $fieldValue = trim($fieldValue);

    $selectedHour = '';
    $selectedMinute = '';

    if (preg_match('/^(\d{1,2}):(\d{2})/', $fieldValue, $matches)) {
        $selectedHour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        $selectedMinute = $matches[2];
    }

    $hours = range(0, 23);
    $minutes = range(0, 55, 5);
    $isRequired = (bool) ($required ?? false);
@endphp

<div class="time-select" data-time-field="{{ $fieldId }}">
    <input type="hidden" name="{{ $fieldName }}" id="{{ $fieldId }}" value="{{ $fieldValue }}">
    <div class="form-row">
        <div class="col-6">
            <select
                class="form-control time-hour-select"
                id="{{ $fieldId }}_hour"
                data-target="{{ $fieldId }}"
                data-time-part="hour"
                {{ $isRequired ? 'required' : '' }}
            >
                @unless($isRequired)
                    <option value="">Jam</option>
                @endunless
                @foreach($hours as $hour)
                    @php $hourValue = str_pad((string) $hour, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $hourValue }}" {{ $selectedHour === $hourValue ? 'selected' : '' }}>{{ $hourValue }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <select
                class="form-control time-minute-select"
                id="{{ $fieldId }}_minute"
                data-target="{{ $fieldId }}"
                data-time-part="minute"
                {{ $isRequired ? 'required' : '' }}
            >
                @unless($isRequired)
                    <option value="">Menit</option>
                @endunless
                @foreach($minutes as $minute)
                    @php $minuteValue = str_pad((string) $minute, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $minuteValue }}" {{ $selectedMinute === $minuteValue ? 'selected' : '' }}>{{ $minuteValue }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
