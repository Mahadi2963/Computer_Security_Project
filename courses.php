<!DOCTYPE html>
<html>

<head>
    <title>UIU Online Courses</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: lightblue;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        #searchBar {
            display: flex;
            justify-content:space-around;
            align-items: center;
            margin-bottom: 20px;
            height: 100px;
            width: 100%;
            background-color: white;
        }

        #courseSearch {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
            width: 60%;
        }

        #searchButton{
            width: 30%;
            background-color: #008EFF;
            color: white;
            border: none;
            height: 40px;
        }

        .course {
            border: 1px solid #ccc;
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .course h2 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }

        .course p {
            margin-top: 10px;
            color: #666;
        }

        .course a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-right: 10px;
        }

        .course a:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <br><br><br>


    <body>




    <div class="container">
        <h2>Resources</h2>
        


        <div id="searchBar">
            <input type="text" id="courseSearch" placeholder="Search for courses...">
            <button id="searchButton">Search</button>
        </div>


        <div id="courseList">
            
        


        <?php
        $courses = array(
            array(
                'title' => 'Operating System Concepts/Operating Systems',
                'instructor' => 'Rayhan Ahamed',
                'description' => 'Learn the basics of programming with this introductory course.',
                'link' => 'https://www.youtube.com/watch?v=479fFX0CZ-Y&list=PL3_ATDyQLqPiuxm-GjBI8lXFp9M19v-lD',
                'qslink' => 'https://drive.google.com/drive/folders/1OQhsHDdU3cQFeIPlxtCwFkzr-_rg6Ojm?usp=share_link'
            ),
            array(
                'title' => 'Software Engineering',
                'instructor' => 'Rafi ul Rashid',
                'description' => 'Learn the basics of software methodology with this introductory course.',
                'link' => 'https://www.youtube.com/watch?v=c1amLpllQSE&list=PL3_ATDyQLqPgepsuDv5zQX97CTQWu5_Rr',
                'qslink' => 'https://drive.google.com/drive/folders/1OQhsHDdU3cQFeIPlxtCwFkzr-_rg6Ojm?usp=share_link'
            ),
            array(
                'title' => 'Web Programming',
                'instructor' => 'Md Saidul Haque Anik',
                'description' => 'Learn the basics of programming with this introductory course.',
                'link' => 'https://www.youtube.com/watch?v=UdHRHLvn4g4',
                'qslink' => 'https://drive.google.com/drive/folders/1fpBg8OcUkEftg7Bqjta-YYiVK87FNp_U?usp=share_link'
            ),
            // Add more courses as needed


            array(
                'title' => 'Data Science Fundamentals',
                'instructor' => 'Mike Johnson',
                'description' => 'Explore the essentials of data science and learn data analysis techniques.',
                'link' => 'https://www.youtube.com/watch?v=ua-CiDNNj30&list=PLWKjhJtqVAblQe2CCWqV4Zy3LY01Z8aF1',
                'qslink' => 'https://drive.google.com/drive/folders/1NHw2LYh4DGOKxiNFkNplL8N9wbfq9rgB?usp=share_link'
            ),

            array(
                'title' => 'Web Development 101',
                'instructor' => 'Jane Doe',
                'description' => 'Get started with web development and learn HTML, CSS, and JavaScript.',
                'link' => 'https://example.com/courses/web-development-101',
                'qslink' => 'https://drive.google.com/drive/folders/1fpBg8OcUkEftg7Bqjta-YYiVK87FNp_U?usp=share_link'
            ),


            array(
                'title' => ' Compiler/Compiler Design',
                'instructor' => 'Nahid Hossain',
                'description' => 'Get basic idea of how compiler work in computer.',
                'link' => 'https://www.youtube.com/watch?v=OrNSY64eAOo&list=PL3_ATDyQLqPidPjIs1Zh9opdanOP3mgKQ',
                'qslink' => 'https://drive.google.com/drive/folders/1fpBg8OcUkEftg7Bqjta-YYiVK87FNp_U?usp=share_link'
            ),

        );




        foreach ($courses as $course) {
            echo '<div class="course">';
            echo '<h2>' . $course['title'] . '</h2>';
            echo '<p><strong>Instructor:</strong> ' . $course['instructor'] . '</p>';
            echo '<p>' . $course['description'] . '</p>';
            echo '<a href="' . $course['link'] . ' ">Enroll Now</a>';

            // Add the "Qs bank" button
            echo '<a href="' . $course['qslink'] . '/qs-bank">Qs Bank</a>';

            echo '</div>';
        }
        ?>

    </div>





        </div>
    </div>

    <script>
        const courses = <?php echo json_encode($courses); ?>;
        const courseSearch = document.getElementById('courseSearch');
        const searchButton = document.getElementById('searchButton');
        const courseListContainer = document.getElementById('courseList');

        function generateCourseList(filteredCourses) {
            courseListContainer.innerHTML = '';

            if (filteredCourses.length === 0) {
                const noResultMessage = document.createElement('p');
                noResultMessage.textContent = 'No courses found.';
                courseListContainer.appendChild(noResultMessage);
            } else {
                filteredCourses.forEach(course => {
                    const courseDiv = document.createElement('div');
                    courseDiv.classList.add('course');
                    courseDiv.innerHTML = `
                        <h2>${course.title}</h2>
                        <p><strong>Instructor:</strong> ${course.instructor}</p>
                        <p>${course.description}</p>
                        <a href="${course.link}">Lectures</a>
                        <a href="${course.qslink}/qs-bank">Question Bank</a>
                    `;
                    courseListContainer.appendChild(courseDiv);
                });
            }
        }

        function handleSearch() {
            const searchValue = courseSearch.value.trim().toLowerCase();
            const filteredCourses = courses.filter(course => {
                const courseTitle = course.title.toLowerCase();
                return courseTitle.includes(searchValue);
            });
            generateCourseList(filteredCourses);
        }

        searchButton.addEventListener('click', handleSearch);
        courseSearch.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                handleSearch();
            }
        });

        generateCourseList(courses);

    </script>
</body>

</html>

