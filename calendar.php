<?php

//Deprecatedエラーを非表示
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

$bg = $_POST['bg-img'];
$aspect = $_POST['aspect'];
$period = $_POST['期間'];
$size = $_POST['大きさ'];
$position = $_POST['位置'];
$weekstart = $_POST['週の開始日'];
$sat = $_POST['休日表示_土曜'];
$sun = $_POST['休日表示_日曜'];
$holiday = $_POST['休日表示_祝日'];
$arrangement = $_POST['組み'];

//////画像を合成
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

////数字画像の位置指定
// 16:9（Windows）の場合
if ($aspect == "1609") {
//    　　　横組の場合
    if ($arrangement == "横") {
        if ($position == "左上") {
            $px = 40;
            $py = 40;
        } else if ($position == "左下") {
            $px = 40;
            $py = 1080 - (1080 * $size) - 40;
        } else if ($position == "中央") {
            $px = (1920 - (1920 * $size)) / 2;
            $py = (1080 - (1080 * $size)) / 2;
        } else if ($position == "右上") {
            $px = 1920 - (1920 * $size) - 40;
            $py = 40;
        } else if ($position == "右下") {
            $px = 1920 - (1920 * $size) - 40;
            $py = 1080 - (1080 * $size) - 40;
        }

    //      縦組の場合
    } else if ($arrangement == "縦") {
        if ($position == "左上") {
            $px = 40;
            $py = 40;
        } else if ($position == "左下") {
            $px = 40;
            $py = 1080 - (1080 * $size);
        } else if ($position == "中央") {
            $px = (1920 - (500 * $size)) / 2;
            $py = (1080 - (1080 * $size)) / 2;
        } else if ($position == "右上") {
            $px = 1920 - (500 * $size) - 40;
            $py = 40;
        } else if ($position == "右下") {
            $px = 1920 - (500 * $size) - 40;
            $py = 1080 - (1080 * $size) - 40;
        }
    }
}

// 16:10（Mac）の場合
if ($aspect == "1610") {
    //　　　横組の場合
    if ($arrangement == "横") {
        if ($position == "左上") {
            $px = 140;
            $py = 40;
        } else if ($position == "左下") {
            $px = 140;
            $py = 1080 - (1080 * $size) - 40;
        } else if ($position == "中央") {
            $px = (1920 - (1920 * $size)) / 2;
            $py = (1080 - (1080 * $size)) / 2;
        } else if ($position == "右上") {
            $px = 1920 - (1920 * $size) - 140;
            $py = 40;
        } else if ($position == "右下") {
            $px = 1920 - (1920 * $size) - 140;
            $py = 1080 - (1080 * $size) - 40;
        }
    } //   縦組の場合
    else if ($arrangement == "縦") {
        if ($position == "左上") {
            $px = 140;
            $py = 40;
        } else if ($position == "左下") {
            $px = 140;
            $py = 1080 - (1080 * $size) - 40;
        } else if ($position == "中央") {
            $px = (1920 - (500 * $size)) / 2;
            $py = (1080 - (1080 * $size)) / 2;
        } else if ($position == "右上") {
            $px = 1920 - (500 * $size) - 140;
            $py = 40;
        } else if ($position == "右下") {
            $px = 1920 - (500 * $size) - 140;
            $py = 1080 - (1080 * $size) - 40;
        }
    }
}
//　画像を合成
@ImageCopyResampled($img, $img2, $px, $py, 0, 0, $sx * $size, $sy * $size, $sx, $sy);

//////保存
//フォルダ「calendar」の中に日付の名前のフォルダを作成
$folder = "calendar/" . date('Ymd');
if (!file_exists($folder)) {
    mkdir($folder);
}

//// 16:09ならそのまま保存
//ファイル名をランダムに生成
$file_name = "calendar/" . date('Ymd') . "/" . md5(date('Y-m-dH:i:s')) . ".png";

//保存
if($aspect == "1609") {
    imagepng($img, $file_name);
    imagedestroy($img);
}

////16:10ならトリミングして保存
else if($aspect == "1610"){
//    ひとまず1920×1080で合成し保存
    imagepng($img, "combine.png");
    
//トリミング後の土台の画像を作る
    $img1610 = imagecreatetruecolor(1728, 1080);
    
//トリミング前の画像を読み込む
    $baseImage = imagecreatefrompng("combine.png");
    
//トリミング後の土台の画像に合わせてトリミング前の画像を縮小しコピーペーストする
    imagecopyresampled($img1610, $baseImage, -96, 0, 0, 0, 1920, 1080, 1920, 1080);
    
// 保存
    imagepng($img1610, $file_name);
    imagedestroy($img);
    }
    
//////溜まるキャッシュを削除
//ディレクトリ名を取得
$dir = dirname(__FILE__) . '/calendar/';

// 古いファイルを削除
$list = get_file_dir_list($dir);


//2分以上前のファイルを削除
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

