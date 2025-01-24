<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>留言板</title>
    <!-- Include Semantic UI CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <!-- Include jQuery (required for Semantic UI JavaScript) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Semantic UI JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <style>
        body {
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="ui container">
    <h1 class="ui header">留言板</h1>
    <form method="post" action="index.php" class="ui form">
        <div class="field">
            <label for="channel">选择频道:</label>
            <select id="channel" name="channel" class="ui dropdown">
                <option value="http://127.0.0.1:8000/general.php?c=mi">原神</option>
                <option value="http://127.0.0.1:8000/general.php?c=elden">老头环</option>
            </select>
        </div>
        <button type="submit" class="ui primary button">进入频道</button>
    </form>

<?php
include('class.php');

if (isset($_POST['channel'])) {
    $url = $_POST['channel'];
    if (preg_match("/file:/i", $url)) die("<div style='color: red'>hacker!<div>");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if ($response === false) {
        echo "请求失败: " . curl_error($ch);
    } else {
        echo "<br><div>" . $response . "</div>";
    }
    curl_close($ch);

    $parsedUrl = parse_url($url);
    parse_str($parsedUrl['query'], $queryParams);
    $currentChannel = $queryParams['c'];
    echo '<h2 class="ui dividing header">发送留言</h2>';
    echo '<form id="messageForm" method="post" enctype="multipart/form-data" action="index.php" class="ui form">';
    echo '<input type="hidden" name="channel" value="' . htmlspecialchars($url) . '">';
    echo '<input type="hidden" name="currentChannel" value="' . htmlspecialchars($currentChannel) . '">';

    echo '<div class="field">';
    echo '<label for="nickname">昵称:</label>';
    echo '<input type="text" id="nickname" name="nickname" required>';
    echo '</div>';

    echo '<div class="field">';
    echo '<label for="contentType">Type:</label>';
    echo '<select id="contentType" name="contentType" class="ui dropdown">';
    echo '<option value="Text">文本</option>';
    echo '<option value="Picture">图片</option>';
    echo '</select>';
    echo '</div>';

    echo '<div id="textContent" class="field">';
    echo '<label for="content">内容:</label>';
    echo '<textarea id="content" name="content" required></textarea>';
    echo '</div>';

    echo '<div id="pictureContent" class="field" style="display: none;">';
    echo '<label for="picture">图片:</label>';
    echo '<input type="file" id="picture" name="picture">';
    echo '</div>';

    echo '<button type="button" class="ui primary button" onclick="submitForm()">发送留言</button>';
    echo '</form>';
}

if (isset($_POST['content'])) {
    $nickname = $_POST['nickname'];
    $contentType = $_POST['contentType'];
    $channel = $_POST['currentChannel'];
    $content = "";
    if ($contentType === 'Text') {
        $content = $_POST['content'];
    } elseif ($contentType === 'Picture') {
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] == UPLOAD_ERR_OK) {
            $content = base64_encode(file_get_contents($_FILES['picture']['tmp_name']));
        }
    }
    $message = new Message($nickname, date('Y-m-d H:i:s'), $contentType, $content, $channel);
    $serializedMessage = base64_encode(serialize($message));
    $apiUrl = "http://127.0.0.1:8000/api.php";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(['data' => $serializedMessage]));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($curl);
    curl_close($curl);
}
?>
<form id="reloadForm" method="post" action="index.php" style="display:none;">
    <input type="hidden" name="channel" value="<?php echo htmlspecialchars($url); ?>">
</form>
<script>
    document.getElementById('contentType').addEventListener('change', function() {
        var textContent = document.getElementById('textContent');
        var pictureContent = document.getElementById('pictureContent');

        if (this.value === 'Text') {
            textContent.style.display = 'block';
            pictureContent.style.display = 'none';
        } else if (this.value === 'Picture') {
            textContent.style.display = 'none';
            pictureContent.style.display = 'block';
        }
    });
    function submitForm() {
        var form = document.getElementById('messageForm');
        var formData = new FormData(form);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php', true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log("success");
                setTimeout(function() {
                    document.getElementById('reloadForm').submit();
                }, 1000); 
            } else {
                console.log('fail: ' + xhr.statusText);
            }
        };
        xhr.send(formData);
    }
</script>
</div>
</body>
</html>
