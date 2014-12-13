<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('save_img')) {
  function save_img($data,$blog_id,$name) {
    $img=(explode(",",$data));
    $extension = explode("/",$img[0]);
    $extension = explode(";",$extension[1]);
    $img = imagecreatefromstring(base64_decode($img[1]));
    if($img != false)
    {
        if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/upload/blog/'.$blog_id)) {
          mkdir($_SERVER['DOCUMENT_ROOT'].'/upload/blog/'.$blog_id);
        }
       imagejpeg($img, $_SERVER['DOCUMENT_ROOT'].'/upload/blog/'.$blog_id.'/'.$name.'.'.$extension[0]);
       return '/upload/blog/'.$blog_id.'/'.$name.'.'.$extension[0];
    }
  }
}