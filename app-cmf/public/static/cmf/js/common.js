// ajax方法封装
function ajaxJson(mothod,url, data, dataType, successfn, loadingfn){
    data = (data == null || data == "" || typeof(data) == "undefined") ? "" : data;
    $.ajax({ 
        type: mothod, 
        url: url, 
        data: data, 
        async: true, 
        dataType: dataType, 
        success: successfn, 
        beforeSend: loadingfn, 
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            console.log(textStatus); 
            console.log(errorThrown);
        }
    });
}

// index.html页面左侧菜单栏渲染
// 树形结构菜单渲染函数
function treeMenu(a){
    this.tree=a||[];
    this.groups={};
};
treeMenu.prototype={
    init:function(pid){
        this.group();
        return this.getDom(this.groups[pid]);
    },
    group:function(){
        for(var i=0;i<this.tree.length;i++){
            if(this.groups[this.tree[i].parentid]){
                this.groups[this.tree[i].parentid].push(this.tree[i]);

            }else{
                this.groups[this.tree[i].parentid]=[];
                this.groups[this.tree[i].parentid].push(this.tree[i]);
            }
        }
    }, 
    getDom:function(a){
        if(!a){return ''}
        var html='';
        for(var i=0;i<a.length;i++){
            html+='<li class="treeview"><a href=javascript:openapp("'+a[i].src+'","'+a[i].id+'","'+a[i].name+'",true);><i class="first_icon fa fa-circle-o skin-blue"></i> <span>'+a[i].name+'</span></a><ul class="treeview-menu">';
            html+=this.getDom(this.groups[a[i].id]);
            html+='</ul></li>\n';
        };
        return html;            
    } 
};

// --------------------各个表单表单验证-----------------------------
// 用户组验证封装
function validate_group(id){
  $("#"+id+"").validate({
      rules: {
        group_name: "required"          
      },
      messages: {
        group_name: "用户组名称不能为空"
      },
      submitHandler:function(form){
          var type = $("form").attr("data-id");
          console.log(type); 
          if(type == "GroupForm"){
            var new_group = group_data();
            console.log(new_group);
            ajaxJson("post",groupViewAdd,new_group,'json',reload);
          }
          else{
            var id = parseInt($(".group_id").html());
            var old_group = group_data();
            old_group.id = id;
            console.log(old_group);
            ajaxJson("post",groupViewEdit,old_group,'json',reload);            
          }
      } 
  });   
}
// 菜单验证封装
function validate_menu(id){
  $("#"+id+"").validate({
      rules: {
        menu_name: "required",
        application: "required",
        control: "required", 
        way: "required",  
        // param: "required"       
      },
      messages: {
        menu_name: "菜单名称不能为空",
        application: "应用名称不能为空",
        control: "控制器名称不能为空", 
        way: "方法不能为空",  
        // param: "参数不能为空"                            
      },
      submitHandler:function(form){
          var type = $("form").attr("data-id");  
          if(type == "MenuForm"){
            var new_menu = menu_data();
            ajaxJson("post",menuViewAdd,new_menu,'json',refresh);
          }
          else{
            var id = parseInt($(".menu_id").html()); 
            var old_menu = menu_data();
            old_menu.id = id;
            console.log(old_menu);
            ajaxJson("post",menuViewEdit,old_menu,'json',refresh);            
          }
      }  
  });  
}

// 用户列表验证封装
function validate_info(id){
  $("#"+id+"").validate({
      rules: {
        user: "required"           
      },
      messages: {
        user: "用户名不能为空"
      },
      submitHandler:function(form){ 
        var type = $("form").attr("data-id");
        if(type == "AdminForm"){
          var new_info = info_data();
          console.log(new_info);
          ajaxJson("post",logicAdd,new_info,'json',reload);
        }
        else{
          var id = parseInt($(".user_id").html());
          var old_info = info_data();
          old_info.id = id;
          console.log(old_info);
          ajaxJson("post",logicEdit,old_info,'json',reload);            
        }
      }  
  });  
}
// 角色管理验证封装
function validate_role(id){
  $("#"+id+"").validate({
    rules: {
      role: "required"                    
    },
    messages: {         
      role: "角色名不能为空"           
    },
    submitHandler:function(form){
        var type = $("form").attr("data-id");  
        if(type == "RoleForm"){
          var role = $("#role").val();
          var new_role = {"role":role};
          console.log(new_role);
          ajaxJson("POST",logicAdd,new_role,'json',reload);
        }
        else{
          var id = parseInt($(".role_id").html());
          var role = $("#role").val();
          old_role = {"id":id,"role":role};
          console.log(old_role);
          ajaxJson("POST",logicEdit,old_role,'json',reload);            
        }
    }     
  });  
}

