<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المجلدات</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        h1 {
            color: #007bff;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            margin: 10px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .folder-list {
            margin-top: 20px;
            text-align: left;
            margin-bottom: 20px;
        }
        .folder-list li {
            background-color: #e9ecef;
            padding: 8px;
            margin: 5px;
            border-radius: 5px;
            list-style-type: none;
        }
        .folder-list li a {
            color: #007bff;
            text-decoration: none;
        }
        .folder-list li a:hover {
            text-decoration: underline;
        }
        .input-group {
            margin-top: 20px;
            text-align: center;
        }
        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            background-color: #28a745;
            color: white;
            display: none;
        }
        .message.error {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>إدارة المجلدات</h1>

        <button onclick="showCreateForm()">إنشاء ملف جديد</button>
        <button onclick="showFolders()">عرض المجلدات</button>
        <button onclick="deleteAll()">حذف جميع المجلدات</button>

        <div id="createForm" style="display: none;">
            <h2>إنشاء ملف جديد</h2>
            <input type="text" id="link" placeholder="أدخل الرابط هنا" />
            <button onclick="createFile()">إنشاء</button>
        </div>

        <div id="folderList" class="folder-list" style="display: none;">
            <h2>المجلدات الموجودة</h2>
            <ul id="folders"></ul>
        </div>

        <div id="message" class="message"></div>
    </div>

    <script>
        function showCreateForm() {
            document.getElementById('createForm').style.display = 'block';
            document.getElementById('folderList').style.display = 'none';
        }

        function showFolders() {
            fetch('process.php?action=list')
                .then(response => response.json())
                .then(data => {
                    let folders = data.folders;
                    let folderList = document.getElementById('folders');
                    folderList.innerHTML = '';
                    if (folders.length > 0) {
                        folders.forEach(folder => {
                            let li = document.createElement('li');
                            li.innerHTML = `<a href="${folder}" target="_blank">${folder}</a>`;
                            folderList.appendChild(li);
                        });
                    } else {
                        folderList.innerHTML = 'لا توجد مجلدات لعرضها.';
                    }
                    document.getElementById('folderList').style.display = 'block';
                    document.getElementById('createForm').style.display = 'none';
                });
        }

        function createFile() {
            let link = document.getElementById('link').value;
            if (link === '') {
                showMessage('❌ يجب إدخال رابط!', 'error');
                return;
            }

            fetch('process.php?action=create', {
                method: 'POST',
                body: new URLSearchParams({ link })
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('http')) {
                    showMessage(`✅ تم إنشاء الملف بنجاح! رابط المجلد: <a href="${data}" target="_blank">${data}</a>`);
                } else {
                    showMessage(data, 'error');
                }
            });
        }

        function deleteAll() {
            fetch('process.php?action=delete_all', { method: 'POST' })
                .then(response => response.text())
                .then(data => showMessage(data));
        }

        function showMessage(message, type = '') {
            let messageBox = document.getElementById('message');
            messageBox.classList.remove('error');
            if (type === 'error') {
                messageBox.classList.add('error');
            }
            messageBox.innerHTML = message;
            messageBox.style.display = 'block';
            setTimeout(() => {
                messageBox.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
