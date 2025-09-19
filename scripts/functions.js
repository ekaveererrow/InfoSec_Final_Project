//add item in inventory & update stocks
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const userForm = document.getElementById("userForm");
    const tableBody = document.getElementById("tableBody");

    // Open Add Modal
    openModalBtn.addEventListener("click", () => {
        modal.style.display = "flex";
    });
    
    closeModalBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Close modal when clicking outside the modal content
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    function updateInventoryStats() {
        const tableBody = document.getElementById("tableBody");
        const rows = tableBody.getElementsByTagName("tr");
        const categories = new Set();
        let totalQuantity = 0;
        let lowStockCount = 0;
        let outOfStockCount = 0;
    
        for (let row of rows) {
            const productName = row.cells[0].textContent.trim();
            const quantityText = row.cells[2].textContent.trim();
            const quantity = parseInt(quantityText.split(" ")[0], 10); // Extract numeric quantity
            const thresholdText = row.cells[3].textContent.trim();
            const threshold = parseInt(thresholdText.split(" ")[0], 10); // Extract numeric threshold
            const availability = row.cells[5].textContent.trim();
    
            // Track unique product categories
            categories.add(productName);
    
            // Calculate total quantity
            totalQuantity += quantity;
    
            // Count low stock and out-of-stock items
            if (availability === "Out of Stock") {
                outOfStockCount++;
            } else if (availability === "Low Stock") {
                lowStockCount++;
            }
        }
    
        // Update stats in the UI
        const statsElements = document.querySelectorAll(".inventory-stats div");
        statsElements[0].innerHTML = `<strong>Item Category</strong> <br>${categories.size}`;
        statsElements[1].innerHTML = `<strong>Total Items</strong> <br>${totalQuantity}`;
        statsElements[2].innerHTML = `<strong>Low Stocks</strong> <br>${lowStockCount}`;
        statsElements[3].innerHTML = `<strong>Out of Stock</strong> <br>${outOfStockCount}`;
    }
    
    // Call update function whenever an item is added
    userForm.addEventListener("submit", function () {
        setTimeout(updateInventoryStats, 100); // Slight delay to ensure new row is added before counting
    });
    
    // Run once on page load to initialize stats
    document.addEventListener("DOMContentLoaded", updateInventoryStats);
    
    // Handle form submission
    userForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent page refresh

        // Get values from form
        const productName = document.getElementById("productName").value.trim();
        const buyingPrice = document.getElementById("buyingPrice").value.trim();
        const quantity = parseInt(document.getElementById("quantity").value, 10);
        const threshold = parseInt(document.getElementById("threshold").value, 10);
        const expiryDate = document.getElementById("expiryDate").value;

        // Validation
        if (!productName || !buyingPrice || isNaN(quantity) || isNaN(threshold) || !expiryDate) {
            alert("Please fill in all fields correctly.");
            return;
        }

        // Determine availability status
        let availability, availabilityClass;
        if (quantity === 0) {
            availability = "Out of Stock";
            availabilityClass = "danger";
        } else if (quantity < (threshold / 2)) {
            availability = "Low Stock";
            availabilityClass = "warning";
        } else {
            availability = "In-Stock";
            availabilityClass = "success";
        }

        // Create new table row
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td>${productName}</td>
            <td>$${buyingPrice}</td>
            <td>${quantity} Pieces</td>
            <td>${threshold} Pieces</td>
            <td>${expiryDate}</td>
            <td class="${availabilityClass}">${availability}</td>
        `;

        // Append new row to table
        tableBody.appendChild(newRow);

        // Reset form and close modal
        userForm.reset();
        modal.style.display = "none";
    });
});

// update stocks
document.addEventListener("DOMContentLoaded", function () {
    const updateModalBtn = document.getElementById("updateModalBtn");
    const updateStockModal = document.getElementById("updateStockModal");
    const closeModal = document.getElementById("closeModal");
    const addStockUpdate = document.getElementById("addStockUpdate");
    const lessStockUpdate = document.getElementById("lessStockUpdate");

    if (updateModalBtn && updateStockModal && closeModal && addStockUpdate) {
        updateModalBtn.addEventListener("click", function () {
            updateStockModal.style.display = "flex";
        });

        closeModal.addEventListener("click", function () {
            updateStockModal.style.display = "none";
        });

        // addStockUpdate.addEventListener("click", function () {
        //     const product = document.getElementById("product").value;
        //     const quantity = document.getElementById("quantity").value;

        //     if (product && quantity) {
        //         console.log("Product:", product);
        //         console.log("Quantity:", quantity);
        //         updateStockModal.style.display = "none";
        //     } else {
        //         alert("Please enter product and quantity");
        //     }
        // });
    } else {
        console.error("One or more modal elements are missing.");
    }
});

//MARK AS ABSENT/PRESENT AND ADD PERSONNEL
document.addEventListener("DOMContentLoaded", function () {
    const personnelTable = document.getElementById("personnelTable");
    const openFormBtn = document.getElementById("openFormBtn");
    const modal = document.getElementById("personnelFormModal");
    const personnelForm = document.getElementById("personnelForm");
    const closeFormBtn = document.getElementById("closeFormBtn");

    // Open modal
    openFormBtn.addEventListener("click", function () {
        modal.style.display = "flex";
    });

    // Close modal
    closeFormBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Add personnel on form submit
    // personnelForm.addEventListener("submit", function (event) {
    //     event.preventDefault(); // Prevent page refresh

    //     // Get form values
    //     // const name = document.getElementById("name").value;
    //     const position = document.getElementById("position").value;
    //     const contact = document.getElementById("contact").value;
    //     const email = document.getElementById("email").value;

    //     // Create new row
    //     const newRow = document.createElement("tr");
    //     newRow.innerHTML = `
    //         <td>${name}</td>
    //         <td>${position}</td>
    //         <td>${contact}</td>
    //         <td>${email}</td>
    //         <td class="status success">Present</td>
    //         <td><button class="btn btn-outline toggle-status">Mark Absent</button></td>
    //     `;

    //     // Append to table
    //     personnelTable.appendChild(newRow);

    //     // Clear form fields
    //     personnelForm.reset();

    //     // Close modal
    //     modal.style.display = "none";
    // });

    // Toggle status (Mark Absent/Present)
    personnelTable.addEventListener("click", function (event) {
        if (event.target.classList.contains("toggle-status")) {
            const row = event.target.closest("tr");
            const statusCell = row.querySelector(".status");

            if (statusCell.textContent.trim() === "present") {
                statusCell.textContent = "absent";
                statusCell.classList.remove("success");
                statusCell.classList.add("danger");
                event.target.textContent = "Mark Present";
            } else {
                statusCell.textContent = "present";
                statusCell.classList.remove("danger");
                statusCell.classList.add("success");
                event.target.textContent = "Mark Absent";
            }
        }
    });
});


