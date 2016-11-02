<div class="form-group" id="idOption">
    <div class="input-group">
        <div class="form-line">
            <input type="file" class="file" name="optionImage[idOption]" onchange="readURL(this, 'preview-idOption')">
            <input type="text" name="optionText[idOption]" id="content-option-idOption" class="form-control"
                   placeholder="{{ trans('polls.placeholder.option') }}">
        </div>
        <span class="input-group-btn">
            <button class="btn btn-success" type="button" onclick="showOptionImage('idOption')">
                <span class="glyphicon glyphicon-picture"></span>
            </button>
            <button class="btn btn-danger" type="button" onclick="removeOpion('idOption')">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </span>
    </div>
    <img id="preview-idOption" src="#" class="preview-image" />
</div>
