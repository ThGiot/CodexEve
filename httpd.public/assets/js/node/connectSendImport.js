import FetchHandler from '../classes/FetchHandler.js';
import Toast from '../classes/Toast.js';
import { connect_send_sms } from './connect_send_sms.js';

const fetcher = new FetchHandler(true);

export function connectSendImport(params) {
  const { message, formId } = params;

  const formElement = document.getElementById(formId);
  const buttonSubmit = document.getElementById('buttonSubmit');
  buttonSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Chargement...';
  buttonSubmit.disabled = true;

  if (!formElement) {
    console.error("Formulaire non trouvé");
    return;
  }

  // Créer un objet FormData à partir de l'élément form
  const formData = new FormData(formElement);

  // Spécifiez des paramètres supplémentaires si nécessaire
  formData.append("node", "action");
  formData.append("action", "connect_send_byexcell");

  fetcher.sendRequest(formData)
  .then(data => {
    if (data.success) {
      if (data.execute === 'true') {
        connect_send_sms({ messageId: data.messageId });
      } else {
        formElement.reset(); 
        const message = "Message enregistré et programmé";
            const options = {
                position: 'top-right', // Par exemple, 'top-right', 'bottom-center', etc.
                actionButton: false,    // Si vous voulez afficher le bouton "Take action"
                closeButton: true,     // Si vous voulez afficher le bouton "Close"
                //backgroundColor: 'red', // Couleur de fond que vous souhaitez utiliser
                delay: 2000
                };
        const myToast = new Toast(message, options);
        buttonSubmit.innerHTML = 'Envoyer';
        buttonSubmit.disabled = false;
      }
    } else {
      alert('Erreur : ' + data.message);
    }
  })
  .catch(error => {
    console.error('Une erreur est survenue :', error);
    buttonSubmit.innerHTML = 'Envoyer';
    buttonSubmit.disabled = false;
  });

}