// function validate_user(){
//   $("#set-user").validate({
//     submitHandler:function(form){
//       upload();
//     }  
//   })
// }

// 用户信息上传验证
function validate_user(){
  $("#set-user").validate({
    rules: {
      // password: "required",
      confirm_password: {
        // required: true,
        equalTo: "#password"
      }      
    },
    messages: {
      password: "密码不能为空",
      confirm_password: {
        required: "确认密码不能为空",
        equalTo: "两次密码输入不一致"
      }      
    },
    submitHandler:function(form){
      upload();
    }  
  })
}

// --------------------各个表单提交数据封装-----------------------------
// 获取菜单新增数据
function menu_data(){
  var parentid = parseInt($("#parent").val());  
  var parent = $("#parent").find("option:selected").text();
  var group = $("#menu_name").val();
  var app = $("#application").val();
  var control = $("#control").val();
  var fn = $("#way").val();
  var param = $("#param").val();
  var state = $("#menu_state").val();   
  var menu = {"parentid":parentid,"parent":parent,"group":group,"app":app,"control":control,"fn":fn,"param":param,"state":state};
  return menu;
}

// 获取用户组新增数据
function group_data(){
  var parentid = parseInt($("#parent").val());  
  var parent = $("#parent").find("option:selected").text().trim();
  var group = $("#group_name").val();
  var state = $("#state").val();   
  var g_data = {"parentid":parentid,"parent":parent,"group":group,"state":state};
  return g_data;
}

// 获取用户新增数据
function info_data(){
  var user = $("#user").val();
  var group_id = $("#group").val();
  var role_id = $("#role").val();
  var info_data = {"username":user,"group_id":group_id,"role_id":role_id};
  return info_data;
}

// 绘制角色下拉列表
function role_list(){
  ajaxJson("get",role,null,'json',role_list);
  function role_list(data){
    var role_len = data.data.length;
    var role_html = "";
    for(var i= 0;i < role_len;i++){
      var role_item = data.data[i];
      // console.log(role_item);
      role_html += '<option value="'+role_item.id+'">'+role_item.role+'</option>';
    }
    $("#role").empty().append(role_html);
  }
}
// --------------------全局函数定义-----------------------------
// 刷新函数
function reload(data){
  if(data.status == 1){
    balloon_alert("alert-success","fa-check-square-o","操作成功"); 
    setTimeout(delay,500);      
  }
  else{
    balloon_alert("alert-error","fa-times-circle","操作失败");
  }

}
function delay(){
  location.reload(true);
  return;
} 
// 树形结构下子类全选
// function checkPower(){
//   $(this).hasClass("expandable")

//   // $('#text_box').on('ifChecked', function(event){
//   //   $('input').iCheck('check');
//   // });
//   // $('#text_box').on('ifUnchecked', function(event){
//   //   $('input').iCheck('uncheck');
//   // });
// }

// 全选
function allchk(){
    var chknum=$("tbody :checkbox").size();
    var chk=0;
    $("tbody :checkbox").each(function(){
        if($(this).prop("checked")=="checked") {
            chk++;
        }
    });
    if(chknum==chk) {
        $("#chkall").prop("checked","checked");
    }else {
        $("#chkall").prop("checked",false);
    };
}


/**
 * [checkboxAll 全选checkbox]
 * @param  {[type]} name [选择全选checkbox的名称]
 * @return {[type]}      [description]
 */
function checkboxClick(obj){
   var ischecked=obj.attr("checked");
   if(ischecked=="checked") {
    obj.removeAttr("checked");
   }else {
    obj.attr("checked","checked");
   }  
}

/**
 * [delMutil 批量删除]
 * 1、获取选中的id
 * 2、作为数组数据传递
 * @return {[type]} [description]
 */
function delMutil(){
    var delArr=[];
    $("tbody :checkbox").each(function(i){//获取唯一键值
        if(this.checked){
            var delId=parseInt($(this).parent().siblings(".data-id").text().trim());
            delArr.push(delId);
        }
    });
    return delArr;
}


//全选或全不选
function changeCheck(){
  $("#chkall").click(function(){
    if(this.checked) {
        $("tbody :checkbox").prop("checked","checked");
      }else {
        $("tbody :checkbox").prop("checked",false);
      }
    });
  $("tbody :checkbox").click(function(){//复选框单选
    allchk();
  });
}

