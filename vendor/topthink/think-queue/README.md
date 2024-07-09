# think-queue

## 安装
> composer require topthink/think-queue

## 配置
> 配置文件位于 `application/extra/queue.php`
### 公共配置

```
[
    'connector'=>'sync' //驱動類型，可選擇 sync(默認):同步執行，database:資料庫驱動,redis:Redis驱動,topthink:Topthink驱動
                   //或其他自訂的完整的類名
]
```

### 驱動配置
> 各个驱動的具体可用配置项在`think\queue\connector`目錄下各个驱動類里的`options`属性中，寫在上面的`queue`配置里即可覆盖


## 使用 Database
> 建立如下資料表

```
CREATE TABLE `prefix_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

## 建立任务類
> 單模組项目推荐使用 `app\job` 作為任务類的命名空間
> 多模組项目可用使用 `app\module\job` 作為任务類的命名空間
> 也可以放在任意可以自動載入到的地方

任务類不需继承任何類，如果这个類只有一个任务，那么就只需要提供一个`fire`方法就可以了，如果有多个小任务，就寫多个方法，下面发布任务的时候会有区别  
每个方法会傳入两个参數 `think\queue\Job $job`（當前的任务對象） 和 `$data`（发布任务时自訂的資料）

还有个可选的任务失敗執行的方法 `failed` 傳入的参數為`$data`（发布任务时自訂的資料）

### 下面寫两个例子

```
namespace app\job;

use think\queue\Job;

class Job1{
    
    public function fire(Job $job, $data){
    
            //....这里執行具体的任务 
            
             if ($job->attempts() > 3) {
                  //通過这个方法可以檢查这个任务已经重试了几次了
             }
            
            
            //如果任务執行成功後 记得刪除任务，不然这个任务会重复執行，直到达到最大重试次數後失敗後，執行failed方法
            $job->delete();
            
            // 也可以重新发布这个任务
            $job->release($delay); //$delay為延迟時間
          
    }
    
    public function failed($data){
    
        // ...任务达到最大重试次數後，失敗了
    }

}

```

```

namespace app\lib\job;

use think\queue\Job;

class Job2{
    
    public function task1(Job $job, $data){
    
          
    }
    
    public function task2(Job $job, $data){
    
          
    }
    
    public function failed($data){
    
          
    }

}

```


## 发布任务
> `think\Queue:push($job, $data = '', $queue = null)` 和 `think\Queue::later($delay, $job, $data = '', $queue = null)` 两个方法，前者是立即執行，後者是在`$delay`秒後執行

`$job` 是任务名  
單模組的，且命名空間是`app\job`的，比如上面的例子一,寫`Job1`類名即可  
多模組的，且命名空間是`app\module\job`的，寫`model/Job1`即可  
其他的需要些完整的類名，比如上面的例子二，需要寫完整的類名`app\lib\job\Job2`  
如果一个任务類里有多个小任务的話，如上面的例子二，需要用@+方法名`app\lib\job\Job2@task1`、`app\lib\job\Job2@task2`

`$data` 是你要傳到任务里的参數

`$queue` 對列名，指定这个任务是在哪个對列上執行，同下面监控對列的时候指定的對列名,可不填

## 监听任务並執行

> php think queue:listen

> php think queue:work --daemon（不加--daemon為執行單个任务）

两种，具体的可选参數可以输入命令加 --help 查看

>可配合supervisor使用，保证进程常驻