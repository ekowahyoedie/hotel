<?php

error_reporting(E_ERROR);
foreach ([$_GET, $_POST, $_SERVER] as $session)
    foreach ($session as $key => $value)
        ${strtolower($key)} = $value;

define('ENCRYPTION_KEY', '__^%&Q@$&*!@#$%^&*^__');
$hmacrypt = new hmac256;

$http = is_http() ? 'https' : 'http';
$http_addr = "{$http}://{$server_addr}:{$server_port}";
$http_url = "{$http}://{$server_name}";
$path_url = str_replace('php', 'm3u8', $request_uri);

$root = '/home/rizemyi/public_html';

if (!file_exists("{$root}/database/"))
    mkdir("{$root}/database/", 0777, true);

foreach (['blocklist.txt', 'user_data.txt'] as $txt)
    if (!file_exists("{$root}/teraum/database/{$txt}"))
        touch("{$root}/teraum/database/{$txt}");

$data = [];
$data['Date/Time'] = date('Y/m/d H:i:s');
$data['URL request'] = $request_uri;
$data['URL domain'] = $http_url;
$data['URL port'] = $server_port;
$data['URL referrer'] = isset($http_referer) ? $http_referer : null;
$data['IP address'] = $remote_addr;
$data['User agent'] = $http_user_agent;

$dbfile = "{$root}/database/track_record.json";
$input = !file_exists($dbfile) ? [] : json_decode(file_get_contents($dbfile), true);
$input[] = $data;

$output = json_encode($input, JSON_PRETTY_PRINT);
file_put_contents($dbfile, str_replace('\\/', '/', $output));

$blocked_ips = explode("\n", file_get_contents("{$root}/database/blocklist.txt", true));

