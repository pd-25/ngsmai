<div id="renewalModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Please do your server renewal</h2>
        <p>Your server will expire on 21st Dec. Please take action.</p>
    </div>
</div>

<style>
    /* Modal styles */
    .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        /* Black with opacity */
    }

    .modal-content {
        background-color: white;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<script>
    window.onload = function() {
        // Function to show the modal
        function showModal() {
            var modal = document.getElementById("renewalModal");
            var closeModal = document.getElementById("closeModal");
            modal.style.display = "block"; // Show the modal

            // Close the modal when the user clicks on the close button
            closeModal.onclick = function() {
                modal.style.display = "none"; // Hide the modal
            }

            // Close the modal if the user clicks outside of the modal content
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }

        // Show the modal initially and then every 5 minutes
        setInterval(showModal, 1 * 60 * 1000); // Show modal every 5 minutes in milliseconds

        // Show the modal immediately after page load
        showModal();
    }
</script>
