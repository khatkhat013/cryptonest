// Plan Details Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('planDetailsModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const plan = button.getAttribute('data-plan');
            const duration = button.getAttribute('data-duration');
            const quantity = button.getAttribute('data-quantity');
            const revenue = button.getAttribute('data-revenue');

            // Update modal content
            modal.querySelector('.plan-name').textContent = plan + ' Plan';
            modal.querySelector('.plan-duration').textContent = duration;
            modal.querySelector('.plan-quantity').textContent = quantity;
            modal.querySelector('.plan-revenue').textContent = revenue;

            // Update modal title
            modal.querySelector('#planDetailsModalLabel').textContent = 'Join AI Arbitrage - ' + plan + ' Plan';
        });
    }
});