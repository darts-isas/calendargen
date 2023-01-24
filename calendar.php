<?php

$bg = $_POST['bg-img'];
$period = $_POST['期間'];
$size = $_POST['大きさ'];
$position = $_POST['位置'];
$weekstart = $_POST['週の開始日'];
$sat = $_POST['休日表示_土曜'];
$sun = $_POST['休日表示_日曜'];
$holiday = $_POST['休日表示_祝日'];
$arrangement = $_POST['組み'];

//　取り込む画像の名前の参照方法
$img_name2 = $period . '_' . $weekstart . '_' . $sat . $sun . $holiday . '_' . $arrangement;
$img_name2 = "./img/" . $img_name2 . ".png";

$img_name = $bg;
$img_name = "./img/bg/" . $img_name . ".png";


// 合成する画像を取り込む/背景
$img = imagecreatefrompng($img_name);

// 合成する画像を取り込む/数字
$img2 = imagecreatefrompng($img_name2);

// 合成する画像のサイズを取得
$sx = imagesx($img2);
$sy = imagesy($img2);

//　数字画像の位置指定
if ($arrangement == "横") {
    if ($position == "左上") {
        $px = 50;
        $py = 50;
    } else if ($position == "左下") {
        $px = 50;
        $py = 1080 - (1080 * $size) - 50;
    } else if ($position == "中央") {
        $px = (1920 - (1920 * $size)) / 2;
        $py = (1080 - (1080 * $size)) / 2;
    } else if ($position == "右上") {
        $px = 1920 - (1920 * $size) - 50;
        $py = 50;
    } else if ($position == "右下") {
        $px = 1920 - (1920 * $size) - 50;
        $py = 1080 - (1080 * $size) - 050;
    }
}

//　縦組の場合
if ($arrangement == "縦") {
    if ($position == "左上") {
        $px = 0;
        $py = 0;
    } else if ($position == "左下") {
        $px = 0;
        $py = 1080 - (1080 * $size);
    } else if ($position == "中央") {
        $px = (1920 - (500 * $size)) / 2;
        $py = (1080 - (1080 * $size)) / 2;
    } else if ($position == "右上") {
        $px = 1920 - (500 * $size);
        $py = 0;
    } else if ($position == "右下") {
        $px = 1920 - (500 * $size);
        $py = 1080 - (1080 * $size);
    }
}

//print $position;
//print $px;
//print $py;

//　画像を合成
@ImageCopyResampled($img, $img2, $px, $py, 0, 0, $sx * $size, $sy * $size, $sx, $sy);

//前々日のディレクトリを削除
$dir = dirname(__FILE__) . '/calendar/';
$list = get_file_dir_list($dir);

del_file_dir($list, '-2 minute');

function get_file_dir_list($dir=''){
    if ( !$dir || !is_dir($dir) ){ die('dirを正しく設定してください。');}
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $dir,
            FilesystemIterator::SKIP_DOTS
            |FilesystemIterator::KEY_AS_PATHNAME
            |FilesystemIterator::CURRENT_AS_FILEINFO
        ), RecursiveIteratorIterator::CHILD_FIRST
    );
    $list = array();
    foreach($iterator as $pathname => $info){
        $list[] = $pathname;
    }
    return $list;
}
function del_file_dir( $list=array(), $expire_date_str='-1 month' ){
    //削除期限
    date_default_timezone_set('Asia/Tokyo');
    $expire_timestamp = 0;
    if (($expire_timestamp = strtotime($expire_date_str)) === false) { die("The expire string : ({$expire_date_str}) is bogus"); }

    foreach ($list as $file_path) {
        if ( preg_match("/\.gitkeep/", $file_path) ){ continue; } // .gitkeep は削除しない
        $mod = filemtime( $file_path );
        if($mod < $expire_timestamp){
            if (is_dir($file_path)){
                //echo 'ディレクトリ削除します : '. $file_path.date("Y-m-d H:i:s", $mod)."\n";
                rmdir($file_path) or die("can not delete directory:({$file_path})");
            }
            if (is_file($file_path)){
                //echo 'ファイル削除します : '. $file_path.date("Y-m-d H:i:s", $mod)."\n";
                unlink($file_path) or die("can not delete file:({$file_path})");
            }
        }
    }
}

