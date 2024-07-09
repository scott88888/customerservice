<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Model\LiveChannelConfig;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* 简單使用 *******************************************************

/**
    建立一个直播频道
    频道的名稱是test_rtmp_live。直播產生的m3u8文件叫做test.m3u8，该索引文件包含3片ts文件，每片ts文件的时長為5秒（这只是一个建议值，具体的时長取决于關鍵帧）。
 */
$config = new LiveChannelConfig(array(
            'description' => 'live channel test',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello.m3u8'
        ));
$info = $ossClient->putBucketLiveChannel($bucket, 'test_rtmp_live', $config);
Common::println("bucket $bucket liveChannel created:\n" . 
"live channel name: ". $info->getName() . "\n" .
"live channel description: ". $info->getDescription() . "\n" .
"publishurls: ". $info->getPublishUrls()[0] . "\n" .
"playurls: ". $info->getPlayUrls()[0] . "\n");

/**
    對建立好的频道，可以使用listBucketLiveChannels来进行列举已达到管理的目的。
    prefix可以按照前缀过滤list出来的频道。
    max_keys表示迭代器内部一次list出来的频道的最大數量，这个值最大不能超过1000，不填写的話默认為100。
 */
$list = $ossClient->listBucketLiveChannels($bucket);
Common::println("bucket $bucket listLiveChannel:\n" . 
"list live channel prefix: ". $list->getPrefix() . "\n" .
"list live channel marker: ". $list->getMarker() . "\n" .
"list live channel maxkey: ". $list->getMaxKeys() . "\n" .
"list live channel IsTruncated: ". $list->getIsTruncated() . "\n" .
"list live channel getNextMarker: ". $list->getNextMarker() . "\n");

foreach($list->getChannelList()  as $list)
{
    Common::println("bucket $bucket listLiveChannel:\n" . 
    "list live channel IsTruncated: ". $list->getName() . "\n" .
    "list live channel Description: ". $list->getDescription() . "\n" .
    "list live channel Status: ". $list->getStatus() . "\n" .
    "list live channel getNextMarker: ". $list->getLastModified() . "\n");
}
/**
    建立直播频道之后拿到推流用的play_url（rtmp推流的url，如果Bucket不是公共读写权限那么还需要带上签名，见下文示例）和推流用的publish_url（推流产生的m3u8文件的url）
 */
$play_url = $ossClient->signRtmpUrl($bucket, "test_rtmp_live", 3600, array('params' => array('playlistName' => 'playlist.m3u8')));
Common::println("bucket $bucket rtmp url: \n" . $play_url);
$play_url = $ossClient->signRtmpUrl($bucket, "test_rtmp_live", 3600);
Common::println("bucket $bucket rtmp url: \n" . $play_url);

/**
   建立好直播频道，如果想把这个频道禁用掉（断掉正在推的流或者不再允许向一个地址推流），应该使用putLiveChannelStatus接口，将频道的status改成“Disabled”，如果要将一个禁用狀態的频道启用，那么也是调用这个接口，将status改成“Enabled”
 */
$resp = $ossClient->putLiveChannelStatus($bucket, "test_rtmp_live", "enabled");

/**
    建立好直播频道之后调用getLiveChannelInfo可以得到频道相關的訊息
 */
$info = $ossClient->getLiveChannelInfo($bucket, 'test_rtmp_live');
Common::println("bucket $bucket LiveChannelInfo:\n" . 
"live channel info description: ". $info->getDescription() . "\n" .
"live channel info status: ". $info->getStatus() . "\n" .
"live channel info type: ". $info->getType() . "\n" .
"live channel info fragDuration: ". $info->getFragDuration() . "\n" .
"live channel info fragCount: ". $info->getFragCount() . "\n" .
"live channel info playListName: ". $info->getPlayListName() . "\n");

/**
    如果想查看一个频道历史推流记录，可以调用getLiveChannelHistory。目前最多可以看到10次推流的记录
 */
$history = $ossClient->getLiveChannelHistory($bucket, "test_rtmp_live");
if (count($history->getLiveRecordList()) != 0)
{
    foreach($history->getLiveRecordList() as $recordList)
    {
        Common::println("bucket $bucket liveChannelHistory:\n" . 
        "live channel history startTime: ". $recordList->getStartTime() . "\n" .
        "live channel history endTime: ". $recordList->getEndTime() . "\n" .
        "live channel history remoteAddr: ". $recordList->getRemoteAddr() . "\n");
    }
}

/**
    對于正在推流的频道调用get_live_channel_stat可以获得流的狀態訊息。
    如果频道正在推流，那么stat_result中的所有字段都有意义。
    如果频道闲置或者处于“Disabled”狀態，那么status為“Idle”或“Disabled”，其他字段無意义。
 */
$status = $ossClient->getLiveChannelStatus($bucket, "test_rtmp_live");
Common::println("bucket $bucket listLiveChannel:\n" . 
"live channel status status: ". $status->getStatus() . "\n" .
"live channel status ConnectedTime: ". $status->getConnectedTime() . "\n" .
"live channel status VideoWidth: ". $status->getVideoWidth() . "\n" .
"live channel status VideoHeight: ". $status->getVideoHeight() . "\n" .
"live channel status VideoFrameRate: ". $status->getVideoFrameRate() . "\n" .
"live channel status VideoBandwidth: ". $status->getVideoBandwidth() . "\n" .
"live channel status VideoCodec: ". $status->getVideoCodec() . "\n" .
"live channel status AudioBandwidth: ". $status->getAudioBandwidth() . "\n" .
"live channel status AudioSampleRate: ". $status->getAudioSampleRate() . "\n" .
"live channel status AdioCodec: ". $status->getAudioCodec() . "\n");

/**
 *  如果希望利用直播推流产生的ts文件產生一个點播列表，可以使用postVodPlaylist方法。
 *  指定起始時間為当前時間减去60秒，结束時間為当前時間，这意味着将產生一个長度為60秒的點播视频。
 *  播放列表指定為“vod_playlist.m3u8”，也就是说这个接口调用成功之后会在OSS上產生一个名叫“vod_playlist.m3u8”的播放列表文件。
 */
$current_time = time();
$ossClient->postVodPlaylist($bucket,
    "test_rtmp_live", "vod_playlist.m3u8", 
    array('StartTime' => $current_time - 60, 
          'EndTime' => $current_time)
);

/**
 *  如果一个直播频道已经不打算再使用了，那么可以调用delete_live_channel来刪除频道。
 */
$ossClient->deleteBucketLiveChannel($bucket, "test_rtmp_live");
