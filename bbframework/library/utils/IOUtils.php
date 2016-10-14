<?php

// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NullYang <635384073@qq.com>
// +----------------------------------------------------------------------

namespace util;


class IOUtils {

	// +----------------------------------------------------------------------
	// | 存在判断
	// +----------------------------------------------------------------------

	/**
	 * 判断文件或者文件夹是否存在
	 *
	 * @param $path 路径
	 * @return bool
	 */
	public static function exists($path){
		return file_exists($path);
	}



	// +----------------------------------------------------------------------
	// | 属性
	// +----------------------------------------------------------------------

	/**
	 * 获得文件名 + 后缀名
	 *
	 * @param $path 路径
	 * @return mixed
	 */
	public static function filename($path){
		return pathinfo($path, PATHINFO_BASENAME);
	}



	/**
	 * 获得文件后缀名
	 *
	 * @param $path 路径
	 * @return mixed
	 */
	public static function extension($path){
		return pathinfo($path, PATHINFO_EXTENSION);
	}



	/**
	 * 获得文件或目录属性
	 *
	 * @param $path 路径
	 * @return array
	 */
	public static function attributes($path){
		return stat($path);
	}



	// +----------------------------------------------------------------------
	// | 类型
	// +----------------------------------------------------------------------

	/**
	 * 判断路径指向的对象是否是文件
	 *
	 * @param $path 路径
	 * @return bool
	 */
	public static function is_file($path){
		return is_file($path);
	}



	/**
	 * 判断路径指向的对象是否是目录
	 *
	 * @param $path 路径
	 * @return bool
	 */
	public static function is_directory($path){
		return is_dir($path);
	}



	/**
	 * 判断路径指向的对象是否是链接
	 *
	 * @param $path 路径
	 * @return bool
	 */
	public static function is_link($path){
		return is_link($path);
	}


	// +----------------------------------------------------------------------
	// | 内容尺寸/大小
	// +----------------------------------------------------------------------

	/**
	 * 获得文件内容长度
	 *
	 * @param $path 路径
	 * @return int
	 */
	public static function filesize($path){
		return filesize($path);
	}



	// +----------------------------------------------------------------------
	// | 返回文件信息
	// +----------------------------------------------------------------------

	/**
	 * 返回文件信息
	 *
	 * @param $path 路径
	 * @return mixed
	 */
	public static function pathinfo($path){
		return pathinfo($path);
	}



	/**
	 * 获得文件的完整路径
	 *
	 * @param $path 路径
	 * @return string
	 */
	public static function full_path($path){
		return realpath($path);
	}



	/**
	 * 获取路径下的文件所有文件的集合
	 *
	 * @param $dir 路径
	 * @param $type 类型
	 * @return array
	 */
	public static function filepathes($dir, $file_type = null){
		$tmp_array = array();

		if (is_dir($dir))
		{
			if ($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					$path = $dir .'/'. $file;
					if(is_file($path) === true){
						if(!empty($file_type)){
							if(substr(strrchr($file, '.'), 1) == $file_type){
								array_push($tmp_array, self::full_path($path));
							}
						}else{
							array_push($tmp_array, self::full_path($path));
						}
					}
				}
				closedir($dh);
			}
		}

