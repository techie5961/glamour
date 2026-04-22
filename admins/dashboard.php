<?php
#echo password_hash('ABALAK@5',PASSWORD_DEFAULT);
#exit;
session_start();

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Access Denied',
        'message' => 'Please login as admin first'
    ];
    header('Location: login.php');
    exit();
}

// Load users data from one level up
$users_file = '../users.json';
$users = [];
if(file_exists($users_file)){
    $users = json_decode(file_get_contents($users_file), true);
    if(!is_array($users)) $users = [];
}
$total_users = count($users);
$premium_users = count(array_filter($users, function($u) { return isset($u['upgraded']) && $u['upgraded'] == 'yes'; }));

// Load transactions from one level up
$transactions_file = '../transactions.json';
$transactions = [];
if(file_exists($transactions_file)){
    $transactions = json_decode(file_get_contents($transactions_file), true);
    if(!is_array($transactions)) $transactions = [];
}

// Load tasks from one level up
$tasks_file = '../tasks.json';
$tasks = [];
if(file_exists($tasks_file)){
    $tasks = json_decode(file_get_contents($tasks_file), true);
    if(!is_array($tasks)) $tasks = [];
}
$active_tasks = count(array_filter($tasks, function($t) { return isset($t['status']) && $t['status'] == 'active'; }));

// Load existing settings if any
$settings_file = '../admin_settings.json';
$settings = [];
if(file_exists($settings_file)){
    $settings = json_decode(file_get_contents($settings_file), true);
}

