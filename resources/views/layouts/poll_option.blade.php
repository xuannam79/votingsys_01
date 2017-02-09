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
            'onkeyup' => "checkOptionSame(this)",
        ]) !!}
        <span class="input-group-addon pick-date">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
        <span class="input-group-btn">
            <button class="btn btn-darkcyan-not-shadow upload-photo" type="button">
                <span class="glyphicon glyphicon-picture"></span>
            </button>
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
                    {{ trans('polls.image_preview') }}
                </span>
            </div>
        </a>
        <div class="fa fa-times deleteImg"></div>
    </div>
    <!--END: Win-Frame Add Image -->
</div>
