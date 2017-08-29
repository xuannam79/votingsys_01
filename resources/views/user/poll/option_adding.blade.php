@php
    $idNewOption = rand();
@endphp
<div class="parent-vote-new-option li-parent-vote js-execute-options" id="{{ $idNewOption }}">
        @if ($poll->multiple == trans('polls.label.multiple_choice'))
            <div class="checkbox checkbox-primary">
            {!! Form::checkbox('newOption[' . $idNewOption . ']', null, false, [
                    'class' => 'poll-new-option poll-option-detail-not-image new-option checkbox',
                ])
            !!}
        @else
            <div class="radio radio-primary">
            {!! Form::radio('newOption[' . $idNewOption . ']', null, false, [
                    'class' => 'poll-new-option poll-option-detail-not-image new-option',
                ])
            !!}
        @endif
        <label>
            <div class="input-group date date-time-picker">
                {!! Form::text('optionText[' . $idNewOption . ']', null, [
                    'class' => 'text-new-option form-control',
                    'autocomplete' => 'off',
                    'placeholder' => trans('polls.placeholder.option'),
                ]) !!}
                <span class="input-group-addon pick-date">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </label>
        <br>
        <!-- PROCESSING AN IMAGE -->
        <input type="file" id="input-file-image" name="optionImage[]">
        <!--START: Win-Frame Add Image -->
        <div class="box-media-image box-frame">
            <a class="media-image upload-photo" href="javascript:void(0)">
                <div class="image-frame">
                    <div class="image-ratio">
                        <img src="" id="preview-idOption" class="render-img thumbOption"/>
                    </div>
                    <span class="cz-label label-new">
                        {{ trans('polls.label_for.option_image') }}
                    </span>
                </div>
            </a>
            <div class="fa fa-times deleteImg"></div>
        </div>
        <!--END: Win-Frame Add Image -->
        <div class="has-error" id="error_option" data-message="{{ json_encode($messageImage) }}">
            <span id="title-error" class="help-block"></span>
        </div>

        <!-- PROCESSING EDITOR-->
        <div class="box-control-editor">
            <div class="des-quill-editor"></div>
            {!! Form::hidden('optionDescription[' . $idNewOption . ']', null) !!}
            <div class="box-des-option">
                <div class="inline-tooltip is-active">
                    <a class="btn btn-rotate tooltip-control">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
                    <div class="inline-tooltip-menu">
                        <a class="btn btn-scale upload-photo" data-toggle="tooltip" title="{{ trans('polls.label.add_an_image') }}">
                            <i class="fa fa-file-image-o" aria-hidden="true"></i>
                        </a>
                        <a class="btn btn-scale js-add-des-for-option" data-toggle="tooltip" title="{{ trans('polls.label.add_a_description') }}">
                            <i class="fa fa-pencil" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: PROCESSING EDITOR -->
    </div>
</div>
