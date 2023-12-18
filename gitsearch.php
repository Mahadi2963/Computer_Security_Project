<!DOCTYPE html>
<html>

<head>
    <title>Git Search</title>



    <style>
    h2 {
      text-align: center;
    }



    button {
      width: 90%;
      height: 40px;
      background-color:#008EFF;
    }


  </style>








    <br><br><br><br>
    <h3 style="text-align: center;" class="text-dark">Git Search</h3>


    <div class="container">


        <section class="page-section">
            <h4 style="text-align: center;">
                <div class="container box">

                    <h2>Search Options</h2>

                    <button class="btn1" onclick="func(this)" value="searchByUsername.php">Search by Username</button>
                    <br><br>
                    <button class="btn12" onclick="func(this)" value="searchByRepoName.php">Search by Repository Name</button>

                </div>

                
        <script>
            function func(link) {
                location.href = link.value;
            }
        </script>
            </h4>
        </section>
        <br>
        <div class="container">



        </div>
        </section>



    </div>