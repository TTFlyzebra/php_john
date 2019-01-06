<?php
class MyImage{
	
	/**
	 * 剪切图片
	 * @param unknown $file
	 * @param unknown $height
	 * @param unknown $width
	 */
	public static function crop($file,$height,$width){
		$image = new \Think\Image(); 
		$image->open($file);//将图片裁剪为400x400并保存为corp.jpg
		$image->crop($height, $width)->save($file);
	}
	
	/**
	 * 缩放图片
	 * @param unknown $file
	 * @param unknown $height
	 * @param unknown $width
	 */
	public static function thumb($file,$height,$width,$scaleType=\Think\Image::IMAGE_THUMB_CENTER){
		$image = new \Think\Image();
		$image->open($file);
		$image->thumb($height, $width,$scaleType)->save($file);
	}
	
}