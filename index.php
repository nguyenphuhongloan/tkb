<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="bootstrap.css">
    <link rel="stylesheet" href="main.css">
</head>

<body>

    <?php
    $allthu = ["Hai", "Ba", "Tư", "Năm", "Sáu", "Bảy"];
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        include("simple_html_dom.php");
        $html = file_get_html('http://thongtindaotao.sgu.edu.vn/Default.aspx?page=thoikhoabieu&sta=1&id=' . $id);
        if ($html != "") {
            $masv = $html->find('#ctl00_ContentPlaceHolder1_ctl00_lblContentMaSV', 0);
            if($masv!=null)
                $masv = $masv->plaintext;
            $tenNS =
                $html->find('#ctl00_ContentPlaceHolder1_ctl00_lblContentTenSV', 0);
            if($tenNS != null)
                $tenNS = $tenNS->plaintext;
            // echo "<script>document.getElementById('masv').innerHTML=$masv</script>";
            $allresult = [];
            foreach ($html->find('table.body-table') as $e) {
                $str = htmlentities($e);
                $str =  strip_tags(str_replace('<', '?<', $e));
                $arr = explode('?', $str);
                $new = [];
                foreach ($arr as $key => $value) { //lọc bỏ value rỗng
                    if ($value != "")
                        array_push($new, $value);
                }
                if (count($new) > 1) { // xử lý value bỏ đi table con
                    $result = [];
                    array_push($result, $new[1]);
                    array_push($result, $new[2]);

                    $thu = [];
                    foreach ($new as $key => $value) {
                        foreach ($allthu as $value2) {
                            if ($value == $value2) {
                                array_push($thu, $value);
                            }
                        }
                    }
                    $length = count($thu);
                    array_push($result, $thu);
                    $tbd = [];
                    $st = [];
                    $phong = [];
                    foreach ($new as $key => $value) {
                        foreach ($thu as $key2 => $value2) {
                            if ($value == $value2) {
                                array_push($tbd, $new[$key + $length]);
                                array_push($st, $new[$key + $length + $length]);
                                array_push($phong, $new[$key + $length * 3]);
                                break;
                            }
                        }
                    }
                    array_push($result, $tbd);
                    array_push($result, $st);
                    array_push($result, $phong);
                    array_push($allresult, $result);
                }
            }

            $flag = [];
            foreach ($allresult as $key => $value) {
                $length = count($value[2]);
                $info = [];
                for ($i = 0; $i < $length; $i++) {
                    $thu = $value[2][$i];
                    $tietbd = $value[3][$i];
                    $st = $value[4][$i];
                    $flag[$tietbd][$thu] = $st;
                }
            }
            foreach ($flag as $key => $value) {
                foreach ($value as $thu => $st) {
                    for ($i = 1; $i < $st; $i++) {
                        $flag[$key + $i][$thu] = "X";
                    }
                }
            }
        }
    }
    ?>
    <div class="form-input">
        <form action="index.php" method="get">
            <label>Nhập mã số sinh viên</label>
            <br>
            <input type="text" name="id">
            <button>OK</button>
            <?php
                if(isset($masv) && $masv!=null)
                    echo "<div> Mã sinh viên: <span>$masv</span></div>";
            if (isset($tenNS) && $tenNS!=null)
            echo "<div> Tên sinh viên: <span>$tenNS</span></div>";
            ?>
            
            
        </form>

    </div>
    <table class="table-bordered">
        <tr>
            <th style="background-color: white;"></th>
            <th>Thứ 2</th>
            <th>Thứ 3</th>
            <th>Thứ 4</th>
            <th>Thứ 5</th>
            <th>Thứ 6</th>
            <th>Thứ 7</th>
        </tr>
        <?php
        $time = ["7h - 7h50", "7h50 - 8h40", "9h - 9h50", "9h50 - 10h40", "10h50 - 11h30", "1h - 1h50", "1h50 - 2h40", "3h - 3h50", "3h50 - 4h40", "4h40 - 5h30"];
        for ($i = 1; $i <= 10; $i++) {

            echo "<tr>";
            echo "<td class='tiet'>Tiết $i <br>" . $time[$i - 1] . "</td>";
            foreach ($allthu as $thu) {
                if (isset($flag[$i][$thu])) {
                    $st = $flag[$i][$thu];
                    if (is_numeric($st)) {
                        echo "<td rowspan='$st'>";
                        foreach ($allresult as $value) {
                            // echo "thu = $thu, tiet = $i";
                            $length = count($value[2]);
                            for ($j = 0; $j < $length; $j++) {
                                if ($value[2][$j] == $thu && $value[3][$j] == $i) {
                                    $sophong = $value[5][$j];
                                    echo "<div class='mamon'>$value[0]</div>";
                                    echo "<div class='tenmon'>$value[1]</div>";
                                    echo "<div class='sophong'>$sophong</div>";
                                }
                            }
                        }

                        echo "</td>";
                    }
                } else {
                    echo "<td></td>";
                }
            }
            echo "</tr>";
        }
        ?>
    </table>
</body>

</html>