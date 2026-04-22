<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_email'])){
    header('Location: login.php');
    exit();
}

// Load users data
$users = json_decode(file_get_contents('users.json'), true);
$current_user = null;
$user_key = null;

// Find current user
foreach($users as $key => $user){
    if($user['email'] == $_SESSION['user_email']){
        $current_user = $user;
        $user_key = $key;
        break;
    }
}

// Load tasks from same directory
$tasks = [];
if(file_exists('tasks.json')){
    $tasks = json_decode(file_get_contents('tasks.json'), true);
    if(!is_array($tasks)){
        $tasks = [];
    }
}

// Load tasks performed tracking
$tasks_performed = [];
if(file_exists('tasks_performed.json')){
    $tasks_performed = json_decode(file_get_contents('tasks_performed.json'), true);
    if(!is_array($tasks_performed)){
        $tasks_performed = [];
    }
}

// Filter out tasks that user has already performed
$user_email = $_SESSION['user_email'];
$completed_task_ids = [];
foreach($tasks_performed as $performed){
    if($performed['user_email'] == $user_email){
        $completed_task_ids[] = $performed['task_id'];
    }
}

// Remove completed tasks from available tasks
$available_tasks = [];
foreach($tasks as $task){
    if(!in_array($task['id'], $completed_task_ids)){
        $available_tasks[] = $task;
    }
}
$tasks = $available_tasks;

// Load admin settings for bank details
$admin_settings = [];
if(file_exists('../admin_settings.json')){
    $admin_settings = json_decode(file_get_contents('../admin_settings.json'), true);
}

// Conversion rate: 1 EUR = 1800 NGN
define('EUR_TO_NGN', 1800);

// Premium fee in NGN
$PREMIUM_FEE_NGN = 14000;
$PREMIUM_FEE_EUR = $PREMIUM_FEE_NGN / EUR_TO_NGN; // 7.78 EUR

// Update user data if needed
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_balance'])){
    $users[$user_key]['balance'] = $current_user['balance'] + floatval($_POST['amount']);
    file_put_contents('users.json', json_encode($users));
    $current_user['balance'] = $users[$user_key]['balance'];
    $_SESSION['user_balance'] = $current_user['balance'];
    $_SESSION['notify'] = [
        'status' => 'success',
        'title' => 'Earnings Updated!',
        'message' => 'You earned ₦'.number_format(floatval($_POST['amount']) * EUR_TO_NGN, 2).' successfully!'
    ];
}

// Handle withdrawal - ALWAYS closed
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['withdraw'])){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Withdrawal Portal Closed',
        'message' => 'Withdrawal portal is currently closed. Please keep earning! It will open soon.'
    ];
    header('Location: dashboard.php');
    exit();
}

// Handle upgrade
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upgrade'])){
    if($current_user['balance'] >= $PREMIUM_FEE_EUR){
        $users[$user_key]['balance'] = $current_user['balance'] - $PREMIUM_FEE_EUR;
        $users[$user_key]['upgraded'] = 'yes';
        file_put_contents('users.json', json_encode($users));
        $current_user = $users[$user_key];
        $_SESSION['user_balance'] = $current_user['balance'];
        $_SESSION['user_upgraded'] = 'yes';
        $_SESSION['notify'] = [
            'status' => 'success',
            'title' => 'Upgrade Successful! 🎉',
            'message' => 'Welcome to Glamour Premium! You now earn maximum rewards.'
        ];
    } else {
        $_SESSION['notify'] = [
            'status' => 'error',
            'title' => 'Upgrade Failed',
            'message' => 'Insufficient balance. Need ₦' . number_format($PREMIUM_FEE_NGN, 0) . ' (≈ €' . number_format($PREMIUM_FEE_EUR, 2) . ') for Premium upgrade.'
        ];
    }
    header('Location: dashboard.php');
    exit();
}

// Handle notification display
$notification = null;
if(isset($_SESSION['notify'])){
    $notification = $_SESSION['notify'];
    unset($_SESSION['notify']);
}

