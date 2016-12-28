@if ($isHasImage)
    <img src="{{ $imagePath }}" width="{{ $size }}px" height="{{ $size }}px"
         class="option-image-bar-chart"><span class="option-name-bar-chart"
                                              style="margin-left: {{ $marginLeft }}px">{{ $optionName }}</span>
@else
    <p>{{ $optionName }}</p>
@endif

