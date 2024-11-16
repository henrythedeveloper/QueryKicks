document.addEventListener('DOMContentLoaded', function() {
    const moneyModal = document.getElementById('addMoneyModal');
    const moneyModalClose = document.querySelector('.money-modal-close');
    const addMoneyForm = document.getElementById('addMoneyForm');

    window.openMoneyModal = function() {
        moneyModal.style.display = 'block';
    }

    window.closeMoneyModal = function() {
        moneyModal.style.display = 'none';
    }

    if (moneyModalClose) {
        moneyModalClose.onclick = closeMoneyModal;
    }

    window.addEventListener('click', function(event) {
        if (event.target === moneyModal) {
            closeMoneyModal();
        }
    });

    if (addMoneyForm) {
        addMoneyForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const amountInput = document.getElementById('amount');
            const amount = parseFloat(amountInput.value);

            if (isNaN(amount) || amount <= 0) {
                alert('Please enter a valid amount greater than 0');
                return;
            }

            const submitBtn = addMoneyForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('index.php?controller=UserController&action=updateMoney', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        amount: amount.toFixed(2)
                    })
                });

                // Log the raw response for debugging
                const responseText = await response.text();
                console.log('Raw response:', responseText);

                // Try to parse the response as JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (error) {
                    console.error('JSON parse error:', error);
                    throw new Error('Invalid server response');
                }

                if (data.success) {
                    const balanceDisplay = document.querySelector('.user-money span');
                    balanceDisplay.textContent = `Balance: $${parseFloat(data.newBalance).toFixed(2)}`;
                    addMoneyForm.reset();
                    closeMoneyModal();
                    alert('Money added successfully!');
                } else {
                    throw new Error(data.message || 'Error adding money');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(`Error: ${error.message}`);
            } finally {
                submitBtn.textContent = originalBtnText;
                submitBtn.disabled = false;
            }
        });
    }
});