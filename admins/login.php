<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Glamour — Sign In | Turn Content Into Cash</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-color: var(--color-bg-primary);
      font-family: 'Inter', sans-serif;
      color: var(--color-text-primary);
      line-height: 1.5;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    /* ========== ROOT VARIABLES ========== */
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
      --color-yellow-glow-1: #FFF3A3;
      --color-text-primary: #FFFFFF;
      --color-text-secondary: #CFCFCF;
      --color-text-muted: #A8A8A8;
      --color-orange-1: #FFB347;
      --color-blue-1: #1A2A3A;
      --color-blue-2: #223F5A;
    }

    /* animated background glow */
    .glow-bg {
      position: fixed;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 0;
      background: radial-gradient(circle at 20% 30%, rgba(0, 255, 178, 0.05), transparent 60%),
                  radial-gradient(circle at 80% 70%, rgba(46, 230, 230, 0.04), transparent 50%);
    }

    .login-container {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 480px;
      margin: 40px 20px;
    }

    /* card style */
    .login-card {
      background: rgba(18, 18, 24, 0.85);
      backdrop-filter: blur(12px);
      border-radius: 10px;
      padding: 48px 40px;
      border: 1px solid rgba(46, 230, 230, 0.25);
      box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(0, 255, 178, 0.1) inset;
      transition: transform 0.3s ease;
    }

    .login-card:hover {
      border-color: rgba(0, 255, 178, 0.4);
      box-shadow: 0 30px 50px -15px rgba(0, 255, 178, 0.15);
    }

    /* logo area */
    .logo {
      text-align: center;
      margin-bottom: 32px;
    }
    .logo h1 {
      font-size: 2.4rem;
      font-weight: 800;
      background: linear-gradient(135deg, #fff, var(--color-gold-1));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      letter-spacing: -0.02em;
    }
    .logo span {
      color: var(--color-accent-cyan-1);
      background: none;
    }
    .logo p {
      color: var(--color-text-muted);
      font-size: 0.9rem;
      margin-top: 8px;
    }

    /* form group */
    .input-group {
      margin-bottom: 24px;
      position: relative;
    }
    .input-group i {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--color-text-muted);
      font-size: 1.2rem;
      transition: color 0.2s;
      pointer-events: none;
    }
    .input-group input {
      width: 100%;
      background: rgba(10, 10, 15, 0.7);
      border: 1.5px solid rgba(46, 230, 230, 0.2);
      border-radius: 5px;
      padding: 16px 20px 16px 50px;
      font-size: 1rem;
      color: var(--color-text-primary);
      font-family: 'Inter', sans-serif;
      transition: all 0.25s ease;
      outline: none;
    }
    .input-group input:focus {
      border-color: var(--color-neon-green-1);
      box-shadow: 0 0 0 3px rgba(0, 255, 178, 0.2);
      background: rgba(18, 18, 28, 0.9);
    }
    .input-group input::placeholder {
      color: var(--color-text-muted);
      opacity: 0.6;
    }

    /* options row */
    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 28px;
      font-size: 0.85rem;
    }
    .checkbox {
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--color-text-secondary);
      cursor: pointer;
    }
    .checkbox input {
      width: 18px;
      height: 18px;
      accent-color: var(--color-neon-green-1);
      cursor: pointer;
    }
    .forgot-link {
      color: var(--color-accent-cyan-1);
      text-decoration: none;
      font-weight: 500;
      transition: 0.2s;
    }
    .forgot-link:hover {
      color: var(--color-neon-green-1);
      text-shadow: 0 0 5px rgba(0,255,178,0.5);
    }

    /* buttons */
    .btn-login {
      width: 100%;
      background: linear-gradient(105deg, var(--color-neon-green-1), var(--color-neon-green-2));
      border: none;
      padding: 16px;
      border-radius: 5px;
      font-weight: 700;
      font-size: 0.8rem;
      color: #0B0B0B;
      cursor: pointer;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      font-family: 'Inter', sans-serif;
      margin-bottom: 20px;
      box-shadow: 0 6px 14px rgba(0, 255, 178, 0.2);
    }
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 22px -8px rgba(0, 255, 178, 0.4);
      filter: brightness(1.02);
    }

    /* divider */
    .divider {
      display: flex;
      align-items: center;
      text-align: center;
      color: var(--color-text-muted);
      font-size: 0.8rem;
      margin: 24px 0;
    }
    .divider::before, .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid rgba(46, 230, 230, 0.2);
    }
    .divider span {
      margin: 0 12px;
    }

    /* social buttons */
    .social-login {
      display: flex;
      gap: 16px;
      justify-content: center;
    }
    .social-btn {
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(46, 230, 230, 0.25);
      border-radius: 60px;
      padding: 12px 20px;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      color: var(--color-text-secondary);
      font-weight: 500;
      text-decoration: none;
      transition: 0.2s;
      cursor: pointer;
    }
    .social-btn:hover {
      background: rgba(0, 255, 178, 0.08);
      border-color: var(--color-accent-cyan-1);
      color: var(--color-neon-green-1);
      transform: translateY(-2px);
    }

    /* signup link */
    .signup-prompt {
      text-align: center;
      margin-top: 32px;
      color: var(--color-text-muted);
      font-size: 0.9rem;
    }
    .signup-prompt a {
      color: var(--color-gold-1);
      text-decoration: none;
      font-weight: 600;
      margin-left: 6px;
      transition: 0.2s;
    }
    .signup-prompt a:hover {
      color: var(--color-neon-green-1);
      text-decoration: underline;
    }

    /* ========== BEAUTIFUL NOTIFICATION ========== */
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
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(0, 255, 178, 0.2);
      display: flex;
      align-items: center;
      gap: 14px;
      transform: translateX(120%);
      transition: transform 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
      z-index: 1000;
      font-family: 'Inter', sans-serif;
    }
    .notification.show {
      transform: translateX(0);
    }
    .notification-icon {
      font-size: 1.8rem;
      color: var(--color-neon-green-1);
    }
    .notification-content {
      flex: 1;
    }
    .notification-title {
      font-weight: 700;
      font-size: 1rem;
      margin-bottom: 4px;
      color: var(--color-text-primary);
    }
    .notification-message {
      font-size: 0.85rem;
      color: var(--color-text-secondary);
    }
    .notification-close {
      background: none;
      border: none;
      color: var(--color-text-muted);
      font-size: 1.2rem;
      cursor: pointer;
      transition: 0.2s;
      padding: 4px;
    }
    .notification-close:hover {
      color: var(--color-neon-green-1);
    }
    /* notification types */
    .notification.success {
      border-left-color: var(--color-neon-green-1);
    }
    .notification.error {
      border-left-color: #FF4D4D;
    }
    .notification.error .notification-icon {
      color: #FF6B6B;
    }
    .notification.info {
      border-left-color: var(--color-accent-cyan-1);
    }

    /* loading state */
    .btn-login.loading {
      pointer-events: none;
      opacity: 0.8;
    }
    .btn-login.loading i.fa-spinner {
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* responsive */
    @media (max-width: 520px) {
      .login-card {
        padding: 32px 24px;
      }
      .notification {
        left: 16px;
        right: 16px;
        max-width: none;
        top: 16px;
      }
    }
  </style>
</head>
<body>
<div class="glow-bg"></div>

<div class="login-container">
  <div class="login-card">
    <div class="logo">
      <h1>GLAMOUR<span>.</span></h1>
      <p>ADMIN LOGIN</p>
    </div>

    <form method="POST" action="login_process.php" id="loginForm">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input name="tag" type="text" id="emailInput" placeholder="Email address" required autocomplete="email">
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input name="password" type="password" id="passwordInput" placeholder="Password" required autocomplete="current-password">
      </div>

      

      <button type="submit" class="btn-login" id="loginBtn">
        <i class="fas fa-arrow-right-to-bracket"></i> Sign In
      </button>
    </form>

    

    

    
  </div>
</div>

<!-- Beautiful Notification Component -->
<div id="beautyNotify" class="notification">
  <div class="notification-icon"><i class="fas fa-check-circle"></i></div>
  <div class="notification-content">
    <div class="notification-title">Welcome back, Creator</div>
    <div class="notification-message">You're now signed in. Ready to earn?</div>
  </div>
  <button class="notification-close"><i class="fas fa-times"></i></button>
</div>


<?php
if(isset($_SESSION['notify'])){
	$notify=$_SESSION['notify'];
	echo '<div id="beautyNotify" class="notification '.$notify['status'].' show">
  <div class="notification-icon"><i class="fas fa-check-circle"></i></div>
  <div class="notification-content">
    <div class="notification-title">'.$notify['title'].'</div>
    <div class="notification-message">'.$notify['message'].'</div>
  </div>
  <button onclick="this.parentNode.remove()" class="notification-close"><i class="fas fa-times"></i></button>
</div>';
unset($_SESSION['notify']);
}


?>


</body>
</html>