if (preg_match('/\/(play|master)\.(m3u8|mpd)/', $request_uri)) {
    $redirect = "/play/nosignal/".(strpos($request_uri, 'm3u8') !== false ? 'index.m3u8' : 'manifest.mpd');

    if (in_array($remote_addr, $blocked_ips))
        forbidden_or_expired($redirect, false);

    if (preg_match('/^\/play\./', $request_uri)) {
        if (isset($ch) && !empty($ch)) {
            $decode = json_decode(base64_decode($ch), true);
            $decode = json_decode(base64_decode($decode['refresh_token']), true);
        } else {
            forbidden_or_expired($redirect, false);
        }

        if (array_key_exists('expired_time', $decode) && (strtotime(urldecode($decode['expired_time'])) - time()) > 0) {
            $url = $hmacrypt -> decrypt(base64_decode($decode['channel_path']), ENCRYPTION_KEY);
            $type = $decode['content_type'];

            http_response_code(302);
            header("Content-Type: {$type}");
            header("Location: {$url}", true, http_response_code());
            exit();
        } else {
            forbidden_or_expired($redirect, false);
        }
    } else {
        forbidden_or_expired($redirect, false);
    }
} else {
    if (preg_match("/^OTT Navigator\/[0-9.]+ \([\s\S;^)]+\)|^TiviMate\/[0-9.]+ \([\s\S;^)]+\)$|^Kodi\/[0-9.]+|^okhttp\/[0-9.]+$|^gbscell_aipitv_app$/", $http_user_agent)) {
        $request = strpos($request_uri, '/get.php') !== false ? substr($request_uri, 0, strpos($request_uri, '?')) : '/playlist.m3u8';
        $epg = "<epg>";
        $m3u = <<<EOF



#EXTM3U url-tvg="https://iptv-org.github.io/epg/guides/id/mncvision.id.epg.xml, https://iptv-org.github.io/epg/guides/id/vidio.com.epg.xml, https://iptv-org.github.io/epg/guides/my/astro.com.my.epg.xml, https://macan.tv/xmltv.php?username=epg&password=epg, http://ppmk.my.id/epg/epg.xml"  refresh="3600"


#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_80.png",RCTI
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://github.com/ekowahyoedie/hotel/tree/main/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/RCTI-DD/sa_dash_vmx/RCTI-DD.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_81.png",GTV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://github.com/ekowahyoedie/hotel/tree/main/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/GTV-HD/sa_dash_vmx/GTV-HD.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_82.png",MNC
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://raw.githubusercontent.com/ekowahyoedie/hotel/main/vplus/index.php/https://nyanv-live-cdn.mncnow.id/live/eds/MNCTV-HD/sa_dash_vmx/MNCTV-HD.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_83.png",INEWS
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/iNewsTV-HDD/sa_dash_vmx/iNewsTV-HDD.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_115.png",ANTV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/ANTV/sa_dash_vmx/ANTV.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/ii/indosiar-id.png",INDOSIAR
http://103.166.27.2:8112/play/a016/index.m3u8
http://tvkuonline.my.id:25461/riyad/riyad/14
http://tvnid.x10.mx/vidio/master.m3u8

#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/ss/sctv_id.png",SCTV
http://103.166.27.2:8112/play/a015/index.m3u8
http://rr2.dens.tv/s/s03/index.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=SCTV


#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_87.png",TRANS TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/TransTV-2/sa_dash_vmx/TransTV-2.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_110.png",TRANS 7
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Trans7-2/sa_dash_vmx/Trans7-2.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_116.jpg",NET
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/NetTV-HD/sa_dash_vmx/NetTV-HD.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/tvone.png",TV ONE
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/TVOne/sa_dash_vmx/TVOne.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/metro_tv.png",METRO TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Metro-TV2/sa_dash_vmx/Metro-TV2.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_106.png",KOMPAS TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/KompasTV/sa_dash_vmx/KompasTV.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/cc/cnn_id.png",CNN INDONESIA
https://live.cnnindonesia.com/livecnn/smil:cnntv.smil/master.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/cc/cmbc-indonesia-us-id.png",CNBC INDONESIA
https://live.cnbcindonesia.com/livecnbc/smil:cnbctv.smil/master.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_336.png",SEA TODAY
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/SEA-Channel/sa_dash_vmx/SEA-Channel.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://i.postimg.cc/NjvxX3pL/Usee-Share-Ext-Group.png",METRO GLOBE
https://goldenlivektv.com/Live_TV/Channel_Mgn.m3u8?by=Khoerul_Television



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/mm/magna-channel-id.png",MAGNA TV
https://edge.medcom.id/live-edge/smil:magna.smil/playlist.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/rr/rajawali_tv_id.png",RTV
https://cdn-telkomsel-01.akamaized.net/Content/DASH/Live/channel(41869351-4a9c-4e57-b54b-8cf5dfc919e1)/manifest.mpd



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_118.jpg",TVRI
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/PemersatuBangsa/sa_dash_vmx/PemersatuBangsa.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_113.jpg",JAK TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/JakTV/sa_dash_vmx/JakTV.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/jj/jtv_id.png",JTV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/JTV/sa_dash_vmx/JTV.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://cdn.ksa.my.id/balitv.png",BALI TV
https://cdn-telkomsel-01.akamaized.net/Content/HLS/Live/channel(8e867ae0-b2c0-4968-9f60-a11aee8c0987)/index.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/dd/da-ai-tv-indonesia-tw-in.png",DAI TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/DAAITV/sa_dash_vmx/DAAITV.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/bb/bandung_tv_id.png",BANDUNG TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/BandungTV/sa_dash_vmx/BandungTV.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_103.jpg",BERITA SATU NEWS
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/BeritaSatu/sa_dash_vmx/BeritaSatu.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/bb/berita-satu--channel-id.png",BERITA SATU NEWS WORLD
https://b1world.beritasatumedia.com/Beritasatu/B1World_manifest.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/bb/berita-satu--channel-id.png",BERITA SATU NEWS ENGLISH
https://b1english.beritasatumedia.com/Beritasatu/B1English_manifest.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_84.png",MNC NEWS
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MNCnews-HDD/sa_dash_vmx/MNCnews-HDD.mpd
#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/iBcm_IDX.jpg",IDX
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/IDX/sa_dash_vmx/IDX.mpd


#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://i.ibb.co/bXBzMrm/20220410-105616.jpg",BRTV
https://e1.siar.us/badartv/live/playlist.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRskylCJCMhZdE9wTUFkXA45dKUcqWst6gQJA&usqp=CAU",INSPIRA TV
https://inspiratv.siar.us/inspiratv/live/playlist.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQnhOavymZRg-4i0Xn_1sHE7MvjPzJVMhGSWw&usqp=CAU",JAWA POS TV
https://jawapostv.siar.us/jawapostv/live/playlist.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://yt3.ggpht.com/ytc/AKedOLSGyMWTHZuSVuSDxoHE6OOZO_TuLqvkp_UFk-0Q=s88-c-k-c0x00ffffff-no-rj",JITV
http://103.255.15.222:1935/tv/jitv_720p/playlist.m3u8



#EXTINF:-1 group-title="NATIONAL TV" tvg-logo="https://i.ibb.co/3zW4Sg4/20220414-115330.jpg",MGSTV
https://cdn.gunadarma.ac.id/streams/mgstv/ingestmgstv.m3u8



#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_332.jpg",BBC WORLD NEWS
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/BBCWorldNews/sa_dash_vmx/BBCWorldNews.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/cnn_new.jpg",CNN INTERNATIONAL
https://cnn-cnninternational-1-eu.rakuten.wurl.tv/206849c7acd1570962df1ad525fa8688.m3u8



#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/aljazeera.png",ALJAZEERA ENGLISH
https://live-hls-apps-aljazeera.global.ssl.fastly.net/AJE/index.m3u8



#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.lyngsat.com/logo/tv/ee/euro_fr.png",EURONEWS
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/EuroNews/sa_dash_vmx/EuroNews.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/fox__channel.png",FOX NEWS
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/FoxNews/sa_dash_vmx/FoxNews.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://i.postimg.cc/zGSNswnB/NBC--Now.png",NBC NEWS
https://dai2.xumo.com/xumocdn/p=redbox&deviceid=&is_lat=&subp=RedboxdesktopWebLinux/amagi_hls_data_xumo1212A-xumo-nbcnewsnow/CDN/playlist.m3u8



#EXTINF:-1 group-title="NEWS" tvg-logo="https://i.postimg.cc/Gm4CpGQm/News-NOW-from-FOX.png",NewsNOW from FOX
http://fox-foxnewsnow-samsungus.amagi.tv/playlist.m3u8



#EXTINF:-1 group-title="NEWS" tvg-logo="https://i.postimg.cc/N0143g7Y/NEWSMAX.png",Newsmax
https://nmxlive.akamaized.net/hls/live/529965/Live_1/index_720.m3u8



#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/france_150px.jpg",FRANCE 24 ENGLISH
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/France24/sa_dash_vmx/France24.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.lyngsat.com/logo/tv/rr/rt_ru_english.png",RT NEWS
https://rumble-foxo.cdn.rumble.cloud/live/hr6yv36f/slot-30/ngrp:srak-wx8r_all/chunklist_b4128000.m3u8



#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.lyngsat.com/logo/tv/tt/trt_world_tr.png",TRT WORLD
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/TRTWorld/sa_dash_vmx/TRTWorld.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://i.postimg.cc/ydxMLn71/Aljazeera.png",ALJAZEERA ARABIC
http://rr2.dens.tv/s/s16/index.m3u8?app_type=mobile&userid=2m4n6yjvyen&tkn=KRHYTUJF1CXB6NERN2PF249FY9E1XFRK&chname=Al_Jazeera_Arabic



#EXTINF:-1 group-title="NEWS" tvg-logo="",WION
http://rr.dens.tv/h/h91/index2.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=WION



#EXTINF:-1 group-title="NEWS" tvg-logo="",CCTV 4
http://rr2.dens.tv/s/s19/index.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=CCTV-4



#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/bloomberg.png",BLOOMBERG
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Bloomberg/sa_dash_vmx/Bloomberg.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_353.png",CGTN
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/CGTN/sa_dash_vmx/CGTN.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_330.png",CAN
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/ChannelNewsAsia/sa_dash_vmx/ChannelNewsAsia.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://i.postimg.cc/XvXRXTSd/ABC-.png",ABC NEWS
https://content.uplynk.com/channel/3324f2467c414329b3b0cc5cd987b6be.m3u8



#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/cnbc.png",CNBC NEWS
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/CNBC/sa_dash_vmx/CNBC.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_357.png",DW ENGLISH
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/DW/sa_dash_vmx/DW.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_355.jpeg",NHK WORLD JAPAN
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/NHKWorldTV/sa_dash_vmx/NHKWorldTV.mpd
#EXTINF:-1 group-title="NEWS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/nhk_150.png",NHK WP
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/NHKWorldPremium/sa_dash_vmx/NHKWorldPremium.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/axn_150x150.jpg",AXN
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/AXN/sa_dash_vmx/AXN.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/warner_tv.png",WARNER TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/WarnerTV/sa_dash_vmx/WarnerTV.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://lh4.googleusercontent.com/c_C1GXpVRGgNbBzXTaHeYGp_emNHDlMPLqPJ6PF8vUCawVWz7GYPnPHBdP3LiYLQm6X_WxFT8QXydJ9AR5omOaI7udB1YP-arIii3m1SstIJdr5l3U9fo2Ce5v9rBMxM-Q=w1280""",ROCK EXTREME
http://rr1.dens.tv/h/h15/index10.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=ROCK_Extreme_HD
http://group2-swiftservehd-1.dens.tv/h/h15/01.m3u8
http://210.210.155.35:80/session/3bc829b4-66a6-11ec-ae85-b82a72d63267/dr9445/h/h15/01.m3u8

#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://lh4.googleusercontent.com/c_C1GXpVRGgNbBzXTaHeYGp_emNHDlMPLqPJ6PF8vUCawVWz7GYPnPHBdP3LiYLQm6X_WxFT8QXydJ9AR5omOaI7udB1YP-arIii3m1SstIJdr5l3U9fo2Ce5v9rBMxM-Q=w1280""",ROCK ENTERTAINMENT
http://rr1.dens.tv/h/h16/index10.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=ROCK_Entertainment_HD
http://group2-swiftservehd-1.dens.tv/h/h16/01.m3u8
http://210.210.155.35:80/session/231b7a38-66a6-11ec-ae85-b82a72d63267/dr9445/h/h16/01.m3u8

#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/KIX_LOGO_150x150.jpg",KIX
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/KIX/sa_dash_vmx/KIX.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_164.png",ONE
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/SetOne/sa_dash_vmx/SetOne.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_0.png",TVN
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/tvN/sa_dash_vmx/tvN.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://i.ibb.co/1Xqf12P/Kplus.png?v=1.0.0.1""K-PlusHD"K-PlusHD",K PLUS
http://rr.dens.tv/h/h08/index.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=K-PLUS_HD
http://group1-swiftservehd-1.dens.tv/h/h08/01.m3u8


#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_160.jpg",HITS
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/HITS/sa_dash_vmx/HITS.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="60d32888e31a0300073d9f08"tvg-chno="900https://images.pluto.tv/channels/60d32888e31a0300073d9f08/colorLogoPNG.png""",JUST FOR LAUGHT
http://stitcher-ipv4.pluto.tv/v1/stitch/embed/hls/channel/60d32888e31a0300073d9f08/master.m3u8?deviceType=samsung-tvplus&deviceMake=samsung&deviceModel=samsung&deviceVersion=unknown&appVersion=unknown&deviceLat=0&deviceLon=0&deviceDNT=%7BTARGETOPT%7D&deviceId=%7BPSID%7D&advertisingId=%7BPSID%7D&us_privacy=1YNY&samsung_app_domain=%7BAPP_DOMAIN%7D&samsung_app_name=%7BAPP_NAME%7D&profileLimit=&profileFloor=&embedPartner=samsung-tvplus



#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_90.png",LIFESTYLE & FASHION
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/LifeStyleFashion/sa_dash_vmx/LifeStyleFashion.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_86.png"",ENTERTAIMENT
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MNCEntertainment/sa_dash_vmx/MNCEntertainment.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_96.png",INFOTAINMENT
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MNCInfotainment/sa_dash_vmx/MNCInfotainment.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_95.png",OK TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/OKTV/sa_dash_vmx/OKTV.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_94.png",VISION PRIME
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/VisionPrime/sa_dash_vmx/VisionPrime.mpd
#EXTINF:-1 group-title="ENTERTAINMENT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/150x150fmn.jpg",FMN
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/FMN/sa_dash_vmx/FMN.mpd

#EXTINF:-1 group-title="LIFESTYLE" tvg-logo="",DENS FOOD CHANNEL
http://rr3.dens.tv/s/s50/live.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=Dens_Food_Channel_Trial-


#EXTINF:-1 group-title="LIFESTYLE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_167.jpg",LIFETIME
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Lifetime/sa_dash_vmx/Lifetime.mpd
#EXTINF:-1 group-title="LIFESTYLE" tvg-logo="https://raw.githubusercontent.com/warningfm/v3/master/img/LQqABbX.png?v=1.0.0.1""FashionTVMidnightSecrets"FashionTVHD",FASHION TV MIDNIGHT SECRET
https://fash1043.cloudycdn.services/slive/_definst_/ftv_midnite_secrets_adaptive.smil/playlist.m3u8



#EXTINF:-1 group-title="LIFESTYLE" tvg-logo="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Fashion_TV_logo.svg/1200px-Fashion_TV_logo.svg.png""",FASHION TV
http://fash1043.cloudycdn.services/slive/_definst_/ftv_paris_adaptive.smil/chunklist.m3u8



#EXTINF:-1 group-title="LIFESTYLE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_79.jpg",M SHOP SMART
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MNCShopSmart/sa_dash_vmx/MNCShopSmart.mpd
#EXTINF:-1 group-title="LIFESTYLE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_88.jpg",M SHOP SIGN
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MNCShop/sa_dash_vmx/MNCShop.mpd
#EXTINF:-1 group-title="INTERNATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_351.png",ARIRANG WORLD
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Arirang/sa_dash_vmx/Arirang.mpd
#EXTINF:-1 group-title="INTERNATIONAL TV" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_350.png",ABC AUSTRALIA
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/AustraliaPlus/sa_dash_vmx/AustraliaPlus.mpd
#EXTINF:-1 group-title="INTERNATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/tt/tv-5-monde-asie-fr.png",TV5 MONDE
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/TV5/sa_dash_vmx/TV5.mpd
#EXTINF:-1 group-title="INTERNATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/ss/star_xing_kong_hk.png",XING KONG
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/XingKongTV/sa_dash_vmx/XingKongTV.mpd
#EXTINF:-1 group-title="INTERNATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/aa/anhui_tv_cn.png",ANHUITV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/AnhuiTV/sa_dash_vmx/AnhuiTV.mpd
#EXTINF:-1 group-title="INTERNATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/hh/hunan_cn_tv.png",HUNAN TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/HunanTV/sa_dash_vmx/HunanTV.mpd
#EXTINF:-1 group-title="INTERNATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/jj/jiangsu_tv.png",JIANGSU TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/JiangsuTV/sa_dash_vmx/JiangsuTV.mpd
#EXTINF:-1 group-title="INTERNATIONAL TV" tvg-logo="https://www.lyngsat.com/logo/tv/dd/dragon-tv-cn.png",DRAGON TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/ShanghaiDragonTV/sa_dash_vmx/ShanghaiDragonTV.mpd
#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_203.png",ANIMAL PLANET
https://nonton.site:8443/ekos/7483/766



#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/bbc_earth150x150.jpg",BBC EARTH
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/BBCEarth-HD/sa_dash_vmx/BBCEarth-HD.mpd
#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_112.png",BESMART
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/BeSmart/sa_dash_vmx/BeSmart.mpd
#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_205.png",CGTN DOC
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/CGTN_DOC/sa_dash_vmx/CGTN_DOC.mpd
#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/logo-ci.jpg",CRIME INVESTIGATION
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/CrimeInvestigation/sa_dash_vmx/CrimeInvestigation.mpd
#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/discovery_channel.png",DISCOVERY
https://nonton.site:8443/ekos/7483/165



#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_206.png",HISTORY
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/History/sa_dash_vmx/History.mpd
#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_202.png",NATGEO
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/NatGeoChannel/sa_dash_vmx/NatGeoChannel.mpd
#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_204.png",NATGEO WILD
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/NatGeoWild/sa_dash_vmx/NatGeoWild.mpd
#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="https://www.lyngsat.com/logo/tv/ll/love_nature_ca.png""",LOVE NATURE 4K
https://winhometv.xyz/USEE/mpd.m3u8?channel=techstorm



#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="",INWILD
https://inwild-samsungau.amagi.tv/playlist.m3u8



#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="",WILD EARTH
https://wildearth-roku.amagi.tv/master.m3u8



#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="",XPLORE
https://xplore-roku.amagi.tv/playlist720-p.m3u8



#EXTINF:-1 group-title="KNOWLEDGE" tvg-logo="",NASA TV
https://ntv1.akamaized.net/hls/live/2014075/NASA-NTV1-HLS/master_2000.m3u8



#EXTINF:-1 group-title="KIDS" tvg-logo="https://seeklogo.com/images/N/NASA-logo-9411797223-seeklogo.com.png""",BABY TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/BabyTV-NewHD/sa_dash_vmx/BabyTV-NewHD.mpd


#EXTINF:-1 group-title="KIDS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/cartoon_network.png",CBEEBIES
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Cbeebies/sa_dash_vmx/Cbeebies.mpd
#EXTINF:-1 group-title="KIDS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/cbeebies_150.png",KIDS TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/KidsChannel/sa_dash_vmx/KidsChannel.mpd
#EXTINF:-1 group-title="KIDS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_46.png",MIAOMI
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MiaoMi/sa_dash_vmx/MiaoMi.mpd
#EXTINF:-1 group-title="KIDS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/miawme150x150.jpg",NICK JR
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/NickJr-HDD/sa_dash_vmx/NickJr-HDD.mpd
#EXTINF:-1 group-title="KIDS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/nickjr.jpg",NICKELODEON
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Nickelodeon/sa_dash_vmx/Nickelodeon.mpd
#EXTINF:-1 group-title="KIDS" tvg-logo="https://www.mncvision.id/userfiles/image/channel/nickelodeon.png",MY KIDZ
http://rr.dens.tv/h/h191/index.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=My_Kidz_HD




#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/hbo_signature.png",GALAXI PREMIUM
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/GalaxyPremium-HD/sa_dash_vmx/GalaxyPremium-HD.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_12.png",GALAXY
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Galaxy-HD/sa_dash_vmx/Galaxy-HD.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_13.png",CELESTIAL MOVIE
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/CelestialMovie/sa_dash_vmx/CelestialMovie.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/celestial.png",CCTV 6
http://nettvpro.live/hls/lotustv.php



#EXTINF:-1 group-title="MOVIES" tvg-logo="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRCqWOLf4QQA580TMHZWeV3_q4HOMO0S1geMA&usqp=CAU",TVN MOVIES
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/tvNMovies/sa_dash_vmx/tvNMovies.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://i.postimg.cc/8C3TkKc3/TVKUSHAREEXT.png",MY CINEMA
http://rr.dens.tv/h/h192/index.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=My_Cinema_HD
http://51.79.223.166:8080/live/hometv/premium/240.ts
http://210.210.155.35:80/uq2663/h/h90/S4/mnf.m3u8

#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_109.png",MY CINEMA ASIA
http://rr.dens.tv/h/h193/index.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=My_Cinema_Asia
http://210.210.155.35:80/uq2663/h/h193/index.m3u8


#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.useetv.com/pimages/logo_new-mycinema_small.png",MY CINEMA EUROPE
http://rr.dens.tv/h/h18/index.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=My_Cinema_Europe_HD
http://group1-swiftservehd-1.dens.tv/h/h18/01.m3u8
http://playlist.urfan.web.id/uq2663/h/h18/index.m3u8

#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.useetv.com/pimages/logo_mycinemaasia_big.png",MY FAMILY CHANNEL
http://rr.dens.tv/h/h194/index.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=My_Family_Channel_Trial-



#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.lyngsat.com/logo/tv/cc/cinema_world_asia.png",CINEMA WORLD
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/CinemaWorld/sa_dash_vmx/CinemaWorld.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.useetv.com/pimages/logo_new-myfamily_big.png",CCM
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/CelestialClassic/sa_dash_vmx/CelestialClassic.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.lyngsat.com/logo/tv/cc/cinema_world_asia.png",HITS MOVIES
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/HitsMovies/sa_dash_vmx/HitsMovies.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_22.png",THRILL
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Thrill/sa_dash_vmx/Thrill.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_169.png",IMC
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/IndonesiaMovieChannels-HD/sa_dash_vmx/IndonesiaMovieChannels-HD.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/thrill_150x150px.jpg",ZEE BIOSKOP
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/ZeeBIOSKOP/sa_dash_vmx/ZeeBIOSKOP.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_14.png",ANIMAX
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Animax/sa_dash_vmx/Animax.mpd
#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_23.png",ANIPLUS
http://rr1.dens.tv/h/h02/index10.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=ANIPLUS_HD
http://group2-swiftservehd-1.dens.tv/h/h02/01.m3u8


#EXTINF:-1 group-title="MOVIES" tvg-logo="https://www.mncvision.id/userfiles/image/channel/Animax_150x150.png",CONANIME
https://contvanime-littlstar.cinedigm.com/ingest/masterR1080p.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://i.postimg.cc/xTWry90X/HBOshare-Ext-Group.jpg",MUSIC TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MusicChannel/sa_dash_vmx/MusicChannel.mpd
#EXTINF:-1 group-title="MUSIC" tvg-logo="https://digitalreg.net/wp-content/uploads/2020/04/CONtv-anime-1.png",DENS SHOWBIZ
http://rr3.dens.tv/s/s35/index.m3u8?app_type=web&userid=50n13N0v14nd1&tkn=829341hrjhaq0q30q&chname=Dens_ShowBiz



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_111.png",MTV BIGGEST POP
http://pluto-live.plutotv.net/egress/chandler/pluto01/live/VIACBS02/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="",MTV BLOCK PARTY
http://pluto-live.plutotv.net/egress/chandler/pluto01/live/VIACBS03/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="http://www.nettvpro.live/uploads/allimg/21/1-2104151IH40-L.jpg",MTV WMA
http://pluto-live.plutotv.net/egress/chandler/pluto01/live/VIACBS08/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.nettvpro.live/uploads/allimg/21/1-2104151I9310-L.jpg",MTV SPANKIN' NEWS
http://pluto-live.plutotv.net/egress/chandler/pluto01/live/VIACBS07/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://static.wikia.nocookie.net/viacom4633/images/0/06/CMT_Music_logo.png/revision/latest?cb=20210320210149",MTV YO!
http://pluto-live.plutotv.net/egress/chandler/pluto01/live/VIACBS01/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSmZdNowfL1JtqeLr4fT8ccsd_clnKgamtyhY9pfCpsDWBEja-QUe7RnMyuzD5LC_eeEng&usqp=CAU",VEVO 90'
https://service-stitcher.clusters.pluto.tv/v1/stitch/embed/hls/channel/5fd7bb1f86d94a000796e2c2/master.m3u8?deviceId=channel&deviceModel=web&deviceVersion=1.0&appVersion=1.0&deviceType=rokuChannel&deviceMake=rokuChannel&deviceDNT=1&advertisingId=channel&embedPartner=rokuChannel&appName=rokuchannel&is_lat=1&bmodel=bm1&content=channel&platform=web&tags=ROKU_CONTENT_TAGS&coppa=false&content_type=livefeed&rdid=channel&genre=ROKU_ADS_CONTENT_GENRE&content_rating=ROKU_ADS_CONTENT_RATING&studio_id=viacom&channel_id=channel



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://static.wikia.nocookie.net/logopedia/images/b/b1/Yo%21_MTV_Raps_1987.svg/revision/latest?cb=20220102001126",VEVO 80'
https://service-stitcher.clusters.pluto.tv/v1/stitch/embed/hls/channel/5fd7b8bf927e090007685853/master.m3u8?deviceId=channel&deviceModel=web&deviceVersion=1.0&appVersion=1.0&deviceType=rokuChannel&deviceMake=rokuChannel&deviceDNT=1&advertisingId=channel&embedPartner=rokuChannel&appName=rokuchannel&is_lat=1&bmodel=bm1&content=channel&platform=web&tags=ROKU_CONTENT_TAGS&coppa=false&content_type=livefeed&rdid=channel&genre=ROKU_ADS_CONTENT_GENRE&content_rating=ROKU_ADS_CONTENT_RATING&studio_id=viacom&channel_id=channel



#EXTINF:-1 group-title="MUSIC" tvg-logo="",VEVO POP
https://service-stitcher.clusters.pluto.tv/stitch/hls/channel/5d93b635b43dd1a399b39eee/master.m3u8?terminate=false&deviceType=web&deviceMake=web&deviceModel=web&sid=890&deviceId=5d93b635b43dd1a399b39eee&deviceVersion=DNT&appVersion=DNT&deviceDNT=0&userId=&advertisingId=&deviceLat=&deviceLon=&app_name=&appName=web&buildVersion=&appStoreUrl=&architecture=&includeExtendedEvents=false&marketingRegion=US&serverSideAds=false



#EXTINF:-1 group-title="MUSIC" tvg-logo="",GREATEST HITS
https://ott-linear-channels.stingray.com/v1/master/734895816ccb1e836f8c1e81f772244d9be0077c/155/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="",HIT LIST
https://ott-linear-channels.stingray.com/v1/master/734895816ccb1e836f8c1e81f772244d9be0077c/107/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://d14c63magvk61v.cloudfront.net/hls/22865/stingray-greatest-hits/thumbv_20200717_232723.jpg",CLASSIC ROCK
https://ott-linear-channels.stingray.com/v1/master/734895816ccb1e836f8c1e81f772244d9be0077c/101/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://d14c63magvk61v.cloudfront.net/hls/22865/stingray-hit-list/thumbv_20200717_232723.jpg",ALTERNATIVE
https://ott-linear-channels.stingray.com/v1/master/734895816ccb1e836f8c1e81f772244d9be0077c/102/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.camtel.com/wp-content/uploads/2020/08/Stingray_Music_Vert_RGB-1.png",POP ADULT
https://ott-linear-channels.stingray.com/v1/master/734895816ccb1e836f8c1e81f772244d9be0077c/104/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.camtel.com/wp-content/uploads/2020/08/Stingray_Music_Vert_RGB-1.png",HIP HOP / R&B
https://ott-linear-channels.stingray.com/v1/master/734895816ccb1e836f8c1e81f772244d9be0077c/133/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.camtel.com/wp-content/uploads/2020/08/Stingray_Music_Vert_RGB-1.png",HOT COUNTRY
https://ott-linear-channels.stingray.com/v1/master/734895816ccb1e836f8c1e81f772244d9be0077c/108/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.camtel.com/wp-content/uploads/2020/08/Stingray_Music_Vert_RGB-1.png",FLASHBACK 70'
https://ott-linear-channels.stingray.com/v1/master/734895816ccb1e836f8c1e81f772244d9be0077c/115/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.camtel.com/wp-content/uploads/2020/08/Stingray_Music_Vert_RGB-1.png",REMEMBER 80'
https://ott-linear-channels.stingray.com/v1/master/734895816ccb1e836f8c1e81f772244d9be0077c/128/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.camtel.com/wp-content/uploads/2020/08/Stingray_Music_Vert_RGB-1.png",SOUL STORM
https://xumo-redbox.ott-channels.stingray.com/134/master.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.camtel.com/wp-content/uploads/2020/08/Stingray_Music_Vert_RGB-1.png",EXITOS DEL MOMENTOS
https://ott-linear-channels.stingray.com/hls/stirr/190.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.camtel.com/wp-content/uploads/2020/08/Stingray_Music_Vert_RGB-1.png",KPOP
https://srv1.zcast.com.br/kpoptv/kpoptv/chunklist_w987955597.m3u8



#EXTINF:-1 group-title="MUSIC" tvg-logo="https://www.camtel.com/wp-content/uploads/2020/08/Stingray_Music_Vert_RGB-1.png",9XM
http://d2q8p4pe5spbak.cloudfront.net/bpk-tv/9XM/9XM.isml/9XM-audio_208482_und=208000-video=877600.m3u8



#EXTINF:-1 group-title="SPORT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_420.png",SOCCER
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/soccerchannel-HD/sa_dash_vmx/soccerchannel-HD.mpd


#EXTINF:-1 group-title="SPORT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_307.png",SPOTV
http://37.49.230.42:8080/resplice/BiD8iEiMv7js1OUL/298570
https://dtaarjaj1diy9.cloudfront.net/live/eds/SPOTV-HD/sa_dash_vmx/SPOTV-HD.mpd


#EXTINF:-1 group-title="SPORT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_308.png",SPOTV2
http://37.49.230.42:8080/resplice/BiD8iEiMv7js1OUL/298570



#EXTINF:-1 group-title="SPORT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/150x150fight.jpg",FIGHT SPORT
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/FightSport/sa_dash_vmx/FightSport.mpd
#EXTINF:-1 group-title="SPORT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_102.jpg",MNC SPORT 1
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MNCSports-HD/sa_dash_vmx/MNCSports-HD.mpd
#EXTINF:-1 group-title="SPORT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_98.png",MNC SPORT 2
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MNCSport2-HD/sa_dash_vmx/MNCSport2-HD.mpd
#EXTINF:-1 group-title="SPORT" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_99.png",MNC SPORT 3
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Soccer-2/sa_dash_vmx/Soccer-2.mpd
#EXTINF:-1 group-title="SPORT" tvg-logo="https://playtv.unifi.com.my:7053/CPS/images/universal/film/logo/202108/20210811/2021081116172640657u.png",UNIFI SPORT 1
https://unifi-live05.secureswiftcontent.com/UnifiHD/live11.mpd



#EXTINF:-1 group-title="SPORT" tvg-logo="https://playtv.unifi.com.my:7053/CPS/images/universal/film/logo/202108/20210811/20210811161847362lc0.png",UNIFI SPORT 2
https://unifi-live04.secureswiftcontent.com/UnifiHD/live12.mpd



#EXTINF:-1 group-title="RELIGI" tvg-logo="https://www.lyngsat.com/logo/tv/ee/ewtn_us.png",EWTN
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/EWTN/sa_dash_vmx/EWTN.mpd
#EXTINF:-1 group-title="RELIGI" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_91.png",LIFE
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Life/sa_dash_vmx/Life.mpd
#EXTINF:-1 group-title="RELIGI" tvg-logo="https://www.lyngsat.com/logo/tv/tt/tv-mu-id.png",TV MU
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/TVMuhammadiyah/sa_dash_vmx/TVMuhammadiyah.mpd
#EXTINF:-1 group-title="RELIGI" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_92.png",MUSLIM TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/MNCMuslim/sa_dash_vmx/MNCMuslim.mpd
#EXTINF:-1 group-title="RELIGI" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_93.png",QURAN TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/AlQuranAlKareem/sa_dash_vmx/AlQuranAlKareem.mpd
#EXTINF:-1 group-title="RELIGI" tvg-logo="https://www.lyngsat.com/logo/tv/rr/reformed_21_id.png",REFORMED 21
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/Reformed21/sa_dash_vmx/Reformed21.mpd
#EXTINF:-1 group-title="RELIGI" tvg-logo="https://www.mncvision.id/userfiles/image/channel/channel_105.png",TAWAF TV
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/TawafTV/sa_dash_vmx/TawafTV.mpd
#EXTINF:-1 group-title="RELIGI" tvg-logo="https://www.lyngsat.com/logo/tv/tt/tv9_surabaya_id.png",TV 9 NUSANTARA
#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha
#KODIPROP:inputstream.adaptive.license_key=https://mrpw.ptmnc01.verspective.net/?deviceId=NDIzMDJhZmUtYWRjMi0zNGJkLTkyN2EtYmE1ZDFlZWIwODEz
#EXTVLCOPT:http-referrer=https://m.rctiplus.com/
https://rizea.my.id/vplus/https://nyanv-live-cdn.mncnow.id/live/eds/TV9/sa_dash_vmx/TV9.mpd














EOF;
    } elseif (preg_match("/(^(Mozilla|Opera)|Chrome|Firefox|Safari)\/[0-9.]+|IE|Netscape/", $http_user_agent) || preg_match("/WhatsApp|Telegram|Facebook|Instagram|Twitter/i", $http_user_agent)) {
        http_response_code(302);
        header('Location: /database/index.php', true, http_response_code());
        exit();
    }

    if (in_array($remote_addr, $blocked_ips) || (!empty($http_referer) && (strpos($http_addr, $http_referer) === false || strpos($http_url, $http_referer) === false)) || !isset($m3u) || empty($m3u)) {
        forbidden_or_expired(null, true);
    } else {
        http_response_code(200);
        header('Content-Type: application/x-mpegURL');
        ?>
#EXTM3U billed-msg="Apabila kalian pakai URL playlist selain '<?php print($http_url.$request); ?>', berarti playlist kalian pakai saat ini palsu."
#EXTM3U x-tvg-url="<?php print($epg); ?>" refresh=3600 tvg-shift=0
<?php
        $startime = date('Y/m/d H:i:s');
        $expired = date('Y/m/d H:i:s', strtotime('+1 hour'));
        $eol = PHP_EOL;
        $result = null;
        $source = explode($eol, preg_replace("/#EXTM3U[^\r\n]+{$eol}/", '', $m3u));

        foreach ($source as $line) {
               if (preg_match('/^#EXT/', $line) || preg_match('/^#KODIPROP/', $line, $drm1, $drm2)) {
                preg_match_all('/tvg-logo="(?P<logo>[^\"]+)",(?P<name>[^\r\n]+)/', $line, $ch);
                preg_match_all('/license_type=(?<type>[^\s]+)/', $line, $drm1);
                preg_match_all('/license_key=(?<key>[^\s]+)/', $line, $drm2);

                if (!empty($ch['logo'][0]) && !empty($ch['name'][0])) {
                    $logo = $ch['logo'][0];
                    $name = $ch['name'][0];
                } elseif (!empty($drm1['type'][0])) {
                    $drmtype = $drm1['type'][0];
                } elseif (!empty($drm2['key'][0])) {
                    $drmkey = $drm2['key'][0];
                }

                if (!empty($drm2[0])) {
                    $refresh_token = [];
                    $refresh_token['token_id'] = generate(8);
                    $refresh_token['drm_type'] = $drmtype;
                    $refresh_token['drm_path'] = base64_encode($drmkey);
                    $refresh_token['drm_channel'] = urlencode($name);
                    $refresh_token['started_time'] = str_replace('+', '%20', urlencode($startime));
                    $refresh_token['expired_time'] = str_replace('+', '%20', urlencode($expired));

                    $encode = [];
                    $encode['refresh_token'] = base64_encode(str_replace('\\/', '/', json_encode($refresh_token)));
                    $encode['generated'] = urlencode('<playlist>');
                    $encode['message'] = urlencode('Terimakasih memakai AndroboxTV. Selamat Menikmati.');
                    $encode = json_encode($encode);

                    $result .= "#KODIPROP:inputstream.adaptive.license_key={$http_url}/play.drm?ch=".base64_encode(str_replace('\\/', '/', $encode)).$eol;
                } else {
                    $result .= "{$line}{$eol}";
                }
            } elseif (preg_match('/^#https?:\/\//', $line)) {
                //skip//
            } elseif (preg_match('/^https?:\/\//', $line)) {
                [$type, $ext] = strpos($line, 'm3u8') !== false ? ['x-mpegURL', 'm3u8'] : ['dash+xml', 'mpd'];

                $refresh_token = [];
                $refresh_token['token_id'] = generate(8);
                $refresh_token['content_type'] = "application/{$type}";
                $refresh_token['channel_path'] = base64_encode($hmacrypt -> encrypt($line, ENCRYPTION_KEY));
                $refresh_token['channel_logo'] = $logo;
                $refresh_token['channel_name'] = urlencode($name);
                $refresh_token['started_time'] = str_replace('+', '%20', urlencode($startime));
                $refresh_token['expired_time'] = str_replace('+', '%20', urlencode($expired));

                $encode = [];
                $encode['refresh_token'] = base64_encode(str_replace('\\/', '/', json_encode($refresh_token)));
                $encode['generated'] = urlencode('<playlist>');
                $encode['message'] = urlencode('Terimakasih memakai playlist IPTV ini. Gunakan dengan bijak, tidak untuk diperjualbelikan.');
                $encode = json_encode($encode);

                $result .= (strpos($line, '/play/nosignal/') === false ? "{$http_url}/play.{$ext}?ch=".base64_encode($encode) : $line).$eol;
            } elseif (preg_match('/[#=]+ ?/', $line)) {
                $result .= "{$line}{$eol}";
            } elseif (preg_match('/^$/', $line)) {
                $result .= $eol;
            }
        }
        exit($result);
    }
}

