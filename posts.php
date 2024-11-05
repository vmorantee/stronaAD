<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wydarzenia - Polski Związek Flankowy</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        #navbar {
            background-color: #333;
            overflow: hidden;
        }

        #navbar a {
            float: left;
            display: block;
            color: #fff;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        #navbar a:hover {
            background-color: #575757;
        }

        #bodycontainer {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        #titl {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        #maincontainer {
            display: flex;
            justify-content: space-between;
            height: 80vh; /* Adjust to leave space for other elements */
        }

        #postcontainer {
            flex: 3;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            overflow-y: auto; /* Enable vertical scrolling */
            height: 100%; /* Ensure it takes up full available height */
        }

        .post-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            text-decoration: none;
            color: black;
        }

        .post-card:hover {
            transform: translateY(-5px);
        }

        .image-container {
            height: 200px;
            overflow: hidden;
            border-radius: 8px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .post-banner {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .post-content {
            padding: 15px;
        }

        .post-title {
            font-size: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .post-meta {
            font-size: 14px;
            color: #777;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        .post-description {
            font-size: 16px;
            color: #555;
            overflow: hidden; /* Hide overflow if necessary */
            text-overflow: ellipsis; /* Add ellipsis if text overflows */
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Limit to 3 lines */
            -webkit-box-orient: vertical;
        }

        #map {
            width: 100%;
            height: 400px;
            margin-top: 20px;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            margin-top: 50px;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-left, .footer-right {
            width: 45%;
        }
    </style>
</head>
<body>

<div id="navbar">
    <a href="#">Strona główna</a>
    <a href="#">O nas</a>
    <a href="#">Kontakt</a>
</div>

<div id="bodycontainer">
    <div id="titl">Wydarzenia Polskiego Związku Flankowego</div>
    <div id="maincontainer">
        <div id="postcontainer">
            <!-- Posty wydarzeń będą tutaj generowane -->
        </div>
    </div>
</div>

<div id="map"></div>

<footer>
    <div class="footer-container">
        <div class="footer-left">
            <h3>Kontakt</h3>
            <ul>
                <li>Email: kontakt@flankowy.pl</li>
                <li>Telefon: +48 123 456 789</li>
            </ul>
        </div>
        <div class="footer-right">
            <h3>Polski Związek Flankowy</h3>
        </div>
    </div>
</footer>

<script>
    // Przykładowe wydarzenia
    const events = [
        {
            name: "Turniej Flankowy w Warszawie",
            imageUrl: "https://via.placeholder.com/300x200",  // Zamień na obrazki wydarzeń
            location: { lat: 52.2297, lng: 21.0122 },
            parameters: {
                param1: "Poziom: Zaawansowany",
                param2: "Data: 15 października 2024",
                param3: "Nagrody: 10 000 PLN"
            }
        },
        {
            name: "Trening Flankowy w Krakowie",
            imageUrl: "https://via.placeholder.com/300x200",
            location: { lat: 50.0647, lng: 19.945 },
            parameters: {
                param1: "Poziom: Amatorski",
                param2: "Data: 22 października 2024",
                param3: "Nagrody: Brak"
            }
        },
        {
            name: "Zawody Flankowe w Gdańsku",
            imageUrl: "https://via.placeholder.com/300x200",
            location: { lat: 54.352, lng: 18.6466 },
            parameters: {
                param1: "Poziom: Profesjonalny",
                param2: "Data: 30 października 2024",
                param3: "Nagrody: 5 000 PLN"
            }
        }
    ];

    // Funkcja do generowania postów wydarzeń
    function displayEvents() {
        const postContainer = document.getElementById("postcontainer");

        events.forEach(event => {
            const postCard = document.createElement("a");
            postCard.href = "#";
            postCard.classList.add("post-card");

            const imageContainer = document.createElement("div");
            imageContainer.classList.add("image-container");

            const image = document.createElement("img");
            image.src = event.imageUrl;
            image.alt = event.name;
            image.classList.add("post-banner");

            imageContainer.appendChild(image);

            const postContent = document.createElement("div");
            postContent.classList.add("post-content");

            const postTitle = document.createElement("h2");
            postTitle.classList.add("post-title");
            postTitle.textContent = event.name;

            const postMeta = document.createElement("div");
            postMeta.classList.add("post-meta");
            postMeta.innerHTML = `
                <span>${event.parameters.param1}</span>
                <span>${event.parameters.param2}</span>
                <span>${event.parameters.param3}</span>
            `;

            postContent.appendChild(postTitle);
            postContent.appendChild(postMeta);

            postCard.appendChild(imageContainer);
            postCard.appendChild(postContent);

            postContainer.appendChild(postCard);
        });
    }

    // Inicjalizacja mapy Google
    function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 6,
            center: { lat: 52.2297, lng: 21.0122 }
        });

        events.forEach(event => {
            const marker = new google.maps.Marker({
                position: event.location,
                map: map,
                title: event.name
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `<h2>${event.name}</h2><p>${event.parameters.param1}</p><p>${event.parameters.param2}</p><p>${event.parameters.param3}</p>`
            });

            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });
        });
    }

    // Wyświetlanie wydarzeń
    window.onload = () => {
        displayEvents();
    };
</script>

<!-- Google Maps API - Wprowadź tutaj swój klucz API -->
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap">
</script>

</body>
</html>
