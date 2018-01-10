<div class="form-group" id="idOption">
    {!! Form::file('optionImage[idOption]', [
      'class' => 'file',
    ]) !!}
    <div class="input-group date datetimepicker" id="option-poll">
        {!! Form::text('optionText[idOption]', null, [
            'class' => 'form-control',
            'id' => 'optionText-idOption',
            'placeholder' => trans('polls.placeholder.option'),
            'onfocus' => "addAutoOption('idOption')",
            'onclick' => "addAutoOption('idOption')",
            'onblur' => "checkOptionSame(this)",
            'onkeyup' => "checkOptionSame(this)"
        ]) !!}
        <span class="input-group-addon pick-date">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
        <span class="input-group-btn">
            <button class="btn btn-danger btn-remove-option" type="button" onclick="removeOpion('idOption')">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </span>
    </div>
    <!--START: Win-Frame Add Image -->
    <div class="box-media-image">
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
    <div class="des-quill-editor"></div>
    {!! Form::hidden('optionDescription[idOption]', null, [
        'id' => 'optionDescription-idOption',
    ]) !!}
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
    <!--END: Win-Frame Add Image -->
</div>