?>
<!doctype html>
<html lang="jpn">
<head>
    <base href="/" />
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/css/lightbox.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.7.1/js/lightbox.min.js"
            type="text/javascript"></script>
    <title>DARTSカレンダー2023 カスタマイズツール</title>
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
            <input type="radio" name="bg-img" value="02" id="02" <?php if ($_POST['bg-img'] == "02") {
                    echo "checked";
                } ?>>
                <label for="02">
                    <img src="img/bg/for-preview/02.jpg" alt="美星スペースガードセンターで観測されたホームズ彗星">
                </label>

                <input type="radio" name="bg-img" value="01" id="01" <?php if ($_POST['bg-img'] == "01") {
                    echo "checked";
                } ?>>
                <label for="01">
                    <img src="img/bg/for-preview/01.jpg" alt="「かぐや」月面DEMデータを用いて作成した3DCG画像">
                </label>

                <input type="radio" name="bg-img" value="18" id="18" <?php if ($_POST['bg-img'] == "18") {
                    echo "checked";
                } ?>>
                <label for="18">
                    <img src="img/bg/for-preview/18.jpg" alt="「ひので」日食">
                </label>

                <input type="radio" name="bg-img" value="14" id="14" <?php if ($_POST['bg-img'] == "14") {
                    echo "checked";
                } ?>>
                <label for="14">
                    <img src="img/bg/for-preview/14.jpg" alt="「あかり」が観測したマゼラン星雲の遠赤外線画像.png">
                </label>

                <input type="radio" name="bg-img" value="16" id="16" <?php if ($_POST['bg-img'] == "16") {
                    echo "checked";
                } ?>>
                <label for="16">
                    <img src="img/bg/for-preview/16.jpg" alt="noname">
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

<!--                <input type="radio" name="bg-img" value="07" id="07" --><?php //if ($_POST['bg-img'] == "07") {
//                    echo "checked";
//                } ?><!---->
<!--                <label for="07">-->
<!--                    <img src="img/bg/for-preview/07.png" alt="「あけぼの」衛星が紫外線で見たオーロラサブストーム">-->
<!--                </label>-->

                <input type="radio" name="bg-img" value="15" id="15" <?php if ($_POST['bg-img'] == "15") {
                    echo "checked";
                } ?>>
                <label for="15">
                    <img src="img/bg/for-preview/15.jpg" alt="「ひので」可視光・磁場望遠鏡(SOT)によって撮影されたCaII H線(396.9nm)の画像">
                </label>

                <input type="radio" name="bg-img" value="17" id="17" <?php if ($_POST['bg-img'] == "17") {
                    echo "checked";
                } ?>>
                <label for="17">
                    <img src="img/bg/for-preview/17.jpg" alt="地球">
                </label>

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
                        <a href="img/ex/example07.png" data-lightbox="example"
                           data-title="16:9/一年/大/右下/月曜始まり/土曜-なし/日曜-なし/祝日-なし/横組み の使用例">
                            <img src="img/ex/example07.png" alt="例１">
                        </a>

                        <a href="img/ex/example02.png" data-lightbox="example"
                           data-title="16:10/4・5・6月/中/中央/月曜始まり/土曜-青/日曜-赤/祝日-なし/縦組み の使用例">
                            <img src="img/ex/example02.png" alt="例２">
                        </a>

                        <a href="img/ex/example08.png" data-lightbox="example"
                           data-title="16:9/1~6月/特大/右下/日曜始まり/土曜-なし/日曜-赤/祝日-赤/縦組み の使用例">
                            <img src="img/ex/example08.png" alt="例３">
                        </a>

                        <a href="img/ex/example09.png" data-lightbox="example"
                           data-title="16:10/1・2・3月/大/中央/月曜始まり/土曜-なし/日曜-なし/祝日-なし/横組み の使用例">
                            <img src="img/ex/example09.png" alt="例４">
                        </a>

                        <a href="img/ex/example06.png" data-lightbox="example"
                           data-title="16:10/3・4月/中/右下/日曜始まり/土曜-青/日曜-赤/祝日-赤/縦組み の使用例">
                            <img src="img/ex/example06.png" alt="例５">
                        </a>

                        <a href="img/ex/example10.png" data-lightbox="example"
                           data-title="16:9/11・12月/極小/左上/日曜始まり/土曜-青/日曜-赤/祝日-赤/横組み の使用例">
                            <img src="img/ex/example10.png" alt="例６">
                        </a>
                    </div>
                </div>
            </section>

            <!--  合成プレビュー表示/アスペクト比選択肢  -->
            <section class="preview-img">
                <p>プレビューボタンで選択を反映</p>
                <img src="<?php echo $file_name;?>">

                <div class="aspect">
                    <input type="radio" id="1609" name="aspect" value="1609" <?php if ($_POST['aspect'] == "1609") {
                            echo "checked";} ?>>
                        <label for="1609">16:9（Windows）</label>

                        <input type="radio" id="1610" name="aspect" value="1610" <?php if ($_POST['aspect'] == "1610") {
                            echo "checked";} ?>>
                    <label for="1610">16:10（Mac）</label>
                </div>
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

                        <input type="radio" id="0.65" name="大きさ" value="0.65" <?php if ($_POST['大きさ'] == "0.65") {
                            echo "checked";
                        } ?>>
                        <label for="0.65">中</label>

                        <input type="radio" id="0.8" name="大きさ" value="0.8" <?php if ($_POST['大きさ'] == "0.8") {
                            echo "checked";
                        } ?>>
                        <label for="0.8">大</label>

                        <input type="radio" id="0.95" name="大きさ" value="0.95" <?php if ($_POST['大きさ'] == "0.95") {
                            echo "checked";
                        } ?>>
                        <label for="0.95">特大</label>
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
<footer><p>このツールは<a href="https://darts.isas.jaxa.jp/">DARTS</a>にアーカイブされた公開データを使って、<a href="https://www.vivivit.com/tanakanonoka">田中乃々華</a>さん（武蔵野美術大学）とDARTSメンバーが協力のもとに制作・公開しました。<br>ソースコードは<a href="https://github.com/darts-isas/calendargen">こちら</a>から公開されています。利用は、<a href="https://www.isas.jaxa.jp/researchers/data-policy/">宇宙科学研究所のデータポリシー</a>に従います。<br>問い合わせ先: darts-admin@ML.isas.jaxa.jp</p></footer>

</body>
</html>
