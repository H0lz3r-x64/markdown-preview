<?php
require 'vendor/autoload.php';

$input = '';
$markdown = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['text']) && $_POST['text'] !== '') {
        $input = $_POST['text'];
    } else {
        $input = '[!NOTE]<br>No input provided<br>';
    }

    // Initialize Parsedown
    $parsedown = new Parsedown();

    // Convert Markdown to HTML
    $dirtyHtml = $parsedown->text($input);

    // Configure HTML Purifier
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);

    // Clean HTML
    $markdown = $purifier->purify($dirtyHtml);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Markdown Renderer</title>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.9/dist/purify.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="favicon.ico" type="image/ico">

    <link rel="stylesheet" href="libs\github-markdown-css-5.5.1\github-markdown.css">
    <style>
        body {
            background-color: #1e1e1e;
            color: #c6c6c6;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
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
            width: 880px;
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

        #rendered-bar {
            box-sizing: border-box;
            width: 880px;
            margin: 0 auto;
            padding: 10px;
            background-color: #686868;
            color: #c6c6c6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        #rendered-bar button {
            background-color: #007acc;
            /* keep the blue color */
            color: #fff;
            /* keep the white color */
            border: none;
            border-radius: 0;
            /* remove border-radius to make buttons square */
            padding: 10px;
            cursor: pointer;
        }

        #rendered-bar button:hover {
            background-color: #005999;
            /* darken the blue color on hover */
        }

        #container {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        #button-group {
            margin-left: auto;
        }

        #button-group button {
            background-color: #007acc;
            color: #fff;
            border: none;
            border-radius: 0;
            padding: 10px;
            cursor: pointer;
        }

        #button-group button:hover {
            background-color: #005999;
        }

        #filename {
            background-color: transparent;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            color: white;
            padding: 5px;
            outline: none;
            /* simulate a border */
        }

        #filename:focus {
            background-color: #1a1a1a;
            outline: 2px solid #007acc;
            caret-color: #007acc;
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

    <div id="container">
        <div id="rendered-bar">
            <input type="text" id="filename" value="Rendered markdown">
            <div id="button-group">
                <button id="copy">Copy</button>
                <button id="download-raw">Download Raw</button>
                <button id="download-html">Download HTML</button>
            </div>
        </div>
        <article id="content" class="markdown-body">
        </article>
    </div>

    <script>
        var input = DOMPurify.sanitize(<?= json_encode($input); ?>);
        var markdown = DOMPurify.sanitize(<?= json_encode($markdown); ?>);
        document.getElementById('content').innerHTML = markdown;
        document.getElementById('input_area').value = input;

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

        document.getElementById('copy').addEventListener('click', function () {
            var textarea = document.createElement('textarea');
            textarea.textContent = markdown;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        });


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

        document.getElementById('download-raw').addEventListener('click', function () {
            var filename = document.getElementById('filename').value;
            var blob = new Blob([input], { type: "text/plain;charset=utf-8" });
            var url = URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = filename + '.md';
            link.click();
        });

        document.getElementById('download-html').addEventListener('click', async function () {
            var filename = document.getElementById('filename').value;

            var cssUrl = 'libs/github-markdown-css-5.5.1/github-markdown.css';
            var response = await fetch(cssUrl);
            var css = await response.text();

            var html = '<!DOCTYPE html>\n<html>\n<head>\n';
            html += '<style>\nbody { background: #0d1117; margin: 25px; }\n' + css + '\n</style>\n';
            console.log(css)
            html += '</head>\n<body>\n<article id="content" class="markdown-body">\n';
            html += markdown; // assuming 'markdown' contains the rendered HTML
            html += '\n</article>\n</body>\n</html>';
            console.log(html)

            var blob = new Blob([html], { type: "text/html;charset=utf-8" });
            var url = URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = filename + '.html';
            link.click();
        });
    </script>
</body>

</html>