var menu_json = "<option value='-1'>一级菜单</option><option value='5'>设置</option><option value='1' >&nbsp;├ 网站设置</option><option value='11' >&nbsp;├ 个人信息</option><option value='2' > 用户管理</option><option value='21' >&nbsp;└ 用户管理列表</option><option value='3' > 角色管理</option><option value='36' > 用户组管理</option>";
function initSelect(){
  // var menu = "<option value='设置' >设置</option><option value='网站配置' >&nbsp;├ 网站配置</option><option value='个人信息' >&nbsp;├ 个人信息</option><option value='网站设置' >&nbsp;└ 网站设置</option><option value='用户管理' > 用户管理</option><option value='12' >&nbsp;└ 用户管理列表</option><option value='角色管理' > 角色管理</option><option value='用户组管理' > 用户组管理</option><option value='菜单管理' >&nbsp;└ 菜单管理</option>";
  // $("#parent").empty().append(menu_json);
  $("#parent").get(0).selectedIndex= -1;  
}
function setSelect(value){
  // var menu = "<option value='一级菜单' >一级菜单</option><option value='设置' >设置</option><option value='网站设置' >&nbsp;├ 网站设置</option><option value='个人信息' >&nbsp;├ 个人信息</option><option value='网站设置' >&nbsp;└ 网站设置</option><option value='用户管理' > 用户管理</option><option value='12' >&nbsp;└ 用户管理列表</option><option value='角色管理' > 角色管理</option><option value='角色管理列表' > 角色管理列表</option><option value='用户组管理' > 用户组管理</option><option value='菜单管理' >&nbsp;└ 菜单管理</option>";
  // $("#parent").empty().append(menu_json);
  $("#parent").select2().val(value).trigger("change");  
}

// --------------------树形结构初始化-----------------------------
// 菜单列表树形结构渲染
function treetable(a){
    this.tree=a||[];
    this.groups={};
};
treetable.prototype={
    init:function(pid){
        this.group();
        return this.getDom(this.groups[pid]);
    },
    group:function(){
        for(var i=0;i<this.tree.length;i++){
            if(this.groups[this.tree[i].parentid]){
              this.groups[this.tree[i].parentid].push(this.tree[i]);

            }else{
              this.groups[this.tree[i].parentid]=[];
              this.groups[this.tree[i].parentid].push(this.tree[i]);
            }
        }
    },
    getDom:function(a){
        if(!a){return ''}
        var html='';
        for(var i=0;i<a.length;i++){
            html+='<tr id="'+a[i].id+'" pid="'+a[i].parentid+'"><td class="level"></td><td class="parent_id hide" data-parent="'+a[i].parentid+'"></td><td class="item-id" controller="true">'+a[i].id+'</td><td class="item-group" controller="true">'+a[i].name+'</td><td controller="true">'+a[i].src+'</td><td><button class="btn-flat btn-primary form-control new_child mr" data-toggle="modal" data-target="#add_menu">添加子组</button><button class="btn-flat btn-primary form-control edit_menu mr" data-toggle="modal" data-target="#add_menu">编辑</button><button class="btn-flat btn-primary form-control del_menu">删除</button></td></tr>';
            html+=this.getDom(this.groups[a[i].id]);
        };
        return html;   
    }    
};



