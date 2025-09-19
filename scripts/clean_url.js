const urlParams = new URLSearchParams(window.location.search);

if (urlParams.has('add_personnel_success') && urlParams.get('add_personnel_success') === '1') { // Add personnel Success
    displayAlertAndRedirect('Personnel added successfully!');
} 
else if (urlParams.has('add_personnel_error') && urlParams.get('add_personnel_error') === '1') { // Add personnel Error
    displayAlertAndRedirect('Failed to add personnel. Please fill in all required fields.');
} 
else if (urlParams.has('update_material_success') && urlParams.get('update_material_success') === '1') { // Add Supplier Error
    displayAlertAndRedirect('Failed to add supplier. Please fill in all required fields.');
}  
else if (urlParams.has('add_supplier_success') && urlParams.get('add_supplier_success') === '1') { // Add supplier Success
    displayAlertAndRedirect('Supplier added successfully!');
}
else if (urlParams.has('add_supplier_error') && urlParams.get('add_supplier_error') === '2') { // Add Supplier Error - Invalid contact number
    displayAlertAndRedirect('Failed to add supplier. Invalid contact number format.'); 
}
else if (urlParams.has('remove_supplier_success') && urlParams.get('remove_supplier_success') === '1') { // Remove supplier Success
    displayAlertAndRedirect('Supplier deleted successfully!');
} else if (urlParams.has('remove_supplier_error') && urlParams.get('remove_supplier_error') === '1') { // Remove supplier Error
    displayAlertAndRedirect('Failed to delete supplier.');
}
else if (urlParams.has('add_material_success') && urlParams.get('add_material_success') === '1') { // Add material Success
    displayAlertAndRedirect('Material added successfully!');
} 
else if (urlParams.has('update_material_success') && urlParams.get('update_material_success') === '1') { // Update material Success
    displayAlertAndRedirect('Material updated Successfully!');
} 
else if (urlParams.has('update_material_error') && urlParams.get('update_material_error') === '1') { // Update material Error
    displayAlertAndRedirect('Failed to update material.');
}
else if (urlParams.has('add_order_success') && urlParams.get('add_order_success') === '1') { // Add order Success
    displayAlertAndRedirect('Order added successfully!');
}
else if (urlParams.has('add_order_error') && urlParams.get('add_order_error') === '1') { // Add order Error
    displayAlertAndRedirect('Failed to add order.');
}
else if (urlParams.has('order_received_success') && urlParams.get('order_received_success') === '1') {
    displayAlertAndRedirect('Order received successfully!');
}

function displayAlertAndRedirect(alertMessage) {
    alert(alertMessage);
    window.history.replaceState({}, document.title, window.location.pathname);
}