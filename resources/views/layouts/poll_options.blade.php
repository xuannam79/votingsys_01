@if (isset($page) && $page == "edit")
    {{
       Form::open([
           'route' => ['user-poll.update', $poll->id],
           'method' => 'PUT',
           'id' => 'form_update_poll',
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
    <!-- LIST OLD OPTION -->
    @foreach($poll->options as $option)
        <div id="{{ $option->id }}" class="row">
            <div class="col-lg-10 col-lg-offset-1 well">
                <div class="panel panel-success ">
                    <div class="panel-heading">
                        {{ trans('polls.label.step_2') }} {{ $loop->index + 1 }}
                        <button type="button" class="btn btn-danger btn-delete-option-duplicate"
                                onclick="removeOpion('{{ $option->id }}', 'edit')">
                            <span class="fa fa-trash"></span> {{ trans('polls.button.remove') }}
                        </button>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            {{ Form::text('option['. $option->id .']', $option->name, ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group">
                            <div class="col-lg-3">
                                <img src="{{ asset($option->showImage()) }}" class="image-option">
                            </div>
                            <div class="col-lg-1">
                                <h2>
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </h2>
                            </div>
                            <div class="col-lg-3">
                                <img id="preview_{{ $option->id }}" src="#" class="preview-image image_edit" />
                            </div>
                        </div>
                        <div class="form-group">
                            {{
                                Form::file('image['. $option->id .']', [
                                    'onchange' => 'readURL(this, "preview_' . $option->id . '")',
                                    'class' => 'form-control',
                                ])
                            }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

<!-- OPTION LISTS -->
<div class="col-lg-12">
    <div class="poll-option"></div>
</div>

@if ((isset($page) && ($page == 'edit' || $page == 'duplicate')))
    <input type="submit" class="btn btn-success" name="btn_edit" value="{{ trans('polls.button.save_option') }}">
    {{ Form::close() }}
@endif

