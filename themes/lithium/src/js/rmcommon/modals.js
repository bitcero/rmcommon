const modals = {
  /**
   * Base template used for all modals
   */
  template: function (options) {
    // Extract vars
    const {message, content, title, icon, width, size, id, closeButton, helpButton, color} = options;

    let modal = document.createElement('div');
    modal.className = 'modal fade ';

    if (id) {
      modal.id = id;
    }

    const final_content = content || message;
    const final_width = () => {
      switch (width) {
        case 'small':
          return 'modal-sm';
        case 'large':
          return 'modal-lg';
        case 'extra-large':
          return 'modal-xl';
        default:
          return '';
      }
    }

    modal.setAttribute('tabindex', '-1');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-labelledby', 'modal-title');
    modal.setAttribute('aria-hidden', 'true');

    modal.innerHTML = `<div class="modal-dialog ${final_width()}" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        ${title ? `<h5 class="modal-title" id="modal-title">${title}</h5>` : ''}
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${final_content}
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-end justify-content-between">
                        ${closeButton ? `<button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>` : ''}
                    </div>
                </div>
            </div>`;

    return modal;
  },

  show: function (options) {
    const modal = this.template(options);
    document.body.appendChild(modal);
    let bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    modal.addEventListener('hidden.bs.modal', event => {
      modal.remove();
    });
    return modal;
  },
};

modals.dialog = modals.show;

(function () {
  // Detach all modals with class attach-to-body and attach them to body
  let modals = document.querySelectorAll('.modal.attach-to-body');
  modals.forEach(modal => {
    document.body.appendChild(modal);
  });

  modals = document.querySelectorAll('.modal[data-cu-body-attach]');
  modals.forEach(modal => {
    document.body.appendChild(modal);
  });

  // Show all modal elements with attribute data-cu-show
  let show = document.querySelectorAll('.modal[data-cu-show]');
  show.forEach(modal => {
    let bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  });
})();