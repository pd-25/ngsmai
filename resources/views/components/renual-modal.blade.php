<div id="renewalModal" class="modal">
    <div class="modal-content">
        <h2 class="modal-title">Server Renewal Reminder</h2>
        <p>Your server is set to expire on <strong>21st December</strong>. Please take action to avoid service
            disruption.</p>
        <div class="modal-actions">
            <button class="action-button">Renew Now</button>
        </div>
    </div>
</div>

<style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        /* Slightly darker overlay */
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.3s ease-in-out;
        /* Smooth fade-in effect */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fff;
        border-radius: 10px;
        padding: 30px;
        width: 400px;
        max-width: 90%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        /* Soft shadow */
        text-align: center;
    }

    /* Modal Title */
    .modal-title {
        font-family: 'Arial', sans-serif;
        font-size: 24px;
        color: #333;
        margin-bottom: 15px;
        font-weight: 600;
    }

    /* Modal Text */
    .modal-content p {
        font-family: 'Arial', sans-serif;
        font-size: 16px;
        color: #555;
        margin-bottom: 20px;
    }

    /* Button Styles */
    .action-button {
        background-color: #007bff;
        /* Blue background */
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        font-weight: 500;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .action-button:hover {
        background-color: #0056b3;
        /* Darker blue on hover */
    }

    /* Animation for Modal */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Prevent clicks from closing the modal */
    .modal-overlay {
        pointer-events: none;
    }
</style>
<script>
    function showModal() {
        var modal = document.getElementById("renewalModal");
        var closeModal = document.getElementById("closeModal");
        console.log("showModal function called");
        modal.style.display = "block";
        closeModal.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    }

    showModal();
    console.log("Initial modal shown");

    setInterval(function() {
        console.log("5 minutes passed - showing modal");
        showModal();
    }, 1 * 60 * 1000);
</script>
