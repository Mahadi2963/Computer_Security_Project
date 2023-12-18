<?php 
include 'admin/db_connect.php'; 
?>



<!DOCTYPE html>
<html>

<head>
    <title>Search </title>
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


        #dsn{
            width: 50%;
            text-align: center;
        }
        button a{
            text-decoration: none;
            color: white;
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
        <h1>GitHub - Search by Repository Name</h1>

        <form method="GET">
            <label for="repoName">
                <h4>Search Repositories by Name:</h4>
            </label>
            <input type="text" id="repoName" name="repoName" />
            <button type="submit" name="searchRepoBtn">Search</button>
            <br>

            <button id="dsn"><a href="index.php">Back to home page</a></button>
        </form>
    </div>

    <br>
    <br>



    <ul id="repoList">
        <!-- Repository list will be displayed here -->
        <?php
        
        if (isset($_GET['repoName'])) {
            $repoName = $_GET['repoName'];
            $searchApiUrl = "https://api.github.com/search/repositories?q=$repoName";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $searchApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: AppName']);
            $searchJson = curl_exec($ch);
            curl_close($ch);

            $searchResults = json_decode($searchJson, true);

            if (isset($searchResults['items']) && !empty($searchResults['items'])) {
                echo '<h2>Repositories</h2>';
                foreach ($searchResults['items'] as $result) {
                    echo '<li><a href="' . $result['html_url'] . '" target="_blank">' . $result['name'] . '</a>: ' . $result['description'] . '</li>';
                }
            } else {
                echo '<li>No repositories found for the given name.</li>';
            }
        }
        ?>
    </ul>
</body>

</html>