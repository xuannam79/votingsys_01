/**
 * Add Image For Option
 */
function jqAddImageOption(config) {
    var config = config || config;
    this.initDom(config);
    this.bindEvents();
}

jqAddImageOption.prototype.extendOptions = function (config) {
    var defaults = {
        /**
         * DOM of option
         */
        wrapperPoll: '.poll-option',
        parentOption: '.form-group',
        thumbImageOption: '.render-img',
        elParentOption: '',
        srcThumbImageOption: '',
        btnChooseImage: '.upload-photo',
        horizontalWrapper: '',

        /**
         * DOM Conent Thumbnail Option
         */
        boxThumb: '.box-media-image',
        deleteImg: '.deleteImg',
        elBoxThumb: '',
        elBtnChooseImage: '',

        // Message Validate
        messages: 'div[data-poll]',

        /**
         * DOM of win-frame option to add image that by link or upload file
         */
        frImage: "#frame-upload-image",
        frUploadFile: '.photo-tb-upload',
        frPreImage: '.img-pre-option',
        frAddImgLink: '.add-image-by-link',
        frInputText: '.photo-tb-url-txt',
        frDelPhoto: '.photo-tb-del',
        frConfirmYes: '.btn-yes',
        frInputFileTemp: '.fileImgTemp',
        frContentError : '.error-win-img',
    }
    var options = $.extend(defaults, config);

    return options;
}

jqAddImageOption.prototype.initDom = function (config) {
    var options = this.extendOptions(config);

    this.wrapperPoll = options.wrapperPoll;
    this.parentOption = options.parentOption;
    this.thumbImageOption = options.thumbImageOption;
    this.elParentOption = options.elParentOption;
    this.srcThumbImageOption = options.srcThumbImageOption;
    this.btnChooseImage = options.btnChooseImage;
    this.horizontalWrapper = options.horizontalWrapper;

    this.boxThumb = options.boxThumb;
    this.deleteImg = options.deleteImg;
    this.elBoxThumb = options.elBoxThumb;
    this.elBtnChooseImage = options.elBtnChooseImage;

    // Object Message For Client
    this.messages = this.setMessage(options.messages);

    this.frUploadFile = options.frUploadFile;
    this.frImage = options.frImage;
    this.frPreImage = options.frPreImage;
    this.frAddImgLink = options.frAddImgLink;
    this.frInputText = options.frInputText;
    this.frDelPhoto = options.frDelPhoto;
    this.frConfirmYes = options.frConfirmYes;
    this.frInputFileTemp = options.frInputFileTemp;
    this.frContentError = options.frContentError;
    this.frInputHiddenTemp = '';
    this.addByLink = false;
}

jqAddImageOption.prototype.setMessage = function (selectorMessage) {
    var dataMessage = $(selectorMessage).data(selectorMessage.match(/div\[data-(.*)\]/)[1]);

    if (typeof dataMessage === "undefined") {
        return;
    }

    if (typeof dataMessage.message != "undefined") {
        return dataMessage.message;
    }

    return dataMessage;
}

jqAddImageOption.prototype.bindEvents = function () {
    $(this.wrapperPoll).on('click', this.btnChooseImage, this.showIframe.bind(this));
    $(this.frUploadFile).on('click', this.showBoxUpload.bind(this));
    $(this.frInputFileTemp).on('change', this.preImage.bind(this));
    $(this.frAddImgLink).on('click', this.addImageByLink.bind(this));
    $(this.frDelPhoto).on('click', this.delPhoto.bind(this));
    $(this.frConfirmYes).on('click', this.confirmYes.bind(this));
    $(this.wrapperPoll).on('click', this.deleteImg, this.deleteQuick.bind(this));
}

jqAddImageOption.prototype.deleteQuick = function (e) {
    var self = e.currentTarget;
    var elBoxThumb = $(self).closest(this.boxThumb);

    this.handleDelete(elBoxThumb);
}

jqAddImageOption.prototype.handleDelete = function (elBoxThumb) {
    if (!elBoxThumb.siblings('input[name^=optionDeleteImage]').length) {
        var idOption = elBoxThumb.closest(this.parentOption).prop('id');
        var inputDeleteImg = $('<input>').attr({
            type: 'hidden',
            name: 'optionDeleteImage[' + idOption + ']',
            value: idOption,
        });
        elBoxThumb.closest(this.parentOption).prepend(inputDeleteImg);
    }

    elBoxThumb.hide();
    elBoxThumb.find(this.thumbImageOption).attr('src', '');
    elBoxThumb.siblings('input[name^=optionImage]').val('');
}

jqAddImageOption.prototype.addImageByLink = function () {
    var urlImage = $(this.frInputText).val().trim();

    if (urlImage == '') {
        this.showMessage(this.messages.empty_link_image);

        return;
    }

    this.checkTimeLoadImage(urlImage, function (result) {
        if (result == 'success') {
            this.removeMessage();

            var idOption = this.elParentOption.attr('id');
            var inputUrlText = $('<input>').attr({
                type: 'hidden',
                name: 'optionImage[' + idOption + ']',
                value: ''
            });

            var elOldUrlText = this.elParentOption.find('input[type=hidden]')
                .not('input[name^=optionDeleteImage]')
                .not('input[name^=optionDescription]');

            if (elOldUrlText.length) {
                elOldUrlText.remove();
            }

            this.elParentOption.prepend(inputUrlText);
            this.frInputHiddenTemp = urlImage;
            this.addByLink = true;

            $(this.frInputFileTemp).val('');
            $(this.frPreImage).attr('src', urlImage);
            $(this.frInputText).val('');

        } else if (result == 'error') {
            this.showMessage(this.messages.not_type_url_image);
        } else {
            this.showMessage(this.messages.time_out_url_image);
        }
    }.bind(this));
}

