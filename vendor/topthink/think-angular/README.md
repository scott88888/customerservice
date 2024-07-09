
#think-angular

> 仿angularjs的php模板引擎

## 使用说明

此模板引擎針對能够使用angularjs的php開发者编寫, 主要特點是 不需要額外的标签定義, 全部使用属性定義, 寫好模板文件在IDE中不会出现警告和錯誤, 格式化程式碼的时候很整洁, 因為套完的模板文件还是规范的html

注: 一个标签上可以使用多个模板指令, 指令有前後顺序要求, 所以要注意属性的顺序, 在單标签上使用模板属性时一定要使用<code>/></code>结束, 如 <code>&lt;input php-if="$is_download" type="button" value="下载" />, &lt;img php-if="$article['pic']" src="{&dollar;article.pic}" /></code> 等等, 具体可参考手册.  

## 安装方法

使用composer安装模版引擎方法: <code>composer require topthink/think-angular</code>

## 開发手册
看云文档托管平台: http://www.kancloud.cn/shuai/php-angular

## 示例程式碼
参考/test目錄 