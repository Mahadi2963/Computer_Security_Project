
<!DOCTYPE html>
<html>

<head>
    <title>GitHub API Example - Search by Username</title>
    <style>
        

        img {
            border-radius: 50%;
        }


        .container {
            height: 150px;
            box-shadow: 0px 4px 50px 0px gray;
            text-align: center;
        }

        input {
            width: 30%;
            height: 35px;

        }

        button {
            width: 30%;
            height: 33px;
            background-color: #008EFF;
        }

        a{
            text-decoration: none;
            color: black;
        }

        a:hover{
            text-decoration: none;
            color: black;
        }



        button a{
            text-decoration: none;
            color: black;
        }
    </style>
</head>

<body>



    <?php
    include('header.php');
    ?>

    <br>
    <br>

    <div class="container">

        <h1>GitHub - Search by Username</h1>

        <form method="GET">
            <label for="username"><h4>Enter GitHub Username:</h4></label> </label>
            <input type="text" id="username" name="username" />
            <button type="submit" name="searchBtn">Search</button>

            <br>

            <button id="dsn"><a href="index.php">Back to home page</a></button>
        </form>

    </div>
    <br>
    <br>

    <div id="userInfo">
        <!-- User information will be displayed here -->
        <?php
        if (isset($_GET['username'])) {
            $username = $_GET['username'];
            $userApiUrl = "https://api.github.com/users/$username";
            $reposApiUrl = "https://api.github.com/users/$username/repos";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $userApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: YourAppName']); // Replace with your app name
            $userJson = curl_exec($ch);
            curl_close($ch);

            $user = json_decode($userJson, true);

            echo '<h2>User Information</h2>';
            if (isset($user['login'])) {
                echo '<img src="' . $user['avatar_url'] . '" alt="User Avatar" width="200" height="200"><br>';
                echo '<p><strong>Name:</strong> ' . $user['name'] . '</p>';
                echo '<p><strong>Location:</strong> ' . $user['location'] . '</p>';
                echo '<p><strong>Followers:</strong> ' . $user['followers'] . '</p>';
                echo '<p><strong>Following:</strong> ' . $user['following'] . '</p>';

                // Display repository list
                echo '<h2>Repositories</h2>';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $reposApiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: YourAppName']); // Replace with your app name
                $reposJson = curl_exec($ch);
                curl_close($ch);

                $repos = json_decode($reposJson, true);

                if (!empty($repos)) {
                    echo '<ul>';
                    foreach ($repos as $repo) {
                        echo '<li><a href="' . $repo['html_url'] . '" target="_blank">' . $repo['name'] . '</a>: ' . $repo['description'] . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No repositories found.</p>';
                }
            } else {
                echo '<p>User not found.</p>';
            }
        }
        ?>
    </div>


</body>

</html>