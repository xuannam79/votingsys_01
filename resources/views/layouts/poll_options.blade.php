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
<div class="col-lg-12 form-group">
    <div class="error_option"></div>
</div>

@if ((isset($page) && ($page == 'edit' || $page == 'duplicate')))
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
                                {{ Form::text('optionText['. $option->id .']', $option->name, ['class' => 'form-control', 'onkeyup' => 'checkOptionSame(this)']) }}
                            @else
                                {{ Form::text('option['. $option->id .']', $option->name, ['class' => 'form-control']) }}
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="col-lg-2">
                                <img src="{{ asset($option->showImage()) }}" class="image-option">
                            </div>
                            <div class="col-lg-1 icon-change" id="icon-id-{{ $option->id }}">
                                <h2>
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </h2>
                            </div>
                            <div class="col-lg-2">
                                <img id="preview_{{ $option->id }}" src="#" class="preview-image image_edit" />
                            </div>
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
                                {{
                                    Form::file('image[' . $option->id . ']', [
                                        'onchange' => 'readURL(this, "preview_' . $option->id . '", "icon-id-' . $option->id . '")',
                                        'class' => 'form-control',
                                    ])
                                }}
                            @endif
                        </div>
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