$is_upgraded = ($current_user['upgraded'] == 'yes');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Glamour Dashboard | Premium Earnings</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   <link href="vitecss/fonts/fonts.css" rel="stylesheet">
  <!-- Font Awesome 6 (free icons) -->
  <link rel="stylesheet" href="vitecss/css/app.css">
   <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #0B0B0B 0%, #0F0F0F 100%);
            font-family: 'Inter', sans-serif,'DM sans';
            color: #FFFFFF;
            min-height: 100vh;
        }

        :root {
            --color-bg-primary: #0B0B0B;
            --color-bg-secondary: #121212;
            --color-dark-green-1: #0F2A1F;
            --color-dark-green-2: #12352A;
            --color-accent-cyan-1: #2FE6E6;
            --color-accent-cyan-2: #1ECAD3;
            --color-accent-cyan-3: #00CFC8;
            --color-neon-green-1: #00FFB2;
            --color-neon-green-2: #1EDFA3;
            --color-gold-1: #FFD54A;
            --color-gold-2: #FFC107;
            --color-text-primary: #FFFFFF;
            --color-text-secondary: #CFCFCF;
            --color-text-muted: #A8A8A8;
            --color-orange-1: #FFB347;
            --color-orange-2: #FF9E2C;
            --color-blue-1: #1A2A3A;
            --color-blue-2: #223F5A;
        }

        /* Mobile First Design */
        .mobile-container {
            max-width: 500px;
            margin: 0 auto;
            background: var(--color-bg-primary);
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 40px rgba(0,0,0,0.5);
        }

      header{
        padding:10px;
      }

        .welcome-text {
            font-size: 0.85rem;
            color: var(--color-text-muted);
            margin-bottom: 8px;
        }

        .user-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #fff, var(--color-neon-green-1));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .balance-card {
            background: rgba(255, 193, 7, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 20px;
            margin-top: 16px;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .balance-label {
            font-size: 0.75rem;
            color: var(--color-text-muted);
            margin-bottom: 8px;
        }

        .balance-amount {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--color-neon-green-1);
            line-height: 1;
        }

        .balance-amount small {
            font-size: 1rem;
            font-weight: 400;
        }

        .balance-euro {
            font-size: 0.9rem;
            color: var(--color-text-secondary);
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed rgba(255,255,255,0.2);
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 12px;
        }

        .badge-premium {
            background: linear-gradient(135deg, var(--color-gold-2), var(--color-orange-1));
            color: #0B0B0B;
        }

        .badge-basic {
            background: rgba(255,255,255,0.1);
            color: var(--color-text-secondary);
        }

        /* Upgrade Button inside Balance Card */
        .upgrade-now-btn {
            background: linear-gradient(135deg, var(--color-gold-2), var(--color-orange-1));
            border: none;
            padding: 10px 16px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.8rem;
            color: #0B0B0B;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            transition: all 0.3s ease;
            width: 100%;
            justify-content: center;
        }

        .upgrade-now-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.05);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            padding: 20px;
        }

        .stat-card {
            background: var(--color-bg-secondary);
            border-radius: 20px;
            padding: 16px 12px;
            text-align: center;
            border: 1px solid rgba(46, 230, 230, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:active {
            transform: scale(0.95);
        }

        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 4px 0;
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--color-text-muted);
        }

        /* Section Title */
        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            padding: 0 20px;
            margin: 24px 0 12px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Tasks Grid */
        .tasks-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 0 20px;
        }

        .task-card {
            background: linear-gradient(135deg, var(--color-bg-secondary), #0A0A0A);
            border-radius: 24px;
            padding: 20px;
            border: 1px solid rgba(46, 230, 230, 0.15);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .task-card:hover {
            transform: translateY(-2px);
            border-color: rgba(0, 255, 178, 0.3);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .task-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-neon-green-1);
        }

        .task-reward {
            background: rgba(0, 255, 178, 0.1);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--color-neon-green-1);
        }

        .task-description {
            color: var(--color-text-secondary);
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .task-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .task-deadline {
            font-size: 0.7rem;
            color: var(--color-text-muted);
        }

        .task-deadline i {
            margin-right: 4px;
        }

        .task-link {
            color: var(--color-accent-cyan-1);
            text-decoration: none;
            font-size: 0.8rem;
            word-break: break-all;
        }

        .task-link:hover {
            color: var(--color-neon-green-1);
        }

        .btn-task {
            background: linear-gradient(105deg, var(--color-neon-green-1), var(--color-neon-green-2));
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.8rem;
            color: #0B0B0B;
            cursor: pointer;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-task:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 255, 178, 0.3);
        }

        .btn-claim {
            background: linear-gradient(105deg, var(--color-gold-2), var(--color-orange-1));
            border: none;
            padding: 10px 20px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.8rem;
            color: #0B0B0B;
            cursor: pointer;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-claim:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }

        .empty-tasks {
            text-align: center;
            padding: 60px 20px;
            color: var(--color-text-muted);
        }

        .empty-tasks i {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        /* Earning Modules Grid */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            padding: 0 20px;
        }

        .module-card {
            background: linear-gradient(135deg, var(--color-bg-secondary), #0A0A0A);
            border-radius: 20px;
            padding: 16px;
            border: 1px solid rgba(46, 230, 230, 0.15);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .module-card:active {
            transform: scale(0.98);
        }

        .module-icon {
            font-size: 1.8rem;
            margin-bottom: 12px;
        }

        .module-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .module-earn {
            font-size: 0.8rem;
            color: var(--color-neon-green-1);
            font-weight: 600;
        }

        .module-desc {
            font-size: 0.7rem;
            color: var(--color-text-muted);
            margin-top: 8px;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 500px;
            margin: 0 auto;
            background: rgba(18, 18, 18, 0.95);
            backdrop-filter: blur(20px);
            display: flex;
            justify-content: space-around;
            padding: 12px 20px 20px;
            border-top: 1px solid rgba(46, 230, 230, 0.1);
            z-index: 100;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            color: var(--color-text-muted);
            text-decoration: none;
            font-size: 0.7rem;
            transition: all 0.2s;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 12px;
        }

        .nav-item i {
            font-size: 1.2rem;
        }

        .nav-item.active {
            color: gold;
            background: rgba(255, 193, 7, 0.05);
        }

        /* Content Sections */
        .content-section {
            display: none;
            padding-bottom: 80px;
            animation: fadeIn 0.3s ease;
        }

        .content-section.active-section {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Upgrade Card */
        .upgrade-card {
            background: linear-gradient(135deg, var(--color-gold-2), var(--color-orange-1));
            margin: 20px;
            padding: 24px;
            border-radius: 24px;
            color: #0B0B0B;
            text-align: center;
        }

        .upgrade-card h3 {
            font-size: 1.3rem;
            margin: 12px 0;
        }

        .upgrade-btn {
            background: #0B0B0B;
            color: var(--color-gold-1);
            border: none;
            padding: 12px 24px;
            border-radius: 40px;
            font-weight: 700;
            font-size: 0.9rem;
            margin-top: 16px;
            cursor: pointer;
            width: 100%;
        }

        .upgrade-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Form Styles */
        .form-group {
            padding: 0 20px;
            margin-bottom: 20px;
        }

        .form-input {
            width: 100%;
            padding: 14px;
            background: var(--color-bg-secondary);
            border: 1px solid rgba(46, 230, 230, 0.2);
            border-radius: 16px;
            color: white;
            font-size: 1rem;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--color-neon-green-1);
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--color-neon-green-1), var(--color-neon-green-2));
            border: none;
            border-radius: 16px;
            color: #0B0B0B;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
        }

        /* Notification Toast */
        .toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--color-bg-secondary);
            border-left: 4px solid;
            padding: 14px 20px;
            border-radius: 12px;
            z-index: 1000;
            max-width: 90%;
            width: 400px;
            animation: slideDown 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-100%);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Referral Link */
        .referral-link {
            background: var(--color-bg-primary);
            padding: 12px;
            border-radius: 12px;
            margin: 16px 20px;
            word-break: break-all;
            font-size: 0.8rem;
            border: 1px solid rgba(0,255,178,0.2);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: var(--color-bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--color-neon-green-1);
            border-radius: 4px;
        }
        
        /* Coming Soon Tooltip */
        .coming-soon-toast {
            position: fixed;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.9);
            color: var(--color-neon-green-1);
            padding: 12px 24px;
            border-radius: 40px;
            font-size: 0.9rem;
            font-weight: 600;
            z-index: 1000;
            animation: fadeUp 0.3s ease;
            box-shadow: 0 0 20px rgba(0,255,178,0.3);
            white-space: nowrap;
        }
        
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        /* Locked overlay for modules when not upgraded */
        .module-card.locked {
            opacity: 0.7;
            position: relative;
        }

        .module-card.locked::after {
            content: '\f023';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 10px;
            right: 10px;
            color: var(--color-gold-1);
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
<div class="mobile-container w-full">
    <!-- Header -->
    <div class="header p-20">
       <div class="w-full row align-center g-10 space-between">
         <div class="welcome-text">Welcome back,</div>
         <div class="row g-5">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M221.8,175.94C216.25,166.38,208,139.33,208,104a80,80,0,1,0-160,0c0,35.34-8.26,62.38-13.81,71.94A16,16,0,0,0,48,200H88.81a40,40,0,0,0,78.38,0H208a16,16,0,0,0,13.8-24.06ZM128,216a24,24,0,0,1-22.62-16h45.24A24,24,0,0,1,128,216Z"></path></svg>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M172,120a44,44,0,1,1-44-44A44.05,44.05,0,0,1,172,120Zm60,8A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88.09,88.09,0,0,0-91.47-87.93C77.43,41.89,39.87,81.12,40,128.25a87.65,87.65,0,0,0,22.24,58.16A79.71,79.71,0,0,1,84,165.1a4,4,0,0,1,4.83.32,59.83,59.83,0,0,0,78.28,0,4,4,0,0,1,4.83-.32,79.71,79.71,0,0,1,21.79,21.31A87.62,87.62,0,0,0,216,128Z"></path></svg>

         </div>
       </div>
        <div class="user-name"><?php echo htmlspecialchars($_SESSION['user_first_name']); ?>!</div>
        <div class="balance-card column g-10">
            <div class="balance-label">Total Balance</div>
            <div class="balance-amount">₦<?php echo number_format($current_user['balance'] * EUR_TO_NGN, 2); ?><small></small></div>
            <div class="balance-euro">≈ €<?php echo number_format($current_user['balance'], 2); ?> EUR</div>
            
            <div class="row align-center w-full g-10">
			<div onclick="redirectToUpgrade()" class="h-50 bg-gold c-black br-5 bold w-full row font-1 align-center justify-center">Activate</div>
            <div onclick="redirectToUpgrade()" style="background:rgba(255,255,255,0.1);color:white;" class="h-50 bg-gold font-1 br-5 bold w-full row align-center justify-center">Withdraw</div>
			</div>
        </div>
    </div>


<section class="p-x-20">
    <div style="background:rgba(255, 193, 7, 0.05);border:1px solid rgba(255, 193, 7, 0.3)" class=" g-10 w-full br-10 column g-10 g-10 p-20">
    <strong class="desc">Earn</strong>
    <div class="grid g-10 place-center grid-4 g-10">
        <div onclick="redirectToUpgrade()" class="column align-center g-5">
            <div style="background:rgba(255,255,255,0.1);color:gold;" class="h-50 br-5 perfect-square column g-10 align-center justify-center no-shrink">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M192,72V184a16,16,0,0,1-16,16H32a16,16,0,0,1-16-16V72A16,16,0,0,1,32,56H176A16,16,0,0,1,192,72Zm58,.25a8.23,8.23,0,0,0-6.63,1.22L209.78,95.86A4,4,0,0,0,208,99.19v57.62a4,4,0,0,0,1.78,3.33l33.78,22.52a8,8,0,0,0,8.58.19,8.33,8.33,0,0,0,3.86-7.17V80A8,8,0,0,0,250,72.25Z"></path></svg>

            </div>
            <span>Live</span>
        </div>
          <div onclick="redirectToUpgrade()" class="column align-center g-5">
            <div style="background:rgba(255,255,255,0.1);color:gold;" class="h-50 br-5 perfect-square column g-10 align-center justify-center no-shrink">
   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M128,24A104,104,0,0,0,36.18,176.88L24.83,210.93a16,16,0,0,0,20.24,20.24l34.05-11.35A104,104,0,1,0,128,24ZM84,140a12,12,0,1,1,12-12A12,12,0,0,1,84,140Zm44,0a12,12,0,1,1,12-12A12,12,0,0,1,128,140Zm44,0a12,12,0,1,1,12-12A12,12,0,0,1,172,140Z"></path></svg>

            </div>
            <span>Chat</span>
        </div>
          <div onclick="redirectToUpgrade()" class="column align-center g-5">
            <div style="background:rgba(255,255,255,0.1);color:gold;" class="h-50 br-5 perfect-square column g-10 align-center justify-center no-shrink">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M240,102c0,70-103.79,126.66-108.21,129a8,8,0,0,1-7.58,0c-3.35-1.8-63.55-34.69-92.68-80.89A4,4,0,0,1,34.92,144H72a8,8,0,0,0,6.66-3.56l9.34-14,25.34,38a8,8,0,0,0,9.16,3.16,8.23,8.23,0,0,0,4.28-3.34L140.28,144H160a8,8,0,0,0,8-8.53,8.18,8.18,0,0,0-8.25-7.47H136a8,8,0,0,0-6.66,3.56l-9.34,14-25.34-38a8,8,0,0,0-9.17-3.16,8.25,8.25,0,0,0-4.27,3.34L67.72,128H23.53a4,4,0,0,1-3.83-2.81A76.93,76.93,0,0,1,16,102,62.07,62.07,0,0,1,78,40c20.65,0,38.73,8.88,50,23.89C139.27,48.88,157.35,40,178,40A62.07,62.07,0,0,1,240,102Z"></path></svg>

            </div>
            <span>Fitness</span>
        </div>
          <div onclick="redirectToUpgrade()" class="column align-center g-5">
            <div style="background:rgba(255,255,255,0.1);color:gold;" class="h-50 br-5 perfect-square column g-10 align-center justify-center no-shrink">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M160,129.89,175.06,160H144.94l6.36-12.7v0ZM224,48V208a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32H208A16,16,0,0,1,224,48ZM207.16,188.42l-40-80a8,8,0,0,0-14.32,0L139.66,134.8a62.31,62.31,0,0,1-23.61-10A79.61,79.61,0,0,0,135.6,80H152a8,8,0,0,0,0-16H112V56a8,8,0,0,0-16,0v8H56a8,8,0,0,0,0,16h63.48a63.73,63.73,0,0,1-15.3,34.05,65.93,65.93,0,0,1-9-13.61,8,8,0,0,0-14.32,7.12,81.75,81.75,0,0,0,11.4,17.15A63.62,63.62,0,0,1,56,136a8,8,0,0,0,0,16,79.56,79.56,0,0,0,48.11-16.13,78.33,78.33,0,0,0,28.18,13.66l-19.45,38.89a8,8,0,0,0,14.32,7.16L136.94,176h46.12l9.78,19.58a8,8,0,1,0,14.32-7.16Z"></path></svg>

            </div>
            <span>Lingua</span>
        </div>
    </div>
</div>
</section>

<section class="p-x-20 m-top-20">
    <div style="background:rgba(255, 193, 7, 0.05);border:1px solid rgba(255, 193, 7, 0.3)" class=" g-10 w-full br-10 column g-10 g-10 p-20">
    <strong class="desc">More Ways toEarn</strong>
    <div class="grid g-10 place-center grid-4 g-10">
        <div onclick="redirectToUpgrade()" class="column align-center g-5">
            <div style="background:rgba(255,255,255,0.1);color:gold;" class="h-50 br-5 perfect-square column g-10 align-center justify-center no-shrink">
   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm40.55,110.58-52,36A8,8,0,0,1,104,164V92a8,8,0,0,1,12.55-6.58l52,36a8,8,0,0,1,0,13.16Z"></path></svg>

            </div>
            <span>Watch</span>
        </div>
          <div onclick="redirectToUpgrade()" class="column align-center g-5">
            <div style="background:rgba(255,255,255,0.1);color:gold;" class="h-50 br-5 perfect-square column g-10 align-center justify-center no-shrink">
 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M224,104v50.93c0,46.2-36.85,84.55-83,85.06A83.71,83.71,0,0,1,80.6,215.4C58.79,192.33,34.15,136,34.15,136a16,16,0,0,1,6.53-22.23c7.66-4,17.1-.84,21.4,6.62l21,36.44a6.09,6.09,0,0,0,6,3.09l.12,0A8.19,8.19,0,0,0,96,151.74V32a16,16,0,0,1,16.77-16c8.61.4,15.23,7.82,15.23,16.43V104a8,8,0,0,0,8.53,8,8.17,8.17,0,0,0,7.47-8.25V88a16,16,0,0,1,16.77-16c8.61.4,15.23,7.82,15.23,16.43V112a8,8,0,0,0,8.53,8,8.17,8.17,0,0,0,7.47-8.25v-7.28c0-8.61,6.62-16,15.23-16.43A16,16,0,0,1,224,104Z"></path></svg>

            </div>
            <span>Tap</span>
        </div>
          <div onclick="redirectToUpgrade()" class="column align-center g-5">
            <div style="background:rgba(255,255,255,0.1);color:gold;" class="h-50 br-5 perfect-square column g-10 align-center justify-center no-shrink">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M247.93,124.52C246.11,77.54,207.07,40,160.06,40A88.1,88.1,0,0,0,81.29,88.67h0A87.48,87.48,0,0,0,72,127.73,8.18,8.18,0,0,1,64.57,136,8,8,0,0,1,56,128a103.66,103.66,0,0,1,5.34-32.92,4,4,0,0,0-4.75-5.18A64.09,64.09,0,0,0,8,152c0,35.19,29.75,64,65,64H160A88.09,88.09,0,0,0,247.93,124.52Zm-50.27,9.14a8,8,0,0,1-11.32,0L168,115.31V176a8,8,0,0,1-16,0V115.31l-18.34,18.35a8,8,0,0,1-11.32-11.32l32-32a8,8,0,0,1,11.32,0l32,32A8,8,0,0,1,197.66,133.66Z"></path></svg>

            </div>
            <span>Upload</span>
        </div>
          <div onclick="redirectToUpgrade()" class="column align-center g-5">
            <div style="background:rgba(255,255,255,0.1);color:gold;" class="h-50 br-5 perfect-square column g-10 align-center justify-center no-shrink">
 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M215,219.85a8,8,0,0,1-7,4.15H160a8,8,0,0,1-6.75-3.71l-40.49-63.63L53.92,221.38a8,8,0,0,1-11.84-10.76l61.77-68L41.25,44.3A8,8,0,0,1,48,32H96a8,8,0,0,1,6.75,3.71l40.49,63.63,58.84-64.72a8,8,0,0,1,11.84,10.76l-61.77,67.95,62.6,98.38A8,8,0,0,1,215,219.85Z"></path></svg>

            </div>
            <span>Social</span>
        </div>
    </div>
</div>
</section>
   

  

   

    <!-- Withdraw Section -->
    <div id="withdrawSection" class="content-section">
        <div class="section-title">💸 Withdraw Funds</div>
        
        <?php if(isset($admin_settings['bank']) && !empty($admin_settings['bank']['account_number'])): ?>
        <div class="stats-grid" style="grid-template-columns: 1fr; margin-bottom: 20px;">
            <div class="stat-card" style="text-align: left;">
                <div><strong>Bank:</strong> <?php echo htmlspecialchars($admin_settings['bank']['bank_name']); ?></div>
                <div style="margin-top: 8px;"><strong>Account:</strong> <?php echo htmlspecialchars($admin_settings['bank']['account_number']); ?></div>
                <div style="margin-top: 8px;"><strong>Name:</strong> <?php echo htmlspecialchars($admin_settings['bank']['account_name']); ?></div>
            </div>
        </div>
        <?php endif; ?>
        
        <form method="POST" onsubmit="handleWithdraw(event)">
            <div class="form-group">
                <input type="number" name="withdraw_amount" placeholder="Enter amount in EUR (min €10)" step="0.01" class="form-input">
                <small style="color: var(--color-text-muted); display: block; margin-top: 5px;">≈ ₦<?php echo number_format(10 * EUR_TO_NGN, 0); ?> minimum</small>
            </div>
            <div class="form-group">
                <button type="submit" name="withdraw" class="btn-primary">Withdraw Now</button>
            </div>
        </form>
        <div class="stats-grid" style="grid-template-columns: 1fr; margin-top: 20px;">
            <div class="stat-card">
                <div class="stat-value">24 hours</div>
                <div class="stat-label">Processing Time</div>
            </div>
        </div>
    </div>

    <!-- Referrals Section -->
    <div id="referralsSection" class="content-section">
        <div class="section-title">👥 Refer & Earn</div>
        <?php
// Get current URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$current_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$referral_code = md5($_SESSION['user_email']);
$full_referral_url = rtrim($current_url, '/') . "/register.php?ref=" . $referral_code;
?>
<div class="referral-link" id="referralLink">
    <?php echo $full_referral_url; ?>
</div>
        <div class="form-group">
            <button class="btn-primary" onclick="copyReferral()">📋 Copy Referral Link</button>
        </div>
        <div class="stats-grid" style="grid-template-columns: 1fr;">
            <div class="stat-card">
                <div class="stat-value">₤12,000 (₦<?php echo number_format(6 * EUR_TO_NGN, 0); ?>)</div>
                <div class="stat-label">Direct Referral Bonus</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">₤400</div>
                <div class="stat-label">1st Level Commission</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">₤100</div>
                <div class="stat-label">2nd Level Commission</div>
            </div>
        </div>
    </div>

    <!-- Profile Section -->
    <div id="profileSection" class="content-section">
        <div class="section-title">👤 My Profile</div>
        <div class="stats-grid" style="grid-template-columns: 1fr;">
            <div class="stat-card" style="text-align: left;">
                <div><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name']); ?></div>
                <div style="margin-top: 12px;"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></div>
                <div style="margin-top: 12px;"><strong>Phone:</strong> <?php echo htmlspecialchars($_SESSION['user_phone']); ?></div>
                <div style="margin-top: 12px;"><strong>Status:</strong> <?php echo $current_user['upgraded'] == 'yes' ? 'Premium Member' : 'Basic Member'; ?></div>
                <div style="margin-top: 12px;"><strong>Member since:</strong> <?php echo date('F j, Y'); ?></div>
            </div>
        </div>
        <div class="form-group">
            <a href="logout.php" class="btn-primary" style="display: block; text-align: center; text-decoration: none;">🚪 Logout</a>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div onclick="document.querySelectorAll('.nav-item').forEach((data)=>{data.classList.remove('active')});this.classList.add('active')" class="nav-item active" data-section="dashboard">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M240,208H224V136l2.34,2.34A8,8,0,0,0,237.66,127L139.31,28.68a16,16,0,0,0-22.62,0L18.34,127a8,8,0,0,0,11.32,11.31L32,136v72H16a8,8,0,0,0,0,16H240a8,8,0,0,0,0-16Zm-88,0H104V160a4,4,0,0,1,4-4h40a4,4,0,0,1,4,4Z"></path></svg>

           
        </div>
        <div onclick="document.querySelectorAll('.nav-item').forEach((data)=>{data.classList.remove('active')});this.classList.add('active');redirectToUpgrade()" onclick="this.classList.add('active')" class="nav-item" data-section="earn" onclick="handleEarnClick(event)">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M128,24C74.17,24,32,48.6,32,80v96c0,31.4,42.17,56,96,56s96-24.6,96-56V80C224,48.6,181.83,24,128,24Zm80,104c0,9.62-7.88,19.43-21.61,26.92C170.93,163.35,150.19,168,128,168s-42.93-4.65-58.39-13.08C55.88,147.43,48,137.62,48,128V111.36c17.06,15,46.23,24.64,80,24.64s62.94-9.68,80-24.64Zm-21.61,74.92C170.93,211.35,150.19,216,128,216s-42.93-4.65-58.39-13.08C55.88,195.43,48,185.62,48,176V159.36c17.06,15,46.23,24.64,80,24.64s62.94-9.68,80-24.64V176C208,185.62,200.12,195.43,186.39,202.92Z"></path></svg>

          
        </div>
        <div onclick="document.querySelectorAll('.nav-item').forEach((data)=>{data.classList.remove('active')});this.classList.add('active');redirectToUpgrade()" class="nav-item" data-section="withdraw">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M216,64H56a8,8,0,0,1,0-16H192a8,8,0,0,0,0-16H56A24,24,0,0,0,32,56V184a24,24,0,0,0,24,24H216a16,16,0,0,0,16-16V80A16,16,0,0,0,216,64Zm-36,80a12,12,0,1,1,12-12A12,12,0,0,1,180,144Z"></path></svg>

           
        </div>
        <div onclick="document.querySelectorAll('.nav-item').forEach((data)=>{data.classList.remove('active')});this.classList.add('active');redirectToUpgrade()" class="nav-item" data-section="referrals">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="CurrentColor" height="20" width="20"><path d="M230.93,220a8,8,0,0,1-6.93,4H32a8,8,0,0,1-6.92-12c15.23-26.33,38.7-45.21,66.09-54.16a72,72,0,1,1,73.66,0c27.39,8.95,50.86,27.83,66.09,54.16A8,8,0,0,1,230.93,220Z"></path></svg>

           
        </div>
        
    </div>
</div>

<script>
    var isUpgraded = <?php echo $is_upgraded ? 'true' : 'false'; ?>;
    
    // Redirect to upgrade page if not upgraded
    function redirectToUpgrade() {
        window.location.href = 'upgrade.php';
    }
    
   
   
    
 
  

  
 

 
</script>
</body>
</html>