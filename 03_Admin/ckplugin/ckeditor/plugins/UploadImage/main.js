
/* +++++++++++++++++++++++++UploadImage's functions++++++++++++++++++++++++++*/

$(window).ready(function () {
    $(".fuTagsUI").click(function () {
        var tagName = $(this).html();
        if($('#UI_tag').val().indexOf(tagName) == -1){
            $('#UI_tag').addTag(tagName);
        }
    });

    $("#send_to_editor").click(function(){
        var is_checked = ($(this).prop("checked"))? true : false;
        if(is_checked){
            $("#positionHolder").show();
        }else{
            $("#positionHolder").hide();
        }

    });
    $('#UI_tag').tagsInput({width:'94%', height:'auto' });

    $("#UI_upload_file").change(function(){
        readURL(this);
    });
});


function UploadImage(s) {
    // send Editor name to popup for calling back
    var currentEditor = CKEDITOR.currentInstance.name;

    // initialize a new popup
    $("#popUpUploadImage").dialog({
        autoOpen:false,
        modal:true,
        width:600,
        draggable:true,
        resizable:true,
        show:"fade",
        hide:"fade",
        buttons: {
            Cancel: function() {
                $(this).dialog("close");
            },
            Save: function() {
                checkThisForm();
            },
            "New Upload":function(){
                newUpload();
            }
        }
    });
    $("#popUpUploadImage").dialog("open");
    newUpload();

    // hide new upload when first load
    $('.ui-dialog-buttonpane button:contains("New Upload")').button().hide();
    $("#currentEditor_UI").val(currentEditor);
    return false;
}



function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#UI_image').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
        $("#UI_image").show();
        $("#infoHolder").show();
        $("#UI_imageName").html(input.files[0].name);
        $("#UI_imageType").html(input.files[0].type);
    }
}
function checkThisForm(){
    var hasError  = false;
    if($("#UI_upload_file").val() == ""){
        $("#UI_image_err").show();
        hasError = true;
    }
    if($("#UI_title").val() == ""){
        $("#UI_title_err").show();
        hasError = true;
    }
    if($("#UI_caption").val() == ""){
        $("#UI_caption_err").show();
        hasError = true;
    }
    if(hasError){
        return false;
    }

    var xhr = new XMLHttpRequest();
    var fileToUpload = document.getElementById("UI_upload_file").files[0];
    var uploadStatus = xhr.upload;

    var myProgess = 0;
    var myProgessTex = 0;
    uploadStatus.addEventListener("progress", function (ev) {
        $("#processBar").empty();
        $("#processBar").append('<div id="fileuploading" style="text-align: center; padding: 5px; color: #000000;"></div><div id="uploadPercentage" style="background: #00ff00; opacity: 0.5; height: 20px; text-align: center; color: #000000;"></div>');

        if (ev.lengthComputable) {
            myProgess = (ev.loaded / ev.total) * 50;
            if (myProgess.toFixed){
                myProgessTex = myProgess.toFixed(0);
            }else{
                myProgessTex = myProgess;
            }
            $("#uploadPercentage").css('width',((ev.loaded / ev.total) * 50 + "%"));
            $('#uploadPercentage').html((myProgessTex + "%"));

        }
    }, false);
    uploadStatus.addEventListener("error", function (ev) {$("#errorMsg").html(ev)}, false);
    uploadStatus.addEventListener("load", function (ev) {doAfterUploading(xhr)}, false);
    xhr.open(
        "POST",
        "/contents/uploadobject",
        true
    );

    xhr.setRequestHeader("Cache-Control", "no-cache");
    xhr.setRequestHeader("Content-Type", "multipart/form-data");
    xhr.setRequestHeader("X-File-Name", fileToUpload.name);
    xhr.setRequestHeader("X-File-Size", fileToUpload.size);
    xhr.setRequestHeader("X-File-Type", fileToUpload.type);
    xhr.setRequestHeader("X-Object-Type", "image");
    xhr.setRequestHeader("Content-Type", "application/octet-stream");
    xhr.send(fileToUpload);

}
function doAfterUploading(xhr)
{
    xhr.onreadystatechange = function() {
        if(xhr.readyState == 4 && xhr.status == 200)  {
            var data = $("#popUpUploadImage :input").serialize();
            $.ajax({
                type: "post",
                dataType: "json",
                url: '/contents/uploadobject',
                data: data,
                error:function(){

                },
                submit:function(){
                    $("#uploadPercentage").css('width',("75%"));
                    $('#uploadPercentage').html(("75%"));
                },
                success:function(data){

                    $("#uploadPercentage").css('width',("100%"));
                    $('#uploadPercentage').html(("100%"));

                    //show New upload button and hide Save button when done uploading
                    $('.ui-dialog-buttonpane button:contains("New Upload")').button().show();
                    $('.ui-dialog-buttonpane button:contains("Save")').button().hide();

                    setTimeout(function(){
                        if($("#send_to_editor").prop("checked") == 1){
                            var position =  $("input:radio[name=position]:checked").val();
                            var style = "";
                            if(position == 3){ // center
                                var imageTag = "<div style='text-align: center;height: 100%; width:100%'><img src='"+data.path+"'/></div>";
                            }else if(position == 4){ //right
                                var imageTag = "<img src='"+data.path+"'style='float:right'/>";
                            }else{ // left & no set
                                var imageTag = "<img src='"+data.path+"'style='float:left'/>";
                            }

                            var currentEditor_UI = $("#currentEditor_UI").val();
                            var currentEditor = CKEDITOR.instances[currentEditor_UI];

                            currentEditor.insertHtml( imageTag );
                            newUpload();
                            closeDialog($("#popUpUploadImage"));

                        }
                    },1000);
                }
            });
        }
    };
}
function closeDialog(selector){
    $(selector).dialog('close');
}

function newUpload(){

    $(":input", $("#popUpUploadImage")).not(":button, :submit, :reset, :hidden").each(function () {
        this.value = this.defaultValue;
    });

    $("#UI_tag").importTags("");

    $("#UI_image").hide();
    $("#infoHolder").hide();
    $("#processBar").html("");
    $("#errorMsg").html("");
    $(".UI_error").each(function(){
        $(this).hide();
    });
    $('.ui-dialog-buttonpane button:contains("New Upload")').button().hide();
    $('.ui-dialog-buttonpane button:contains("Save")').button().show();
}