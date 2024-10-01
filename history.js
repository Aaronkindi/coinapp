document.addEventListener("DOMContentLoaded", () => {
  let transactionData = [];

  function displayhistoryData(transactionData, pageIndex) {
    const assetsContainer = document.querySelector(".history");
    assetsContainer.innerHTML = "";

    const start = pageIndex * 5;
    const end = start + 5;
    const pageData = transactionData.slice(start, end);

    pageData.forEach((coin) => {
      const card = document.createElement("div");
      card.classList.add("asset-card");
    });
    card.innerHTML = `
    <img src="${item.iconUrl}" class="crypto-logo" alt="${item.cryptoname}">
    <h6>${item.cryptoname}</h6>
    <p>Amount: ${parseFloat(item.amount).toFixed(2)}</p>
    <p>Price: ${parseFloat(item.cryptoprice).toFixed(2)}</p>
    `;

    assetsContainer.appendChild(card);
  }

  updateActiveDot(pageIndex); // Update the active pagination dot

  displayhistoryData(transactionData, 0);
});
console.log(transactionData);
