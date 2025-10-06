window.smartAction = function(config) {
    const {
      action,
      formId = null,
      modalId = null,
      confirmButtonId = null,
      extra = {},
      onSuccess = null,
      onError = null,
      endpoint = "node.php",
      node = "action",
      confirm = true,
    } = config;
  
    const payload = { node, action, ...extra };
  
    if (formId) {
      const form = document.getElementById(formId);
      if (form) payload.form_field = getFormFields(form);
      else console.warn(`[smartAction] Formulaire introuvable : ${formId}`);
    }
  
    if (modalId && confirm) {
      const modal = new bootstrap.Modal(document.getElementById(modalId));
      modal.show();
  
      const confirmBtn = document.getElementById(confirmButtonId);
      if (!confirmBtn) {
        console.warn(`[smartAction] Bouton ${confirmButtonId} introuvable.`);
        return;
      }
  
      confirmBtn.onclick = () => {
        modal.hide();
        sendRequest(payload, endpoint, onSuccess, onError);
      };
    } else {
      sendRequest(payload, endpoint, onSuccess, onError);
    }
  };
  
  function getFormFields(form) {
    return Array.from(form.elements)
      .filter(el => el.name && el.type !== "submit")
      .map(el => ({
        type: el.type,
        name: el.name,
        value: el.value,
        disabled: el.disabled,
      }));
  }
  
  function sendRequest(payload, endpoint, onSuccess, onError) {
    fetch(endpoint, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(payload),
    })
      .then(res => res.json())
      .then(result => {
        showBootstrapToast(result.message || "Action terminée", !result.success);
        if (result.success && typeof onSuccess === "function") onSuccess(result);
        else if (!result.success && typeof onError === "function") onError(result);
      })
      .catch(err => {
        console.error("[smartAction] Erreur fetch :", err);
        showBootstrapToast("Erreur de communication avec le serveur", true);
        if (typeof onError === "function") onError(err);
      });
  }
  
  function showBootstrapToast(message, isError = false) {
    // Supprimer les anciens toasts s’ils existent
    const oldToast = document.getElementById('smart-toast');
    if (oldToast) oldToast.remove();
  
    const toast = document.createElement('div');
    toast.id = 'smart-toast';
  
    toast.className = `
      toast align-items-center border-0 position-fixed top-0 end-0 m-4 
      ${isError ? 'bg-danger text-white' : 'bg-light text-dark'}
    `.trim();
  
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
  
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
  
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
      </div>
    `;
  
    document.body.appendChild(toast);
  
    const bsToast = new bootstrap.Toast(toast, {
      delay: isError ? 5000 : 3000,
      autohide: true
    });
    bsToast.show();
  }
  