<?php
// ==========================================
// Dark Mode File Manager | Gold Accent
// Max border-radius: 5px | NO form resubmission on refresh
// Two independent forms: Text Writer + File Upload
// ==========================================

// --- Configuration ---
define('TEXT_FILE', 'file.txt');
define('UPLOAD_DIR', 'uploads/');

// Create uploads directory if needed
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// --- Helper: Redirect to same page without query strings ---
function redirect_self() {
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// --- Handle File Upload ---
$upload_status = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_action'])) {
    if (isset($_FILES['user_file']) && $_FILES['user_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['user_file'];
        $original_name = basename($file['name']);
        $safe_name = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $original_name);
        $target_path = UPLOAD_DIR . $safe_name;
        
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $_SESSION['upload_status'] = "✓ Uploaded: " . htmlspecialchars($original_name);
        } else {
            $_SESSION['upload_status'] = "✗ Move failed. Check permissions.";
        }
    } else {
        $_SESSION['upload_status'] = "✗ No file or upload error.";
    }
    redirect_self();
}

// --- Handle Text File Write ---
$text_status = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text_action'])) {
    $new_content = isset($_POST['text_content']) ? $_POST['text_content'] : '';
    if (file_put_contents(TEXT_FILE, $new_content) !== false) {
        $_SESSION['text_status'] = "✓ Saved to file.txt";
    } else {
        $_SESSION['text_status'] = "✗ Write failed.";
    }
    redirect_self();
}

// --- Delete uploaded files ---
if (isset($_GET['delete_file']) && !empty($_GET['delete_file'])) {
    $file_to_delete = UPLOAD_DIR . preg_replace('/[^a-zA-Z0-9_.-]/', '_', basename($_GET['delete_file']));
    if (file_exists($file_to_delete) && is_file($file_to_delete)) {
        unlink($file_to_delete);
    }
    redirect_self();
}

// Start session to store flash messages
session_start();

// --- Retrieve flash messages and clear them ---
$upload_status = $_SESSION['upload_status'] ?? null;
$text_status = $_SESSION['text_status'] ?? null;
unset($_SESSION['upload_status'], $_SESSION['text_status']);

// --- Read current text file content ---
$current_content = file_exists(TEXT_FILE) ? file_get_contents(TEXT_FILE) : '';

