<?php
// Check if a shortcut is received
if (isset($_GET['q'])) {
    // Variable
    $shortcut = htmlspecialchars($_GET['q']);

    // Check if it is a valid shortcut
    $bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    $result = $req->fetch();

    if ($result['x'] != 1) {
        header('location: index.php?error=true&message=Unknown URL address');
        exit();
    }

    // Redirect to the actual URL
    $req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
    $req->execute(array($shortcut));

    $result = $req->fetch();
    header('location: ' . $result['url']);
    exit();
}

// Handle form submission
if (isset($_POST['url'])) {
    // Variable
    $url = $_POST['url'];

    // Validation
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        // Not a valid link
        header('location: index.php?error=true&message=Invalid URL address');
        exit();
    }

    // Generate shortcut
    $shortcut = crypt($url, rand());

    // Check if the URL has already been shortened
    $bdd = new PDO('mysql:host=localhost;dbname=bitly;charset=utf8', 'root', '');
    $req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
    $req->execute(array($url));

    $result = $req->fetch();

    if ($result['x'] != 0) {
        header('location: index.php?error=true&message=Address already shortened');
        exit();
    }

    // Insert the new URL and shortcut into the database
    $req = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES(?, ?)');
    $req->execute(array($url, $shortcut));

    header('location: index.php?short=' . $shortcut);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Express URL Shortener</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #2980B9;
            background: linear-gradient(to right, #FFFFFF, #6DD5FA, #2980B9);
            min-width: 400px;
            margin: 0;
            padding: 0;
            color: #333;
        }
        #hello {
            padding: 20px;
        }
        header {
            text-align: center;
        }
        #logo {
            max-width: 150px;
        }
        h1 {
            font-size: 2em;
        }
        h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        input[type="url"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-right: 10px;
            width: 60%;
        }
        input[type="submit"] {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
        #result {
            margin-top: 20px;
            text-align: center;
        }
        #brands {
            padding: 20px;
            text-align: center;
        }
        .picture {
            max-width: 100px;
            margin: 10px;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        footer a {
            color: #3498db;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- PRESENTATION -->
    <section id="hello">
        <!-- CONTAINER -->
        <div class="container">
            <!-- HEADER -->
            <header>
                <img src="pictures/logo.png" alt="logo" id="logo">
            </header>

            <!-- VP -->
            <h1>Long URL? Shorten it!</h1>
            <h2>Significantly better and shorter than others.</h2>

            <!-- FORM -->
            <form method="post" action="">
                <input type="url" name="url" placeholder="Paste a link to shorten" required>
                <input type="submit" value="Shorten">
            </form>

            <?php if (isset($_GET['error']) && isset($_GET['message'])) { ?>
                <div class="center">
                    <div id="result">
                        <b><?php echo htmlspecialchars($_GET['message']); ?></b>
                    </div>
                </div>
            <?php } else if (isset($_GET['short'])) { ?>
                <div class="center">
                    <div id="result">
                        <b>SHORTENED URL: </b>
                        <a href="http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>" target="_blank">
                            http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <!-- BRANDS -->
    <section id="brands">
        <!-- CONTAINER -->
        <div class="container">
            <h3>These brands trust us</h3>
            <img src="pictures/1.png" alt="1" class="picture">
            <img src="pictures/2.png" alt="2" class="picture">
            <img src="pictures/3.png" alt="3" class="picture">
            <img src="pictures/4.png" alt="4" class="picture">
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <img src="pictures/logo2.png" alt="logo" id="logo"><br>
        2018 © Bitly<br>
        <a href="#">Contact</a> - <a href="https://www.linkedin.com/in/igor-maquin/">About</a>
    </footer>
</body>
</html>