//日付のディレクトリを作成
$folder = "calendar/".date('Ymd');
if(!file_exists($folder)){
    mkdir($folder);
}

//ファイル名をランダムに生成
$file_name = "calendar/".date('Ymd')."/".md5(date('Y-m-dH:i:s')).".png";

// 別名で保存
imagepng($img, $file_name);

imagedestroy($img);

//　合成された画像を表示
//header('location:combine.png');

?>
<!doctype html>
<html lang="jpn">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/css/lightbox.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/js/lightbox.min.js"
            type="text/javascript"></script>
    <title>DARTS fromJAXA 2023カレンダー カスタマイズツール</title>
</head>

<body>

<header>
    <p class="title">DARTSカレンダー2023 カスタマイズツール</p>
    <p class="sub-title">あなたのデスクトップに合わせた壁紙をお作りいただけます</p>
</header>

<main>
    <!--  背景画像の選択肢　 -->
    <form action="calendar.php" method="post">
        <section class="images-library">
            <div class="container">

                <input type="radio" name="bg-img" value="01" id="01" <?php if ($_POST['bg-img'] == "01") {
                    echo "checked";
                } ?>>
                <label for="01">
                    <img src="img/bg/for-preview/01.jpg" alt="かぐや月面DEMデータを用いて作成した3DCG画像">
                </label>

                <input type="radio" name="bg-img" value="02" id="02" <?php if ($_POST['bg-img'] == "02") {
                    echo "checked";
                } ?>>
                <label for="02">
                    <img src="img/bg/for-preview/02.jpg" alt="美星スペースガードセンターで観測されたホームズ彗星">
                </label>

                <input type="radio" name="bg-img" value="03" id="03" <?php if ($_POST['bg-img'] == "03") {
                    echo "checked";
                } ?>>
                <label for="03">
                    <img src="img/bg/for-preview/03.jpg" alt="オーロラ">
                </label>

                <input type="radio" name="bg-img" value="04" id="04" <?php if ($_POST['bg-img'] == "04") {
                    echo "checked";
                } ?>>
                <label for="04">
                    <img src="img/bg/for-preview/04.jpg" alt="「ひので」のX線望遠鏡で観測した太陽の画像">
                </label>

                <input type="radio" name="bg-img" value="05" id="05" <?php if ($_POST['bg-img'] == "05") {
                    echo "checked";
                } ?>>
                <label for="05">
                    <img src="img/bg/for-preview/05.jpg" alt="リュウグウから回収された試料">
                </label>

                <!--                <input type="radio" name="bg-img" value="06" id="06" -->
                <?php //if($_POST['bg-img'] == "06"){echo "checked";} ?><!---->
                <!--                <label for="06">-->
                <!--                    <img src="img/bg/for-preview/06.jpg" alt="リュウグウから回収された試料_フラッシュ">-->
                <!--                </label>-->

                <input type="radio" name="bg-img" value="07" id="07" <?php if ($_POST['bg-img'] == "07") {
                    echo "checked";
                } ?>>
                <label for="07">
                    <img src="img/bg/for-preview/07.jpg" alt="「あけぼの」衛星が紫外線で見たオーロラサブストーム">
                </label>

                <!--                <input type="radio" name="bg-img" value="08" id="08" -->
                <?php //if($_POST['bg-img'] == "08"){echo "checked";} ?><!---->
                <!--                <label for="08">-->
                <!--                    <img src="img/bg/08.png" alt="「あけぼの」衛星が紫外線で見たオーロラサブストーム_ヨリ">-->
                <!--                </label>-->

                <input type="radio" name="bg-img" value="09" id="09" <?php if ($_POST['bg-img'] == "09") {
                    echo "checked";
                } ?>>
                <label for="09">
                    <img src="img/bg/for-preview/09.jpg" alt="衛星てんま">
                </label>

                <input type="radio" name="bg-img" value="10" id="10" <?php if ($_POST['bg-img'] == "10") {
                    echo "checked";
                } ?>>
                <label for="10">
                    <img src="img/bg/for-preview/10.jpg" alt="衛星あけぼの">
                </label>

                <input type="radio" name="bg-img" value="11" id="11" <?php if ($_POST['bg-img'] == "11") {
                    echo "checked";
                } ?>>
                <label for="11">
                    <img src="img/bg/for-preview/11.jpg" alt="">
                </label>

                <input type="radio" name="bg-img" value="12" id="12" <?php if ($_POST['bg-img'] == "12") {
                    echo "checked";
                } ?>>
                <label for="12">
                    <img src="img/bg/for-preview/12.jpg" alt="">
                </label>

                <input type="radio" name="bg-img" value="13" id="13" <?php if ($_POST['bg-img'] == "13") {
                    echo "checked";
                } ?>>
                <label for="13">
                    <img src="img/bg/for-preview/13.jpg" alt="">
                </label>
            </div>
        </section>

        <!--  合成例の画像  -->
        <div class="container">
            <section class="example-img">
                <div class="container">
                    <div class="example">
                        <p>生成例<br>クリックで拡大表示</p>
                        <a href="/img/ex/example01.png" data-lightbox="example"
                           data-title="一年/大/左下/月曜始まり/土曜-なし/日曜-なし/祝日-なし/横組み の場合">
                            <img src="/img/ex/example01.jpg" alt="例１">
                        </a>

                        <a href="/img/ex/example02.png" data-lightbox="example"
                           data-title="4・5・6月/中/月曜始まり/土曜-青/日曜-赤/祝日-なし/縦組み の場合">
                            <img src="/img/ex/example02.jpg" alt="例２">
                        </a>

                        <a href="/img/ex/example03.png" data-lightbox="example"
                           data-title="半年/特大/右上/月曜始まり/土曜-なし/日曜-なし/祝日-なし/横組み の場合">
                            <img src="/img/ex/example03.jpg" alt="例３">
                        </a>

                        <a href="/img/ex/example04.png" data-lightbox="example"
                           data-title="3・4月/中/中/日曜始まり/土曜-青/日曜-赤/祝日-赤/横組み の場合">
                            <img src="/img/ex/example04.jpg" alt="例４">
                        </a>
                    </div>
                </div>
            </section>

            <!--  合成プレビュー表示  -->
            <section class="preview-img">
                <p>プレビューボタンで選択を反映</p>
                <img src="<?php echo $file_name;?>">
            </section>

            <!--    カレンダー部分の選択肢    -->
            <section class="choice">
                <div class="container">
                    <div class="period">
                        <div class="period-p">
                            <p>期間</p>
                        </div>
                        <div class="period-choice">
                            <div class="period-blocks">
                                <p>２ヶ月間</p>
                                <input type="radio" id="0102" name="期間"
                                       value="0102" <?php if ($_POST['期間'] == "0102") {
                                    echo "checked";
                                } ?>>
                                <label for="0102">1・2月</label>

                                <input type="radio" id="0304" name="期間"
                                       value="0304" <?php if ($_POST['期間'] == "0304") {
                                    echo "checked";
                                } ?>>
                                <label for="0304">3・4月</label>

                                <input type="radio" id="0506" name="期間"
                                       value="0506" <?php if ($_POST['期間'] == "0506") {
                                    echo "checked";
                                } ?>>
                                <label for="0506">5・6月</label>

                                <input type="radio" id="0708" name="期間"
                                       value="0708" <?php if ($_POST['期間'] == "0708") {
                                    echo "checked";
                                } ?>>
                                <label for="0708">7・8月</label>

                                <input type="radio" id="0910" name="期間"
                                       value="0910" <?php if ($_POST['期間'] == "0910") {
                                    echo "checked";
                                } ?>>
                                <label for="0910">9・10月</label>

                                <input type="radio" id="1112" name="期間"
                                       value="1112" <?php if ($_POST['期間'] == "1112") {
                                    echo "checked";
                                } ?>>
                                <label for="1112">11・12月</label>
                            </div>
                            <div class="period-blocks">
                                <p>３ヶ月間</p>
                                <input type="radio" id="010203" name="期間"
                                       value="010203" <?php if ($_POST['期間'] == "010203") {
                                    echo "checked";
                                } ?>>
                                <label for="010203">1・2・3月</label>

                                <input type="radio" id="040506" name="期間"
                                       value="040506" <?php if ($_POST['期間'] == "040506") {
                                    echo "checked";
                                } ?>>
                                <label for="040506">4・5・6月</label>

                                <input type="radio" id="070809" name="期間"
                                       value="070809" <?php if ($_POST['期間'] == "070809") {
                                    echo "checked";
                                } ?>>
                                <label for="070809">7・8・9月</label>

                                <input type="radio" id="101112" name="期間"
                                       value="101112" <?php if ($_POST['期間'] == "101112") {
                                    echo "checked";
                                } ?>>
                                <label for="101112">10・11・12月</label>
                            </div>
                            <div class="period-blocks">
                                <p>半年間</p>
                                <input type="radio" id="前半年" name="期間"
                                       value="前半年" <?php if ($_POST['期間'] == "前半年") {
                                    echo "checked";
                                } ?>>
                                <label for="前半年">１〜６月</label>

                                <input type="radio" id="後半年" name="期間"
                                       value="後半年" <?php if ($_POST['期間'] == "後半年") {
                                    echo "checked";
                                } ?>>
                                <label for="後半年">７〜１２月</label>

                                <p class="period-a-year">一年間</p>
                                <input type="radio" id="一年" name="期間"
                                       value="一年" <?php if ($_POST['期間'] == "一年") {
                                    echo "checked";
                                } ?>>
                                <label for="一年">一年</label>

                            </div>
                        </div>
                    </div>

                    <div class="size">
                        <p class="size-p">大きさ</p>
                        <input type="radio" id="0.2" name="大きさ" value="0.2" <?php if ($_POST['大きさ'] == "0.2") {
                            echo "checked";
                        } ?>>
                        <label for="0.2">極小</label>

                        <input type="radio" id="0.3" name="大きさ" value="0.3" <?php if ($_POST['大きさ'] == "0.3") {
                            echo "checked";
                        } ?>>
                        <label for="0.3">小</label>

                        <input type="radio" id="0.5" name="大きさ" value="0.5" <?php if ($_POST['大きさ'] == "0.5") {
                            echo "checked";
                        } ?>>
                        <label for="0.5">中</label>

                        <input type="radio" id="0.8" name="大きさ" value="0.8" <?php if ($_POST['大きさ'] == "0.8") {
                            echo "checked";
                        } ?>>
                        <label for="0.8">大</label>

                        <input type="radio" id="0.9" name="大きさ" value="0.9" <?php if ($_POST['大きさ'] == "0.9") {
                            echo "checked";
                        } ?>>
                        <label for="0.9">特大</label>
                    </div>

                    <div class="position">
                        <p class="position-p">位置</p>
                        <input type="radio" id="左上" name="位置" value="左上" <?php if ($_POST['位置'] == "左上") {
                            echo "checked";
                        } ?>>
                        <label for="左上">左上</label>

                        <input type="radio" id="左下" name="位置" value="左下" <?php if ($_POST['位置'] == "左下") {
                            echo "checked";
                        } ?>>
                        <label for="左下">左下</label>

                        <input type="radio" id="中央" name="位置" value="中央" <?php if ($_POST['位置'] == "中央") {
                            echo "checked";
                        } ?>>
                        <label for="中央">中央</label>

                        <input type="radio" id="右上" name="位置" value="右上" <?php if ($_POST['位置'] == "右上") {
                            echo "checked";
                        } ?>>
                        <label for="右上">右上</label>

                        <input type="radio" id="右下" name="位置" value="右下" <?php if ($_POST['位置'] == "右下") {
                            echo "checked";
                        } ?>>
                        <label for="右下">右下</label>

                    </div>

                    <div class="weekstart">
                        <p class="weekstart-p">週の開始日</p>
                        <input type="radio" id="月曜始まり" name="週の開始日"
                               value="月曜始まり" <?php if ($_POST['週の開始日'] == "月曜始まり") {
                            echo "checked";
                        } ?>>
                        <label for="月曜始まり">月曜始まり</label>

                        <input type="radio" id="日曜始まり" name="週の開始日"
                               value="日曜始まり" <?php if ($_POST['週の開始日'] == "日曜始まり") {
                            echo "checked";
                        } ?>>
                        <label for="日曜始まり">日曜始まり</label>
                    </div>

                    <div class="holiday-color">
                        <div class="holiday-color-p">
                            <p>休日表示色</p>
                        </div>
                        <div class="holiday-color-choice">
                            <div>
                                <p>土曜</p>
                                <input type="radio" id="土なし" name="休日表示_土曜"
                                       value="なし" <?php if ($_POST['休日表示_土曜'] == "なし") {
                                    echo "checked";
                                } ?>>
                                <label for="土なし">なし</label>

                                <input type="radio" id="土青" name="休日表示_土曜"
                                       value="青" <?php if ($_POST['休日表示_土曜'] == "青") {
                                    echo "checked";
                                } ?>>
                                <label for="土青">青</label>
                            </div>
                            <div>
                                <p>日曜</p>
                                <input type="radio" id="日なし" name="休日表示_日曜"
                                       value="なし" <?php if ($_POST['休日表示_日曜'] == "なし") {
                                    echo "checked";
                                } ?>>
                                <label for="日なし">なし</label>

                                <input type="radio" id="日赤" name="休日表示_日曜"
                                       value="赤" <?php if ($_POST['休日表示_日曜'] == "赤") {
                                    echo "checked";
                                } ?>>
                                <label for="日赤">赤</label>
                            </div>
                            <div>
                                <p>祝日</p>
                                <input type="radio" id="祝なし" name="休日表示_祝日"
                                       value="なし" <?php if ($_POST['休日表示_祝日'] == "なし") {
                                    echo "checked";
                                } ?>>
                                <label for="祝なし">なし</label>

                                <input type="radio" id="祝赤" name="休日表示_祝日"
                                       value="赤" <?php if ($_POST['休日表示_祝日'] == "赤") {
                                    echo "checked";
                                } ?>>
                                <label for="祝赤">赤</label>
                            </div>
                        </div>
                    </div>

                    <div class="arrange">
                        <p class="arrange-p">組み</p>
                        <input type="radio" id="横" name="組み" value="横" <?php if ($_POST['組み'] == "横") {
                            echo "checked";
                        } ?>>
                        <label for="横">横組み</label>

                        <input type="radio" id="縦" name="組み" value="縦" <?php if ($_POST['組み'] == "縦") {
                            echo "checked";
                        } ?>>
                        <label for="縦">縦組み</label>
                    </div>
                </div>
            </section>
        </div>

        <!--   プレビューボタン     -->
        <div class="preview-btn">
            <input type="submit" value="preview" id="preview">
            <label for="preview">プレビュー</label>
        </div>

        <!--ダウンロードボタン-->
        <div class="download-btn">
            <a type="submit" href="<?php echo $file_name;?>" download="<?php echo $file_name;?>" class="download" id="download">ダウンロード</a>
        </div>
    </form>
</main>
<footer><p>© ️DARTS JAXA</p></footer>

</body>
</html>