// --- List uploaded files ---
$uploaded_files = is_dir(UPLOAD_DIR) ? array_diff(scandir(UPLOAD_DIR), ['.', '..']) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Gold File Manager | Dark Mode</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #0a0c10;
            font-family: system-ui, -apple-system, 'Segoe UI', 'Helvetica Neue', sans-serif;
            padding: 24px 16px 48px;
            min-height: 100vh;
            color: #e8edf5;
        }

        .container {
            max-width: 620px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        /* Card style - dark, max border-radius 5px */
        .card {
            background: #121418;
            padding: 22px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            border: 1px solid #24282f;
            border-radius: 5px;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #24282f;
        }

        .card-header h2 {
            font-size: 1.4rem;
            font-weight: 500;
            letter-spacing: -0.2px;
            color: #e2e8f0;
        }

        .emoji-icon {
            font-size: 1.6rem;
            opacity: 0.85;
        }

        /* Gold primary accents */
        .gold-accent {
            color: #e6b422;
        }

        button {
            background: #1e2128;
            border: 1px solid #e6b422;
            color: #e6b422;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 500;
            font-size: 0.9rem;
            width: 100%;
            cursor: pointer;
            transition: 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button:hover {
            background: #e6b422;
            color: #0a0c10;
            border-color: #e6b422;
        }

        button:active {
            transform: scale(0.97);
        }

        textarea {
            width: 100%;
            padding: 14px;
            font-size: 0.92rem;
            font-family: 'SF Mono', 'Fira Code', monospace;
            background: #0c0e12;
            border: 1px solid #2a2f38;
            border-radius: 5px;
            color: #e2e8f0;
            resize: vertical;
            min-height: 180px;
            line-height: 1.5;
            transition: 0.15s;
        }

        textarea:focus {
            outline: none;
            border-color: #e6b422;
            box-shadow: 0 0 0 2px rgba(230, 180, 34, 0.2);
        }

        input[type="file"] {
            width: 100%;
            padding: 12px;
            background: #0c0e12;
            border: 1px solid #2a2f38;
            border-radius: 5px;
            color: #cbd5e1;
            font-size: 0.85rem;
        }

        input[type="file"]::file-selector-button {
            background: #1e2128;
            border: 1px solid #3a404a;
            padding: 6px 16px;
            border-radius: 5px;
            color: #e6b422;
            font-weight: 500;
            margin-right: 14px;
            cursor: pointer;
            transition: 0.1s;
        }

        input[type="file"]::file-selector-button:hover {
            background: #2a2f38;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #9ca3af;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-msg {
            margin-top: 16px;
            padding: 10px 14px;
            border-radius: 5px;
            font-size: 0.8rem;
            background: #1e2229;
            color: #b9c7d9;
            border-left: 3px solid #e6b422;
        }

        .file-list {
            margin-top: 20px;
            background: #0f1115;
            border-radius: 5px;
            padding: 12px 14px;
        }

        .file-list h4 {
            font-size: 0.8rem;
            font-weight: 500;
            color: #e6b422;
            margin-bottom: 12px;
            letter-spacing: 0.3px;
        }

        .file-badge {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1a1e24;
            padding: 8px 14px;
            margin-bottom: 8px;
            border-radius: 5px;
            font-size: 0.8rem;
            border: 1px solid #252b33;
        }

        .file-badge span {
            font-family: monospace;
            color: #cbd5e6;
            word-break: break-all;
            max-width: 65%;
        }

        .delete-link {
            background: #2a1f1f;
            color: #f87171;
            text-decoration: none;
            padding: 4px 14px;
            border-radius: 5px;
            font-size: 0.7rem;
            font-weight: 500;
            transition: 0.1s;
        }

        .delete-link:active {
            background: #3a2a2a;
        }

        a.view-link {
            color: #e6b422;
            text-decoration: none;
            margin-right: 10px;
            font-size: 0.7rem;
        }

        a.view-link:active {
            opacity: 0.7;
        }

        .footer-note {
            text-align: center;
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 14px;
        }

        /* mobile adjustments */
        @media (max-width: 550px) {
            body {
                padding: 12px 12px 32px;
            }
            .card {
                padding: 18px;
            }
            .card-header h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- ========== FORM 1: TEXT EDITOR ========== -->
    <div class="card">
        <div class="card-header">
            <span class="emoji-icon">📄</span>
            <h2>file.txt editor</h2>
        </div>
        <form method="POST" action="">
            <div class="form-group">
                <label>✍️ Content (creates or overwrites file.txt)</label>
                <textarea name="text_content" required placeholder="Write anything...&#10;This will be saved to file.txt"></textarea>
            </div>
            <button type="submit" name="text_action" value="1">💾 Save text file</button>
            <?php if ($text_status): ?>
                <div class="status-msg"><?php echo htmlspecialchars($text_status); ?></div>
            <?php endif; ?>
        </form>
        <?php if (file_exists(TEXT_FILE)): ?>
            <div class="file-list" style="margin-top: 14px;">
                <div class="file-badge" style="justify-content: flex-start; gap: 12px;">
                    <span>📁 <?php echo TEXT_FILE; ?></span>
                    <span style="font-size:0.7rem; color:#e6b422;"><?php echo round(filesize(TEXT_FILE) / 1024, 1); ?> KB</span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========== FORM 2: FILE UPLOAD ========== -->
    <div class="card">
        <div class="card-header">
            <span class="emoji-icon">⬆️</span>
            <h2>Upload file</h2>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>📎 Images, documents, etc.</label>
                <input type="file" name="user_file" accept="*/*" required>
            </div>
            <button type="submit" name="upload_action" value="1">📤 Upload</button>
            <?php if ($upload_status): ?>
                <div class="status-msg"><?php echo htmlspecialchars($upload_status); ?></div>
            <?php endif; ?>
        </form>

        <?php if (!empty($uploaded_files)): ?>
        <div class="file-list">
            <h4>📂 stored files (<?php echo count($uploaded_files); ?>)</h4>
            <?php foreach ($uploaded_files as $file): 
                $file_path = UPLOAD_DIR . $file;
                $is_image = @exif_imagetype($file_path) !== false;
            ?>
            <div class="file-badge">
                <span><?php echo $is_image ? '🖼️' : '📄'; ?> <?php echo htmlspecialchars($file); ?></span>
                <div style="display: flex; gap: 8px;">
                    <?php if ($is_image): ?>
                        <a href="<?php echo htmlspecialchars(UPLOAD_DIR . $file); ?>" target="_blank" class="view-link">view</a>
                    <?php endif; ?>
                    <a href="?delete_file=<?php echo urlencode($file); ?>" class="delete-link" onclick="return confirm('Delete <?php echo addslashes($file); ?>?')">del</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="status-msg" style="background:#171b22; margin-top: 16px;">✨ No files yet. Upload images or documents.</div>
        <?php endif; ?>
    </div>

    <!-- minimal info card - clean -->
    <div class="card" style="background: #111317; border-color: #20242b;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div style="font-size: 0.7rem; line-height: 1.4; color: #8b98a9;">
                📍 <?php echo htmlspecialchars(basename(__DIR__)); ?>/<br>
                📄 file.txt &nbsp;| 📁 uploads/
            </div>
            <span style="font-size: 1.4rem; opacity: 0.5;">🌟</span>
        </div>
        <div class="footer-note" style="margin-top: 12px;">
            Two independent forms — text saves to file.txt, uploads go to /uploads/
        </div>
    </div>
</div>
</body>
</html>