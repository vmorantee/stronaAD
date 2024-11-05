<?php
require 'navbar.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O Nas - Flanki</title>
    <link rel="stylesheet" href="profile.css">
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #1e2025;
            border-radius: 8px;
            color: #ffffff;
        }
        h1, h2 {
            color: #1da1f2;
        }
        .section {
            margin-bottom: 20px;
        }
        .section p {
            font-size: 16px;
            color: #e1e8ed;
        }
        .video-container {
            position: relative;
            padding-bottom: 56.25%; 
            height: 0;
            overflow: hidden;
            max-width: 100%;
            background: #000;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Kim Jesteśmy</h1>
        <div class="section">
            <p>Flanki to dynamiczna gra strategiczna, która przyciąga graczy szukających emocjonujących wyzwań i intensywnych rozgrywek. Nasza społeczność składa się z różnorodnych graczy, którzy łączą pasję do gier planszowych z chęcią rywalizacji i wspólnej zabawy. Flanki to nie tylko gra, ale także doświadczenie społeczne, które rozwija umiejętności strategiczne i współpracy.</p>
            <p>Gracze we Flanki to prawdziwi mistrzowie taktyki i strategii. Nasza społeczność obejmuje zarówno początkujących, jak i doświadczonych graczy, którzy dzielą się swoimi strategiami i technikami, aby pomóc innym w doskonaleniu swoich umiejętności. Każda gra we Flanki to szansa na naukę czegoś nowego, wypróbowanie różnych strategii i współpraca z innymi graczami w celu osiągnięcia wspólnych celów.</p>
            <p>Flanki przyciąga graczy, którzy cenią sobie strategiczne myślenie i elastyczność. Dzięki różnym scenariuszom i strategiom, każda rozgrywka jest wyjątkowa i dostarcza nowych wyzwań. Bez względu na to, czy grasz w gronie przyjaciół, czy uczestniczysz w zawodach, Flanki zapewnia niezapomniane doświadczenia i emocje.</p>
            <p>Dołącz do naszej społeczności i odkryj, jak Flanki może wzbogacić Twoje życie o nowe znajomości, wyzwania i przyjemność z gry!</p>
        </div>
        <h2>Jak Grać</h2>
        <div class="video-container">
            <iframe src="https://www.youtube.com/embed/AiCPtLuKr0Y" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</body>
</html>
