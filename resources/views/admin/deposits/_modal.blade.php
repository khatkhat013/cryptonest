<!-- Edit Deposit Modal (shared partial) -->
<div class="modal fade" id="editDepositModal" tabindex="-1" aria-labelledby="editDepositModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editDepositModalLabel">Edit Deposit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="edit-deposit-form" method="POST" action="">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" id="modal-amount" class="form-control" step="0.00000001" />
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
        <select name="action_status_id" id="modal-status" class="form-select">
          @foreach($statuses ?? App\Models\ActionStatus::orderBy('id')->get() as $s)
            <option value="{{ $s->id }}">{{ ucfirst($s->name) }}</option>
          @endforeach
        </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Populate modal and set action
var editModal = document.getElementById('editDepositModal');
if (editModal) {
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var amount = button.getAttribute('data-amount');
        var statusId = button.getAttribute('data-status-id');
        var action = button.getAttribute('data-action');

        document.getElementById('modal-amount').value = amount;
        document.getElementById('modal-status').value = statusId;

        var form = document.getElementById('edit-deposit-form');
        if (action) {
            form.action = action;
        } else {
            form.action = '/admin/deposits/' + id + '/status';
        }
    });
}
</script>
@endpush

@push('scripts')
<script>
// AJAX submit for edit-deposit-form to surface errors and ensure proper POST
document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('edit-deposit-form');
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    var action = form.action;
    if (!action) return alert('Form action not set');

    var submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    var formData = new FormData(form);

    fetch(action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData,
      credentials: 'same-origin'
    }).then(function(res) {
      // Read the body once as text, then attempt to parse JSON if needed.
      return res.text().then(function(text) {
        if (res.ok) {
          // Success: close modal and reload to reflect changes
          var modalEl = document.getElementById('editDepositModal');
          var modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();
          window.location.reload();
          return;
        }

        // Not OK: try to parse JSON body for a useful message, otherwise use raw text
        var parsed = null;
        try {
          parsed = text ? JSON.parse(text) : null;
        } catch (e) {
          parsed = null;
        }

        if (parsed && parsed.message) {
          throw parsed;
        }

        if (parsed) {
          throw parsed;
        }

        throw { message: text || 'Unknown error' };
      });
    }).catch(function(err) {
      console.error('Update failed', err);
      var msg = (err && err.message) ? err.message : JSON.stringify(err);
      alert('Failed to update deposit: ' + msg);
    }).finally(function() {
      if (submitBtn) submitBtn.disabled = false;
    });
  });
});
</script>
@endpush

@push('scripts')
<script>
// Delete deposit via fetch with CSRF token (shared for deposits page)
document.addEventListener('DOMContentLoaded', function() {
  var csrfToken = '{{ csrf_token() }}';
  document.querySelectorAll('.delete-deposit-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      if (!confirm('Are you sure you want to delete this deposit?')) return;
      var action = btn.getAttribute('data-action');
      if (!action) return alert('Delete action URL missing');

      fetch(action, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        }
      }).then(function(res) {
        if (res.ok) {
          // reload to reflect deletion
          window.location.reload();
        } else {
          return res.text().then(function(txt) { throw new Error(txt || 'Delete failed'); });
        }
      }).catch(function(err) {
        console.error('Delete failed', err);
        alert('Failed to delete deposit. See console for details.');
      });
    });
  });
});
</script>
@endpush