// ADD A SUPPLIER
document.addEventListener("DOMContentLoaded", function () {
    const personnelTable = document.getElementById("supplierTable");
    const openFormBtn = document.getElementById("openFormBtn");
    const modal = document.getElementById("supplierFormModal");
    const personnelForm = document.getElementById("supplierForm");
    const closeFormBtn = document.getElementById("closeFormBtn");

    // Open modal
    openFormBtn.addEventListener("click", function () {
        modal.style.display = "flex";
    });

    // Close modal
    closeFormBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Add supplier on form submit
    // supplierForm.addEventListener("submit", function (event) {
    //     event.preventDefault(); // Prevent page refresh

    //     // Get form values
    //     const name = document.getElementById("name").value;
    //     const product = document.getElementById("product").value;
    //     const contact = document.getElementById("contact").value;
    //     const email = document.getElementById("email").value;
    //     const otw = document.getElementById("otw").value;

    //     // Create new row
    //     const newRow = document.createElement("tr");
    //     newRow.innerHTML = `
    //         <td>${name}</td>
    //         <td>${product}</td>
    //         <td>${contact}</td>
    //         <td>${email}</td>
    //         <td>${otw}</td>
    //     `;

    //     // Append to table
    //     personnelTable.appendChild(newRow);

    //     // Clear form fields
    //     personnelForm.reset();

    //     // Close modal
    //     modal.style.display = "none";
    // });
});


