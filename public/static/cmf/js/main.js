$(document).ready(function() {
  $('#AdminList').DataTable( {
      "bDestroy":true,
      "ajax": {
        "url": "userData",
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
			{"data":null,"orderable":false,"class":"center check","render":function(obj){
				return '<input type="checkbox"/>'
			}},      
			{"data":"id","class":"data-id"},
			{"data":"user_name","class":""},
      {"data":"role","class":""},
			{"data":"group","class":""},              
      {"data":null,"class":"center setBtn","render":function(obj){
          var btn= '<button class="btn-flat btn-primary form-control edit_info" data-toggle="modal" data-target="#add_info">编辑</button>'
          return btn;
      }}								                                                              
      ]
  }); 
  $('#RoleList').DataTable( {
      "bDestroy":true,
      "ajax": {
        "url": "roleData",
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
      {"data":null,"orderable":false,"class":"center check","render":function(obj){
        return '<input type="checkbox"/>'
      }},      
      {"data":"id","class":"data-id"},
      {"data":"role","class":"data-id"},                 
      {"data":null,"class":"center setBtn","render":function(obj){
          var btn= '<button class="btn-flat btn-primary form-control set_power mr" data-toggle="modal" data-target="#add_admin">权限设置</button>'
          +'<button class="btn-flat btn-primary form-control edit_role" data-toggle="modal" data-target="#add_admin">编辑</button>';
          return btn;

      }}                                                                              
      ]
  });

  // 新增用户信息
  $(".new_info").click(function(){
    $('#add_info').load("./modal/add_admin.html",function(){
      // 用户新增页面表单验证
      $("#AdminForm").validate({
          rules: {
            user: "required",
            password: "required"              
          },
          messages: {
            user: "用户名不能为空",
            password: "密码不能为空"
          }
      });     
    })
  })

  // // 编辑用户信息
  // $("#AdminList").on("click",".edit_info",function(){
  //   $('#add_info').load("./modal/add_info.html",function(){
  //     $("#InfoForm").validate({
  //         rules: {
  //           password: "required",
  //           confirm_password: {
  //             required: true,
  //             equalTo: "#password"
  //           },                       
  //         },
  //         messages: {         
  //           password: "密码不能为空",
  //           confirm_password: {
  //             required: "确认密码不能为空",
  //             equalTo: "两次密码输入不一致"
  //           },              
  //         }
  //     });        
  //   })
  // }) 

  // $("#RoleList").on("click",".set_power",function(){
  //   $('#add_role').load("./modal/edit_power.html",function(){
  //     // treeview初始化
  //     $("#power_set").treeview({
  //       toggle: function() {
  //         console.log("%s was toggled.", $(this).find(">span").text());
  //       }
  //     }); 
  //     // icheck美化
  //     $('.power').iCheck({
  //       checkboxClass: 'icheckbox_square-blue',
  //       radioClass: 'iradio_square-blue',
  //       increaseArea: '10%' // optional
  //     });           
  //   })
  // })
  

  // 新增角色信息
  $(".new_role").click(function(){
    $('#add_role').load("./modal/add_role.html",function(){
      validate_role("RoleForm");      
    })
  }) 
  // 编辑用户信息
  $("#RoleList").on("click",".edit_role",function(){
    $('#add_admin').load("./modal/edit_role.html",function(){
      validate_role("RoleEdit");          
    })
  })    

  // 用户组模态框加载
  $(".new_group").click(function(){
    $('#add_group').load("./modal/add_group.html",function(){
      validate_group("GroupForm");
    })
  })
  $(".edit_group").click(function(){
    $('#add_group').load("./modal/edit_group.html",function(){
      validate_group("GroupEdit");     
    })
  })  

  // 上传菜单
  $(".upload").click(function(){
    $('#add_group').load("./modal/upload.html",function(){})
  })
  $(".upload-menu").click(function(){
    $('#add_menu').load("./modal/upload.html",function(){})
  })  

  // 菜单模态框加载
  $(".new_menu").click(function(){
    $('#add_menu').load("viewAdd",function(){
      validate_menu("MenuForm");       
    })
  })

    // 菜单模态框加载
  $(".edit_menu").click(function(){
    $('#add_menu').load("./modal/edit_menu.html",function(){
      validate_menu("MenuEdit");       
    })
  })


  //全选或全不选
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

  // 删除效果
  $("#btn-delMutil").click(function(){
    delList();
  })

})