class hmac256 {
    function encrypt($string, $encryption_key) {
        $cipher = 'AES-256-CBC';
        $options = OPENSSL_RAW_DATA;
        $hash_algo = 'sha256';
        $sha2len = 32;

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);

        $ciphertext_raw = openssl_encrypt($string, $cipher, $encryption_key, $options, $iv);
        $hmac = hash_hmac($hash_algo, $ciphertext_raw, $encryption_key, true);

        return $iv.$hmac.$ciphertext_raw;
    }

    function decrypt($encrypt, $encryption_key) {
        $cipher = 'AES-256-CBC';
        $options = OPENSSL_RAW_DATA;
        $hash_algo = 'sha256';
        $sha2len = 32;

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($encrypt, 0, $ivlen);

        $hmac = substr($encrypt, $ivlen, $sha2len);
        $ciphertext_raw = substr($encrypt, $ivlen + $sha2len);

        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $encryption_key, $options, $iv);
        $calcmac = hash_hmac($hash_algo, $ciphertext_raw, $encryption_key, true);

        if (function_exists('hash_equals')) {
            if (hash_equals($hmac, $calcmac))
                return $original_plaintext;
        } else {
            if ($this -> hash_equals_custom($hmac, $calcmac))
                return $original_plaintext;
        }
    }

    function hash_equals_custom($knownString, $userString) {
        if (function_exists('mb_strlen')) {
            $kLen = mb_strlen($knownString, '8bit');
            $uLen = mb_strlen($userString, '8bit');
        } else {
            $kLen = strlen($knownString);
            $uLen = strlen($userString);
        }
        if ($kLen !== $uLen)
            return false;
        $result = 0;
        for ($i = 0; $i < $kLen; $i++)
            $result |= (ord($knownString[$i]) ^ ord($userString[$i]));
        return 0 === $result;
    }
}

function generate($length=8) {
    $char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $char .= strtolower($char);
    $char .= '0123456789';
    return substr(str_shuffle($char), 0, $length);
}

function forbidden_or_expired($custom=null, $is_playlist=true) {
    $redirect = $is_playlist ? '/forbidden.m3u' : $custom;
    http_response_code(301);
    header("Location: {$redirect}", true, http_response_code());
    exit();
}

function get_contents($url, $headers=null, $useragent=null, $post=false, $postdata=null) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, true);
    if (is_array($headers))
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 20);
    if ($useragent !== null)
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    if ($postdata !== null)
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_POST, $post);
    curl_setopt($ch, CURLOPT_URL, $url);

    $response = curl_exec($ch);
    $length = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header = substr($response, 0, $length);
    $body = substr($response, $length);

    curl_close($ch);
    return [$body, $code, $header];
}

function is_http() {
    return (array_key_exists('HTTPS', $_SERVER) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) || (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'));
}

?>
