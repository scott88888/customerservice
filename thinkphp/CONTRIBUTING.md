如何贡献我的源程式碼
===

此文档介绍了 ThinkPHP 团队的组成以及运转机制，您送出的程式碼将给 ThinkPHP 项目带来什么好处，以及如何才能加入我们的行列。

## 通過 Github 贡献程式碼

ThinkPHP 目前使用 Git 来控制程序版本，如果你想為 ThinkPHP 贡献源程式碼，請先大致了解 Git 的使用方法。我们目前把项目托管在 GitHub 上，任何 GitHub 使用者都可以向我们贡献程式碼。

参与的方式很简單，`fork`一份 ThinkPHP 的程式碼到你的仓库中，修改后送出，并向我们发起`pull request`申請，我们会及时對程式碼进行审查并处理你的申請。审查通過后，你的程式碼将被`merge`进我们的仓库中，这样你就会自動出现在贡献者名單里了，非常方便。

我们希望你贡献的程式碼符合：

* ThinkPHP 的编碼规范
* 适当的注释，能让其他人读懂
* 遵循 Apache2 開源协议

**如果想要了解更多细节或有任何疑问，請继续阅读下面的内容**

### 注意事项

* 本项目程式碼格式化标准选用 [**PSR-2**](http://www.kancloud.cn/thinkphp/php-fig-psr/3141)；
* 类名和类文件名遵循 [**PSR-4**](http://www.kancloud.cn/thinkphp/php-fig-psr/3144)；
* 對于 Issues 的处理，請使用诸如 `fix #xxx(Issue ID)` 的 commit title 直接關閉 issue。
* 系统会自動在 PHP 5.4 5.5 5.6 7.0 和 HHVM 上测试修改，其中 HHVM 下的测试容许报错，請确保你的修改符合 PHP 5.4 ~ 5.6 和 PHP 7.0 的语法规范；
* 管理员不会合并造成 CI faild 的修改，若出现 CI faild 請檢查自己的源程式碼或修改相应的[單元测试文件](tests)；

## GitHub Issue

GitHub 提供了 Issue 功能，该功能可以用于：

* 提出 bug
* 提出功能改进
* 反馈使用体验

该功能不应该用于：

 * 提出修改意见（涉及程式碼署名和修订追溯問題）
 * 不友善的言论

## 快速修改

**GitHub 提供了快速編輯文件的功能**

1. 登入 GitHub 帐号；
2. 浏览项目文件，找到要进行修改的文件；
3. 點擊右上角铅笔图标进行修改；
4. 填写 `Commit changes` 相關内容（Title 必填）；
5. 送出修改，等待 CI 驗證和管理员合并。

**若您需要一次送出大量修改，請继续阅读下面的内容**

## 完整流程

1. `fork`本项目；
2. 克隆(`clone`)你 `fork` 的项目到本地；
3. 新建分支(`branch`)并检出(`checkout`)新分支；
4. 新增本项目到你的本地 git 仓库作為上游(`upstream`)；
5. 进行修改，若你的修改包含方法或函數的增减，請记得修改[單元测试文件](tests)；
6. 变基（衍合 `rebase`）你的分支到上游 master 分支；
7. `push` 你的本地仓库到 GitHub；
8. 送出 `pull request`；
9. 等待 CI 驗證（若不通過则重复 5~7，不需要重新送出 `pull request`，GitHub 会自動更新你的 `pull request`）；
10. 等待管理员处理，并及时 `rebase` 你的分支到上游 master 分支（若上游 master 分支有修改）。

*若有必要，可以 `git push -f` 强行推送 rebase 后的分支到自己的 `fork`*

*绝對不可以使用 `git push -f` 强行推送修改到上游*

### 注意事项

* 若對上述流程有任何不清楚的地方，請查阅 GIT 教程，如 [这个](http://backlogtool.com/git-guide/cn/)；
* 對于程式碼**不同方面**的修改，請在自己 `fork` 的项目中**建立不同的分支**（原因参见`完整流程`第9條备注部分）；
* 变基及交互式变基操作参见 [Git 交互式变基](http://pakchoi.me/2015/03/17/git-interactive-rebase/)

## 推荐资源

### 開发环境

* XAMPP for Windows 5.5.x
* WampServer (for Windows)
* upupw Apache PHP5.4 ( for Windows)

或自行安装

- Apache / Nginx
- PHP 5.4 ~ 5.6
- MySQL / MariaDB

*Windows 使用者推荐新增 PHP bin 目录到 PATH，方便使用 composer*

*Linux 使用者自行配置环境， Mac 使用者推荐使用内置 Apache 配合 Homebrew 安装 PHP 和 MariaDB*

### 編輯器

Sublime Text 3 + phpfmt 插件

phpfmt 插件参數

```json
{
	"autocomplete": true,
	"enable_auto_align": true,
	"format_on_save": true,
	"indent_with_space": true,
	"psr1_naming": false,
	"psr2": true,
	"version": 4
}
```

或其他 編輯器 / IDE 配合 PSR2 自動格式化工具

### Git GUI

* SourceTree
* GitHub Desktop

或其他 Git 图形界面客户端