// 菜单栏函数渲染
function drawTable(){
   ajaxJson("get",menuTree,null,'json',drawTree);
   function drawTree(data){
        var treeLen = data.data.length;
        var html=new treetable(data.data).init(0);
        $("#MenuList tbody").empty().append(html);
        var option = {
            theme:'vsStyle',
            expandLevel : 1,
            beforeExpand : function($treeTable, id) {
                //判断id是否已经有了孩子节点，如果有了就不再加载，这样就可以起到缓存的作用
                // if ($('.' + id, $treeTable).length) { return; }
                if ($('.' + id, $treeTable).length) { $(this).addClass("per"); }
                //这里的html可以是ajax请求

                var html = '';

                $treeTable.addChilds(html);
            },
            onSelect : function($treeTable, id) {
            }

        };
        $('#MenuList').treeTable(option);


    $("#MenuList").on("click",".del_menu",function(){
        var isParent = $(this).parents("tr").attr("haschild");
        var item_id = parseInt($(this).parent().siblings(".item-id").html());
        if(isParent == "true"){
          // alert("该记录含有下属子级，不可直接删除");
          balloon_alert("alert-danger","fa-exclamation-triangle","该记录含有下属子级，不可直接删除");
        }
        else{
          swal({ 
              title: "",   
              text: "您确定要确定删除该条记录吗？",   
              type: "warning",   
              showCancelButton: true,   
              confirmButtonColor: "#d9534f",  
              cancelButtonText: "取消",   
              confirmButtonText: "确定",   
              closeOnConfirm: true
            }, 
            function() { 
              var id_num = {"id":item_id};
              ajaxJson("get",logicDelete,id_num,'json',refresh);
          });           
        }          
    })


    /**
     * [新建菜单模态框加载]
     * 1、填充父级下拉框
     * 2、作为数组数据传递
     */
    $(".new_menu").click(function(){
      $('#add_menu').load(menuViewAdd,function(){
          validate_menu("MenuForm");
          initSelect(); 
      })
        
    })
    $("#MenuList").on("click",".new_child",function(){
      var data_group = parseInt($(this).parent().siblings(".item-id").html());
      $('#add_menu').load(menuViewAdd,function(){
          validate_menu("MenuForm");
          setSelect(data_group);
          $("#parent").attr('disabled',true);
      })
    })
    /**
     * [编辑菜单模态框加载]
     * 1、填充父级下拉框
     * 2、填充该条数据对应数据
     */    
    $("#MenuList").on("click",".edit_menu",function(){
        var data_id = parseInt($(this).parent().siblings(".item-id").html());
        var data_parent = $(this).parent().siblings(".parent_id").attr("data-parent");
        $('#add_menu').load(menuViewEdit,function(){         
            for(var i=0;i<treeLen;i++){
                var item = data.data[i];
                if(item.id == data_id){
                  setSelect(data_parent);  
                  // name = item.name.split(" ");
                  var str = item.name;
                  str = str.replace(/&nbsp;/g,''); // 将空格设置为空
                  console.log(str)
                  var mm = str.split("", 1);  // 将空格设置为空
                  var icon = mm.join();
                  str = str.replace(icon,'')
                  $("#menu_name").val(str.trim());
                  var str = item.src;
                  str=str.substr(1);
                  var len = str.split("/");
                  $("#application").val(len[0]);
                  $("#control").val(len[1]);
                  $("#way").val(len[2]);
                  $("#param").val(len[3]);
                  $("#menu_state").val(item.state); 
                  $(".menu_id").html(data_id); 
                }
            }
            validate_menu("MenuEdit"); 

        })
    })

    // add by MJ 2016.10.8
    // 导入菜单
    $(".upload-menu").click(function(){
      $('#add_menu').load(importMenu,function(){
        $(".upload_btn").click(function(){
          upload_file();
        })
        
      })
    })

    // 导出菜单
    $(".download-menu").click(function(){
      ajaxJson("get",exportMenu,null,'json',download_file);
      function download_file(data){
        if(data.status == 1){
          var down_html = '<a class="btn-flat btn-primary form-control download-menu" href="'+data.data+'" download=""><i class="fa fa-check-square-o"></i>菜单生成成功</a>';
          $(".download_cube").empty().append(down_html);
        }
        else{
          balloon_alert("alert-error","fa-times-circle","菜单生成失败"); 
          // alert("菜单生成失败");
        }
      }

    })        


  }
}
// 用户组列表树形结构渲染
function treegroup(a){
    this.tree=a||[];
    this.groups={};
};
treegroup.prototype={
    init:function(pid){
        this.group();
        return this.getDom(this.groups[pid]);
    },
    group:function(){
        for(var i=0;i<this.tree.length;i++){
            if(this.groups[this.tree[i].parentid]){
              this.groups[this.tree[i].parentid].push(this.tree[i]);

            }else{
              this.groups[this.tree[i].parentid]=[];
              this.groups[this.tree[i].parentid].push(this.tree[i]);
            }
        }
    },
    getDom:function(a){
        if(!a){return ''}
        var html='';
        for(var i=0;i<a.length;i++){
            html+='<tr id="'+a[i].id+'" pid="'+a[i].parentid+'"><td class="level"></td><td class="parent_id hide" data-parent="'+a[i].parentid+'"></td><td class="item-id" controller="true">'+a[i].id+'</td><td class="item-group" controller="true">'+a[i].group+'</td><td><button class="btn-flat btn-primary form-control new_child mr" data-toggle="modal" data-target="#add_group">添加子组</button><button class="btn-flat btn-primary form-control edit_group mr" data-toggle="modal" data-target="#add_group">编辑</button><button class="btn-flat btn-primary form-control del_group">删除</button></td></tr>';
            html+=this.getDom(this.groups[a[i].id]);
        };
        return html;   
    }    
};
// 用户组管理列表
function drawGroup(){
   ajaxJson("get",groupTree,null,'json',Group);
   function Group(data){
        var groupLen = data.data.length;
        var html=new treegroup(data.data).init(0);
        $("#GroupList tbody").empty().append(html);
        var option = {
            theme:'vsStyle',
            expandLevel : 1,
            beforeExpand : function($treeTable, id) {
                //判断id是否已经有了孩子节点，如果有了就不再加载，这样就可以起到缓存的作用
                if ($('.' + id, $treeTable).length) { return; }
                //这里的html可以是ajax请求
                $treeTable.addChilds(html);
            },
            onSelect : function($treeTable, id) {
                // window.console && console.log('onSelect:' + id);
            }

        };
        $('#GroupList').treeTable(option);
        changeCheck();

        // 用户组模态框加载
        $(".new_group").click(function(){
          $('#add_group').load(groupViewAdd,function(){
            
            validate_group("GroupForm");
          })
        })

        // 删除记录
        $("#GroupList").on("click",".del_group",function(){
            var isParent = $(this).parents("tr").attr("haschild");
            var item_id = parseInt($(this).parent().siblings(".item-id").html());
            del_item(isParent,item_id,logicDelete);
        }) 


        $("#GroupList").on("click",".edit_group",function(){
          var data_id = parseInt($(this).parent().siblings(".item-id").html());
          var data_parent = $(this).parent().siblings(".parent_id").attr("data-parent");      
          $('#add_group').load(groupViewEdit,function(){
            
            for(var i=0;i<groupLen;i++){
                var item = data.data[i];
                if(item.id == data_id){ 
                  setSelect(data_parent);
                  var str = item.group;
                  str = str.replace(/&nbsp;/g,''); // 将空格设置为空
                  var mm = str.split("", 1);  // 将空格设置为空
                  var icon = mm.join();
                  str = str.replace(icon,'')
                  $("#group_name").val(str.trim());
                  $("#state").val(item.state); 
                  $(".group_id").html(data_id);           
                }
            }
            $('#parent').change(function(){
              $("#group_name").val("");
              $("#state").val("");            
            })        
            validate_group("GroupEdit");     
          })      
        })

        $("#GroupList").on("click",".new_child",function(){
          var data_group = parseInt($(this).parent().siblings(".item-id").html()); 
          console.log(data_group);     
          $('#add_group').load(groupViewAdd,function(){
            setSelect(data_group);
            $("#parent").attr('disabled',true);        
            validate_group("GroupForm");     
          })      
        }) 

        // add by MJ 2016.10.8
        // 导入菜单
        $(".upload").click(function(){
          $('#add_group').load(importGroup,function(){
            $(".upload_btn").click(function(){
              upload_file_group();
            })
            
          })
        })

        // 导出菜单
        $(".download").click(function(){
          ajaxJson("get",exportGroup,null,'json',download_file);
          function download_file(data){
            if(data.status == 1){
              var down_html = '<a class="btn-flat btn-primary form-control download-menu" href="'+data.data+'" download=""><i class="fa fa-check-square-o"></i>菜单生成成功</a>';
              $(".download_button").empty().append(down_html);
            }
            else{
              balloon_alert("alert-error","fa-times-circle","菜单生成失败");               
              // alert("菜单生成失败");
            }
          }

        })   


    }
}

