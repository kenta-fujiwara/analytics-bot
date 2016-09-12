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

function getWeeklyReport($analytics, $profile){
  // セッション数・PV・平均閲覧ページ数・平均セッション時間・直帰率を取得
  $results = $analytics->data_ga->get(
    'ga:' . $profile,
    '7daysAgo',
    'yesterday',
    'ga:sessions,ga:pageviews,ga:pageviewsPerSession,ga:avgSessionDuration,ga:bounceRate'
  );

  // 取得したデータからレポート部分を抽出
  $data = $results->rows;

  // 7日前と昨日の日付を取得
  $start = date("n/d", strtotime("-1 week"));
  $end   = date("n/d", strtotime("-1 day"));

  // データを整形
  $report = $start . '〜' . $end . 'のレポート' . "\n";
  $report .= '訪問数 : ' . $data[0][0] . "\n";
  $report .= '合計PV : ' . $data[0][1] . "\n";
  $report .= '平均閲覧ページ数 : ' . round( $data[0][2], 2 ) . 'ページ' . "\n";
  $report .= '平均滞在時間 : ' . ceil( $data[0][3] ) . '秒' . "\n";
  $report .= '直帰率 : ' . round( $data[0][4], 1 ) . '%' .  "\n";

  return $report;
}

$report = getWeeklyReport($analytics, $profile);
