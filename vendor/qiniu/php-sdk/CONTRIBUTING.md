# 贡献程式碼指南

我们非常欢迎大家来贡献程式碼，我们会向贡献者致以最诚挚的敬意。

一般可以通過在Github上送出[Pull Request](https://github.com/qiniu/php-sdk)来贡献程式碼。

## Pull Request要求

- **[PSR-2 编碼风格标准](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** 。要通過项目中的code sniffer檢查。

- **程式碼格式** 送出前 請按 ./vendor/bin/phpcbf --standard=PSR2  进行格式化。

- **必须新增测试！** - 如果没有测试（單元测试、集成测试都可以），那么送出的补丁是不会通過的。

- **记得更新文档** - 保证`README.md`以及其他相關文档及时更新，和程式碼的变更保持一致性。

- **考虑我们的发布周期** - 我们的版本号会服从[SemVer v2.0.0](http://semver.org/)，我们绝對不会随意变更對外的API。

- **建立feature分支** - 最好不要从你的master分支送出 pull request。

- **一个feature送出一个pull請求** - 如果你的程式碼变更了多个操作，那就送出多个pull請求吧。

- **清晰的commit历史** - 保证你的pull請求的每次commit操作都是有意义的。如果你開发中需要执行多次的即时commit操作，那么請把它们放到一起再送出pull請求。

## 运行测试

``` bash
./vendor/bin/phpunit tests/Qiniu/Tests/

```
