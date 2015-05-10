function CustomImage(s) {
    // send Editor name to popup for calling back
    var currentEditor = CKEDITOR.currentInstance.name;
    gallery_search(currentEditor);

    // initialize a new popup
    $("#jquery-ui-dialog").dialog({
        autoOpen:false,
        modal:true,
        width:600,
        draggable:true,
        resizable:true,
        show:"fade",
        hide:"fade"
    });
    $("#jquery-ui-dialog").dialog("open");

    return false;
}

function gallery_search(currentEditorName)
{

    var keyword = $('#gallery_keyword').val();
    var taxonomy = $('#gallery_taxonomy').val();
    $.get('/contents/search_gallery',{currentEditorName : currentEditorName, taxonomy: taxonomy, keyword: keyword},function(data){
        //$('#jquery-ui-dialog-result').append(data);
        $('#jquery-ui-dialog').html(data);
    });

}

// send IMAGE html back to editor
$(document).ready(function(){
    jQuery(".gallery_select").live("click",function(){
        var currentEditorName = $("#currentEditorName").val();
        var html = $(this).closest('li').find('figure').html();

        var currentEditor = CKEDITOR.instances[currentEditorName];

        currentEditor.insertHtml( html );

        closeDialog($("#jquery-ui-dialog"));
    });
});
