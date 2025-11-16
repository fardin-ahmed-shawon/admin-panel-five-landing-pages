// Adding Cart list & Calculation
let carts = [];
var total_price;

function addToCart(button) {
    // Accessing grand parent which is card class
    let product = button.parentElement.parentElement;
    let id = product.getAttribute("product-id");
    let image = product.getAttribute("product-img");
    let name = product.getAttribute("product-name");
    let price = product.getAttribute("product-price");
    let quantity = product.getAttribute("product-quantity");

    let ind = 0;

    // Checking same product exist or not in the carts
    if (carts.length == 0) {
        carts.push({id, image, name, price, quantity});
        saveToLocalStorage(); // Save to localStorage
    } else {
        carts.forEach(data => {
            if (data.id == id) {
                ind = 1;
            }
        });

        if (ind == 0) {
            carts.push({id, image, name, price, quantity});
            saveToLocalStorage(); // Save to localStorage
        } else {
            alert("Product Already Added!");
        }
    }
    // console.log(carts);
    loadCartItems();
}

// Function to save carts to localStorage
function saveToLocalStorage() {
    localStorage.setItem("carts", JSON.stringify(carts));
}


// Function to load cart items from localStorage
function loadCartItems() {
    const orderItemsContainer = document.getElementById("order-items");
    orderItemsContainer.innerHTML = ""; // Clear existing items

    // Retrieve carts from localStorage
    const storedCarts = JSON.parse(localStorage.getItem("carts")) || [];
    carts = storedCarts;

    // Populate the cart items dynamically
    carts.forEach(cartItem => {
        const orderItem = document.createElement("div");
        orderItem.classList.add("order-item");

        orderItem.innerHTML = `
            <div class="order-product-info">
                <div class="cart-box" id="${cartItem.id}">
                    <div class="p-4" style="display: flex; justify-content: space-between; align-items: center;">
                        <i style="font-size: 23px; cursor: pointer;" onclick="removeCart(this)" class="ri-delete-bin-line cart-remove"></i>
                    </div>
                    <img style="width: 60px; height: 60px;" src="${cartItem.image}" alt="cart-img">
                    <div class="cart-details ml-3">
                        <h5 class="cart-product-title">${cartItem.name}</h5>
                        <div class="cart-quantity">
                            <button class="decrement" onclick="updateQuantity('${cartItem.id}', -1)">-</button>
                            <span class="number">${cartItem.quantity}</span>
                            <button class="increment" onclick="updateQuantity('${cartItem.id}', 1)">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-product-price amount">৳ ${cartItem.price * cartItem.quantity}</div>
        `;

        orderItemsContainer.appendChild(orderItem);
        orderItemsContainer.appendChild(document.createElement("hr")); // Add a separator
    });

    updateSubtotal();
}

// Function to update quantity
function updateQuantity(id, change) {
    carts = carts.map(cartItem => {
        if (cartItem.id === id) {
            cartItem.quantity = Math.max(1, parseInt(cartItem.quantity) + change); // Ensure quantity is at least 1
        }
        return cartItem;
    });

    saveToLocalStorage();
    loadCartItems(); // Reload cart items
}

// Function to update subtotal
function updateSubtotal() {
    const subtotalPriceElement = document.getElementById("subtotal-price");
    const totalPriceElement = document.getElementById("total-price");
    const shippingPriceElement = document.getElementById("shipping-price");

    const subtotal = carts.reduce((sum, cartItem) => sum + cartItem.price * cartItem.quantity, 0);
    const shipping = parseInt(shippingPriceElement.textContent.replace("৳", "").trim()) || 0;

    subtotalPriceElement.textContent = `৳ ${subtotal}`;
    totalPriceElement.textContent = `৳ ${subtotal + shipping}`;
}

// Function to save carts to localStorage
function saveToLocalStorage() {
    localStorage.setItem("carts", JSON.stringify(carts));
}

// Load cart items on page load
document.addEventListener("DOMContentLoaded", loadCartItems);


function removeCart(element) {
    // Get the parent element containing the cart item
    const cartBox = element.closest(".cart-box");
    const cartId = cartBox.id; // Get the ID of the cart item

    // Filter out the cart item with the matching ID
    carts = carts.filter(cartItem => cartItem.id !== cartId);

    // Save the updated carts array to localStorage
    saveToLocalStorage();

    // Reload the cart items to reflect the changes
    loadCartItems();
}