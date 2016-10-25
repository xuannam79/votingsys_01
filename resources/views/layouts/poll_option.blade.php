<div class="input-group" id="idOption">
    <div class="form-line">
        <input type="text" name="option[idOption]" id="content-option-idOption" class="form-control"
               placeholder="{{ trans('polls.placeholder.option') }}">
        <img id="preview-idOption" src="#" class="preview-image" />
    </div>
    <span class="input-group-addon">
        <button class="btn btn-default" type="button" onclick="showOptionImage('idOption')">
            <span class="glyphicon glyphicon-picture"></span>
        </button>
        <input type="file" class="file" name="optionImage[idOption]" onchange="readURL(this, 'preview-idOption')">
    </span>
    <span class="input-group-addon">
        <button class="btn btn-default" type="button" onclick="removeOpion('idOption')">
            <span class="glyphicon glyphicon-trash"></span>
        </button>
    </span>
</div>
