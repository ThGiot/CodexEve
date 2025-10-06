import FetchHandler from '../classes/FetchHandler.js';
const fetcher = new FetchHandler(true);
export function connect_send_sms(param){
    const { messageId } = param;
    const smsSendData = {
      node: "action",
      action: "connect_sms_send",
      message_id: messageId
    };
  
    // Utilisez une fonction asynchrone anonyme ici
    (async () => {
      let response;
  
      try {
        do {
          response = await fetcher.sendRequest(smsSendData);
          console.log(response);
  
          if (response.isComplete !== 'true') {
            // Attendre avant de réessayer
            console.log(response);
            document.getElementById("progressBar").innerHTML = `<div class="progress-bar bg-success rounded-3" role="progressbar" style="width: ${response.progress}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">${response.progress}%</div>`;
            
            await new Promise(resolve => setTimeout(resolve, 15)); // Attendre 1 seconde
          }
        } while (response.isComplete !== 'true');
        console.info('sms envoyé');
        buttonSubmit.innerHTML = 'Envoyer';
        buttonSubmit.disabled = false;
       
      } catch (error) {
        console.error('Erreur lors de l\'envoi du SMS:', error);
        buttonSubmit.innerHTML = 'Envoyer';
        buttonSubmit.disabled = false;
      }
    })();
  
  }