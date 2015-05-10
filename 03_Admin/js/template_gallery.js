function m_upload(data) {
    var result = data.submit();
}

var key = 0;
var k = 0;
var n = 0;
$('#fileupload').fileupload({
    maxNumberOfFiles: 2,
    done: function (e, data) {
        $.each(data.files, function (index, file) {
            //result from controller
            var key2 = data.result.key;
            //the id of last row of video
            var row = $('table tr:last').attr("id");
            if (row == key2) {
                window.location = "search";
            }
        });

    },
    add: function (e, data) {
        var uploadErrors = [];
        var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
        console.log(data.originalFiles[0]['type']);
        if (!acceptFileTypes.test(data.originalFiles[0]['type'])) {
            uploadErrors.push('Not an accepted file type');
        }
        if (data.originalFiles[0]['size'] > 2 * 1024 * 1024) {
            uploadErrors.push('Filesize is too big');
        }
        if (uploadErrors.length > 0) {
            alert(uploadErrors.join("\n"));
            return false;
        }
        $.each(data.files, function (index, file) {
            var row = $('<tr class="template-upload" id="' + key + '">' +
                '<td><span ><div style="position: relative;margin-top: 20px;">' +
                '<span style="position: absolute;" class="loading"></span> ' +
                '<img style="display: none;overflow: hidden;" class="image_preview" id="img_' + key + '" controls/></div></span></td>' +
                '<td><div class="row">' +
                '<label class="col-lg-2">ファイル名</label>' +
                '<div class="col-lg-6"><div class="name"></div>' +
                '<div class="error text-danger"></div>' +
                '</div></div>' +
                '<div class="row">' +
                '<label class="col-lg-2">Title <span class="txtWarning">*</span></label>' +
                '<div class="col-lg-6"><input class="form-control input-sm" type="text" id="title" name="title" value="" required=""></div>' +
                '</div>' +
                '<div class="row">' +
                '<label class="col-lg-2">Tags <span class="txtWarning">*</span></label>' +
                '<div class="col-lg-6"><span>複数ある場合、カンマ「、」で区切ってください。</span>' +
                '<input class="form-control input-sm tag" name="tag" type="text" tag_id="tag_' + key + '" value="">' +
                '<p>Most frequently used tags:　<span class="text-info"><span class="ftagsHolder_mutimedia" id="fuTags_' + key + '" zone="' + key + '"></span></span></p>' +
                '</div></div>' +
                '<div class="row">' +
                '<label class="col-lg-2">Caption <span class="txtWarning">*</span></label>' +
                '<div class="col-lg-10"><textarea class="form-control" rows="2" id="des" name="des"></textarea></div>' +
                '</div>' +                
                '<div class="row">' +
                '<label class="col-lg-2">URL</label>' +
                '<div class="col-lg-6"><input class="form-control input-sm c-url" type="text" id="url" value="" readonly="readonly"></div>' +
                '</div>' +
                '<div class="row">' +
                '<label class="col-lg-2">コピーライト</label>' +
                '<div class="col-lg-6"><input class="form-control input-sm c-url" name="copyright" type="text" id="url" value=""></div>' +
                '</div>' +
                '<div class="row">' +
                '<label class="col-lg-2">ギャラリー掲載&nbsp;</label>' +
                '<div class="col-lg-6"><input type="checkbox" name="is_gallery" value="1" id="is_gallery"/></div>' +
                '</div>' +
                '</td>' +
//                '<td><p class="size"></p>' +
//                '<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar" class="progress progress-striped active">' +
//                '<div style="width:0%" class="progress-bar progress-bar-success"></div>' +
//                '</div>' +
//                '</td>' +
                '<td style="vertical-align: bottom;padding-left: 50px;">' +
                '<button class="btn btn-warning cancel">削除する</button>' +
                '</td>' +
                '</tr>');
            row.find('.name').text(file.name);
            row.find('.size').text(file.size);
            if (file.error) {
                row.find('.error').text(file.error);
            }
            //add row to table
            $('table .files').append(row);

            var pre = $("#img_" + key);
            readURL(file, pre, row);
            //add tag choose
            $("#fuTags_" + key).html($(".tag_name").html());


            //tag input
            $('[tag_id=tag_' + key + ']').tagsInput({width: '100%', height: '100%'});
            // add tag what user choose to input feild
            $(".fuTags").on("click", function () {
                var zone = $(this).parent().attr("zone");
                if ($('[tag_id=tag_' + zone + ']').val().indexOf($(this).html()) == -1) {
                    $('[tag_id=tag_' + zone + ']').addTag($(this).html());
                }
            });

            key++;

        });
        $('.submit_data').click(function () {

            //sent data  to controller
            var data_input = $('table tr');
            var num = $('table tr:last').attr("id");
            if (k < data_input.length) {
                var id = data_input.eq(k).attr("id");
                var row = $('tr#' + id);
                var a = [];
                //send data
                if (n == id) {
                    var hasError = validateForm();

                    if (hasError){
                        $('.validate_text').html("<b style='color: lightcoral'>Pls fill full of input</b></br>");
                        return false;
                    }
                    else{
                        $('.validate_text').html('');
                    }
                    data.formData = a[id] = {
                        title: row.find('input#title').val(),
                        tag: row.find('input.tag').val(),
                        descripton: row.find('textarea#des').val(),
                        copyright: row.find("[name='copyright']").val(),
                        gallery: row.find("input#is_gallery").is(':checked') ? 1 : 0,
                        key: id
                    };
                    k++
                }

                m_upload(data);
                n++;
            }
        });


    },
    progress: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('.progress-bar').css('width', progress + '%');
    }
});


function readURL(files, preview, row) {

    var reader = new FileReader();

    reader.onload = function (e) {
        preview.attr('src', e.target.result);
        setTimeout(function(){
            row.find('.loading').hide();
            row.find('.image_preview').show();
        },100);

    };
    reader.readAsDataURL(files);
}
function validateForm() {
    var hasError = false;
    var data_input = $('table tr');
    for (var i = 0; i < data_input.length; i++) {
        var row = data_input.eq(i);
        var title = row.find('input#title').val();
        if (title == null || title == "") {
            hasError = true;
            row.find('input#title').addClass('validate');
        }else row.find('input#title').removeClass('validate');
        var tag = row.find('input.tag').val();
        if (tag == null || tag == "") {
            hasError = true;
        }else row.find('input.tag').removeClass('validate');
        var des = row.find('textarea#des').val();
        if (des == null || des == "") {
            row.find('textarea#des').addClass('validate');
            hasError = true;
        }else row.find('textarea#des').removeClass('validate');
    }
//    $('.validate').append("<b style='color: lightcoral'>title</b></br>");
    return hasError;
}