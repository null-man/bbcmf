var params = {
    fileInput: $("#set_icon").get(0),
    /*url:$("#sub_project_set").attr("action"),
    upButton: $("#pro_set").get(0),
    formId:$("#sub_project_set"),*/
    filter: function(files) {
        var arrFiles = [];
        for (var i = 0, file; file = files[i]; i++) {
            if (file.type.indexOf("image") == 0) {
                arrFiles.push(file);
            } else {
                $.Prompt(file.name + '"不是图片格式');
            }
        }
        return arrFiles;
    },
    onSelect: function(files) {
        var  img = '', i = 0;
        var funAppendImage = function() {
            file = files[i];
            if (file) {
                var reader = new FileReader()
                reader.onload = function(e) {
                    img="<img src='" + e.target.result + "'/>";
                    i++;
                    funAppendImage();
                }
                reader.readAsDataURL(file);
            } else {
                if(img){
                    $("div.set-icon").html(img)
                }
            }
        };
        funAppendImage();
    }
};

fileupload = $.extend(fileupload, params);
fileupload.init();

function check_set_form(obj){
    if($("textarea[name='intro']").val()==''){
        $.Prompt('请填写项目介绍',"warn");
        return false;
    }
    return true
}
function check_sen_set_form(obj){
    if($("textarea[name='intro']").val()==''){
        $.Prompt('请填写项目关闭理由');
        return false;
    }
    return true;
}