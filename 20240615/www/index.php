<?php
    function filter($cmd)
    {
        return preg_replace('/where/i','hacker',$cmd);
    }

    $p['name'] = $_GET['name'] ?? 'Guest';
    $p['img'] = 'show.jpg';

    $s = filter(serialize($p));

    $q = unserialize($s);
    $name = $q['name'];
    $img = base64_encode(file_get_contents($q['img'])); // Can You get flag?
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <style>
        body {
            height: 90%;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: block;
            margin: 0 auto 20px auto;
        }
    </style>
</head>
<body>
    <div class="ui container">
        <div class="ui raised segment">
            <div class="ui center aligned header">User Profile</div>
            <div class="ui centered card">
                <div class="image">
                    <img src="data:image/gif;base64,<?php echo $img; ?>" class="profile-img">
                </div>
                <div class="content">
                    <h3 class="ui header">Hi <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></h3>
                </div>
            </div>
            <div class="ui form">
                <div class="field">
                    <label>Enter your name</label>
                    <input type="text" id="name" placeholder="Enter your name">
                </div>
                <button class="ui primary button" onclick="redirectToProfile()">Submit</button>
                <br><br>
                <div class="field">
                    <label>Gift for you</label>
                    <textarea readonly class="ui input"><?php echo htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <script>
        function redirectToProfile() {
            const name = document.getElementById('name').value;
            window.location.href = `?name=${encodeURIComponent(name)}`;
        }
    </script>
</body>
</html>