// 权限树形结构渲染
function treePower(a){
    this.tree=a||[];
    this.groups={};
};
treePower.prototype={
    init:function(pid){
        this.group();
        return this.getDom(this.groups[pid]);
    },
    group:function(){
        for(var i=0;i<this.tree.length;i++){
            if(this.groups[this.tree[i].parentid]){
              this.groups[this.tree[i].parentid].push(this.tree[i]);

            }else{
              this.groups[this.tree[i].parentid]=[];
              this.groups[this.tree[i].parentid].push(this.tree[i]);
            }
        }
    },
     getDom:function(a){
        if(!a){return ''}
        var html='\n<ul>\n';
        for(var i=0;i<a.length;i++){
          if(a[i].check == 0){
            html+='<li class="closed"><input class="power_input" type="checkbox" data-parent="'+a[i].parentid+'" value="'+a[i].id+'">'+a[i].name+'</span>';            
          }
          else{
            html+='<li class="closed"><input class="power_input" type="checkbox" data-parent="'+a[i].parentid+'" value="'+a[i].id+'" checked>'+a[i].name+'</span>';              
          }
            html+=this.getDom(this.groups[a[i].id]);
            html+='</li>\n';
        };
        html+='</ul>\n';
        return html;    
    }      
};


// 角色管理列表
function drawRole(){
  ajaxJson("get",roleData,null,'json',role);
  function role(data){
    var roleLen = data.data.length;
    // 表格数据渲染
    $('#RoleList').DataTable( {
        "bDestroy":true,
        "ajax": {
          "url": roleData,
          "type": "post",
          "data": null
        },          
        "language":{
            "paginate":{
                "next":"下一页",
                "previous":"上一页",
            },
            "lengthMenu":"显示 _MENU_ 条",
            "info":"显示 _START_ 至 _END_ 条,共 _TOTAL_ 条",
            "search":"搜索",
            "searchPlaceholder":"搜索...",
            "zeroRecordsDT":"搜索无结果"
        },
        "columns": [    
        {"data":"id","class":"data-id"},
        {"data":"role","class":"data-id"},                 
        {"data":null,"class":"center setBtn","render":function(obj){
            var btn= '<button class="btn-flat btn-primary form-control set_power mr" data-toggle="modal" data-target="#add_role">权限设置</button>'
            +'<button class="btn-flat btn-primary form-control edit_role mr" data-toggle="modal" data-target="#add_role">编辑</button>'
            +'<button class="btn-flat btn-primary form-control delt_role">删除</button>';         
            return btn;

        }}                                                                              
        ]
    });

    // 新增角色信息
    $(".new_role").click(function(){
      $('#add_role').load(viewAdd,function(){
        validate_role("RoleForm");          
      })
    }) 
    // 编辑角色信息
    $("#RoleList").on("click",".edit_role",function(){
      var data_id = parseInt($(this).parent().siblings(".data-id").html());
      $('#add_role').load(viewEdit,function(){
        for(var i=0;i<roleLen;i++){
            var item = data.data[i];
            if(item.id == data_id){
              $(".role_id").html(data_id);
              $("#role").val(item.role);     
            }
        }        
        validate_role("RoleEdit");          
      })
    })

    // 角色列表数据删除
    $("#RoleList").on("click",".delt_role",function(){
      var item_id = parseInt($(this).parent().siblings(".data-id").html());
      del_tr(item_id,logicDelete);
    })  

    $("#RoleList").on("click",".set_power",function(){
      var data_id = parseInt($(this).parent().siblings(".data-id").html());
      $('#add_role').load(editPower,function(){
        $(".powerRole_id").html("data_id");
        var user_arr = {"id":data_id};
        ajaxJson("post",menuTree,user_arr,'json',rolePower);
        function rolePower(data){

          // var user_len = data.data.length;
          // for(var u=0;u<user_len;u++){
            // var user_info = data.data[u];
            console.log(data.data);
            // if(user_info.id == data_id){
              // console.log(user_info.power);
              var html=new treePower(data.data.power).init(0);
              // console.log(html);
              $("#PowerEdit .power").append(html);
              $("#PowerEdit .power ul:first").addClass("filetree treeview-famfamfam power_set");
              // console.log(html);
              // treeview初始化
              $(".power_set").treeview({
                toggle: function() {
                  console.log("%s was toggled.", $(this).find(">span").text());
                }
              }); 

              // icheck美化
              $('.power_input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '10%' // optional
              });
              
            // }
          // }

          // 设置父类选中子类全选状态
          $('.power_input').on("ifClicked", function (ent){
            if($(this).is(":checked")){
              // alert("选中改未选中");
              $(this).parent().parent("li").removeClass("hascheck").addClass("discheck");
              $("li.discheck .icheckbox_square-blue").removeClass("checked");
            }
            else{
              // alert("未选中改选中");
              $(this).parent().parent("li").removeClass("discheck").addClass("hascheck");
              $("li.hascheck .icheckbox_square-blue").addClass("checked");                

            }
          })

          // 权限设置提交
          $("#PowerEdit").validate({
            submitHandler:function(form){ 
              var check_num = "";
              var power_len = $(".power li").length;
              // console.log(power_len);
              for(var z=0;z<power_len;z++){

                var isCheck = $(".power li").eq(z).children(".icheckbox_square-blue").hasClass("checked");
                if(isCheck == true){
                  var check_id = $(".power li").eq(z).children(".icheckbox_square-blue").children(".power_input").val();
                  // console.log(check_id);
                  // check_num.push(check_id);
                  check_num += check_id + ","
                }
              } 
              console.log(check_num);
              var check_arr = {"id":data_id};
              check_arr.power = check_num;
              // check_arr.power = JSON.stringify(check_num);
              ajaxJson("post",editPower,check_arr,'json',reload);

            }  
          });  

        }           
      })
    })     
           
  }
}


