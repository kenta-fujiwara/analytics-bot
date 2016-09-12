// ライブラリの読み込み
require_once 'google-api-php-client/src/Google/autoload.php';

// サービスアカウントのメールアドレス
$service_account_email = 'analytics-bot@analytics-bot-143204.iam.gserviceaccount.com';

// 秘密キーファイルの読み込み
$key = file_get_contents('analytics-bot-e105379956cc.p12');

// プロファイル(ビュー)ID
$profile = '129347988';

// Googleクライアントのインスタンスを作成
$client = new Google_Client();
$analytics = new Google_Service_Analytics($client);

// クレデンシャルの作成
$cred = new Google_Auth_AssertionCredentials(
    $service_account_email,
    array(Google_Service_Analytics::ANALYTICS_READONLY),
    $key
);
$client->setAssertionCredentials($cred);
if($client->getAuth()->isAccessTokenExpired()) {
  $client->getAuth()->refreshTokenWithAssertion($cred);
}

$result = $analytics->data_ga->get(
  'ga:' . $profile, // アナリティクス ビュー ID
  '7daysAgo',       // データの取得を開始する日付は7日前
  'yesterday',      // データの取得を終了する日付は昨日
  'ga:sessions'     // セッション数を取得する
);

// 結果を出力
echo $result -> rows[0][0];
