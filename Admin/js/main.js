//success: #1bcfb4
// primary: #b66dff
// danger: #fe7c96
// muted: #9C9FA6


// Set Appropriate Order Status Text Color
document.querySelectorAll('tr .order-status').forEach(function(status_text) {
    if (status_text.innerHTML == 'Pending') {
        status_text.style.color = '#b66dff';
    } else if (status_text.innerHTML == 'Canceled') {
        status_text.style.color = '#fe7c96';
    } else {
        status_text.style.color = '#1bcfb4';
    }
  });

// Set Appropriate Payment Status Text Color
document.querySelectorAll('tr .payment-status').forEach(function(status_text) {
    if (status_text.innerHTML == 'Unpaid') {
      status_text.style.color = '#fe7c96';
    } else if (status_text.innerHTML == 'Paid') {
        status_text.style.color = '#1bcfb4';
    } else {
        status_text.style.color = '#9C9FA6';
    }
  });

  //------ Make Changes according to the logic ------//

  document.querySelectorAll('tr').forEach(function(row) {
    var orderStatus = row.querySelector('.order-status');

    if (orderStatus && orderStatus.innerHTML == 'Shipped') {
        // Make Invoice Page
        var shippedButton = row.querySelector('.shipped-button');

        if (shippedButton) {
            shippedButton.innerHTML = 'Not available';
            shippedButton.style.color = '#9C9FA6';
        }
    }

    if (orderStatus && orderStatus.innerHTML == 'Completed') {
        // Make Invoice Page
        var shippedButton = row.querySelector('.shipped-button');
        var completedButton = row.querySelector('.completed-button');
        var canceledButton = row.querySelector('.canceled-button');

        if (shippedButton) {
            shippedButton.innerHTML = 'Not available';
            shippedButton.style.color = '#9C9FA6';
        }
        if (completedButton) {
            completedButton.innerHTML = 'Not available';
            completedButton.style.color = '#9C9FA6';
        }
        if (canceledButton) {
            canceledButton.innerHTML = 'Not available';
            canceledButton.style.color = '#9C9FA6';
        }
    }

    if (orderStatus && orderStatus.innerHTML == 'Canceled') {
        // Payment Page
        var paidBtn = row.querySelector('.paid-btn');
        var cancelBtn = row.querySelector('.cancel-btn');
        if (paidBtn) {
            paidBtn.innerHTML = 'Not available';
            paidBtn.style.color = '#9C9FA6';
        }
        if (cancelBtn) {
            cancelBtn.innerHTML = 'Not available';
            cancelBtn.style.color = '#9C9FA6';
        }
        // Invoice Page
        var invoiceButton = row.querySelector('.invoice-button');
        var shippedButton = row.querySelector('.shipped-button');
        var completedButton = row.querySelector('.completed-button');
        var canceledButton = row.querySelector('.canceled-button');

        if (invoiceButton) {
            invoiceButton.innerHTML = 'Not available';
            invoiceButton.style.color = '#9C9FA6';
        }
        if (shippedButton) {
            shippedButton.innerHTML = 'Not available';
            shippedButton.style.color = '#9C9FA6';
        }
        if (completedButton) {
            completedButton.innerHTML = 'Not available';
            completedButton.style.color = '#9C9FA6';
        }
        if (canceledButton) {
            canceledButton.innerHTML = 'Not available';
            canceledButton.style.color = '#9C9FA6';
        }
    }

    // Payment Page
    var paymentStatus = row.querySelector('.payment-status');
    if (paymentStatus && paymentStatus.innerHTML == 'Paid') {
        var paidBtn = row.querySelector('.paid-btn');
        if (paidBtn) {
            paidBtn.innerHTML = 'Not available';
            paidBtn.style.color = '#9C9FA6';
        }
    }
});




