# The dxcswl base code Package  我们需要一个核心驱动的类哦
 
## 注意

目前处于测试和调试阶段 
我们需要一个来支持他们正常
 
## 安装

> composer require dxcswl/ft

## 使用

~~~
$image = \think\Image::open('./image.jpg');
或者
$image = \think\Image::open(request()->file('image'));


$image->crop(...)
    ->thumb(...)
    ->water(...)
    ->text(....)
    ->save(..);

~~~