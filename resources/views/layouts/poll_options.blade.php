@if (isset($page) && $page == "edit")
    {{
       Form::open([
           'route' => ['user-poll.update', $poll->id],
           'method' => 'PUT',
           'id' => 'form_update_poll_option',
           'role' => 'form',
           'enctype' => 'multipart/form-data',
       ])
    }}
@endif

<!-- ERROR OPTION -->
<div class="col-lg-12 form-group box-error-create">
    <div class="error_option"></div>
</div>

@if ((isset($page) && ($page == 'edit' || $page == 'duplicate')))
    @foreach ($data['viewData']['configOptions'] as $key => $text)
        <label class="config-option-edit">
            {!!
                Form::checkbox('setting[' . $key . ']', $key,
                (isset($page)
                && ($page == 'edit' || $page == 'duplicate')
                && array_key_exists($key, $setting)) ? true : null, [
                    'onchange' => 'settingAdvance(' . $key . ')',
                    'class' => 'switch-checkbox-setting config-option',
                ])
            !!}
            <span class='span-text-setting'>{{ $text }}</span>
        </label>
        @if ($loop->first)
            <br>
        @endif
    @endforeach
    <div class="old-option">
        <!-- LIST OLD OPTION -->
        @foreach($poll->options as $option)
            <div id="{{ $option->id }}" class="col-lg-12 div-option-edit">
                <div class="panel panel-success panel-darkcyan">
                    <div class="panel-heading panel-heading-darkcyan">
                        {{ trans('polls.label.step_2') }} {{ $loop->index + 1 }}
                        <button type="button" class="btn btn-danger btn-xs btn-delete-option-duplicate"
                                onclick="removeOpion('{{ $option->id }}', '{{ $page }}')">
                            <span class="fa fa-trash"></span> {{ trans('polls.button.remove') }}
                        </button>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            @if ($page == 'duplicate')
                                {{ Form::text('optionText['. $option->id .']', $option->name, [
                                    'class' => 'form-control',
                                    'onkeyup' => 'checkOptionSame(this)',
                                ]) }}
                            @else
                                {{ Form::text('option['. $option->id .']', $option->name, [
                                    'class' => 'form-control',
                                    'onkeyup' => 'checkOptionSame(this)',
                                ]) }}
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-4 col-xs-4">
                                <img src="{{ asset($option->showImage()) }}" class="image-option">
                                @if ($option->getOriginal('image'))
                                    <span class="cz-label label-new cz-label-old">
                                        {{ trans('polls.label_for.option_image') }}
                                    </span>
                                    <i class="fa fa-times delete-img-old-option" data-id-option="{{ $option->id }}" aria-hidden="true"></i>
                                    <i class="fa fa-reply restore-img-old-option hidden-restore" data-id-option="{{ $option->id }}" aria-hidden="true"></i>
                                @endif
                                {!! Form::hidden('imageOptionDelete[]', null, [
                                    'id' => 'imageOptionDelete-' . $option->id,
                                ]) !!}
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-2 icon-change" id="icon-id-{{ $option->id }}">
                                <h2>
                                    <i class="fa fa-arrow-right fa-arrow-right-hidden fa_preview_{{ $option->id }} aria-hidden="true"></i>
                                </h2>
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-4">
                                <img id="preview_{{ $option->id }}" src="#" class="preview-image image_edit" />
                            </div>
                        </div>
                        <div class="form-group">

                        </div>
                        <div class="form-group">
                            @if ($page == 'duplicate')
                                <input type="hidden" name="oldImage[{{ $option->id }}]'" value="{{ $option->image }}">
                                {{
                                    Form::file('optionOldImage[' . $option->id . ']', [
                                        'onchange' => 'readURL(this, "preview_' . $option->id . '", "icon-id-' . $option->id . '")',
                                        'class' => 'form-control',
                                    ])
                                }}
                            @else
                                <div class="input-group input-file"">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-choose change-img-old-option" type="button">
                                            @lang('polls.button.choose')
                                        </button>
                                        {{
                                            Form::file('image[' . $option->id . ']', [
                                                'onchange' => 'readURL(this, "preview_' . $option->id . '", "icon-id-' . $option->id . '")',
                                                'class' => 'hidden-input-img',
                                            ])
                                        }}
                                    </span>
                                    {{
                                        Form::text('name-file-img[]', null,[
                                            'class' => 'form-control ' . 'preview_' . $option->id,
                                            'placeholder' => trans('polls.placeholder.choose_a_file'),
                                        ])
                                    }}
                                    <span class="input-group-btn">
                                         <button
                                            class="btn btn-warning btn-reset"
                                            onclick="resetChangeImgOldOption(this, {{ '`preview_' . $option->id . '`' }})"
                                            type="button">@lang('polls.button.reset')</button>
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="form-group edit-description-old edit-description-old-js" data-hide="true">
                            <a href="javascript:void()">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>
                            <p class="show-description-edit">@lang('polls.show_description_to_edit')</p>
                            <p class="hide-description-edit">@lang('polls.hide_description')</p>
                            <a href="javascript:void(0)" class="wrap-up-down">
                                <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                                <i class="fa fa-angle-double-up" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="des-quill-editor-edit des-quill-editor">
                        </div>
                        {!! Form::hidden('oldOptionDescription[' . $option->id . ']', $option->description, [
                            'id' => 'optionDescription-' . $option->id,
                        ]) !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- OPTION LISTS -->
<div class="col-lg-12">
    <div class="poll-option"></div>
</div>

@if (isset($page) && $page == 'edit')
    <div class="col-lg-12">
        <input type="submit" class="btn btn-success btn-darkcyan btn-edit-info btn-xs" name="btn_edit" value="{{ trans('polls.button.save_option') }}">
        <a href="{{ $poll->getAdminLink() }}" class="btn btn-back-edit btn-xs">{{ trans('polls.button.edit_back') }}</a>
   </div>
    {{ Form::close() }}
@endif