jqAddImageOption.prototype.showMessage = function (message) {
    $(this.frContentError).text(message).show();
}

jqAddImageOption.prototype.removeMessage = function (message) {
    $(this.frContentError).text('').hide();
}

jqAddImageOption.prototype.setSrcFramePhoto = function (srcImg) {
    this.removeMessage();
    $(this.frPreImage).attr('src', srcImg);
    $(this.frInputText).val('');
}

jqAddImageOption.prototype.delPhoto = function () {
    $(this.frPreImage).attr('src', '');
    $(this.frInputFileTemp).val('');
    $(this.frInputText).val('');
    this.removeMessage();
    this.frInputHiddenTemp = '';
}

jqAddImageOption.prototype.confirmYes = function () {
    var srcPreImage = $(this.frPreImage).attr('src');
    var elThumbImg = this.elParentOption.find('img');

    if (srcPreImage != '') {
        elThumbImg.attr('src', srcPreImage).show();
        this.elBoxThumb.css('display', 'inline-block');
        this.elParentOption.find('input[type=file]').remove();

        if (this.addByLink) {
            this.elParentOption.find('input[type=hidden]')
                .not('input[name^=optionDeleteImage]')
                .not('input[name^=optionDescription]')
                .val(this.frInputHiddenTemp);
        } else {
            var elCloneInputFile = $(this.frInputFileTemp).clone();
            var idOption = this.elParentOption.attr('id');

            // Add attribute for file input option
            elCloneInputFile.attr({
                name: 'optionImage[' + idOption + ']',
                class : 'file',
                style : ''
            }).removeAttr('style');

            this.elParentOption.find('input[type=hidden]').not('input[name^=optionDescription]').remove();
            this.elParentOption.prepend(elCloneInputFile);
            this.frInputHiddenTemp = '';
        }

        // Init trigger change input File
        $(this.frInputFileTemp).val('');

        // Scroll to div
        this.scrollToDiv();
    } else {
        var elBoxThumb = $(this.elParentOption).find(this.boxThumb);

        this.handleDelete(elBoxThumb);
    }

    //window.checkImageSame();
    $(this.frImage).modal('hide');
}

jqAddImageOption.prototype.showIframe = function (e) {
    var self = e.currentTarget;

    this.removeMessage();

    // Init element when click show box
    this.elParentOption = $(self).closest(this.parentOption);
    this.elBoxThumb = this.elParentOption.find(this.boxThumb);
    this.srcThumbImageOption = this.elParentOption.find(this.thumbImageOption).attr('src');

    if (this.srcThumbImageOption.length) {
        this.setSrcFramePhoto(this.srcThumbImageOption);
    } else {
        $(this.frPreImage).attr('src', '');
    }

    $(this.frImage).modal('show');
}

jqAddImageOption.prototype.showBoxUpload = function (e) {
    $(this.frInputFileTemp).click();
}

jqAddImageOption.prototype.preImage = function (e) {

    var self = this;
    var input = $(self.frInputFileTemp)[0];

    this.addByLink = false;

    if (!this.checkExtensionImage(input.value)) {
        this.showMessage(this.messages.image);

        return;
    }

    if (!this.checkSizeImage(input.files[0])) {
        this.showMessage(this.messages.max_size_image)

        return;
    }

    // passing validate successfully
    this.removeMessage();

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $(self.frPreImage).attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

jqAddImageOption.prototype.checkTimeLoadImage = function(url, callback, timeout) {
    var timeout = timeout || 3000;
    var timedOut = false, timer;
    var img = new Image();

    img.onerror = img.onabort = function() {
        if (!timedOut) {
            clearTimeout(timer);
            callback("error");
        }
    };

    img.onload = function() {
        if (!timedOut) {
            clearTimeout(timer);
            callback("success");
        }
    };

    img.src = url;

    // Time out when load image over 5s
    timer = setTimeout(function() {
        timedOut = true;
        callback("timeout");
    }, timeout);
}

jqAddImageOption.prototype.checkExtensionImage = function (value) {
    var extension = value.substring(value.lastIndexOf('.') + 1).toLowerCase();
    var ruleExtension = ['gif', 'png', 'bmp', 'jpeg', 'jpg'];

    if (ruleExtension.indexOf(extension) > -1) {
        return true;
    }

    return false;
}

jqAddImageOption.prototype.checkSizeImage = function (file) {
    // ~ 1MB
    return (file.size / 1000) < 10240;
}

jqAddImageOption.prototype.scrollToDiv = function (){
    var scrollHeight = $(this.horizontalWrapper).prop("scrollHeight");

    if (scrollHeight > 400) {
        $(this.horizontalWrapper).animate({
                scrollTop: scrollHeight
        }, 1000);
    }
}