// Handle Approve Transaction
if(isset($_GET['approve']) && isset($_GET['id'])){
    $transaction_id = $_GET['id'];
    $transaction_updated = false;
    
    foreach($transactions as $key => $transaction){
        if($transaction['id'] == $transaction_id){
            $transactions[$key]['status'] = 'approved';
            $transaction_updated = true;
            
            // Update user's upgraded status to 'yes'
            foreach($users as $user_key => $user){
                if($user['email'] == $transaction['user_email']){
                    $users[$user_key]['upgraded'] = 'yes';
                    break;
                }
            }
            break;
        }
    }
    
    if($transaction_updated){
        // Save updated users
        file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
        // Save updated transactions
        file_put_contents($transactions_file, json_encode($transactions, JSON_PRETTY_PRINT));
        
        $_SESSION['notify'] = [
            'status' => 'success',
            'title' => 'Transaction Approved',
            'message' => 'User has been upgraded to Premium successfully!'
        ];
    } else {
        $_SESSION['notify'] = [
            'status' => 'error',
            'title' => 'Error',
            'message' => 'Transaction not found.'
        ];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Reject Transaction
if(isset($_GET['reject']) && isset($_GET['id'])){
    $transaction_id = $_GET['id'];
    $transaction_updated = false;
    
    foreach($transactions as $key => $transaction){
        if($transaction['id'] == $transaction_id){
            $transactions[$key]['status'] = 'rejected';
            $transaction_updated = true;
            break;
        }
    }
    
    if($transaction_updated){
        file_put_contents($transactions_file, json_encode($transactions, JSON_PRETTY_PRINT));
        $_SESSION['notify'] = [
            'status' => 'success',
            'title' => 'Transaction Rejected',
            'message' => 'Transaction has been rejected.'
        ];
    } else {
        $_SESSION['notify'] = [
            'status' => 'error',
            'title' => 'Error',
            'message' => 'Transaction not found.'
        ];
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['save_bank'])){
        // Save bank details
        $settings['bank'] = [
            'account_number' => $_POST['account_number'] ?? '',
            'bank_name' => $_POST['bank_name'] ?? '',
            'account_name' => $_POST['account_name'] ?? ''
        ];
        file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT));
        $_SESSION['notify'] = [
            'status' => 'success',
            'title' => 'Bank Details Saved',
            'message' => 'Bank information has been updated successfully'
        ];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    elseif(isset($_POST['save_social'])){
        // Save social links
        $settings['social'] = [
            'whatsapp' => $_POST['whatsapp_link'] ?? '',
            'telegram' => $_POST['telegram_link'] ?? ''
        ];
        file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT));
        $_SESSION['notify'] = [
            'status' => 'success',
            'title' => 'Social Links Saved',
            'message' => 'WhatsApp and Telegram links have been updated'
        ];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    elseif(isset($_POST['post_task'])){
        // Save task to tasks.json
        $tasks = [];
        if(file_exists($tasks_file)){
            $tasks = json_decode(file_get_contents($tasks_file), true);
            if(!is_array($tasks)) $tasks = [];
        }
        
        $new_task = [
            'id' => uniqid('task_'),
            'title' => $_POST['task_title'] ?? '',
            'description' => $_POST['task_description'] ?? '',
            'reward' => floatval($_POST['task_reward'] ?? 0),
            'link' => $_POST['task_link'] ?? '',
            'deadline' => $_POST['task_deadline'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'active'
        ];
        
        array_unshift($tasks, $new_task);
        file_put_contents($tasks_file, json_encode($tasks, JSON_PRETTY_PRINT));
        
        $_SESSION['notify'] = [
            'status' => 'success',
            'title' => 'Task Posted',
            'message' => 'New task has been published successfully'
        ];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Calculate stats
$pending_transactions = count(array_filter($transactions, function($t) { 
    return isset($t['status']) && $t['status'] == 'pending'; 
}));
$approved_transactions = count(array_filter($transactions, function($t) { 
    return isset($t['status']) && $t['status'] == 'approved'; 
}));
$total_volume = array_sum(array_column($transactions, 'amount'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Glamour</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0B0B0B 0%, #0F1A1A 100%);
            font-family: 'Inter', sans-serif;
            color: #FFFFFF;
            min-height: 100vh;
        }

        :root {
            --color-bg-primary: #0B0B0B;
            --color-neon-green-1: #00FFB2;
            --color-neon-green-2: #1EDFA3;
            --color-accent-cyan-1: #2FE6E6;
            --color-gold-1: #FFD54A;
            --color-text-muted: #A8A8A8;
        }

        /* Navbar */
        .navbar {
            background: rgba(18, 18, 24, 0.95);
            backdrop-filter: blur(12px);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,215,0,0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo h2 {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff, var(--color-gold-1));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-badge {
            background:rgba(255, 193, 7, 0.15);
            padding: 8px 16px;
            border-radius: 60px;
            font-size: 0.85rem;
            border: 1px solid gold;
        }

        .logout-btn {
            background: rgba(255, 77, 77, 0.1);
            color: #FF6B6B;
            padding: 8px 20px;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
            border: 1px solid rgba(255, 77, 77, 0.3);
        }

        .logout-btn:hover {
            background: rgba(255, 77, 77, 0.2);
            transform: translateY(-2px);
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .stat-card {
            background: rgba(18, 18, 28, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 28px;
            border: 1px solid rgba(255,215,0,0.2);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(0, 255, 178, 0.4);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--color-neon-green-1);
            margin-bottom: 16px;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--color-text-muted);
            font-size: 0.9rem;
        }

        /* Section Title */
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid var(--color-neon-green-1);
            padding-left: 20px;
        }

        /* Transactions Table */
        .transactions-section {
            background: rgba(18, 18, 28, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 32px;
            padding: 32px;
            border: 1px solid rgba(255,215,0,0.2);
            margin-bottom: 48px;
            overflow-x: auto;
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
        }

        .transaction-table th,
        .transaction-table td {
            padding: 16px 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .transaction-table th {
            color: var(--color-neon-green-1);
            font-weight: 600;
            font-size: 0.85rem;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.15);
            color: #FFC107;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-approved {
            background: rgba(0, 255, 178, 0.15);
            color: #00FFB2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-rejected {
            background: rgba(255, 77, 77, 0.15);
            color: #FF6B6B;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        .btn-approve {
            background: linear-gradient(135deg, #00FFB2, #1EDFA3);
            border: none;
            padding: 6px 16px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.75rem;
            color: #0B0B0B;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-approve:hover {
            transform: scale(1.05);
        }

        .btn-reject {
            background: rgba(255, 77, 77, 0.2);
            border: 1px solid rgba(255, 77, 77, 0.5);
            padding: 6px 16px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.75rem;
            color: #FF6B6B;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            margin-left: 8px;
        }

        .btn-reject:hover {
            background: rgba(255, 77, 77, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--color-text-muted);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        /* Forms Grid */
        .forms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 32px;
            margin-bottom: 48px;
        }

        .form-card {
            background: rgba(18, 18, 28, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 32px;
            border: 1px solid rgba(255,215,0,0.2);
        }

        .form-card h3 {
            font-size: 1.5rem;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-card h3 i {
            color: var(--color-neon-green-1);
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--color-text-muted);
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            background: rgba(10, 10, 15, 0.7);
            border: 1.5px solid rgb(255, 215, 0,0.2);
            border-radius: 5px;
            padding: 14px 18px;
            font-size: 1rem;
            color: #FFFFFF;
            font-family: 'Inter', sans-serif;
            transition: all 0.25s;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            outline: none;
            border-color: var(--color-neon-green-1);
            box-shadow: 0 0 0 3px rgba(0, 255, 178, 0.1);
        }

        .btn-submit {
            background: gold;
            border: none;
            padding: 14px 28px;
            border-radius: 5px;
            font-weight: 700;
            font-size: 0.9rem;
            color: #0B0B0B;
            cursor: pointer;
            transition: all 0.25s;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px gold;
        }

        /* Task Form Specific */
        .task-form .input-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        /* Notification */
        .notification {
            position: fixed;
            top: 24px;
            right: 24px;
            max-width: 380px;
            background: rgba(18, 18, 28, 0.95);
            backdrop-filter: blur(16px);
            border-radius: 32px;
            padding: 16px 20px;
            border-left: 4px solid var(--color-neon-green-1);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            gap: 14px;
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            z-index: 1000;
        }
        .notification.show {
            transform: translateX(0);
        }
        .notification.success {
            border-left-color: var(--color-neon-green-1);
        }
        .notification.error {
            border-left-color: #FF4D4D;
        }
        .notification-icon {
            font-size: 1.8rem;
        }
        .notification.success .notification-icon {
            color: var(--color-neon-green-1);
        }
        .notification.error .notification-icon {
            color: #FF6B6B;
        }
        .notification-close {
            background: none;
            border: none;
            color: var(--color-text-muted);
            font-size: 1.2rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            .forms-grid {
                grid-template-columns: 1fr;
            }
            .navbar {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
            .transaction-table th,
            .transaction-table td {
                padding: 10px 8px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">
        <h2>GLAMOUR<span style="color: #00FFB2;">.</span> ADMIN</h2>
    </div>
    <div class="admin-info">
        <div class="admin-badge">
            <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator'); ?>
        </div>
        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>

<div class="container">
    <!-- Stats Section -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value"><?php echo number_format($total_users); ?></div>
            <div class="stat-label">Total Users</div>
        </div>
      
       
    
    </div>

   
    <!-- Forms Grid -->
    <div class="forms-grid">
        <!-- Bank Details Form -->
        <div class="form-card">
            <h3><i class="fas fa-university"></i> Bank Account Details</h3>
            <form method="POST">
                <div class="input-group">
                    <label>Account Number</label>
                    <input type="text" name="account_number" placeholder="Enter account number" 
                           value="<?php echo htmlspecialchars($settings['bank']['account_number'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label>Bank Name</label>
                    <input type="text" name="bank_name" placeholder="e.g., Moniepoint, Opay, PalmPay" 
                           value="<?php echo htmlspecialchars($settings['bank']['bank_name'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label>Account Name</label>
                    <input type="text" name="account_name" placeholder="Full name on account" 
                           value="<?php echo htmlspecialchars($settings['bank']['account_name'] ?? ''); ?>" required>
                </div>
                <button type="submit" name="save_bank" class="btn-submit">
                    <i class="fas fa-save"></i> Save Bank Details
                </button>
            </form>
        </div>

        <!-- Social Links Form -->
        <div class="form-card">
            <h3><i class="fas fa-share-alt"></i> Verification Links</h3>
            <form method="POST">
                <div style="display: none;" class="input-group">
                    <label><i class="fab fa-whatsapp"></i> WhatsApp Contact Link</label>
                    <input type="url" name="whatsapp_link" placeholder="https://wa.me/234XXXXXXXXXX" 
                           value="<?php echo htmlspecialchars($settings['social']['whatsapp'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label>Payment Verification Link</label>
                    <input type="url" name="telegram_link" placeholder="https://t.me/username" 
                           value="<?php echo htmlspecialchars($settings['social']['telegram'] ?? ''); ?>" required>
                </div>
                <button type="submit" name="save_social" class="btn-submit">
                    <i class="fas fa-save"></i> Save Link
                </button>
            </form>
        </div>
    </div>

   
<!-- Notification -->
<?php if(isset($_SESSION['notify'])): ?>
<div id="autoNotify" class="notification <?php echo $_SESSION['notify']['status']; ?> show">
    <div class="notification-icon">
        <i class="fas <?php echo $_SESSION['notify']['status'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
    </div>
    <div class="notification-content">
        <div class="notification-title"><?php echo htmlspecialchars($_SESSION['notify']['title']); ?></div>
        <div class="notification-message"><?php echo htmlspecialchars($_SESSION['notify']['message']); ?></div>
    </div>
    <button class="notification-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
<?php unset($_SESSION['notify']); endif; ?>

<script>
    // Auto remove notification after 4 seconds
    setTimeout(() => {
        const notif = document.getElementById('autoNotify');
        if(notif) notif.remove();
    }, 4000);
</script>

</body>
</html>