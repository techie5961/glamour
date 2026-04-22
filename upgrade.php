<?php
// payment.php - Glamour Secure Payment Page
// Single account details display
$admin_settings = [];
if(file_exists('admin_settings.json')){
    $admin_settings = json_decode(file_get_contents('admin_settings.json'), true);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Glamour Secure Payment</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="vitecss/fonts/fonts.css" rel="stylesheet">
  <!-- Font Awesome 6 (free icons) -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
 
  <link rel="stylesheet" href="vitecss/css/app.css">  
    <style>
        :root {
            /* === GLAMOUR BRAND PALETTE === */
            --bg-deep: #050505;             /* Deepest Black */
            --bg-gradient: radial-gradient(circle at 50% 40%, #1e1d14 0%, #050505 100%);
            --accent-gold: #e3c04d;         /* Glamour Logo Gold */
            --accent-cyan: #32e0e9;         /* FaceTime Cyan */
            --accent-green: #00ffaa;        /* Button/Success Green */
            --dark-panel: #051410;          /* Deep Green Panel Background */
            
            --text-main: #ffffff;
            --text-muted: #888888;
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 15px 40px rgba(0, 0, 0, 0.7);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            background: var(--bg-deep);
            background: var(--bg-gradient);
            background-attachment: fixed;
            font-family: 'DM sans', sans-serif;
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 2rem 1rem;
            overflow-y: auto;
        }

        .container {
            width: 100%;
            max-width: 420px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem; 
            padding-bottom: 50px;
        }

        /* Top Label (Matches Gold Theme) */
        .info-box.gold { 
            background-color: var(--accent-gold); 
            color: #000; 
            padding: 1.2rem;
            border-radius: 10px;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 800;
            box-shadow: var(--glass-shadow);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Main Glass Card (Matches Earning Panel) */
        .main-payment-card {
            background: var(--dark-panel);
            border: 1px solid rgba(0, 255, 170, 0.15);
            border-radius: 10px;
            padding: 1.8rem;
            box-shadow: var(--glass-shadow);
        }

        .payment-portal-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .payment-portal-section h2 {
            font-size: 0.9rem;
            color: var(--accent-gold);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        #countdown-timer {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--accent-cyan);
            letter-spacing: 2px;
            line-height: 1;
        }

        /* Single Account Box with Dashed Border (Flyer Aesthetic) */
        .account-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1.5px dashed var(--accent-cyan);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.2rem;
            text-align: center;
        }

        .merchant-title { 
            font-size: 0.7rem; 
            color: var(--text-muted); 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin-bottom: 5px; 
        }
        
        .bank-name { 
            font-size: 1rem; 
            font-weight: 700; 
            color: var(--text-main); 
        }
        
        .account-number-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 12px 0;
            padding: 8px 15px;
            background: rgba(50, 224, 233, 0.1);
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .account-number-wrapper:hover { 
            background: rgba(50, 224, 233, 0.2); 
        }

        .account-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1px;
        }

        .copy-icon svg { 
            width: 18px; 
            fill: var(--accent-cyan); 
        }

        .verify-button {
            display: block;
            width: 100%;
            padding: 1.3rem;
            background: transparent;
            color: var(--accent-green);
            border: 2px solid var(--accent-green);
            border-radius: 5px;
            font-weight: 800;
            font-size: 0.9rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: 0.3s;
            text-decoration: none;
            cursor: pointer;
        }
        
        .verify-button:hover {
            background: var(--accent-green);
            color: #000;
            box-shadow: 0 0 20px rgba(0, 255, 170, 0.3);
            transform: translateY(-3px);
        }

        /* Social Proof (Gold Notification) */
        #socialProofNotification {
            position: fixed;
            top: 25px; 
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 380px;
            background: var(--accent-gold);
            color: #000;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 700;
            z-index: 2000;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        #socialProofNotification.show { 
            opacity: 1; 
            top: 35px; 
        }

        .social-name { 
            text-transform: uppercase; 
        }

        /* Warning Modal Styling */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(12px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 3000;
            padding: 20px;
        }
        
        .modal-overlay.active { 
            display: flex; 
        }

        .modal-content {
            background: var(--dark-panel);
            border: 1px solid var(--accent-gold);
            border-radius: 10px;
            padding:20px;
            max-width: 400px;
            width:80%;
            text-align: center;
            box-shadow: var(--glass-shadow);
            position: relative;
        }

        .modal-content h3 { 
            color: var(--accent-gold); 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            margin-bottom: 15px; 
        }
        
        .modal-content p { 
            color: #fff; 
            font-size: 0.9rem; 
            margin-bottom: 20px; 
            line-height: 1.6; 
        }
        
        .modal-content strong { 
            color: var(--accent-cyan); 
        }

        .modal-close-btn {
            position: absolute; 
            top: 15px; 
            right: 20px;
            font-size: 24px; 
            cursor: pointer; 
            color: var(--text-muted);
        }
        
        .modal-close-btn:hover {
            color: var(--accent-gold);
        }

        /* Copy Notification */
        #copy-notification {
            position: fixed; 
            bottom: 80px; 
            left: 50%; 
            transform: translateX(-50%);
            background: var(--accent-cyan); 
            color: #000; 
            padding: 10px 20px;
            border-radius: 10px; 
            font-size: 0.8rem; 
            font-weight: 700;
            opacity: 0; 
            transition: 0.3s; 
            z-index: 1500;
            white-space: nowrap;
        }
        
        #copy-notification.show { 
            opacity: 1; 
        }

        /* Responsive */
        @media (max-width: 480px) {
            .account-number {
                font-size: 1.2rem;
            }
            .container {
                padding-bottom: 30px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Top Label -->
        <div class="info-box gold">
            TRANSFER EXACTLY ₦14,000 TO ACTIVATE YOUR GLAMOUR ACCOUNT.
        </div>
        
        <div class="main-payment-card">
            <div class="payment-portal-section">
                <h2>GLAMOUR GATEWAY</h2>
                <div id="countdown-timer">8:45</div>
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:8px; text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fa-regular fa-clock"></i> Awaiting transfer...
                </p>
            </div>

            <!-- SINGLE ACCOUNT DETAILS DIV -->
            <div class="account-box">
                <p class="merchant-title">OFFICIAL GLAMOUR ACCOUNT</p>
                <p class="bank-name"><?= $admin_settings['bank']['bank_name'] ?></p>
                <div class="account-number-wrapper" data-copy-text="<?= $admin_settings['bank']['account_number'] ?>">
                    <span class="account-number"><?= $admin_settings['bank']['account_number'] ?></span>
                    <span class="copy-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18">
                            <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z" fill="#32e0e9"/>
                        </svg>
                    </span>
                </div>
                <p style="font-size:0.8rem; font-weight:600; opacity: 0.9; margin-top: 5px;">Account Name: <?= $admin_settings['bank']['account_name'] ?></p>
                <p style="font-size:0.7rem; color: var(--accent-gold); margin-top: 10px;">
                    <i class="fa-regular fa-circle-check"></i> Use this account for all Glamour payments
                </p>
            </div>
        </div>
        
        <!-- Action Button -->
        <a target="_blank" href="<?= $admin_settings['social']['telegram'] ?>" class="verify-button" id="verify-payment-button">
            I HAVE MADE THE TRANSFER 
        </a>
    </div>

    <!-- Warning Modal -->
    <div id="warningModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close-btn" id="closeModalX">×</span>
            <h3>⚠️ GLAMOUR NOTICE</h3>
            <p><strong>PLEASE AVOID OPAY</strong>. Transfers from OPay may cause delays in your account activation.</p>
            <p>Use <strong>PalmPay, Moniepoint, POS</strong>, or Bank Apps for instant activation of your membership.</p>
            <button id="dismissModalBtn" class="verify-button" style="padding: 12px; background:var(--accent-green); color:#000; margin-top: 10px;">
                I Understand
            </button>
        </div>
    </div>

    <!-- Notifications -->
    <div id="socialProofNotification"></div>
    <div id="copy-notification">Account Copied!</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- Modal Logic ---
            const modal = document.getElementById('warningModal');
            const closeX = document.getElementById('closeModalX');
            const dismissBtn = document.getElementById('dismissModalBtn');

            // Show modal on load
            modal.classList.add('active'); 
            document.body.style.overflow = 'hidden';

            const closeAll = () => {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            };

            closeX.onclick = closeAll;
            dismissBtn.onclick = closeAll;

            // --- Copy Logic (Single Account) ---
            const copyWrapper = document.querySelector('.account-number-wrapper');
            const copyNotify = document.getElementById('copy-notification');

            if (copyWrapper) {
                copyWrapper.addEventListener('click', (e) => {
                    const textToCopy = copyWrapper.dataset.copyText;
                    if (textToCopy) {
                        navigator.clipboard.writeText(textToCopy).then(() => {
                            copyNotify.classList.add('show');
                            setTimeout(() => copyNotify.classList.remove('show'), 2000);
                        }).catch(() => {
                            // Fallback for older browsers
                            const textArea = document.createElement('textarea');
                            textArea.value = textToCopy;
                            document.body.appendChild(textArea);
                            textArea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textArea);
                            copyNotify.classList.add('show');
                            setTimeout(() => copyNotify.classList.remove('show'), 2000);
                        });
                    }
                });
            }

            // --- Countdown Timer (10 minutes = 600 seconds) ---
            let time = 600;
            const timerEl = document.getElementById('countdown-timer');
            
            const updateTimer = () => {
                if (time <= 0) {
                    timerEl.textContent = "0:00";
                    return;
                }
                time--;
                let m = Math.floor(time / 60);
                let s = time % 60;
                timerEl.textContent = `${m}:${s < 10 ? '0' + s : s}`;
            };
            
            setInterval(updateTimer, 1000);

            // --- Social Proof Notification ---
            const socialEl = document.getElementById('socialProofNotification');
            const users = ["Blessing", "Aisha", "Tobi", "Emeka", "Dera", "Tunde", "Chinedu", "Amaka"];
            const cities = ["Lagos", "Abuja", "Port Harcourt", "Douala", "Ibadan", "Enugu", "Kano"];

            function showProof() {
                const user = users[Math.floor(Math.random() * users.length)];
                const city = cities[Math.floor(Math.random() * cities.length)];
                socialEl.innerHTML = `<span class="social-name">${user}</span> from ${city} just activated +€3.00 ▲`;
                socialEl.classList.add('show');
                
                setTimeout(() => {
                    socialEl.classList.remove('show');
                }, 4000);
                
                setTimeout(showProof, 8000 + Math.random() * 5000);
            }
            
            setTimeout(showProof, 3000);
        });
    </script>

    <!-- Font Awesome for icons (optional fallback) -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
</body>
</html>