<div class="form-group" id="idOption">
    {!! Form::file('optionImage[idOption]', [
      'class' => 'file',
      'onchange' => "readURL(this, 'preview-idOption')"
    ]) !!}
    <div class="input-group date" id="option-poll">
        {!! Form::text('optionText[idOption]', null, [
            'class' => 'form-control',
            'id' => 'optionText-idOption',
            'placeholder' => trans('polls.placeholder.option'),
            'onfocus' => "addAutoOption('idOption')",
            'onclick' => "addAutoOption('idOption')",
            'onblur' => "checkOptionSame(this)",
            'onkeyup' => "checkOptionSame(this)",
        ]) !!}
        <span class="input-group-btn">
            <button class="btn btn-darkcyan-not-shadow" type="button" onclick="showOptionImage('idOption')">
                <span class="glyphicon glyphicon-picture"></span>
            </button>
            <button class="btn btn-danger btn-remove-option" type="button" onclick="removeOpion('idOption')">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </span>
    </div>
    <img id="preview-idOption" src="#" class="preview-image"/>
</div>
