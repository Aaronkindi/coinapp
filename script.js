document.addEventListener("DOMContentLoaded", () => {
  const apiKey = "coinranking411c36e5909bfb3dfcec310d01649a6dd8ea4fee7b5e6dac";
  const apiUrl = "https://api.coinranking.com/v2/coins?limit=15"; // Fetch 15 coins for 3 pages
  let coins = [];
  let portfolioData = []; // Portfolio data passed from PHP

  // Fetch cryptocurrency data
  fetch(apiUrl, {
    method: "GET",
    headers: {
      "x-access-token": apiKey,
    },
  })
    .then((response) => response.json())
    .then((data) => {
      coins = data.data.coins || []; // Ensure coins array is valid
      setupPagination(); // Initialize pagination
      displayCryptoData(0); // Display the first page initially
    })
    .catch((error) => console.error("Error fetching the data:", error));

  // Function to display coins for a specific page
  function displayCryptoData(pageIndex) {
    const assetsContainer = document.querySelector(".assets-container");
    assetsContainer.innerHTML = "";

    const start = pageIndex * 5;
    const end = start + 5;
    const pageCoins = coins.slice(start, end);

    pageCoins.forEach((coin) => {
      const card = document.createElement("div");
      card.classList.add("asset-card");

      // Add an event listener to make the card clickable
      card.addEventListener("click", () => {
        redirectToTransactionPage(coin);
      });

      card.innerHTML = `
        <img src="${coin.iconUrl}" class="crypto-logo" alt="${coin.name}">
        <p>${coin.symbol}</p>
        <p>${coin.name}</p>
        <p>$${parseFloat(coin.price).toFixed(2)}</p>
        <p>${coin.change}%</p>
        <p>$${parseInt(coin.marketCap).toLocaleString()}</p>
      `;

      assetsContainer.appendChild(card);
    });

    updateActiveDot(pageIndex); // Update the active dot
  }

  // Function to redirect to a transaction page with the selected coin's details
  function redirectToTransactionPage(coin) {
    window.location.href = `buycoin.php?symbol=${coin.symbol}&name=${coin.name}&price=${coin.price}`;
  }

  // Function to set up pagination dots
  function setupPagination() {
    const dots = document.querySelectorAll(".dot");
    dots.forEach((dot) => {
      dot.addEventListener("click", () => {
        const pageIndex = parseInt(dot.getAttribute("data-page"));
        displayCryptoData(pageIndex);
      });
    });
  }

  // Function to update the active dot
  function updateActiveDot(activeIndex) {
    const dots = document.querySelectorAll(".dot");
    dots.forEach((dot, index) => {
      if (index === activeIndex) {
        dot.classList.add("active");
      } else {
        dot.classList.remove("active");
      }
    });
  }

  // Search function
  function searchCoins(query) {
    const filteredCoins = coins.filter((coin) =>
      coin.name.toLowerCase().includes(query.toLowerCase())
    );
    displaySearchedCoins(filteredCoins);
  }

  // Function to display the filtered search results
  function displaySearchedCoins(filteredCoins) {
    const assetsContainer = document.querySelector(".assets-container");
    assetsContainer.innerHTML = "";

    filteredCoins.forEach((coin) => {
      const card = document.createElement("div");
      card.classList.add("asset-card");

      card.addEventListener("click", () => {
        redirectToTransactionPage(coin);
      });

      card.innerHTML = `
        <img src="${coin.iconUrl}" class="crypto-logo" alt="${coin.name}">
        <p>${coin.symbol}</p>
        <p>${coin.name}</p>
        <p>$${parseFloat(coin.price).toFixed(2)}</p>
        <p>${coin.change}%</p>
        <p>$${parseInt(coin.marketCap).toLocaleString()}</p>
      `;

      assetsContainer.appendChild(card);
    });
  }

  // Event listener for search input
  const searchInput = document.getElementById("searchInput");
  searchInput.addEventListener("input", (event) => {
    const query = event.target.value;
    if (query) {
      searchCoins(query);
    } else {
      displayCryptoData(0); // Display the first page if search is cleared
    }
  });

  // Event listener for currency change
  document.getElementById("currency").addEventListener("change", function () {
    const selectedCurrency = this.value;
    displayPortfolio(portfolioData, coins, selectedCurrency); // Recalculate portfolio with new currency
  });
});

// Function to fetch and display portfolio data for a specific page using AJAX
function displayPortfolioData(portfolioData, pageIndex) {
  const portfolioContainer = document.querySelector("#portfolio-data");
  portfolioContainer.innerHTML = ""; // Clear any previous data

  const start = pageIndex * 5;
  const end = start + 5;
  const pageData = portfolioData.slice(start, end);

  pageData.forEach((item) => {
    const portfolioItem = document.createElement("div");
    portfolioItem.classList.add("asset-card-portfolio");

    portfolioItem.innerHTML = `
      <img src="${item.iconUrl}" class="crypto-logo" alt="${item.cryptoname}">
      <h6>${item.cryptoname}</h6>
      <p>Amount: ${parseFloat(item.amount).toFixed(2)}</p>
      <p>Price: $${parseFloat(item.cryptoprice).toFixed(2)}</p>
    `;

    portfolioItem.addEventListener("click", () => {
      redirectToTransactionPage(item); // Assuming a similar functionality exists
    });

    portfolioContainer.appendChild(portfolioItem);
  });

  updateActiveDot(pageIndex); // Update the active pagination dot
}

// Example call to the function
displayPortfolioData(portfolioData, 0); // Pass in portfolio data and start at page 0
