var fileBrowse = {
    init: function (objStr, settings) {
        fileBrowse.config = {
            allowedFileTypes: {
                image: ['jpg', 'jpeg', 'png', 'gif'],
                video: ['mp4', "mov"],
                file: ['pdf']
            },
            maxSize: 2097152,
            fileMaxLength: 5,
            remove: true,
            name: "upload",
            messages: {
                size: "File is to big",
                type: "The file type is not allowed"
            },
            before: function (valueObj) {
            },
            error: function () {
            },
            complete: function (inp, type, result, files, valueObj) {
            },
            removed: function (index, valueObj, inp) {
            }
        };
        $.extend(fileBrowse.config, settings);



        //alert(fileBrowse.config.maxSize);
        fileBrowse.obj = $(objStr);
        fileBrowse.files = Array();
        fileBrowse.fileInp = false;
        return this;

    }, MB: function (mb) {
        return ((mb*1024)*1024);

    }, setup: function () {


        if (fileBrowse.obj.data("name") !== undefined) {
            fileBrowse.config.name = fileBrowse.obj.data("name");
        }

        fileBrowse.obj.on("click", ".wa-upload-btn", fileBrowse.browse);
        fileBrowse.obj.on("change", "input[type='file']", fileBrowse.change);
        if (fileBrowse.config.remove) {
            fileBrowse.obj.on("click", ".list-values a", fileBrowse.remove);
        }
        return this;

    }, change: function (e) {
        e.preventDefault();
        var input = this, inp = $(input), holder = inp.parent().parent(), valueObj = holder.find(".list-values"), html;

        if (fileBrowse.files.length <= (fileBrowse.config.fileMaxLength-1)) {
            // Modern browser preview

            fileBrowse.config.before(valueObj);

            if (input.files && input.files[0]) {
                var extension = input.files[0].name.split('.').pop().toLowerCase(), reader = new FileReader(), validateType = fileBrowse.validateType(extension);

                if (fileBrowse.config.maxSize > 0 && input.files[0].size > fileBrowse.config.maxSize) {
                    fileBrowse.fileInp.remove();
                    modal.template("error").show(fileBrowse.config.messages.size);
                    fileBrowse.config.error();
                } else if (validateType === false) {
                    fileBrowse.fileInp.remove();
                    modal.template("error").show(fileBrowse.config.messages.type);
                    fileBrowse.config.error();
                } else {
                    fileBrowse.files.push(input.files[0]);

                    if (validateType === "image") {
                        reader.onload = function (e) {
                            valueObj.append('<a class="img-value active cover" href="#" data-value="1"><img src="'+e.target.result+'" alt=""></a>');
                            fileBrowse.config.complete(inp, validateType, e.target.result, input.files, valueObj);
                        }
                        reader.readAsDataURL(input.files[0]);
                    } else {
                        switch (validateType) {
                            case "video":
                                valueObj.append('<a class="video-value active" href="#" data-value="1" title="'+input.files[0].name+'"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" stroke="#FFFFFF" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"><path d="M22 13l8-5v16l-8-5zM2 8v16h20V8z"/></svg></a>');
                            break;
                            default:
                                valueObj.append('<a class="file-value active" href="#" data-value="1" title="'+input.files[0].name+'"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" stroke="#FFFFFF" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"><path d="M6 2v28h20V10l-8-8zm12 0v8h8"/></svg></svg></a>');
                            break;
                        }
                        fileBrowse.config.complete(inp, validateType, e.target.result, input.files, valueObj);
                    }
                }
            } else {
                var value = inp.val(), basename = fileBrowse.basename(value, 14);
                valueObj.append('<a class="text-value active" href="#">'+basename+'</a>');
                fileBrowse.config.complete(inp, false, value, false, valueObj);
            }
        }

        if (fileBrowse.files.length === fileBrowse.config.fileMaxLength) {
            valueObj.next().css("display", "none")
        }
    }, remove: function (e) {
        e.preventDefault();
        var myClick = $(this), length = fileBrowse.files.length, index = myClick.index();

        fileBrowse.removeIndex(index, myClick);

    }, removeIndex: function (index, btn) {
        var listValues = fileBrowse.obj.find(".list-values"), inp;

        fileBrowse.files.splice(index, 1);

        if (btn !== undefined) {
            btn.remove();
        } else {
            listValues.find("a").eq(index).remove();
        }
        
        if (fileBrowse.files.length === (fileBrowse.config.fileMaxLength-1)) {
            listValues.next().css("display", "block");
        }

        inp = fileBrowse.obj.find(".files input").eq(index);
        inp.remove();
        fileBrowse.config.removed(index, listValues, inp);

    }, clear: function () {
        fileBrowse.files = Array();
        fileBrowse.obj.find(".list-values").empty().next().css("display", "block");
        fileBrowse.obj.find(".files").empty();


    }, browse: function (e) {
        e.preventDefault();
        // Built like this becouse:
        // IE9: The input field can not be moved or copied in IE.
        // If you do than then the input field value will become empty and it is not possible to change input file value either.
        if (fileBrowse.fileInp === false || fileBrowse.fileInp.val().length > 0) {
            fileBrowse.fileInp = $('<input type="file" name="'+fileBrowse.config.name+'[]">').appendTo(fileBrowse.obj.find(".files"));
        }
        fileBrowse.fileInp.trigger("click");
    
    }, basename: function (path, length) {
        var path = path.split(/[\\/]/).pop();
        if (length > 0 && path.length > (length+3)) {
            path = path.substr(0, length)+"...";
        }
        return path;

    }, validateType: function (extension) {
        var found = false;
        if (typeof fileBrowse.config.allowedFileTypes === "object") {
            $.each(fileBrowse.config.allowedFileTypes, function (key, arr) {
                if (arr.indexOf(extension) > -1) {
                    found = key;
                }
            });
        } else {
            found = true;
        }
        return found;

    }, fileArr: function () {
        return fileBrowse.files;
    }

};