// 用户管理列表
function drawUser(){
  ajaxJson("get",userData,null,'json',user);

  function user(data){
    var userLen = data.data.length;
    $('#AdminList').DataTable({
        "bDestroy":true,
        "ajax": {
          "url": userData,
          "type": "post",
          "data": null
        },          
        "language":{
            "paginate":{
                "next":"下一页",
                "previous":"上一页",
            },
            "lengthMenu":"显示 _MENU_ 条",
            "info":"显示 _START_ 至 _END_ 条,共 _TOTAL_ 条",
            "search":"搜索",
            "searchPlaceholder":"搜索...",
            "zeroRecordsDT":"搜索无结果"
        },
        "columns": [     
        {"data":"id","class":"data-id"},
        {"data":"username","class":"username"},
        {"data":"role","class":"role"},
        {"data":"group","class":"group"},              
        {"data":null,"class":"center setBtn","render":function(obj){
            var btn= '<button class="btn-flat btn-primary form-control edit_info mr" data-toggle="modal" data-target="#add_info">编辑</button>'
            +'<button class="btn-flat btn-primary form-control del_info">删除</button>';
            return btn;
        }}                                                                              
        ]
    });

    // 新增用户信息
    $(".new_info").click(function(){
      $('#add_info').load(viewAdd,function(){
        // 用户新增页面表单验证
        role_list(); 
        validate_info("AdminForm");
      })
    })



    // 编辑用户信息
    $("#AdminList").on("click",".edit_info",function(){
      var data_id = parseInt($(this).parent().siblings(".data-id").html());
      console.log(data_id);
      $('#add_info').load(viewEdit,function(){ 
        // role_list(); 
        for(var i=0;i<userLen;i++){
            var item = data.data[i];
            if(item.id == data_id){
              console.log(item)                        
              $("#user").val(item.username);

              $("#group").select2().val(item.group_id).trigger("change");
              $("#role").select2().val(item.role_id).trigger("change");
              $(".user_id").html(data_id);                          
            }
        }         
        validate_info("AdminEdit");
      })
    }) 

    // 用户列表信息删除
    $("#AdminList").on("click",".del_info",function(){
      var item_id = parseInt($(this).parent().siblings(".data-id").html());
      del_tr(item_id,logicDelete);
    })      

  }

}

