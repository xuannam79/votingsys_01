@if ($isHasImage)
    <a href="javascript:void(0);" onclick='showToolTipOptionContent("{{ $imagePath }}", "{{ $optionFullName }}")'>
        <img src="{{ $imagePath }}" width="{{ $size }}px" height="{{ $size }}px"
             class="option-image-bar-chart"><span class="option-name-bar-chart"
                                                  style="margin-left: {{ $marginLeft }}px">{{ $optionName }}</span>
    </a>
@else
    <a href="javascript:void(0);" onclick='showToolTipOptionContent("", "{{ $optionFullName }}")'>{{ $optionName }}</a>
@endif

