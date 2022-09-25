const modals = {
    /**
     * Base template used for all modals
     */
    template: function ({title, content, buttons, cssClass, onCancel, onAccept}) {
        let modal = document.createElement('div');
        modal.className = 'modal fade ';
        modal.setAttribute('tabindex', '-1');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-labelledby', 'modal-title');
        modal.setAttribute('aria-hidden', 'true');
        modal.innerHTML = `<div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        ${title ? '' : `<h5 class="modal-title" id="modal-title">${title}</h5>`}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${content}
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-between">
                        ${buttons}
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
    }
};