function singleUser(){
    validate_user();  
}

// add by MJ 2016.10.8
// 菜单导入功能
function upload_file(){
    // 请求的后端方法
    var url=importMenu;
    // 获取文件
    var file = document.getElementById('upload').files[0];     
    // 初始化一个 XMLHttpRequest 对象
    var xhr = new XMLHttpRequest();
    // 初始化一个 FormData 对象
    var form = new FormData();

    // 携带文件
    form.append("file", file);         
    //开始上传
    xhr.open("POST", url, true);
    //在readystatechange事件上绑定一个事件处理函数
    xhr.onreadystatechange=callback;
    xhr.send(form);

    function callback() {
      if(xhr.readyState == 4){
          if(xhr.status == 200){
              if(xhr.responseText == 1){
                  balloon_alert("alert-success","fa-check-square-o","导入成功");
                  // alert('导入成功');
                  // window.parent.location.reload(true);
                  // return;
              }else{
                  balloon_alert("alert-error","fa-times-circle","导入失败");
                 // alert("导入失败");
             }
          }
      }
    }
} 



// add by null 2016.10.8
// 用户组导入功能
function upload_file_group(){
    // 请求的后端方法
    var url=importGroup;
    // 获取文件
    var file = document.getElementById('upload_group').files[0];     
    // 初始化一个 XMLHttpRequest 对象
    var xhr = new XMLHttpRequest();
    // 初始化一个 FormData 对象
    var form = new FormData();

    // 携带文件
    form.append("file", file);         
    //开始上传
    xhr.open("POST", url, true);
    //在readystatechange事件上绑定一个事件处理函数
    xhr.onreadystatechange=callback;
    xhr.send(form);

    function callback() {
      if(xhr.readyState == 4){
          if(xhr.status == 200){
              if(xhr.responseText == 1){
                  balloon_alert("alert-success","fa-check-square-o","导入成功");
                  // alert('导入成功');
                  // window.parent.location.reload(true);
                  // return;
              }else{
                  balloon_alert("alert-error","fa-times-circle","导入失败");
                 // alert("导入失败");
              }
          }
      }
    }
} 

