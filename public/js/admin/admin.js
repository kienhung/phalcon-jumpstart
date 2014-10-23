
// Delete alert confirm box
function delm(theURL) {
    if (confirm('Are you want to DELETE?')) {
        window.location.href=theURL;
    }         
}

$( document ).ready(function() {

    // Check all checkboxes when the one in a table head is checked:
    $('.tab-content .check-all').click(
        function(){
            // $(this).parent().parent().parent().parent().find("input[type='checkbox']").attr('checked', $(this).is(':checked'));   
            $(this).parent().parent().parent().parent().find("input[type='checkbox']").prop('checked', $(this).is(':checked'));   
        }
    );

    // seleted sidebar
    var activemenu = $('#page-header').attr('rel');
    var activemenuselector = $('#' + activemenu);
    if(activemenuselector.length) {
        activemenuselector.addClass('active');
    }
    
    if ($('#summernote-editor, .summernote-editor').length > 0) {
        $('#summernote-editor, .summernote-editor').summernote({
            height: 300,
            toolbar: [
                //[groupname, [button list]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['picture', 'link', 'table', 'hr', 'video']],
            ],
            // onImageUpload: function(files, editor, welEditable) {
            //     sendFile(files[0], editor, welEditable);
            // }
        });
    }


});

function showAdvanceSetting(t)
{
    var advanceID = t.attr('data-id');
    var advance = $('tr#' + advanceID);
    if (advance.length > 0) {
        var isactived = advance.hasClass('active');
        if (isactived) {
            advance.removeClass('active');
        } else {
            advance.addClass('active');
        }
    }
}

function inputTypeChange(t)
{
    var val = t.val();
    var parent = t.parent().parent();
    var tdParent = t.parent().parent().parent();
    $('.expand-setting').attr('rel', '');
    if (val == 'select') {
        parent.children('.model-choice').attr('rel', 'active');
        parent.children('.model-condition').attr('rel', 'active');
        tdParent.children('.select-setting').attr('rel', 'active');
        $('.expand-setting[rel!="active"]').stop().fadeOut(100, function() {
            setTimeout(function() {
                $('.model-choice[rel="active"]').fadeIn();
                $('.model-condition[rel="active"').fadeIn();
                $('.select-setting[rel="active"').fadeIn();
            }, 150);
        });
    } else if (val == 'imageupload') {
        parent.children('.imageupload-setting').attr('rel', 'active');
        $('.expand-setting[rel!="active"]').stop().fadeOut(100, function() {
            setTimeout(function() {
                $('.imageupload-setting[rel="active"').stop().fadeIn();
            }, 150);
        });
    } else {
        $('.expand-setting').stop().fadeOut();
    }
}

function loadValueText(t)
{
    var model = t.val();
    var parent = t.parent().parent().parent().children('.select-setting');
    var selectValue = parent.children('.select-value').children('select');
    var selectText = parent.children('.select-text').children('select');
    selectValue.attr('disabled', true);
    selectText.attr('disabled', true);
    var data = {
        'model': model
    };
    $.ajax({
        type: "POST",
        data: data,
        url: rooturl_admin + "generator/getColumnTable",
        dataType: 'json',
        success: function(json) {
            var jsonLength = json.length;
            var optionValue = '<option value="0">-- Select value column</option>';
            var optionText = '<option value="0">-- Select text column</option>';
            if (jsonLength > 0) {
                for (var i = 0; i < jsonLength; i++) {
                    optionValue += '<option value="' + json[i].name + '">' + json[i].name + '</option>';
                    optionText += '<option value="' + json[i].name + '">' + json[i].name + '</option>';
                }
                selectValue.html(optionValue);
                selectText.html(optionText);
            }
            selectValue.attr('disabled', false);
            selectText.attr('disabled', false);
        }
    });
}