//Add Order and update stat-box
document.addEventListener("DOMContentLoaded", function() {
    fetchSuppliers();
});

function fetchSuppliers() {
    fetch("suppliers.html")
        .then(response => response.text())
        .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, "text/html");
            let supplierRows = doc.querySelectorAll("#supplierTable tr");

            let supplierDropdown = document.getElementById("supplier");
            supplierDropdown.innerHTML = ""; // Clear existing options

            supplierRows.forEach(row => {
                let supplierName = row.cells[0].textContent.trim();
                let option = document.createElement("option");
                option.value = supplierName;
                option.textContent = supplierName;
                supplierDropdown.appendChild(option);
            });
        })
        .catch(error => console.error("Error fetching suppliers:", error));
}

function updateStats() {
    let totalOrders = document.querySelectorAll("#ordersTable tr").length;
    let receivedOrders = document.querySelectorAll("#ordersTable tr td.success").length;
    let onTheWayOrders = totalOrders - receivedOrders;
    
    document.querySelector(".stat-container .stat-box:nth-child(1) h3").textContent = totalOrders;
    document.querySelector(".stat-container .stat-box:nth-child(2) h3").textContent = receivedOrders;
    document.querySelector(".stat-container .stat-box:nth-child(3) h3").textContent = onTheWayOrders;
}

// Open the modal to add an order
document.getElementById("addOrderBtn").addEventListener("click", function() {
    document.getElementById("orderModal").style.display = "flex";
});

// Close the modal
document.getElementById("closeModal").addEventListener("click", function() {
    document.getElementById("orderModal").style.display = "none";
});

// Handle order form submission
// document.getElementById("orderForm").addEventListener("submit", function(event) {
//     event.preventDefault();
    
//     let supplier = document.getElementById("supplier").value;
//     let product = document.getElementById("product").value;
//     let orderValue = document.getElementById("orderValue").value;
//     let quantity = document.getElementById("quantity").value;
//     let expectedDelivery = document.getElementById("expectedDelivery").value;
//     let orderId = Math.floor(1000 + Math.random() * 9000);
    
//     let newRow = `<tr>
//         <td>${supplier}</td>
//         <td>${product}</td>
//         <td>$${orderValue}</td>
//         <td>${quantity}</td>
//         <td>${orderId}</td>
//         <td>${expectedDelivery}</td>
//         <td class="pending">Pending</td>
//         <td><button class="receivedBtn">Received</button></td>
//     </tr>`;
    
//     document.getElementById("ordersTable").innerHTML += newRow;
//     document.getElementById("orderModal").style.display = "none";

//     updateStats(); // Update stats after adding an order
// });

// Handle order received button
// document.addEventListener("click", function(event) {
//     if (event.target.classList.contains("receivedBtn")) {
//         let row = event.target.closest("tr");
//         row.cells[6].textContent = "Received";
//         row.cells[6].classList.remove("warning", "pending");
//         row.cells[6].classList.add("success");
//         event.target.remove();
//         updateStats();
//     }
// });

// Remove an Item In inventory
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".remove-btn").forEach(button => {
        button.addEventListener("click", function () {
            let materialId = this.getAttribute("data-id");

            if (confirm("Are you sure you want to remove this item?")) {
                fetch("remove_item.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `id=${materialId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`row-${materialId}`).remove();
                    } else {
                        alert("Error removing item.");
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        });
    });
});