// 头像上传文件
function upload(){
    // 请求的后端方法
    var url=userinfo;
    // 获取文件
    var pic = document.getElementById('file').files[0];
    var name = $("#username").val();
    var tel = $("#tel").val();
    var email = $("#email").val();    
    var mark = $("#remark").val();
    var group = $("#group").val();    
    var password = $("#password").val(); 
    var confirm_password = $("#confirm_password").val();        
    // 初始化一个 XMLHttpRequest 对象
    var xhr = new XMLHttpRequest();
    // 初始化一个 FormData 对象
    var form = new FormData();

    // 携带文件
    form.append("nikname", name);
    form.append("head", pic);
    form.append("phone", tel);
    form.append("mail", email);
    // form.append("role", role);
    form.append("mark", mark);
    form.append("password", password);
    form.append("confirm_password", confirm_password);             
    //开始上传
    xhr.open("POST", url, true);
    //在readystatechange事件上绑定一个事件处理函数
    xhr.onreadystatechange=callback;
    xhr.send(form);

    function callback() {
      location.reload(true);
      return;
   }
} 


// 头像上传控件
function previewFile() {
  var preview = document.querySelector('.preview-img');
  var file    = document.querySelector('#file').files[0];
  var reader  = new FileReader();

  reader.addEventListener("load", function () {
    preview.src = reader.result;
  }, false);

  if (file) {
    $(".cover-img").removeClass("hide");
    reader.readAsDataURL(file);
  }
}

// 树形结构表格删除效果函数
function del_item(isParent,item_id,url){
  if(isParent == "true"){
    // alert("该记录含有下属子级，不可直接删除");
    balloon_alert("alert-danger","fa-exclamation-triangle","该记录含有下属子级，不可直接删除");
  }
  else{
    swal_alert(item_id,url);
    // if(confirm('确定要删除吗?')){ 
    //   var id_num = {"id":item_id};
    //   ajaxJson("get",url,id_num,'json',reload);
    //   alert("删除成功");       
    //   return true; 
    // } 
  }  
}

// 单层结构表格删除效果封装
function del_tr(item_id,url){ 
  swal_alert(item_id,url);
}

// sweetalert删除效果封装
function  swal_alert(item_id,url){
  swal({ 
      title: "",   
      text: "您确定要确定删除该条记录吗？",   
      type: "warning",   
      showCancelButton: true,   
      confirmButtonColor: "#d9534f",  
      cancelButtonText: "取消",   
      confirmButtonText: "确定",   
      closeOnConfirm: true
    }, 
    function() { 
      var id_num = {"id":item_id};
      ajaxJson("get",url,id_num,'json',reload);
  });  
}

function refresh(data){
  if(data.status == 1){
    console.log(refresh_menu)
    balloon_alert("alert-success","fa-check-square-o","操作成功");    
    ajaxJson("get",refresh_menu,null,'json',tree);
    function tree(data){
    var html=new treeMenu(data.data).init(0);
    // console.log(html);
    var all_html = '<ul class="sidebar-menu"><li class="header">HEADER</li>'+html+'</ul>'; 
    // console.log(all_html);
    $('.tree', window.parent.document).empty().append(all_html);
    $(".tree").append(all_html); 
    var ul_len = $('ul', window.parent.document).length;
    for(var i=0;i<ul_len;i++){
      if($('ul', window.parent.document).eq(i).children("li").length == 0){
        $('ul', window.parent.document).eq(i).addClass("children");
      }
    }
    $('ul.children', window.parent.document).remove();
    // $("ul.children").remove();

    var li_len = $('.tree li', window.parent.document).length;
    for(var i=0;i<li_len;i++){
      if($('.tree li', window.parent.document).eq(i).children("ul").length != 0){
        $('.tree li', window.parent.document).eq(i).addClass("has_child");
      }
    }

    $('li.has_child', window.parent.document).children("a").append('<i class="fa fa-angle-left pull-right"></i>');
    $('.tree>ul>li', window.parent.document).children("a").children(".first_icon").removeClass("fa-circle-o skin-blue").addClass("fa-link");          
    }
    setTimeout(delay,500);                              
  }
  else{
    balloon_alert("alert-error","fa-times-circle","操作失败");
  }
}

/**
 * [提示框效果封装]
    1、balloon_alert("alert-success","fa-check-square-o","操作成功");   
    2、balloon_alert("alert-error","fa-times-circle","操作失败");
    3、balloon_alert("alert-danger","fa-exclamation-triangle","该记录含有下属子级，不可直接删除");   
 */
function balloon_alert(type,icon,tip){
  // alert框页面代码 
  var alert_html = '<div class="alert '+type+' alert-dismissable g-t-center" style="margin-bottom:0;">'+
                      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+
                      '<p>'+
                        '<i class="icon fa '+icon+'"></i>'+tip+'！' +
                      '</p>'+
                    '</div>';    
  $('.fn-alert', window.parent.document).empty().append(alert_html);
  setTimeout(remove,500);
}

function remove(){
  $('.fn-alert>div', window.parent.document).remove();
}  