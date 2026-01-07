<?php require_once __DIR__ . '/../includes/header.php';  ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Assistance | OliveNest</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
/* RESET */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Georgia', serif;
}

/* PAGE BACKGROUND */
body{
    background:linear-gradient(180deg,#eef3f9,#ffffff);
}

/* CONTAINER */
.container{
    width:90%;
    max-width:1200px;
    margin:auto;
}

/* HERO */
.help-hero{
    background:linear-gradient(135deg,#1d3557,#457b9d);
    padding:110px 0;
    color:#fff;
    text-align:center;
    position:relative;
    overflow:hidden;
}
.help-hero::before{
    content:'';
    position:absolute;
    width:360px;
    height:360px;
    background:rgba(255,255,255,0.08);
    border-radius:50%;
    top:-130px;
    left:-130px;
}
.help-hero h1{
    font-size:48px;
    margin-bottom:15px;
    animation:fadeDown 1s ease;
}
.help-hero p{
    font-size:17px;
    opacity:.9;
    animation:fadeUp 1.2s ease;
}

/* SECTION */
.help-section{
    padding:90px 0;
}

/* BACKGROUND BEHIND CARDS */
.cards-bg{
    background:linear-gradient(135deg,#f0f4fb,#e6edf7);
    padding:90px 0;
    border-radius:40px;
    margin-top:-70px;
}

/* GRID */
.help-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:35px;
}

/* CARD */
.help-card{
    background:rgba(255,255,255,0.92);
    backdrop-filter:blur(14px);
    padding:38px;
    border-radius:24px;
    box-shadow:0 18px 45px rgba(0,0,0,0.12);
    transition:all .45s ease;
    animation:fadeZoom 1s ease;
}
.help-card:hover{
    transform:translateY(-14px) scale(1.04);
    box-shadow:0 28px 65px rgba(0,0,0,0.2);
}
.help-card h3{
    color:#1d3557;
    margin-bottom:14px;
    font-size:20px;
}
.help-card p{
    font-size:15px;
    color:#555;
    line-height:1.75;
}

/* AI ASSISTANT */
.ai-assistant{
    margin-top:100px;
    background:linear-gradient(135deg,#1d3557,#457b9d);
    border-radius:35px;
    padding:60px;
    color:#fff;
    box-shadow:0 30px 80px rgba(0,0,0,0.25);
}
.ai-header h2{
    font-size:34px;
    margin-bottom:10px;
}
.ai-header p{
    opacity:.9;
    font-size:15px;
}

/* CHAT */
.ai-chat{
    margin-top:35px;
    background:#ffffff;
    border-radius:25px;
    padding:30px;
    color:#333;
    max-height:320px;
    overflow-y:auto;
}
.ai-msg{
    background:#f1f5fb;
    padding:15px 20px;
    border-radius:20px;
    margin-bottom:12px;
    max-width:80%;
    animation:fadeUp .6s ease;
}
.ai-msg.ai{
    background:#e6edf7;
    font-weight:600;
}

/* AI INPUT */
.ai-input{
    display:flex;
    margin-top:20px;
    gap:10px;
}
.ai-input input{
    flex:1;
    padding:14px 20px;
    border-radius:30px;
    border:1px solid #ddd;
    outline:none;
}
.ai-input button{
    padding:14px 28px;
    background:#1d3557;
    color:#fff;
    border:none;
    border-radius:30px;
    cursor:pointer;
}
.ai-input button:hover{
    background:#14253d;
}

/* CONTACT BOX */
.contact-box{
    margin-top:90px;
    background:linear-gradient(135deg,#ffffff,#f0f4fb);
    padding:55px;
    border-radius:28px;
    box-shadow:0 22px 55px rgba(0,0,0,0.14);
    animation:fadeUp 1.3s ease;
}
.contact-box h2{
    color:#1d3557;
    margin-bottom:22px;
}
.contact-box p{
    font-size:15px;
    color:#444;
    margin:10px 0;
}

/* BUTTON */
.help-btn{
    display:inline-block;
    margin-top:28px;
    padding:15px 38px;
    background:linear-gradient(135deg,#1d3557,#457b9d);
    color:#fff;
    text-decoration:none;
    border-radius:45px;
    font-weight:600;
    transition:.4s;
}
.help-btn:hover{
    transform:translateY(-5px);
    box-shadow:0 14px 35px rgba(29,53,87,0.45);
}

/* ANIMATIONS */
@keyframes fadeUp{
    from{opacity:0;transform:translateY(45px);}
    to{opacity:1;transform:none;}
}
@keyframes fadeDown{
    from{opacity:0;transform:translateY(-45px);}
    to{opacity:1;transform:none;}
}
@keyframes fadeZoom{
    from{opacity:0;transform:scale(.88);}
    to{opacity:1;transform:none;}
}

/* RESPONSIVE */
@media(max-width:768px){
    .help-hero h1{font-size:36px;}
    .cards-bg{padding:60px 0;}
    .contact-box{padding:38px;}
    .ai-assistant{padding:40px;}
}
</style>
</head>

<body>

<!-- HERO -->
<section class="help-hero">
    <div class="container">
        <h1>Premium Booking Assistance</h1>
        <p>Luxury support with AI guidance for your perfect OliveNest stay</p>
    </div>
</section>

<section class="help-section">

<!-- CARDS -->
<div class="cards-bg">
<div class="container">
<div class="help-grid">

<div class="help-card">
<h3>Easy Booking Process</h3>
<p>Book your stay quickly with our simple and intuitive reservation system.</p>
</div>

<div class="help-card">
<h3>Secure Payments</h3>
<p>Trusted and secure payment options ensure safe transactions.</p>
</div>

<div class="help-card">
<h3>Booking Modifications</h3>
<p>Change or cancel bookings easily with guest-friendly policies.</p>
</div>

<div class="help-card">
<h3>Check-in Assistance</h3>
<p>Smooth check-in and check-out with full guidance.</p>
</div>

<div class="help-card">
<h3>Personal Stay Advisor</h3>
<p>Get room recommendations based on your preferences.</p>
</div>

<div class="help-card">
<h3>Priority Guest Support</h3>
<p>Fast responses and priority handling for premium guests.</p>
</div>

</div>
</div>
</div>

<!-- AI ASSISTANT -->
<div class="container">
<div class="ai-assistant">

<div class="ai-header">
<h2>🤖 OliveNest AI Assistant</h2>
<p>Your smart helper for rooms, booking & stay details</p>
</div>

<div class="ai-chat" id="chatBox">
<div class="ai-msg ai">
Hello! I’m OliveNest AI 🤍  
How can I assist you today?
</div>
</div>

<div class="ai-input">
<input type="text" id="userInput" placeholder="Ask about rooms, prices, booking..." />
<button onclick="sendMessage()">Ask AI</button>
</div>

</div>
</div>

<!-- CONTACT -->
<div class="container">
<div class="contact-box">
<h2>Need Human Assistance?</h2>
<p><strong>📞 Phone:</strong> +91 XXXXX XXXXX</p>
<p><strong>📧 Email:</strong> support@olivenest.com</p>
<p><strong>⏰ Support:</strong> 24/7</p>
<a href="contact.php" class="help-btn">Contact Support</a>
</div>
</div>

</section>

<script>
function sendMessage(){
    const input = document.getElementById("userInput");
    const chat = document.getElementById("chatBox");
    const msg = input.value.trim();
    if(msg === "") return;

    chat.innerHTML += `<div class="ai-msg">${msg}</div>`;

    let reply = "I can help you with rooms, booking, prices,contact, and check-in details 😊";

    if(msg.toLowerCase().includes("room"))
        reply = "We offer Luxury, Family, and Premium rooms starting from ₹1200.";
    else if(msg.toLowerCase().includes("price"))
        reply = "Room prices start from ₹1200 per night depending on type.";
    else if(msg.toLowerCase().includes("check"))
        reply = "Check-in is at 12 PM and check-out is at 11 AM.";
    else if(msg.toLowerCase().includes("book"))
        reply = "You can book easily from the Rooms page. Need recommendations?";
    else if(msg.toLowerCase().includes("contact"))
        reply = "You can  easily contact us from our contact section. Need recommendations?";
    else if(msg.toLowerCase().includes("about"))
        reply = "You can easily see our story from about us section. Need recommendations?";

    setTimeout(()=>{
        chat.innerHTML += `<div class="ai-msg ai">${reply}</div>`;
        chat.scrollTop = chat.scrollHeight;
    },600);

    input.value = "";
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