		return $tmp_array;
	}



	/**
	 * 返回文件/目录的上层目录路径
	 *
	 * @param $path 路径
	 * @return string
	 */
	public static function path_dir($path){
		return dirname($path);
	}



	/**
	 * 获取路径下的文件路径名的集合[txt文件]
	 *
	 * @param $dir 路径
	 * @return array
	 */
	public static function filepathes_txt($dir){
		return self::filepathes($dir, "txt");
	}



	/**
	 * 获取路径下的文件路径名的集合[png文件]
	 *
	 * @param $dir 路径
	 * @return array
	 */
	public static function filepathes_png($dir){
		return self::filepathes($dir, "png");
	}



	// +----------------------------------------------------------------------
	// | 获得文件数量
	// +----------------------------------------------------------------------

	/**
	 * 获得路径下文件数量
	 *
	 * @param $dir 路径
	 * @param $file_type 文件类型
	 * @return int
	 */
	public static function filecount($dir, $file_type){
		return count(self::filepathes($dir, $file_type));
	}



	// +----------------------------------------------------------------------
	// | 复制/移动
	// +----------------------------------------------------------------------

	/**
	 * 复制文件, 返回是否操作成功标识
	 *
	 * @param $src 原始文件路径
	 * @param $dest 目的文件路径
	 * @return bool
	 */
	public static function copyfile($src, $dest){
		return copy($src, $dest);
	}



	// +----------------------------------------------------------------------
	// | 文件操作[增删改查]
	// +----------------------------------------------------------------------

	/**
	 * 生成文件
	 *
	 * @param $path 路径
	 * @return resource
	 */
	public static function touch_file($path){
		return fopen($path, "w");
	}



	/**
	 * 读取文件内容
	 *
	 * @param $path 路径
	 * @return bool|string
	 */
	public static function read_file($path){
		if(!file_exists($path)) return false;

		$file = fopen($path, 'r');
		$content = fread($file,filesize($path));
		fclose($file);

		return $content;
	}



	/**
	 * 读取文件行集合
	 *
	 * @param $path 路径
	 * @return array|bool
	 */
	public static function read_filelines($path){
		if(!file_exists($path)) return false;

		$file = fopen($path, 'rb');
		$tmp_array = array();

		while ( ($line = fgets($file)) !== false) {
			array_push($tmp_array, $line);
		}

		return $tmp_array;
	}


	/**
	 * 写入文件内容
	 *
	 * @param $path 路径
	 * @param $content 内容
	 * @return int|void 字节数或者是false
	 */
	public static function write_file($path, $content){
		return file_put_contents($path, $content);
	}



	/**
	 * 追加文件内容
	 *
	 * @param $path 路径
	 * @param $content 内容
	 * @return int|void 字节数或者false
	 */
	public static function append_file($path, $content){
		return file_put_contents($path, $content, FILE_APPEND);
	}



	/**
	 * 追加文件内容[行]
	 *
	 * @param $path 路径
	 * @param $content 内容
	 * @return int|void 字节数或者false
	 */
	public static function append_line($path, $content){
		return file_put_contents($path, "\n" . $content, FILE_APPEND);
	}



	/**
	 * 输出文件内容
	 *
	 * @param $path 路径
	 * @return bool
	 */
	public static function print_file($path){
		if(!file_exists($path)) return false;

		$file = fopen($path, 'r');

		$content = fread($file,filesize($path));
		echo $content;

		fclose($file);
	}



	/**
	 * 删除文件
	 *
	 * @param $path 路径
	 * @return bool
	 */
	public static function remove_file($path){
		return is_file($path) ? unlink($path) : false;
	}



	/**
	 * 重命名文件或目录
	 *
	 * @param $old_name 当前文件名或路径
	 * @param $new_name 新的文件名或路径
	 * @return bool
	 */
	public static function rename($old_name, $new_name){
		return rename($old_name,$new_name);
	}



	// +----------------------------------------------------------------------
	// | 目录操作[增删改查]
	// +----------------------------------------------------------------------

	/**
	 * 创建文件夹
	 *
	 * @param $path 路径
	 * @return bool
	 */
	public static function mkdir($path){
		return !file_exists($path) ? mkdir($path) : false;
	}



	/**
	 * 删除文件夹以及文件夹里面所有内容
	 *
	 * @param $path
	 */
	public static function rmdir($path){
		if(is_dir($path)){
			if ($handle = opendir( "$path")){
				while(false !== ($item = readdir( $handle))){
					if( $item != "." && $item != ".."){
						if(is_dir("$path/$item")){
							rmdir("$path/$item");
						}else{
							if(unlink( "$path/$item" )) echo "delete file: $path/$item <br/> ";
						}
					}
				}
				closedir( $handle );
				if(rmdir($path)) echo "delete dir: $path <br/> ";
			}
		}
	}



	/**
	 * 创建多级目录
	 *
	 * @param $path 路径
	 * @return bool
	 */
	public static function mkdir_by_file($path){
		return !file_exists($path) ? mkdir($path,0777,true) : false;
	}
}
