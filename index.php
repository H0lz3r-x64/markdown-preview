<?php
// index.php
$markdown = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['text']) && $_POST['text'] !== '') {
        $markdown = $_POST['text'];
    } else {
        $markdown = '
        [!NOTE]
        No input provided
        ';
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Markdown Renderer</title>
    <!-- Include your JS markdown library here. For example, marked.js -->
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.9/dist/purify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="libs\github-markdown-css-5.5.1\github-markdown.css">
    <style>
        body {
            background-color: #1e1e1e;
            color: #c6c6c6;
            font-family: Arial, sans-serif;
            display: flex;
            max-height: 100vh;
            overflow: hidden;
        }

        #sidebar {
            position: relative;
            max-width: 45%;
            min-width: 15%;
        }

        form {
            display: flex;
            flex-direction: column;
            height: 95%;
            padding: 20px;
            padding-top: 0px;
        }

        form h1 {
            text-align: center;
            color: #007acc;
            border-bottom: 2px solid #007acc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        textarea {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #2e2e2e;
            color: #c6c6c6;
            border: none;
            border-radius: 5px;
            height: 150px;
            resize: vertical;
            overflow: auto;
            scrollbar-width: thin;
            scrollbar-color: #007acc #2e2e2e;
        }

        textarea::-webkit-scrollbar {
            width: 12px;
        }

        textarea::-webkit-scrollbar-track {
            background: #2e2e2e;
        }

        textarea::-webkit-scrollbar-thumb {
            background-color: #007acc;
            border-radius: 20px;
            border: 3px solid #2e2e2e;
        }

        input[type="file"] {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #2e2e2e;
            color: #c6c6c6;
            border: none;
            border-radius: 5px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007acc;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #005999;
        }

        .markdown-body {
            box-sizing: border-box;
            min-width: 200px;
            max-width: 980px;
            margin: 0 auto;
            padding: 45px;
            background-color: #2e2e2e;
            border-radius: 5px;
        }

        @media (max-width: 767px) {
            .markdown-body {
                padding: 15px;
            }
        }

        #content {
            flex: 2;
            margin-top: 10px;
            margin-bottom: 10px;
            overflow-y: auto;
            position: relative;
        }

        #content::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 30px;
            background: linear-gradient(to right, #2e2e2e 10px, transparent 10px), linear-gradient(to right, #2e2e2e 20px, transparent 20px) 0 30px;
            border-right: 1px solid #3e3e3e;
            counter-reset: line;
        }

        #dragger {
            width: 10px;
            background: #333;
            cursor: ew-resize;
            position: absolute;
            /* Add this */
            top: 0;
            right: 0;
            /* Change from bottom to right */
            bottom: 0
        }
    </style>
</head>

<body>
    <div id="sidebar">
        <form method="post" enctype="multipart/form-data">
            <h1>Markdown Renderer</h1>
            <textarea id="input_area" name="text" placeholder="Enter markdown text here"></textarea>
            <input type="file" name="file" accept=".md">
            <input type="submit" value="Render">
        </form>
        <div id="dragger"></div>
    </div>

    <article id="content" class="markdown-body">
    </article>

    <script>
        // Get the markdown content from PHP
        var markdown = <?= json_encode($markdown); ?>;
        // Use the JS library to convert markdown to HTML
        document.getElementById('content').innerHTML = DOMPurify.sanitize(marked.parse(markdown));
        document.getElementById('input_area').value = DOMPurify.sanitize(markdown);

        var dragger = document.getElementById('dragger');
        var sidebar = document.getElementById('sidebar');
        dragger.onmousedown = function (e) {
            document.onmousemove = function (e) {
                var percentage = (e.clientX / window.innerWidth) * 100;
                if (percentage > 5 && percentage < 80) {
                    sidebar.style.flex = '0 0 ' + percentage + '%';
                }
            }
        };
        document.onmouseup = function (e) {
            document.onmousemove = null;
        };

        // Select the file input and textarea elements
        var fileInput = document.querySelector('input[type="file"]');
        var textarea = document.getElementById('input_area');

        // Listen for changes on the file input element
        fileInput.addEventListener('change', function (e) {
            // Create a new FileReader instance
            var reader = new FileReader();

            // Read the selected file as text
            reader.readAsText(e.target.files[0]);

            // When the file has been read, set the value of the textarea
            reader.onload = function (e) {
                textarea.value = e.target.result;
            };
        });
    </script>
</body>

</html>