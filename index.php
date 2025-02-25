<?php
require 'openid.php';
try {
    $openid = new LightOpenID($_SERVER['HTTP_HOST']);
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$openid->mode) {
        $openid->identity = 'https://openid.ntpc.edu.tw/';
        $openid->required = array(
            'namePerson/friendly',
            'contact/email',
            'namePerson',
            'birthDate',
            'person/gender',
            'contact/postalCode/home',
            'contact/country/home',
            'pref/language',
            'pref/timezone'
        );
        header('Location: ' . $openid->authUrl());
    }
} catch (ErrorException $e) {
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title></title>
    <link href="favicon.ico" rel="icon" />
    <link href="css/bootstrap.css" rel="stylesheet" />
    <link href="css/table.css" rel="stylesheet" />
    <link href="css/site.css" rel="stylesheet" />
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/site.js"></script>
</head>
<body>
    <?php if ($openid->mode && $openid->validate()) { ?>
        <?php $attr = $openid->getAttributes(); ?>
        <a class="btn btn-sm btn-danger" href="/">登出</a>
        <table class="bordered mt-3">
            <tr>
                <th>欄位</th>
                <th>內容</th>
            </tr>
            <tr>
                <td>帳號</td>
                <td><?= end(array_values(explode('/', $openid->identity))) ?></td>
            </tr>
            <tr>
                <td>識別碼</td>
                <td><?= $attr['contact/postalCode/home'] ?></td>
            </tr>
            <tr>
                <td>姓名</td>
                <td><?= $attr['namePerson'] ?></td>
            </tr>
            <tr>
                <td>暱稱</td>
                <td><?= $attr['namePerson/friendly'] ?></td>
            </tr>
            <tr>
                <td>性別</td>
                <td><?= $attr['person/gender'] == 'M' ? '男' : '女' ?></td>
            </tr>
            <tr>
                <td>出生日期</td>
                <td><?= $attr['birthDate'] ?></td>
            </tr>
            <tr>
                <td>公務信箱</td>
                <td><?= $attr['contact/email'] ?></td>
            </tr>
            <tr>
                <td>單位簡稱</td>
                <td><?= $attr['contact/country/home'] ?></td>
            </tr>
            <tr>
                <td>年級</td>
                <td><?= substr($attr['pref/language'], 0, 2) ?></td>
            </tr>
            <tr>
                <td>班級</td>
                <td><?= substr($attr['pref/language'], 2, 2) ?></td>
            </tr>
            <tr>
                <td>座號</td>
                <td><?= substr($attr['pref/language'], 4, 2) ?></td>
            </tr>
        </table>
        <table class="bordered mt-3">
            <tr>
                <th>單位代碼</th>
                <th>單位全銜</th>
                <th>身分別</th>
                <th>職稱別</th>
                <th>職務別</th>
            </tr>
            <?php foreach (json_decode($attr['pref/timezone']) as $item) { ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td><?= $item->name ?></td>
                    <td><?= $item->role ?></td>
                    <td><?= $item->title ?></td>
                    <td><?= implode('、', $item->groups) ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <form method="post">
            <input type="submit" class="btn btn-sm btn-primary" value="登入" />
        </form>
    <?php } ?>
</body>
</html>