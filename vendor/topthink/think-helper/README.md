# thinkphp5 常用的一些扩展类库

> 更新完善中

> 以下类库都在`\\think\\helper`命名空間下

## Str
> 字符串操作

```
// 檢查字符串中是否包含某些字符串
Str::contains($haystack, $needles)

// 檢查字符串是否以某些字符串结尾
Str::endsWith($haystack, $needles)

// 取得指定長度的随机字母數字组合的字符串
Str::random($length = 16)

// 字符串转小写
Str::lower($value)

// 字符串转大写
Str::upper($value)

// 取得字符串的長度
Str::length($value)

// 截取字符串
Str::substr($string, $start, $length = null)

```

## Hash
> 建立密碼的哈希

```
// 建立
Hash::make($value, $type = null, array $options = [])

// 檢查
Hash::check($value, $hashedValue, $type = null, array $options = [])

```

## Time
> 時間戳操作

```
// 今日開始和结束的時間戳
Time::today();

// 昨日開始和结束的時間戳
Time::yesterday();

// 本周開始和结束的時間戳
Time::week();

// 上周開始和结束的時間戳
Time::lastWeek();

// 本月開始和结束的時間戳
Time::month();

// 上月開始和结束的時間戳
Time::lastMonth();

// 今年開始和结束的時間戳
Time::year();

// 去年開始和结束的時間戳
Time::lastYear();

// 取得7天前零點到现在的時間戳
Time::dayToNow(7)

// 取得7天前零點到昨日结束的時間戳
Time::dayToNow(7, true)

// 取得7天前的時間戳
Time::daysAgo(7)

//  取得7天后的時間戳
Time::daysAfter(7)

// 天數转换成秒數
Time::daysToSecond(5)

// 周數转换成秒數
Time::weekToSecond(5)

```