<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>调度系统</title>

    <!-- Bootstrap core CSS -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>

    
    <table class="table table-hover">
    <tr>
    <td>任务名</td>
    <td>规则</td>
    <td>url</td>
    <td>最后成功时间</td>
    <td>成功次数</td>
    <td>最后失败时间</td>
    <td>失败次数</td>
    <td>状态</td>
    <td>操作</td>
    </tr>
    	<!-- <div class="row"> -->
	<?php 
      	include 'Crondb.php';
	    $db = new mydb();
	    
	    if(!$db){
	        echo $db->lastErrorMsg();
	        return;
	     }

	    // 获取cron数据
	    $sql = 'select * from task';
	    $ret = $db->query($sql);
	    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
	    	// echo '<div class="col-md-4">';
	    	echo '<tr>';
          	echo '<td><h4>'.$row['name'].'</h2></td>';

          	$cron_parts = explode(' ' , $row['rule']);
		    // var_dump($cron_parts);
          	echo '<td><p>'.$row['rule'].'</p></td>';

          	
          	echo '<td><p>'.$row['url'].'</p></td>';
          	echo '<td><p>'.$row['success_time'].'</p></td>';
          	echo '<td><p>'.$row['success_count'].'</p></td>';
          	echo '<td><p>'.$row['fail_time'].'</p></td>';
          	echo '<td><p>'.$row['fail_count'].'</p></td>';
          	echo $row['is_on'] == 1 ? '<td>开启<a class="btn btn-danger" href="#" role="button">立即停止</a>' : '<td>未开启<a class="btn btn-success" href="#" role="button">立即开启</a></td>';
          	echo '<td><p><a class="btn btn-default" href="#" role="button">修改</a><a class="btn btn-default" href="#" role="button">删除</a></p></td>';
        	echo '</tr>';
	    }
	?>
     </table>
      <hr>

      <footer>
        <p>&copy; by null</p>
      </footer>
 

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  </body>
</html>
