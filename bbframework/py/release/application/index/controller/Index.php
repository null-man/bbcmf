<?php

namespace app\index\controller;

use bb\Controller;

class Index extends Controller {

	public function index() {

		$str = <<<STR
<style type="text/css">
*{ padding: 0; margin: 0; } 
div{ padding: 4px 48px;} 
a{color:#2E5CD5;cursor: pointer;text-decoration: none} 
a:hover{text-decoration:underline; } 
body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } 
p{ line-height: 1.6em; font-size: 42px }
</style>
<div style="padding: 24px 48px;"> 
<h1>:)</h1>
<p> bbframework <br/>
<span style="font-size:30px">宝宝巴士PHP开发框架</span>
</p><span style="font-size:22px;">[ <a href="http://www.babybus.com" target="babybus">宝宝巴士技术团队</a> 发布 ]</span>


STR;


        return $str;

	}

}