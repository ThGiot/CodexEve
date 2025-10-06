class Toast {
    constructor(message, options = {}) {
      this.message = message;
      this.options = options;
      this.container = this.createContainer();
      this.toast = this.createToast();
      this.container.appendChild(this.toast);
      document.body.appendChild(this.container);
      this.setPosition();
    }
  
    createContainer() {
      const container = document.createElement('div');
      container.className = 'toast-container';
      return container;
    }
  
    setPosition() {
      const { position = 'top-right' } = this.options;
      const positions = position.split('-');
      const vertical = positions[0];
      const horizontal = positions[1];
  
      this.container.style.position = 'fixed';
      this.container.style[vertical] = '10px';
  
      switch (horizontal) {
        case 'left':
          this.container.style.left = '10px';
          break;
        case 'center':
          this.container.style.left = '50%';
          this.container.style.transform = 'translateX(-50%)';
          break;
        case 'right':
          this.container.style.right = '10px';
          break;
      }
    }

    createToast() {
        const toast = document.createElement('div');
        toast.className = 'toast show align-items-center';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        if (this.options.backgroundColor) {
          toast.style.backgroundColor = this.options.backgroundColor;
        }
    
        const toastContent = document.createElement('div');
        toastContent.className = 'd-flex';
        toastContent.innerHTML = `
          <div class="toast-body">${this.message}</div>
          ${this.options.closeButton !== false ? '<button class="btn ms-2 p-0" type="button" data-bs-dismiss="toast" aria-label="Close"><span class="uil uil-times fs-1"></span></button>' : ''}
        `;
    
        const toastFooter = document.createElement('div');
        toastFooter.className = 'py-3 border-top';
        toastFooter.innerHTML = `
          ${this.options.actionButton ? '<button class="btn btn-primary btn-sm" type="button">Take action</button>' : ''}
          ${this.options.closeButton !== false ? '<button class="btn btn-secondary btn-sm ms-2" type="button" data-bs-dismiss="toast">Close</button>' : ''}
        `;
    
        toast.appendChild(toastContent);
        toast.appendChild(toastFooter);
    
        return toast;
      }
    
      show() {
        const toastOptions = {
            autohide: this.options.hasOwnProperty('autohide') ? this.options.autohide : true,
            delay: this.options.delay || 3000 // Définir un délai par défaut
        };
    
        // Instanciation correcte du toast Bootstrap
        const toastInstance = new bootstrap.Toast(this.toast, toastOptions);
        toastInstance.show();
    
        // Si `autohide` est activé, on programme la suppression après le délai
        if (toastOptions.autohide) {
            setTimeout(() => {
                toastInstance.hide(); // Cache le toast via Bootstrap
                this.toast.remove(); // Supprime du DOM
            }, toastOptions.delay);
        }
    }
    
    
    
    }
    export default Toast;
   