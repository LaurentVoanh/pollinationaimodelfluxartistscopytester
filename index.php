<?php
/**
 * ArtBase Generator - Artist Comparison Version (index1.php)
 */

session_start();
$password = "laurent"; 

// --- AUTHENTICATION ---
if (isset($_POST['login_pass'])) {
    if ($_POST['login_pass'] === $password) {
        $_SESSION['logged_in'] = true;
    } else {
        $error = "Incorrect password";
    }
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - ArtBase</title>
    <style>
        body { background-color: #0F0F0F; color: #F2F2F2; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: #1A1A1A; padding: 30px; border-radius: 8px; border: 1px solid #333; text-align: center; }
        input { background: #2A2A2A; border: 1px solid #444; color: white; padding: 10px; border-radius: 6px; margin-bottom: 15px; width: 200px; }
        button { padding: 10px 20px; border-radius: 6px; border: none; font-weight: bold; cursor: pointer; background: #F2F2F2; color: #0F0F0F; }
    </style>
</head>
<body>
    <div class="login-card">
        <h3>ArtBase Engine V1</h3>
        <form method="POST">
            <input type="password" name="login_pass" placeholder="Password" required><br>
            <button type="submit">Enter</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </div>
</body>
</html>
<?php exit; endif; ?>

<?php
// --- CONFIGURATION ---
$apiKey = 'sk_EgkOCp3y9mfHMaCXZ6SwrKPnCicUqpED';
$baseUrl = "https://gen.pollinations.ai";
$storageDir = __DIR__ . "/imge/";

if (!file_exists($storageDir)) {
    mkdir($storageDir, 0777, true);
}

// --- GENERATION PROCESS ---
if (isset($_GET['action']) && $_GET['action'] === 'process') {
    
    $iterationIndex = isset($_GET['idx']) ? intval($_GET['idx']) : 0;
    $userInput = isset($_GET['subject']) ? $_GET['subject'] : 'a cat';
    
    // 1. Logic: Select the artist corresponding to the current index
    $availableArtists = file('artiste.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    // Check if we have reached the end of the list
    if ($iterationIndex >= count($availableArtists)) {
        echo "END"; // Signal for JavaScript
        exit;
    }

    $chosenArtist = trim($availableArtists[$iterationIndex]);
    $seed = rand(100000, 999999999);
    
    // 2. Prompt Construction: Single artist style + user subject (in English)
    $finalPrompt = "create in the style of artist {$chosenArtist} this: {$userInput}.";

    $encodedPrompt = rawurlencode($finalPrompt);
    $apiUrl = "{$baseUrl}/image/{$encodedPrompt}?model=flux&seed={$seed}&key={$apiKey}&nologo=true&private=true&width=1024&height=1024";

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $imageData) {
        $timestamp = time();
        $baseFileName = "art_comp_" . $iterationIndex . "_" . $seed;
        $imageName = $baseFileName . ".jpg";
        
        file_put_contents($storageDir . $imageName, $imageData);
        
        echo "<div class='image-card'>";
        echo "<img src='imge/$imageName' style='width:100%; border-radius:5px;'>";
        echo "<div class='info'><strong>Artist: $chosenArtist</strong><br>Subject: $userInput</div>";
        echo "</div>";
    } else {
        echo "API Error: $httpCode";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ArtBase - Artist Benchmarking</title>
    <style>
        body { background-color: #0F0F0F; color: #F2F2F2; font-family: 'Inter', sans-serif; padding: 20px; }
        .header { background: #1A1A1A; padding: 20px; border-radius: 8px; border: 1px solid #333; margin-bottom: 20px; text-align: center; }
        .controls { margin-top: 15px; }
        input[type="text"] { background: #2A2A2A; border: 1px solid #444; color: white; padding: 10px; border-radius: 6px; width: 350px; margin-right: 10px; }
        .btn-run { background: #008080; color: white; padding: 10px 25px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .btn-stop { background: #444; color: white; padding: 10px 25px; border: none; border-radius: 6px; cursor: pointer; margin-left:5px; }
        #gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
        .image-card { background: #1A1A1A; border: 1px solid #333; padding: 10px; border-radius: 8px; font-size: 0.9em; }
        .info { margin-top: 8px; color: #00d4ff; }
        .progress-container { width: 100%; background: #222; height: 10px; border-radius: 5px; margin: 20px 0; overflow: hidden; display:none; }
        #progress-bar { width: 0%; height: 100%; background: #008080; transition: width 0.3s; }
    </style>
</head>
<body>

<div class="header">
    <h1>🎨 ArtBase: Artist Benchmark</h1>
    <p>Generate one subject for every artist in your <code>artiste.txt</code> list</p>
    
    <div class="controls">
        <input type="text" id="subjectPrompt" placeholder="What do you want to create? (e.g. a futuristic cat)" value="a cat">
        <button class="btn-run" onclick="startEngine()">Validate & Launch</button>
        <button class="btn-stop" onclick="stopEngine()">Stop</button>
    </div>

    <div class="progress-container" id="pContainer">
        <div id="progress-bar"></div>
    </div>
    <div id="status">Waiting for input...</div>
</div>

<div id="gallery"></div>

<script>
let running = false;

async function startEngine() {
    if(running) return;
    
    const subject = document.getElementById('subjectPrompt').value;
    if(!subject) { alert("Please enter a subject"); return; }

    running = true;
    document.getElementById('pContainer').style.display = 'block';
    document.getElementById('gallery').innerHTML = '';
    
    // We loop through the list; PHP returns "END" when the file is finished
    for(let i=0; i < 1000; i++) { 
        if(!running) break;
        
        document.getElementById('status').innerText = `Generating artist #${i+1}...`;
        
        try {
            const response = await fetch(`?action=process&idx=${i}&subject=${encodeURIComponent(subject)}`);
            const html = await response.text();
            
            if(html.trim() === "END") {
                document.getElementById('status').innerText = "All artists processed!";
                break;
            }
            
            document.getElementById('gallery').insertAdjacentHTML('afterbegin', html);
            document.getElementById('progress-bar').style.width = '50%'; 
            
        } catch (e) {
            console.error("Engine Error", e);
        }
        
        await new Promise(r => setTimeout(r, 800));
    }
    
    running = false;
}

function stopEngine() {
    running = false;
    document.getElementById('status').innerText = "Engine stopped.";
}
</script>

</body>
</html>
